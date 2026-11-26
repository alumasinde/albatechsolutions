<?php

declare(strict_types=1);

namespace AlbaTech\Core;

/**
 * Minimal WhatsApp Cloud API client (graph.facebook.com).
 * Credentials live in .env: WHATSAPP_PHONE_NUMBER_ID + WHATSAPP_ACCESS_TOKEN.
 */
final class WhatsAppApi
{
    private const API_BASE = 'https://graph.facebook.com/v22.0';

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

    /** @return array{ok: bool, status: int, response: array} */
    public static function sendText(string $to, string $body): array
    {
        return self::post([
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'text',
            'text' => ['preview_url' => true, 'body' => $body],
        ]);
    }

    /**
     * Send an approved template. $params as a list fills positional {{1}}/{{2}};
     * an assoc array fills named parameters via parameter_name. If a named call
     * is rejected (template actually uses positional), it retries positionally.
     *
     * @param array<int|string, string> $params
     * @return array{ok: bool, status: int, response: array}
     */
    public static function sendTemplate(string $to, string $template, string $language, array $params): array
    {
        $result = self::post(self::templatePayload($to, $template, $language, $params));
        $isNamed = $params !== [] && array_keys($params) !== range(0, count($params) - 1);
        if (!$result['ok'] && $isNamed) {
            $retry = self::post(self::templatePayload($to, $template, $language, array_values($params)));
            // When both fail, the original error is the informative one — the
            // retry's is usually just "parameter name is missing or empty".
            if ($retry['ok']) {
                return $retry;
            }
        }
        return $result;
    }

    /** @param array<int|string, string> $params */
    private static function templatePayload(string $to, string $template, string $language, array $params): array
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
        return [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => $templateBody,
        ];
    }

    /** @return array{ok: bool, status: int, response: array} */
    private static function post(array $payload): array
    {
        if (!self::configured()) {
            return ['ok' => false, 'status' => 0, 'response' => ['error' => ['message' => 'WhatsApp API is not configured (set WHATSAPP_PHONE_NUMBER_ID and WHATSAPP_ACCESS_TOKEN in .env).']]];
        }
        $ch = curl_init(self::API_BASE . '/' . rawurlencode(trim((string) Env::get('WHATSAPP_PHONE_NUMBER_ID', ''))) . '/messages');
        if ($ch === false) {
            return ['ok' => false, 'status' => 0, 'response' => ['error' => ['message' => 'curl_init failed']]];
        }
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . trim((string) Env::get('WHATSAPP_ACCESS_TOKEN', '')),
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => (string) json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            CURLOPT_TIMEOUT => 15,
        ]);
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
            error_log('[WhatsApp API] HTTP ' . $status . ': ' . substr((string) $raw, 0, 500));
        }
        return ['ok' => $ok, 'status' => $status, 'response' => is_array($decoded) ? $decoded : []];
    }
}
