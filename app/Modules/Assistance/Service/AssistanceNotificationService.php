<?php

declare(strict_types=1);

namespace App\Modules\Assistance\Service;

use App\Core\AuditLog;
use App\Core\BaseService;
use App\Core\Config;
use App\Core\Notifications\NotificationMessage;
use App\Core\Notifications\NotificationService;
use App\Core\Notifications\NotificationResult;
use App\Modules\Assistance\Repository\AssistanceNotificationRepository;
use App\Modules\Assistance\Repository\AssistanceQuoteRepository;
use App\Modules\Assistance\Repository\AssistanceRequestRepository;

final class AssistanceNotificationService extends BaseService
{
    public function __construct(
        private readonly NotificationService $notifications,
        private readonly AssistanceNotificationRepository $notificationRepository,
        private readonly AssistanceQuoteRepository $quotes,
        private readonly AssistanceRequestRepository $requests
    ) {}

    public function requestReceived(array $request): void
    {
        $this->dispatch((int)$request['id'], 'request_received', $request, [
            'reference' => (string)$request['request_number'],
            'name' => (string)$request['name'],
        ]);
    }

    public function quoteSent(array $quote): void
    {
        $this->dispatch((int)$quote['assistance_request_id'], 'quote_sent', $quote, [
            'name' => (string)$quote['name'],
            'quote_number' => (string)$quote['quote_number'],
            'url' => $this->quoteUrl($quote),
        ]);
    }

    public function quoteAccepted(array $quote): void
    {
        $this->dispatch((int)$quote['assistance_request_id'], 'quote_accepted', $quote, [
            'name' => (string)$quote['name'],
            'quote_number' => (string)$quote['quote_number'],
            'url' => $this->quoteUrl($quote),
        ]);
    }

    public function paymentSubmitted(array $quote, int $paymentId, string $paymentRef): void
    {
        $this->dispatch((int)$quote['assistance_request_id'], 'payment_submitted', $quote, [
            'name' => (string)$quote['name'],
            'quote_number' => (string)$quote['quote_number'],
            'payment_ref' => $paymentRef,
        ], 'assistance_payment', $paymentId);
    }

    public function paymentVerified(array $payment, string $receiptUrl): void
    {
        $this->dispatch((int)$payment['assistance_request_id'], 'payment_verified', $payment, [
            'name' => (string)$payment['name'],
            'quote_number' => (string)$payment['quote_number'],
            'amount' => number_format((float)$payment['amount'], 2),
            'url' => $receiptUrl,
        ], 'assistance_payment', (int)$payment['id']);
    }

    public function paymentRejected(array $payment, string $reason): void
    {
        $this->dispatch((int)$payment['assistance_request_id'], 'payment_rejected', $payment, [
            'name' => (string)$payment['name'],
            'quote_number' => (string)$payment['quote_number'],
            'reason' => $reason,
        ], 'assistance_payment', (int)$payment['id']);
    }

    public function workAssigned(array $request): void
    {
        $this->dispatch((int)$request['id'], 'work_assigned', $request, [
            'name' => (string)$request['name'],
            'reference' => (string)$request['request_number'],
            'url' => $this->portalUrl($request),
        ]);
    }

    public function workStarted(array $request, ?int $sourceId = null): void
    {
        $this->dispatch((int)$request['id'], 'work_started', $request, [
            'name' => (string)$request['name'],
            'reference' => (string)$request['request_number'],
            'url' => $this->portalUrl($request),
        ], 'assistance_update', $sourceId);
    }

    public function progressUpdate(array $request, int $updateId, string $message): void
    {
        $this->dispatch((int)$request['id'], 'progress_update', $request, [
            'name' => (string)$request['name'],
            'reference' => (string)$request['request_number'],
            'message' => $message,
            'url' => $this->portalUrl($request),
        ], 'assistance_update', $updateId);
    }

