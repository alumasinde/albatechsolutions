<?php

declare(strict_types=1);

namespace App\Core\Notifications;

use App\Core\Logger;
use GuzzleHttp\Client;

/** WhatsApp Cloud API adapter. Transactional outbound messages use templates. */
final class WhatsAppNotificationChannel implements NotificationChannel
{
    public function name(): string { return 'whatsapp'; }

    public function available(): bool
    {
        return ($_ENV['WHATSAPP_ENABLED'] ?? 'false') === 'true'
            && !empty($_ENV['WHATSAPP_ACCESS_TOKEN'] ?? null)
            && !empty($_ENV['WHATSAPP_PHONE_NUMBER_ID'] ?? null);
    }

    public function send(NotificationMessage $message): NotificationResult
    {
        if (!$this->available()) return NotificationResult::failure('WhatsApp provider is not configured.');
        $to = $this->normaliseKenyanPhone($message->recipient);
        if ($to === null) return NotificationResult::failure('Invalid Kenyan WhatsApp number.');
        if ($message->template === null || $message->template === '') return NotificationResult::failure('WhatsApp template is required.');

        $version = (string)($_ENV['WHATSAPP_GRAPH_VERSION'] ?? 'v23.0');
        $url = 'https://graph.facebook.com/' . rawurlencode($version) . '/' . rawurlencode((string)$_ENV['WHATSAPP_PHONE_NUMBER_ID']) . '/messages';
        $parameters = [];
        foreach ($message->data as $value) {
            $parameters[] = ['type' => 'text', 'text' => mb_substr((string)$value, 0, 1024)];
        }

        try {
            $client = new Client(['timeout' => 15, 'http_errors' => false]);
            $response = $client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . (string)$_ENV['WHATSAPP_ACCESS_TOKEN'],
                    'Content-Type' => 'application/json',
                ],
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
                Logger::warning('WhatsApp provider rejected message', ['status' => $status, 'body' => mb_substr($body, 0, 500)]);
                return NotificationResult::failure('WhatsApp provider rejected the request.');
            }
            $json = json_decode($body, true);
            $id = is_array($json) ? (string)($json['messages'][0]['id'] ?? '') : '';
            return NotificationResult::success($id !== '' ? $id : null);
        } catch (\Throwable $e) {
            Logger::warning('WhatsApp notification failed: ' . $e->getMessage(), ['to' => $to]);
            return NotificationResult::failure('WhatsApp delivery failed.');
        }
    }

    private function normaliseKenyanPhone(string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        if (str_starts_with($digits, '0') && strlen($digits) === 10) $digits = '254' . substr($digits, 1);
        if (str_starts_with($digits, '7') && strlen($digits) === 9) $digits = '254' . $digits;
        if (!preg_match('/^2547\d{8}$/', $digits)) return null;
        return $digits;
    }
}
