<?php

declare(strict_types=1);

namespace App\Core\Notifications;

use App\Core\Logger;
use GuzzleHttp\Client;

/**
 * WhatsApp provider adapter.
 *
 * Supported providers:
 * - meta: WhatsApp Cloud API (production)
 * - callmebot: temporary personal admin alerts only
 */
final class WhatsAppNotificationChannel implements NotificationChannel
{
    public function name(): string { return 'whatsapp'; }

    public function available(): bool
    {
        if (($_ENV['WHATSAPP_ENABLED'] ?? 'false') !== 'true') return false;
        return match ($this->provider()) {
            'callmebot' => !empty($_ENV['CALLMEBOT_API_KEY'] ?? null),
            'meta' => !empty($_ENV['WHATSAPP_ACCESS_TOKEN'] ?? null)
                && !empty($_ENV['WHATSAPP_PHONE_NUMBER_ID'] ?? null),
            default => false,
        };
    }

    public function send(NotificationMessage $message): NotificationResult
    {
        if (!$this->available()) return NotificationResult::failure('WhatsApp provider is not configured.');

        return match ($this->provider()) {
            'callmebot' => $this->sendViaCallMeBot($message),
            'meta' => $this->sendViaMeta($message),
            default => NotificationResult::failure('Unknown WhatsApp provider.'),
        };
    }

    private function provider(): string
    {
        return strtolower(trim((string)($_ENV['WHATSAPP_PROVIDER'] ?? 'meta')));
    }

    private function sendViaCallMeBot(NotificationMessage $message): NotificationResult
    {
        $to = $this->normalisePhone($message->recipient);
        if ($to === null) return NotificationResult::failure('Invalid WhatsApp number.');
        $apiKey = trim((string)($_ENV['CALLMEBOT_API_KEY'] ?? ''));
        if ($apiKey === '') return NotificationResult::failure('CallMeBot API key is not configured.');

        try {
            $client = new Client(['timeout' => 15, 'http_errors' => false]);
            $response = $client->get('https://api.callmebot.com/whatsapp.php', [
                'query' => [
                    'source' => 'AlbaTech',
                    'phone' => '+' . $to,
                    'apikey' => $apiKey,
                    'text' => mb_substr($message->body, 0, 3500),
                ],
            ]);
            $status = $response->getStatusCode();
            $body = (string)$response->getBody();
            if ($status < 200 || $status >= 300 || stripos($body, 'error') !== false) {
                Logger::warning('CallMeBot rejected message', ['status' => $status, 'body' => mb_substr($body, 0, 500)]);
                return NotificationResult::failure('CallMeBot rejected the request.');
            }
            return NotificationResult::success('callmebot-' . substr(hash('sha256', $to . '|' . $message->body . '|' . microtime(true)), 0, 24));
        } catch (\Throwable $e) {
            Logger::warning('CallMeBot notification failed: ' . $e->getMessage(), ['to' => $to]);
            return NotificationResult::failure('CallMeBot delivery failed.');
        }
    }

    private function sendViaMeta(NotificationMessage $message): NotificationResult
    {
        $to = $this->normalisePhone($message->recipient);
        if ($to === null) return NotificationResult::failure('Invalid WhatsApp number.');
        if ($message->template === null || $message->template === '') return NotificationResult::failure('WhatsApp template is required for Meta.');

        $version = (string)($_ENV['WHATSAPP_GRAPH_VERSION'] ?? 'v23.0');
        $url = 'https://graph.facebook.com/' . rawurlencode($version) . '/' . rawurlencode((string)$_ENV['WHATSAPP_PHONE_NUMBER_ID']) . '/messages';
        $parameters = [];
        foreach ($message->data as $value) $parameters[] = ['type' => 'text', 'text' => mb_substr((string)$value, 0, 1024)];

        try {
            $client = new Client(['timeout' => 15, 'http_errors' => false]);
            $response = $client->post($url, [
                'headers' => ['Authorization' => 'Bearer ' . (string)$_ENV['WHATSAPP_ACCESS_TOKEN'], 'Content-Type' => 'application/json'],
                'json' => [
                    'messaging_product' => 'whatsapp',
                    'to' => $to,
                    'type' => 'template',
                    'template' => [
                        'name' => $message->template,
                        'language' => ['code' => $message->language ?: (string)($_ENV['WHATSAPP_TEMPLATE_LANGUAGE'] ?? 'en_US')],
                        'components' => $parameters ? [['type' => 'body', 'parameters' => $parameters]] : [],
                    ],
                ],
            ]);
            $status = $response->getStatusCode();
            $body = (string)$response->getBody();
            if ($status < 200 || $status >= 300) {
                Logger::warning('Meta WhatsApp rejected message', ['status' => $status, 'body' => mb_substr($body, 0, 500)]);
                return NotificationResult::failure('WhatsApp provider rejected the request.');
            }
            $json = json_decode($body, true);
            $id = is_array($json) ? (string)($json['messages'][0]['id'] ?? '') : '';
            return NotificationResult::success($id !== '' ? $id : null);
        } catch (\Throwable $e) {
            Logger::warning('Meta WhatsApp notification failed: ' . $e->getMessage(), ['to' => $to]);
            return NotificationResult::failure('WhatsApp delivery failed.');
        }
    }

    private function normalisePhone(string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        if (str_starts_with($digits, '0') && strlen($digits) === 10) $digits = '254' . substr($digits, 1);
        if (str_starts_with($digits, '7') && strlen($digits) === 9) $digits = '254' . $digits;
        if (!preg_match('/^2547\d{8}$/', $digits)) return null;
        return $digits;
    }
}