    public function workCompleted(array $request, string $note, string $reviewUrl): void
    {
        $this->dispatch((int)$request['id'], 'work_completed', $request, [
            'name' => (string)$request['name'],
            'reference' => (string)$request['request_number'],
            'note' => $note,
            'url' => $this->portalUrl($request),
            'review_url' => $reviewUrl,
        ]);
    }

    /** Retry one failed notification. Intended for a cron/queue worker. */
    public function retry(int $notificationId): bool
    {
        $notification = $this->notificationRepository->find($notificationId);
        if (!$notification || in_array($notification['status'], ['sent', 'exhausted'], true)) return false;
        $data = json_decode((string)($notification['template_data'] ?? '{}'), true);
        $data = is_array($data) ? $data : [];
        $message = new NotificationMessage(
            (string)$notification['channel'],
            (string)$notification['recipient'],
            (string)$notification['subject'],
            (string)($notification['body'] ?? ''),
            is_array($data['whatsapp_parameters'] ?? null) ? $data['whatsapp_parameters'] : [],
            !empty($notification['template_name']) ? (string)$notification['template_name'] : null,
            !empty($notification['template_language']) ? (string)$notification['template_language'] : null
        );
        return $this->deliver($notification, $message);
    }

    private function dispatch(int $requestId, string $event, array $context, array $data, ?string $sourceType = null, ?int $sourceId = null): void
    {
        $preferences = $this->notificationRepository->preference($requestId);
        $channels = [];
        if (!empty($preferences['email_enabled']) && !empty($context['email'])) $channels[] = 'email';
        if (!empty($preferences['sms_enabled']) && !empty($context['phone'])) $channels[] = 'sms';
        if (!empty($preferences['whatsapp_enabled']) && !empty($context['phone'])) $channels[] = 'whatsapp';

        // Honour the customer's preferred channel first, but still allow enabled
        // fallbacks. This is useful when email is unavailable or a WhatsApp template
        // has not yet been approved/configured.
        $preferred = strtolower((string)($context['preferred_contact'] ?? ''));
        if ($preferred === 'phone') $preferred = 'sms';
        if (in_array($preferred, $channels, true)) {
            usort($channels, static fn(string $a, string $b): int => $a === $preferred ? -1 : ($b === $preferred ? 1 : 0));
        }

        foreach (array_unique($channels) as $channel) {
            $template = $this->notificationRepository->template($event, $channel);
            if (!$template) continue;
            if ($this->notificationRepository->notificationExists($requestId, $channel, $event, $sourceType, $sourceId)) continue;

            $recipient = $channel === 'email' ? (string)($context['email'] ?? '') : (string)($context['phone'] ?? '');
            if ($recipient === '') continue;

            $message = $this->buildMessageFromTemplate($channel, $template, $data);
            $notificationId = $this->notificationRepository->createNotification([
                'assistance_request_id' => $requestId,
                'channel' => $channel,
                'event' => $event,
                'recipient' => $recipient,
                'subject' => $message->subject,
                'status' => 'queued',
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'template_name' => $message->template,
                'template_language' => $message->language,
                'body' => $message->body,
                'template_data' => json_encode(['whatsapp_parameters' => $message->data], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            ]);
            $notification = $this->notificationRepository->find($notificationId);
            if ($notification) $this->deliver($notification, $message);
        }
    }

    private function buildMessageFromTemplate(string $channel, array $template, array $data): NotificationMessage
    {
        $subject = $this->render((string)($template['subject_template'] ?? ''), $data);
        $body = $this->render((string)$template['body_template'], $data);
        $templateName = $channel === 'whatsapp' ? ((string)($template['template_name'] ?? '') ?: null) : null;
        $language = $channel === 'whatsapp' ? ((string)($template['language'] ?? '') ?: 'en_US') : null;
        $values = [];
        if ($channel === 'whatsapp') {
            preg_match_all('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', (string)$template['body_template'], $matches);
            foreach ($matches[1] ?? [] as $key) $values[] = (string)($data[$key] ?? '');
        }
        return new NotificationMessage($channel, '', $subject, $body, $values, $templateName, $language);
    }

    private function buildMessage(array $notification, array $template, array $data): NotificationMessage
    {
        $message = $this->buildMessageFromTemplate((string)$notification['channel'], $template, $data);
        return new NotificationMessage($message->channel, (string)$notification['recipient'], $message->subject, $message->body, $message->data, $message->template, $message->language);
    }

    private function deliver(array $notification, NotificationMessage $message): bool
    {
        $attempt = (int)$notification['attempt_count'] + 1;
        $this->notificationRepository->updateNotification((int)$notification['id'], [
            'attempt_count' => $attempt,
            'last_attempt_at' => date('Y-m-d H:i:s'),
        ]);

        $message = new NotificationMessage($message->channel, (string)$notification['recipient'], $message->subject, $message->body, $message->data, $message->template, $message->language);
        $result = $this->notifications->send($message);
        $this->notificationRepository->createAttempt((int)$notification['id'], $attempt, $result->accepted ? 'sent' : 'failed', $result->messageId, $result->error);

        if ($result->accepted) {
            $this->notificationRepository->updateNotification((int)$notification['id'], [
                'status' => 'sent',
                'sent_at' => date('Y-m-d H:i:s'),
                'next_attempt_at' => null,
                'provider_message_id' => $result->messageId,
                'channel_message_id' => $result->messageId,
                'provider_message' => null,
            ]);
            AuditLog::record('assistance_notification.sent', 'assistance_notification', (int)$notification['id'], ['channel'=>$message->channel,'event'=>$notification['event']]);
            return true;
        }

        $maxAttempts = max(1, (int)Config::get('notifications.retry.max_attempts', 3));
        $base = max(1, (int)Config::get('notifications.retry.base_delay_minutes', 5));
        $delay = $base * (2 ** max(0, $attempt - 1));
        $next = $attempt < $maxAttempts ? date('Y-m-d H:i:s', time() + ($delay * 60)) : null;
        $status = $attempt < $maxAttempts ? 'failed' : 'exhausted';
        $this->notificationRepository->updateNotification((int)$notification['id'], [
            'status' => $status,
            'next_attempt_at' => $next,
            'provider_message' => $result->error,
        ]);
        AuditLog::record('assistance_notification.failed', 'assistance_notification', (int)$notification['id'], ['channel'=>$message->channel,'event'=>$notification['event'],'attempt'=>$attempt]);
        return false;
    }

    private function render(string $template, array $data): string
    {
        return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', static fn(array $m): string => (string)($data[$m[1]] ?? ''), $template) ?? $template;
    }

    private function quoteUrl(array $quote): string
    {
        $token = $quote['public_token'] ?? null;
        if (!$token && !empty($quote['public_token_encrypted'])) $token = $this->decryptToken((string)$quote['public_token_encrypted']);
        return rtrim(Config::get('app.url',''), '/') . '/quote/' . rawurlencode((string)$token);
    }

    private function portalUrl(array $request): string
    {
        $token = $request['customer_token'] ?? null;
        if (!$token && !empty($request['customer_token_encrypted'])) $token = $this->decryptToken((string)$request['customer_token_encrypted']);
        return rtrim(Config::get('app.url',''), '/') . '/request/' . rawurlencode((string)$token);
    }

    private function decryptToken(string $encrypted): ?string
    {
        $key=hash('sha256',(string)Config::get('app.key',''),true);
        $raw=base64_decode($encrypted,true);
        if($raw===false || strlen($raw)<28)return null;
        $plain=openssl_decrypt(substr($raw,28),'aes-256-gcm',$key,OPENSSL_RAW_DATA,substr($raw,0,12),substr($raw,12,16));
        return $plain===false?null:$plain;
    }
}
