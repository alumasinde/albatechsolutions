<?php

declare(strict_types=1);

namespace AlbaTech\Controllers;

use AlbaTech\Core\Env;
use AlbaTech\Core\Request;
use AlbaTech\Core\Response;

/**
 * WhatsApp Cloud API webhook.
 *
 * GET  /webhooks/whatsapp  Meta verification handshake (subscribe + verify_token -> echo challenge).
 * POST /webhooks/whatsapp  Incoming message/status events; payloads are appended to
 *                          storage/logs/whatsapp-webhook.log for now. Respond 200 quickly so Meta
 *                          does not retry; processing can be layered on later.
 */
final class WebhookController
{
    public function verify(Request $request): void
    {
        $token = trim((string) Env::get('WHATSAPP_VERIFY_TOKEN', ''));
        $mode = (string) $request->input('hub_mode', $request->input('hub.mode', ''));
        $verifyToken = (string) $request->input('hub_verify_token', $request->input('hub.verify_token', ''));
        $challenge = (string) $request->input('hub_challenge', $request->input('hub.challenge', ''));

        if ($token !== '' && $mode === 'subscribe' && hash_equals($token, $verifyToken) && $challenge !== '') {
            Response::text($challenge);
            return;
        }
        Response::text('Forbidden', 403);
    }

    public function receive(Request $request): void
    {
        $raw = (string) file_get_contents('php://input');

        // Optional signature check when the app secret is configured. Meta sends
        // X-Hub-Signature-256: sha256=<hmac of body with app secret>.
        $secret = trim((string) Env::get('WHATSAPP_APP_SECRET', ''));
        if ($secret !== '') {
            $signature = (string) ($_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '');
            $expected = 'sha256=' . hash_hmac('sha256', $raw, $secret);
            if (!hash_equals($expected, $signature)) {
                Response::text('Forbidden', 403);
                return;
            }
        }

        $payload = json_decode($raw, true);
        $entry = is_array($payload) ? json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : $raw;
        $logDir = dirname(__DIR__, 2) . '/storage/logs';
        if (!is_dir($logDir)) { mkdir($logDir, 0755, true); }
        file_put_contents($logDir . '/whatsapp-webhook.log', '[' . date('c') . '] ' . $entry . "\n", FILE_APPEND | LOCK_EX);

        Response::text('EVENT_RECEIVED');
    }
}
