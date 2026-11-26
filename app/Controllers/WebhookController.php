<?php

declare(strict_types=1);

namespace AlbaTech\Controllers;

use AlbaTech\Core\Env;
use AlbaTech\Core\Request;
use AlbaTech\Core\Response;
use AlbaTech\Core\SmtpMailer;
use AlbaTech\Core\WhatsAppApi;
use AlbaTech\Repositories\SiteSettingsRepository;
use AlbaTech\Repositories\WhatsAppRepository;
use Throwable;

/**
 * WhatsApp Cloud API webhook.
 *
 * GET  /webhooks/whatsapp  Meta verification handshake (subscribe + verify_token -> echo challenge).
 * POST /webhooks/whatsapp  Incoming message/status events. Payloads are logged, inbound messages
 *                          stored in whatsapp_messages, opt-outs (STOP) honoured, and optional
 *                          auto/keyword replies + email alerts sent when enabled in admin settings.
 *                          Always responds 200 quickly so Meta does not retry.
 */
final class WebhookController
{
    /** Keyword patterns -> [reply line, site path]. Replies inside the 24h window need no template. */
    private const KEYWORD_MAP = [
        '/\b(price|pricing|cost|quote|how much|bei)\b/i' => ['Our services and pricing are listed here:', '/services'],
        '/\b(kra|tax|pin|returns?|etims|invoice)\b/i' => ['We help with KRA returns, PIN and eTIMS matters:', '/services/kra-returns-filing'],
        '/\b(sha|health insurance|shif)\b/i' => ['We help with SHA registration:', '/services/sha-registration-help-in-kenya'],
        '/\b(ntsa|driving licen[cs]e|smart dl|dl renew)\b/i' => ['We help with NTSA and Smart DL services:', '/services/ntsa-smart-dl-assistance'],
        '/\b(ecitizen|passport|good conduct|id card)\b/i' => ['We help with eCitizen applications:', '/services/ecitizen-application-assistance'],
        '/\b(register|registration|business name|cr12|company|brs)\b/i' => ['We help with business registration and compliance:', '/services/business-registration'],
        '/\b(website|web ?design|web ?site|landing page|domain|hosting|pos|system)\b/i' => ['We design websites and business systems:', '/services/website-design'],
        '/\b(hours?|open|closing|when|time)\b/i' => ['Our contact details and hours are here:', '/contact-details'],
        '/\b(where|location|address|office|directions?)\b/i' => ['You can find our location and contact details here:', '/contact-details'],
        '/\b(guide|how do|how to|process)\b/i' => ['We publish step-by-step guides here:', '/guides'],
    ];

    private const OPT_OUT_PATTERN = '/^(stop|unsubscribe|opt[ -]?out|remove me|acha|niondokee)\b/i';
    private const OPT_IN_PATTERN = '/^(start|unstop|subscribe|opt[ -]?in)\b/i';

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

        if (is_array($payload)) {
            try {
                $this->process($payload);
            } catch (Throwable $e) {
                error_log('[WhatsApp webhook] ' . $e->getMessage());
            }
        }

