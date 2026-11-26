<?php

declare(strict_types=1);

namespace AlbaTech\Services;

use AlbaTech\Core\Env;
use AlbaTech\Core\SiteUrl;
use AlbaTech\Core\SmtpMailer;
use AlbaTech\Core\WhatsAppApi;
use AlbaTech\Core\WhatsAppPolicy;
use AlbaTech\Repositories\SiteSettingsRepository;
use AlbaTech\Repositories\WhatsAppRepository;
use Throwable;

/**
 * Processes queued webhook events: delivery statuses, inbound messages, media downloads,
 * opt-outs, team alerts and the automatic reply flow.
 *
 * Reply flow for a new conversation: a greeting with three buttons (Compliance help,
 * Website / system, Talk to a person). Any manual reply from the inbox pauses all
 * automation for that number for 24 hours so the bot never talks over a person.
 */
final class WhatsAppInbound
{
    public const BUTTON_COMPLIANCE = 'menu_compliance';
    public const BUTTON_BUILD = 'menu_build';
    public const BUTTON_HUMAN = 'menu_human';
    public const AGENT_PAUSE_HOURS = 24;

    private const MEDIA_TYPES = ['image', 'video', 'audio', 'document', 'sticker'];
    private const MEDIA_EXTENSIONS = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'application/pdf' => 'pdf', 'audio/ogg' => 'ogg', 'audio/mpeg' => 'mp3', 'audio/mp4' => 'm4a', 'audio/aac' => 'aac', 'video/mp4' => 'mp4', 'video/3gpp' => '3gp', 'application/msword' => 'doc', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx', 'application/vnd.ms-excel' => 'xls', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx', 'text/plain' => 'txt'];

    /** @var array<string, string> */
    private array $settings;

    public function __construct(
        private readonly WhatsAppRepository $repo = new WhatsAppRepository(),
        private readonly WhatsAppMessenger $messenger = new WhatsAppMessenger(),
        ?array $settings = null,
    ) {
        $this->settings = $settings ?? (new SiteSettingsRepository())->publicValues();
    }

    /** Claim and process one stored event. Safe to call from the webhook and the worker. */
    public function processEvent(int $eventId): void
    {
        $payload = $this->repo->claimEvent($eventId);
        if ($payload === null) {
            return;
        }
        try {
            $decoded = json_decode($payload, true);
            if (is_array($decoded)) {
                $this->process($decoded);
            }
            $this->repo->finishEvent($eventId, null);
        } catch (Throwable $e) {
            error_log('[WhatsApp event ' . $eventId . '] ' . $e->getMessage());
            $this->repo->finishEvent($eventId, $e->getMessage());
        }
    }

    /** @param array<string, mixed> $payload */
    public function process(array $payload): void
    {
        foreach ((array) ($payload['entry'] ?? []) as $entry) {
            foreach ((array) ($entry['changes'] ?? []) as $change) {
                $value = (array) ($change['value'] ?? []);

                foreach ((array) ($value['statuses'] ?? []) as $status) {
                    $error = (array) (($status['errors'][0] ?? null) ?: []);
                    $this->repo->markStatus(
                        (string) ($status['id'] ?? ''),
                        (string) ($status['status'] ?? ''),
                        isset($error['code']) ? (string) $error['code'] : null,
                        isset($error['title']) ? trim((string) $error['title'] . ' ' . (string) ($error['error_data']['details'] ?? '')) : null
                    );
                }

                $names = [];
                foreach ((array) ($value['contacts'] ?? []) as $contact) {
                    $names[(string) ($contact['wa_id'] ?? '')] = (string) ($contact['profile']['name'] ?? '');
                }

                foreach ((array) ($value['messages'] ?? []) as $message) {
                    $this->handleMessage((array) $message, $names);
                }
            }
        }
    }

    /**
     * @param array<string, mixed> $message
     * @param array<string, string> $names
     */
    private function handleMessage(array $message, array $names): void
    {
        $from = preg_replace('/\D+/', '', (string) ($message['from'] ?? '')) ?? '';
        if ($from === '' || strlen($from) > 20) {
            return;
        }
        $type = (string) ($message['type'] ?? 'text');
        $body = $this->extractBody($message, $type);
        $contactName = mb_substr((string) ($names[(string) ($message['from'] ?? '')] ?? ''), 0, 150);
        $mediaId = in_array($type, self::MEDIA_TYPES, true) ? (string) ($message[$type]['id'] ?? '') : '';

        $id = $this->repo->recordMessage([
            'direction' => 'in',
            'origin' => 'customer',
            'wa_number' => $from,
            'contact_name' => $contactName,
            'message_type' => $type,
            'body' => $body,
            'wa_message_id' => (string) ($message['id'] ?? ''),
            'status' => 'received',
            'payload' => (string) json_encode($message, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'media_id' => $mediaId !== '' ? $mediaId : null,
            'media_mime' => $mediaId !== '' ? mb_substr((string) ($message[$type]['mime_type'] ?? ''), 0, 100) : null,
        ]);
        if ($id === null) {
            return; // Meta retried a message we already stored and handled.
        }

        $this->repo->touchInbound($from, $contactName);
        $source = WhatsAppPolicy::extractSourcePage((string) $body);
        if ($source !== null) {
            $this->repo->setSourcePageIfEmpty($from, $source);
        }
        if ($mediaId !== '') {
            $this->downloadMedia($id, $from, $mediaId);
        }

        $buttonId = $type === 'interactive' ? (string) ($message['interactive']['button_reply']['id'] ?? '') : '';
        $this->respond($from, $contactName, trim((string) $body), $buttonId);
    }

    private function respond(string $from, string $contactName, string $text, string $buttonId): void
    {
        $apiOn = $this->setting('whatsapp_api_enabled', '0') === '1' && WhatsAppApi::configured();

        if ($text !== '' && WhatsAppPolicy::isOptOut($text)) {
            $this->repo->optOut($from);
            $this->alertTeam($from, $contactName, $text . ' (opted out)', true);
            if ($apiOn) {
                $this->messenger->sendText($from, 'You have been unsubscribed from ' . $this->siteName() . ' messages. Reply START any time to opt back in. You can still message us here whenever you need help.', 'system');
            }
            return;
        }

        if ($text !== '' && WhatsAppPolicy::isOptIn($text)) {
            $wasOut = $this->repo->isOptedOut($from);
            $this->repo->optIn($from);
            if ($apiOn && $wasOut) {
                $this->messenger->sendText($from, 'Welcome back. You are subscribed to ' . $this->siteName() . ' messages again. Reply STOP to opt out.', 'system');
            }
        }

        // The team always hears about new conversations, including from opted-out numbers.
        $this->alertTeam($from, $contactName, $text, false);

        if (!$apiOn || $this->repo->isOptedOut($from)) {
            return;
        }
        $contact = $this->repo->contact($from) ?? [];
        if (!empty($contact['bot_paused'])) {
            return; // a person is handling this conversation
        }

        if ($buttonId !== '') {
            $this->handleButton($from, $contactName, $buttonId);
            return;
        }

        $minutesSinceBot = $this->repo->minutesSinceBotMessage($from);

        if ($this->setting('whatsapp_menu_enabled', '1') === '1' && empty($contact['menu_recent']) && $minutesSinceBot >= 720) {
            $greeting = 'Hi' . ($contactName !== '' ? ' ' . $this->firstName($contactName) : '') . ', thanks for contacting ' . $this->siteName() . '. What do you need help with?';
            if (!$this->isOpen()) {
                $greeting .= "\n\n" . $this->setting('whatsapp_after_hours_text', 'We are outside working hours right now and will reply as soon as we open.');
            }
            $result = $this->messenger->sendButtons($from, $greeting, [
                self::BUTTON_COMPLIANCE => 'Compliance help',
                self::BUTTON_BUILD => 'Website / system',
                self::BUTTON_HUMAN => 'Talk to a person',
            ], 'bot');
            if ($result['ok'] || $result['queued']) {
                $this->repo->markMenuSent($from);
            }
            return;
        }

        if ($text !== '' && $this->setting('whatsapp_keyword_replies', '1') === '1' && $minutesSinceBot >= 60) {
            $match = WhatsAppPolicy::keywordTopic($text);
            if ($match !== null) {
                if ($match['topic'] !== 'general') {
                    $this->repo->setTopic($from, $match['topic']);
                }
                $this->messenger->sendText($from, $match['line'] . ' ' . SiteUrl::base() . $match['path'], 'bot');
                return;
            }
        }

        if ($this->setting('whatsapp_menu_enabled', '1') !== '1' && $this->setting('whatsapp_auto_reply_enabled', '1') === '1' && $minutesSinceBot >= 720) {
            $reply = trim($this->setting('whatsapp_auto_reply_text', ''));
            if ($reply !== '') {
                $this->messenger->sendText($from, $reply, 'bot');
            }
        }
    }

    private function handleButton(string $from, string $contactName, string $buttonId): void
    {
        $base = SiteUrl::base();
        $closedNote = $this->isOpen() ? '' : "\n\n" . $this->setting('whatsapp_after_hours_text', '');
        switch ($buttonId) {
            case self::BUTTON_COMPLIANCE:
                $this->repo->setTopic($from, 'compliance');
                $this->messenger->sendText($from, "Great. Tell us the task, for example KRA returns, eTIMS, business registration, eCitizen or SHA, and we will confirm the price before starting.\n\nServices and prices: {$base}/services/kra-ecitizen-help\n\nPlease do not send passwords until we ask for them." . $closedNote, 'bot');
                break;
            case self::BUTTON_BUILD:
                $this->repo->setTopic($from, 'build');
                $this->messenger->sendText($from, "Great. Tell us a little about your business and what you want to build.\n\nRegister and get online: {$base}/services/sme-launchpad\nBusiness systems: {$base}/solutions\nOur work: {$base}/work" . $closedNote, 'bot');
                break;
            case self::BUTTON_HUMAN:
                $this->repo->setTopic($from, 'human');
                $this->messenger->sendText($from, 'Thanks' . ($contactName !== '' ? ' ' . $this->firstName($contactName) : '') . '. A member of our team will reply here personally.' . $closedNote, 'bot');
                $this->repo->pauseBot($from, self::AGENT_PAUSE_HOURS);
                break;
        }
    }

    /** @param array<string, mixed> $message */
    private function extractBody(array $message, string $type): ?string
    {
        $body = match ($type) {
            'text' => (string) ($message['text']['body'] ?? ''),
            'button' => (string) ($message['button']['text'] ?? ''),
            'interactive' => (string) ($message['interactive']['button_reply']['title'] ?? $message['interactive']['list_reply']['title'] ?? ''),
            'location' => 'Shared a location',
            'image', 'video', 'audio', 'document', 'sticker' => trim('[' . $type . '] ' . (string) ($message[$type]['caption'] ?? $message[$type]['filename'] ?? '')),
            default => '[' . $type . ']',
        };
        return $body === '' ? null : mb_substr($body, 0, 4096);
    }

    /** Save customer files privately (outside the web root) before Meta's media link expires. */
    private function downloadMedia(int $messageId, string $from, string $mediaId): void
    {
        try {
            $info = WhatsAppApi::mediaInfo($mediaId);
            if ($info === null || ($info['size'] > 0 && $info['size'] > WhatsAppApi::MAX_MEDIA_BYTES)) {
                return;
            }
            $mime = strtolower(trim(explode(';', $info['mime'])[0]));
            $extension = self::MEDIA_EXTENSIONS[$mime] ?? 'bin';
            $relative = 'whatsapp-media/' . $from . '/' . $messageId . '-' . bin2hex(random_bytes(6)) . '.' . $extension;
            $absolute = self::storagePath($relative);
            if (!is_dir(dirname($absolute)) && !mkdir(dirname($absolute), 0750, true) && !is_dir(dirname($absolute))) {
                return;
            }
            if (WhatsAppApi::downloadMedia($info['url'], $absolute)) {
                $this->repo->updateMessage($messageId, ['media_path' => $relative, 'media_mime' => mb_substr($mime, 0, 100)]);
            }
        } catch (Throwable $e) {
            error_log('[WhatsApp media] ' . $e->getMessage());
        }
    }

    public static function storagePath(string $relative): string
    {
        return dirname(__DIR__, 2) . '/storage/' . ltrim($relative, '/');
    }

    /** Email the team on the first message of a burst (none in the previous 2 hours) or an opt-out. */
    private function alertTeam(string $from, string $contactName, string $text, bool $force): void
    {
        if ($this->setting('whatsapp_email_alerts', '1') !== '1') {
            return;
        }
        if (!$force && $this->repo->inboundWithinMinutes($from, 120) > 1) {
            return;
        }
        $recipient = trim($this->setting('contact_email', ''));
        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            $recipient = trim($this->setting('support_email', ''));
        }
        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            return;
        }
        $preview = $text !== '' ? mb_strimwidth($text, 0, 300, '...') : '[non-text message]';
        $contact = $this->repo->contact($from) ?? [];
        $body = "A new WhatsApp message arrived.\n\n"
            . 'From: +' . $from . ($contactName !== '' ? " ({$contactName})" : '') . "\n"
            . 'Message: ' . $preview . "\n"
            . (!empty($contact['source_page']) ? 'Started from page: ' . $contact['source_page'] . "\n" : '')
            . ($this->repo->isOptedOut($from) ? "Note: this number has opted out of automatic messages. You can still reply personally within 24 hours.\n" : '')
            . "\nReply from the admin inbox: " . SiteUrl::base() . '/admin/whatsapp/' . $from . "\n";
        try {
            SmtpMailer::send($recipient, 'New WhatsApp message from +' . $from, $body, '', 'WhatsApp');
        } catch (Throwable $e) {
            error_log('[WhatsApp email alert] ' . $e->getMessage());
        }
    }

    private function isOpen(): bool
    {
        return WhatsAppPolicy::isOpen($this->setting('whatsapp_business_hours', ''));
    }

    private function siteName(): string
    {
        return trim($this->setting('site_name', '')) ?: (string) Env::get('APP_NAME', 'AlbaTech Solutions');
    }

    private function firstName(string $name): string
    {
        return (string) (preg_split('/\s+/', trim($name))[0] ?? '');
    }

    private function setting(string $key, string $default): string
    {
        return (string) ($this->settings[$key] ?? $default);
    }
}
