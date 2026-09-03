<?php

declare(strict_types=1);

namespace App\Core\Notifications;

use App\Core\Logger;
use GuzzleHttp\Client;

/**
 * Generic JSON SMS adapter. It is intentionally provider-neutral so AlbaTech
 * can connect a Kenyan SMS gateway without coupling the business workflow to
 * one vendor. The endpoint receives: to, message, sender.
 */
final class SmsNotificationChannel implements NotificationChannel
{
    public function name(): string { return 'sms'; }

    public function available(): bool
    {
        return ($_ENV['SMS_ENABLED'] ?? 'false') === 'true'
            && !empty($_ENV['SMS_API_URL'] ?? null)
            && !empty($_ENV['SMS_API_KEY'] ?? null);
    }

    public function send(NotificationMessage $message): NotificationResult
    {
        if (!$this->available()) return NotificationResult::failure('SMS provider is not configured.');
        $to = $this->normaliseKenyanPhone($message->recipient);
        if ($to === null) return NotificationResult::failure('Invalid Kenyan phone number.');

        try {
            $client = new Client(['timeout' => 12, 'http_errors' => false]);
            $response = $client->post((string)$_ENV['SMS_API_URL'], [
                'headers' => [
                    'Authorization' => 'Bearer ' . (string)$_ENV['SMS_API_KEY'],
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'to' => $to,
                    'message' => mb_substr($message->body, 0, 480),
                    'sender' => (string)($_ENV['SMS_SENDER_ID'] ?? 'ALBATECH'),
                ],
            ]);
            $status = $response->getStatusCode();
            $body = (string)$response->getBody();
            if ($status < 200 || $status >= 300) return NotificationResult::failure('SMS provider rejected the request.');
            $json = json_decode($body, true);
            return NotificationResult::success(is_array($json) ? (string)($json['message_id'] ?? $json['id'] ?? '') : null);
        } catch (\Throwable $e) {
            Logger::warning('SMS notification failed: ' . $e->getMessage(), ['to' => $to]);
            return NotificationResult::failure('SMS delivery failed.');
        }
    }

    private function normaliseKenyanPhone(string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        if (str_starts_with($digits, '0') && strlen($digits) === 10) $digits = '254' . substr($digits, 1);
        if (str_starts_with($digits, '7') && strlen($digits) === 9) $digits = '254' . $digits;
        if (!str_starts_with($digits, '254') || strlen($digits) !== 12) return null;
        if (!preg_match('/^2547\d{8}$/', $digits)) return null;
        return '+' . $digits;
    }
}
