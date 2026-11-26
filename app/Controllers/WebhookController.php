<?php

declare(strict_types=1);

namespace AlbaTech\Controllers;

use AlbaTech\Core\Env;
use AlbaTech\Core\Request;
use AlbaTech\Core\Response;
use AlbaTech\Core\WhatsAppPolicy;
use AlbaTech\Repositories\WhatsAppRepository;
use AlbaTech\Services\WhatsAppInbound;
use Throwable;

/**
 * WhatsApp Cloud API webhook.
 *
 * GET  /webhooks/whatsapp  Meta verification handshake.
 * POST /webhooks/whatsapp  Signed events. The body is stored in whatsapp_events and Meta gets
 *                          its 200 straight away; processing (replies, alerts, media) runs after
 *                          the response is sent, and bin/whatsapp-worker.php retries anything
 *                          that did not finish.
 *
 * Unsigned or wrongly signed requests are always rejected. For local testing only, set
 * APP_ENV=local and WHATSAPP_ALLOW_UNSIGNED=true.
 */
final class WebhookController
{
    private const MAX_BODY_BYTES = 1048576;

    public function verify(Request $request): void
    {
        $token = trim((string) Env::get('WHATSAPP_VERIFY_TOKEN', ''));
        $mode = (string) $request->input('hub_mode', $request->input('hub.mode', ''));
        $verifyToken = (string) $request->input('hub_verify_token', $request->input('hub.verify_token', ''));
        $challenge = (string) $request->input('hub_challenge', $request->input('hub.challenge', ''));

        if ($token !== '' && $mode === 'subscribe' && hash_equals($token, $verifyToken) && preg_match('/^[A-Za-z0-9_.-]{1,200}$/', $challenge) === 1) {
            Response::text($challenge);
            return;
        }
        Response::text('Forbidden', 403);
    }

    public function receive(Request $request): void
    {
        $raw = (string) file_get_contents('php://input', false, null, 0, self::MAX_BODY_BYTES + 1);
        if ($raw === '' || strlen($raw) > self::MAX_BODY_BYTES) {
            Response::text('Bad Request', 400);
            return;
        }

        if (!$this->signatureAccepted($raw)) {
            error_log('[WhatsApp webhook] Rejected request with missing or invalid signature from ' . substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45));
            Response::text('Forbidden', 403);
            return;
        }

        $payload = json_decode($raw, true);
        if (!is_array($payload) || ($payload['object'] ?? '') !== 'whatsapp_business_account') {
            Response::text('EVENT_RECEIVED'); // valid signature but nothing for us; do not make Meta retry
            return;
        }

        $repo = new WhatsAppRepository();
        try {
            $eventId = $repo->storeEvent($raw);
        } catch (Throwable $e) {
            // Could not persist: let Meta retry later rather than lose the event.
            error_log('[WhatsApp webhook] Could not store event: ' . $e->getMessage());
            Response::text('Temporarily unavailable', 503);
            return;
        }

        $this->respondAndContinue('EVENT_RECEIVED');

        (new WhatsAppInbound($repo))->processEvent($eventId);
    }

    private function signatureAccepted(string $raw): bool
    {
        $secret = trim((string) Env::get('WHATSAPP_APP_SECRET', ''));
        if (WhatsAppPolicy::signatureValid($raw, $secret, (string) ($_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? ''))) {
            return true;
        }
        return $secret === '' && Env::get('APP_ENV', 'production') === 'local' && Env::bool('WHATSAPP_ALLOW_UNSIGNED', false);
    }

    /** Send the 200 to Meta now and keep running to process the event. */
    private function respondAndContinue(string $body): void
    {
        ignore_user_abort(true);
        set_time_limit(120);
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        http_response_code(200);
        header('Content-Type: text/plain; charset=UTF-8');
        header('Content-Length: ' . strlen($body));
        header('Connection: close');
        echo $body;
        flush();
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } elseif (function_exists('litespeed_finish_request')) {
            litespeed_finish_request();
        }
    }
}
