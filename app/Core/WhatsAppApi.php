<?php

declare(strict_types=1);

namespace AlbaTech\Core;

/**
 * Minimal WhatsApp Cloud API client (graph.facebook.com).
 * Credentials live in .env: WHATSAPP_PHONE_NUMBER_ID + WHATSAPP_ACCESS_TOKEN.
 * Builds payloads and performs HTTP calls only; recording, retries and policy
 * live in AlbaTech\Services\WhatsAppMessenger.
 */
final class WhatsAppApi
{
    private const API_BASE = 'https://graph.facebook.com/v22.0';
    public const MAX_MEDIA_BYTES = 20971520;

    public static function configured(): bool
    {
        return trim((string) Env::get('WHATSAPP_PHONE_NUMBER_ID', '')) !== ''
            && trim((string) Env::get('WHATSAPP_ACCESS_TOKEN', '')) !== '';
    }

    /**
     * Normalize a phone input to international digits-only format for the API.
     * Kenyan numbers entered as 07... / 01... become 2547... / 2541...
     */
    public static function normalizeNumber(string $raw): string
    {
        $digits = preg_replace('/\D+/', '', $raw) ?? '';
        if (str_starts_with($digits, '0') && strlen($digits) === 10) {
            return '254' . substr($digits, 1);
        }
        if (strlen($digits) === 9 && preg_match('/^[17]/', $digits) === 1) {
            return '254' . $digits;
        }
        return $digits;
    }

    public static function textPayload(string $to, string $body): array
    {
        return [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'text',
            'text' => ['preview_url' => true, 'body' => $body],
        ];
    }

    /**
     * Reply buttons (max 3, titles up to 20 characters).
     * @param array<string, string> $buttons id => title
     */
    public static function buttonsPayload(string $to, string $body, array $buttons): array
    {
        $rows = [];
        foreach (array_slice($buttons, 0, 3, true) as $id => $title) {
            $rows[] = ['type' => 'reply', 'reply' => ['id' => (string) $id, 'title' => mb_substr($title, 0, 20)]];
        }
        return [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'interactive',
            'interactive' => ['type' => 'button', 'body' => ['text' => mb_substr($body, 0, 1024)], 'action' => ['buttons' => $rows]],
        ];
    }

    /**
     * $params as a list fills positional {{1}}/{{2}}; an assoc array fills named parameters.
     * @param array<int|string, string> $params
     */
    public static function templatePayload(string $to, string $template, string $language, array $params): array
    {
        $isList = array_keys($params) === range(0, count($params) - 1);
        $parameters = [];
        foreach ($params as $key => $value) {
            $parameter = ['type' => 'text', 'text' => (string) $value];
            if (!$isList) {
                $parameter['parameter_name'] = (string) $key;
            }
            $parameters[] = $parameter;
        }
        $templateBody = ['name' => $template, 'language' => ['code' => $language !== '' ? $language : 'en']];
        if ($parameters !== []) {
            $templateBody['components'] = [['type' => 'body', 'parameters' => $parameters]];
        }
        return ['messaging_product' => 'whatsapp', 'to' => $to, 'type' => 'template', 'template' => $templateBody];
    }

    /**
     * Send an approved template. If a named-parameter call is rejected (template actually
     * uses positional variables), retry once positionally and report whichever is informative.
     *
     * @param array<int|string, string> $params
     * @return array{ok: bool, status: int, response: array, payload: array}
     */
    public static function sendTemplate(string $to, string $template, string $language, array $params): array
    {
        $payload = self::templatePayload($to, $template, $language, $params);
        $result = self::send($payload);
        $isNamed = $params !== [] && array_keys($params) !== range(0, count($params) - 1);
        if (!$result['ok'] && $isNamed && $result['status'] !== 0) {
            $positional = self::templatePayload($to, $template, $language, array_values($params));
            $retry = self::send($positional);
            if ($retry['ok']) {
                return $retry;
            }
        }
        return $result;
    }