        Response::text('EVENT_RECEIVED');
    }

    /** @param array<string, mixed> $payload */
    private function process(array $payload): void
    {
        $settings = (new SiteSettingsRepository())->publicValues();
        $repo = new WhatsAppRepository();

        foreach ((array) ($payload['entry'] ?? []) as $entry) {
            foreach ((array) ($entry['changes'] ?? []) as $change) {
                $value = (array) ($change['value'] ?? []);

                foreach ((array) ($value['statuses'] ?? []) as $status) {
                    $repo->markStatus((string) ($status['id'] ?? ''), (string) ($status['status'] ?? ''));
                }

                $names = [];
                foreach ((array) ($value['contacts'] ?? []) as $contact) {
                    $names[(string) ($contact['wa_id'] ?? '')] = (string) ($contact['profile']['name'] ?? '');
                }

                foreach ((array) ($value['messages'] ?? []) as $message) {
                    $from = preg_replace('/\D+/', '', (string) ($message['from'] ?? '')) ?? '';
                    if ($from === '') {
                        continue;
                    }
                    $type = (string) ($message['type'] ?? 'text');
                    $body = $this->extractBody($message, $type);
                    $contactName = (string) ($names[(string) ($message['from'] ?? '')] ?? '');
                    $repo->record('in', $from, $contactName, $type, $body, (string) ($message['id'] ?? ''), '', (string) json_encode($message, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
                    $this->handleInbound($from, $contactName, $body, $settings, $repo);
                }
            }
        }
    }

    /** @param array<string, mixed> $message */
    private function extractBody(array $message, string $type): ?string
    {
        $body = match ($type) {
            'text' => (string) ($message['text']['body'] ?? ''),
            'button' => (string) ($message['button']['text'] ?? ''),
            'interactive' => (string) (($message['interactive']['button_reply']['title'] ?? $message['interactive']['list_reply']['title'] ?? '')),
            'location' => 'Shared a location',
            'image', 'video', 'audio', 'document', 'sticker' => '[' . $type . '] ' . (string) ($message[$type]['caption'] ?? ''),
            default => '[' . $type . ']',
        };
        return $body === '' ? null : $body;
    }

    /** @param array<string, string> $settings */
    private function handleInbound(string $from, string $contactName, ?string $body, array $settings, WhatsAppRepository $repo): void
    {
        $text = trim((string) $body);
        $apiOn = ($settings['whatsapp_api_enabled'] ?? '0') === '1' && WhatsAppApi::configured();
        $lastOutbound = $repo->lastOutboundAt($from);
        $minutesSinceOutbound = $lastOutbound === null ? PHP_INT_MAX : (int) floor((time() - strtotime($lastOutbound)) / 60);

        if ($text !== '' && preg_match(self::OPT_OUT_PATTERN, $text) === 1) {
            $repo->optOut($from);
            if ($apiOn) {
                $this->sendAndRecord($from, $contactName, 'You have been unsubscribed from AlbaTech Solutions messages. Reply START any time to opt back in.', $repo);
            }
            return;
        }

        if ($text !== '' && preg_match(self::OPT_IN_PATTERN, $text) === 1) {
            $wasOut = $repo->isOptedOut($from);
            $repo->optIn($from);
            if ($apiOn && $wasOut) {
                $this->sendAndRecord($from, $contactName, 'Welcome back — you are subscribed to AlbaTech Solutions messages again. Reply STOP to opt out.', $repo);
            }
        }

        if ($repo->isOptedOut($from)) {
            return;
        }

        // Alert the team once per conversation burst, not per message.
        if (($settings['whatsapp_email_alerts'] ?? '1') === '1' && $repo->inboundSince($from, date('Y-m-d H:i:s', time() - 7200)) === 1) {
            $this->emailAlert($from, $contactName, $body, $settings);
        }

        if (!$apiOn) {
            return;
        }

        if ($text !== '' && ($settings['whatsapp_keyword_replies'] ?? '1') === '1' && $minutesSinceOutbound >= 5) {
            foreach (self::KEYWORD_MAP as $pattern => $target) {
                if (preg_match($pattern, $text) === 1) {
                    $url = rtrim((string) Env::get('APP_URL', ''), '/') . $target[1];
                    $this->sendAndRecord($from, $contactName, $target[0] . ' ' . $url, $repo);
                    return;
                }
            }
        }

        // Generic auto-reply at most once per 12 hours per number so frequent
        // senders are not spammed; replies are free-form inside the 24h window.
        if (($settings['whatsapp_auto_reply_enabled'] ?? '1') === '1' && $minutesSinceOutbound >= 720) {
            $reply = trim((string) ($settings['whatsapp_auto_reply_text'] ?? ''));
            if ($reply !== '') {
                $this->sendAndRecord($from, $contactName, $reply, $repo);
            }
        }
    }

    private function sendAndRecord(string $to, string $contactName, string $body, WhatsAppRepository $repo): void
    {
        $result = WhatsAppApi::sendText($to, $body);
        $waId = (string) ($result['response']['messages'][0]['id'] ?? '');
        $repo->record('out', $to, $contactName, 'text', $body, $waId, $result['ok'] ? 'sent' : 'failed', (string) json_encode($result['response'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    /** @param array<string, string> $settings */
    private function emailAlert(string $from, string $contactName, ?string $body, array $settings): void
    {
        $recipient = trim((string) ($settings['contact_email'] ?? ''));
        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            $recipient = trim((string) ($settings['support_email'] ?? ''));
        }
        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            return;
        }
        $preview = trim((string) $body);
        if (mb_strlen($preview) > 300) {
            $preview = mb_substr($preview, 0, 297) . '...';
        }
        $mailBody = "A new WhatsApp message arrived.\n\n"
            . "From: +" . $from . ($contactName !== '' ? " ({$contactName})" : '') . "\n"
            . "Message: " . ($preview !== '' ? $preview : '[non-text message]') . "\n\n"
            . "Reply from the admin inbox: " . rtrim((string) Env::get('APP_URL', ''), '/') . '/admin/whatsapp/' . $from . "\n";
        try {
            SmtpMailer::send($recipient, 'New WhatsApp message from +' . $from, $mailBody, '', 'WhatsApp');
        } catch (Throwable $e) {
            error_log('[WhatsApp email alert] ' . $e->getMessage());
        }
    }
}