    /** @return array{ok: bool, status: int, response: array, payload: array} */
    public static function send(array $payload): array
    {
        if (!self::configured()) {
            return ['ok' => false, 'status' => -1, 'response' => ['error' => ['code' => 'not_configured', 'message' => 'WhatsApp API is not configured (set WHATSAPP_PHONE_NUMBER_ID and WHATSAPP_ACCESS_TOKEN in .env).']], 'payload' => $payload];
        }
        $url = self::API_BASE . '/' . rawurlencode(trim((string) Env::get('WHATSAPP_PHONE_NUMBER_ID', ''))) . '/messages';
        $result = self::request('POST', $url, (string) json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        $result['payload'] = $payload;
        return $result;
    }

    /** @return array{url: string, mime: string, size: int}|null */
    public static function mediaInfo(string $mediaId): ?array
    {
        if (!self::configured() || preg_match('/^\d{1,40}$/', $mediaId) !== 1) {
            return null;
        }
        $result = self::request('GET', self::API_BASE . '/' . $mediaId, null);
        $url = (string) ($result['response']['url'] ?? '');
        if (!$result['ok'] || !str_starts_with($url, 'https://')) {
            return null;
        }
        return ['url' => $url, 'mime' => (string) ($result['response']['mime_type'] ?? ''), 'size' => (int) ($result['response']['file_size'] ?? 0)];
    }

    /** Download a media URL (needs the bearer token) to $destination. Returns false on any problem. */
    public static function downloadMedia(string $url, string $destination): bool
    {
        $handle = fopen($destination, 'wb');
        if ($handle === false) {
            return false;
        }
        $ch = curl_init($url);
        if ($ch === false) {
            fclose($handle);
            return false;
        }
        $received = 0;
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . trim((string) Env::get('WHATSAPP_ACCESS_TOKEN', ''))],
            CURLOPT_FILE => $handle,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_NOPROGRESS => false,
            CURLOPT_PROGRESSFUNCTION => static function ($ch, float $total, float $downloaded) use (&$received): int {
                $received = (int) $downloaded;
                return $downloaded > self::MAX_MEDIA_BYTES ? 1 : 0;
            },
        ]);
        $ok = curl_exec($ch) !== false && (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE) === 200;
        curl_close($ch);
        fclose($handle);
        if (!$ok || $received > self::MAX_MEDIA_BYTES) {
            @unlink($destination);
            return false;
        }
        return true;
    }

    /** @return array{ok: bool, status: int, response: array} */
    private static function request(string $method, string $url, ?string $body): array
    {
        $ch = curl_init($url);
        if ($ch === false) {
            return ['ok' => false, 'status' => 0, 'response' => ['error' => ['message' => 'curl_init failed']]];
        }
        $headers = ['Authorization: Bearer ' . trim((string) Env::get('WHATSAPP_ACCESS_TOKEN', ''))];
        $options = [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15, CURLOPT_CONNECTTIMEOUT => 5];
        if ($method === 'POST') {
            $headers[] = 'Content-Type: application/json';
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = (string) $body;
        }
        $options[CURLOPT_HTTPHEADER] = $headers;
        curl_setopt_array($ch, $options);
        $raw = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        if ($raw === false) {
            $error = (string) curl_error($ch);
            curl_close($ch);
            error_log('[WhatsApp API] ' . $error);
            return ['ok' => false, 'status' => 0, 'response' => ['error' => ['message' => $error]]];
        }
        curl_close($ch);
        $decoded = json_decode((string) $raw, true);
        $ok = $status >= 200 && $status < 300;
        if (!$ok) {
            error_log('[WhatsApp API] HTTP ' . $status . ': ' . substr((string) $raw, 0, 300));
        }
        return ['ok' => $ok, 'status' => $status, 'response' => is_array($decoded) ? $decoded : []];
    }
}
