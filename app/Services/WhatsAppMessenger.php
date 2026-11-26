<?php

declare(strict_types=1);

namespace AlbaTech\Services;

use AlbaTech\Core\WhatsAppApi;
use AlbaTech\Core\WhatsAppPolicy;
use AlbaTech\Repositories\WhatsAppRepository;
use Throwable;

/**
 * Every outbound WhatsApp message goes through here: it sends, records the message with
 * who sent it (bot / agent / system), and queues temporary failures for the worker to retry.
 *
 * Origins: "bot" = automatic replies, "agent" = a person in the admin inbox,
 * "system" = acknowledgments, opt-out confirmations and tests.
 */
final class WhatsAppMessenger
{
    public function __construct(private readonly WhatsAppRepository $repo = new WhatsAppRepository())
    {
    }

    /** @return array{ok: bool, queued: bool, error: string} */
    public function sendText(string $to, string $body, string $origin, ?int $adminId = null): array
    {
        return $this->dispatch(WhatsAppApi::textPayload($to, $body), 'text', $body, $origin, $adminId, null);
    }

    /**
     * @param array<string, string> $buttons id => title
     * @return array{ok: bool, queued: bool, error: string}
     */
    public function sendButtons(string $to, string $body, array $buttons, string $origin): array
    {
        $label = $body . "\n[Buttons: " . implode(' | ', $buttons) . ']';
        return $this->dispatch(WhatsAppApi::buttonsPayload($to, $body, $buttons), 'interactive', $label, $origin, null, null);
    }

    /**
     * @param array<int|string, string> $params
     * @return array{ok: bool, queued: bool, error: string}
     */
    public function sendTemplate(string $to, string $template, string $language, array $params, string $origin, ?int $adminId = null, string $label = ''): array
    {
        return $this->dispatch(
            WhatsAppApi::templatePayload($to, $template, $language, $params),
            'template',
            $label !== '' ? $label : 'Template: ' . $template,
            $origin,
            $adminId,
            static fn (): array => WhatsAppApi::sendTemplate($to, $template, $language, $params)
        );
    }

    /** Retry queued sends whose backoff has expired. Returns how many were attempted. */
    public function retryDue(int $limit = 20): int
    {
        $count = 0;
        foreach ($this->repo->dueRetries($limit) as $row) {
            $count++;
            $id = (int) $row['id'];
            $payload = json_decode((string) ($row['request_json'] ?? ''), true);
            $number = (string) $row['wa_number'];
            if (!is_array($payload)) {
                $this->repo->updateMessage($id, ['status' => 'failed', 'error_message' => 'Retry payload missing']);
                continue;
            }
            if ($row['origin'] !== 'agent' && $this->repo->isOptedOut($number)) {
                $this->repo->updateMessage($id, ['status' => 'cancelled', 'error_message' => 'Recipient opted out before retry']);
                continue;
            }
            $attempts = (int) $row['attempts'] + 1;
            $result = WhatsAppApi::send($payload);
            [$code, $message] = $this->errorOf($result);
            if ($result['ok']) {
                $this->repo->updateMessage($id, ['status' => 'sent', 'attempts' => $attempts, 'wa_message_id' => $this->messageId($result), 'error_code' => null, 'error_message' => null, 'payload' => $this->json($result['response'])]);
            } elseif (WhatsAppPolicy::isRetryable((int) $result['status'], $code) && $attempts < WhatsAppPolicy::MAX_SEND_ATTEMPTS) {
                $this->repo->updateMessage($id, ['error_code' => $code, 'error_message' => $message]);
                $this->repo->scheduleRetry($id, $attempts, WhatsAppPolicy::backoffSeconds($attempts));
            } else {
                $this->repo->updateMessage($id, ['status' => 'failed', 'attempts' => $attempts, 'error_code' => $code, 'error_message' => $message, 'payload' => $this->json($result['response'])]);
            }
        }
        return $count;
    }

    /**
     * @param (callable(): array{ok: bool, status: int, response: array})|null $sender
     * @return array{ok: bool, queued: bool, error: string}
     */
    private function dispatch(array $payload, string $type, string $bodyForLog, string $origin, ?int $adminId, ?callable $sender): array
    {
        $to = (string) ($payload['to'] ?? '');
        try {
            $result = $sender !== null ? $sender() : WhatsAppApi::send($payload);
        } catch (Throwable $e) {
            $result = ['ok' => false, 'status' => 0, 'response' => ['error' => ['message' => $e->getMessage()]]];
        }
        [$code, $message] = $this->errorOf($result);
        $retryable = !$result['ok'] && $result['status'] !== -1 && WhatsAppPolicy::isRetryable((int) $result['status'], $code);

        try {
            $id = $this->repo->recordMessage([
                'direction' => 'out',
                'origin' => $origin,
                'admin_user_id' => $adminId,
                'wa_number' => $to,
                'contact_name' => $this->repo->contactNameFor($to),
                'message_type' => $type,
                'body' => $bodyForLog,
                'wa_message_id' => $this->messageId($result),
                'status' => $result['ok'] ? 'sent' : 'failed',
                'error_code' => $result['ok'] ? null : $code,
                'error_message' => $result['ok'] ? null : $message,
                'payload' => $this->json($result['response']),
                'request_json' => $retryable ? $this->json($payload) : null,
                'attempts' => 1,
            ]);
            if ($retryable && $id !== null) {
                $this->repo->scheduleRetry($id, 1, WhatsAppPolicy::backoffSeconds(1));
            }
        } catch (Throwable $e) {
            error_log('[WhatsApp record] ' . $e->getMessage());
        }

        return ['ok' => $result['ok'], 'queued' => $retryable, 'error' => $result['ok'] ? '' : $this->describe($code, $message)];
    }

    /** @return array{0: string, 1: string} [code, message incl. Meta details] */
    private function errorOf(array $result): array
    {
        $error = $result['response']['error'] ?? [];
        $code = (string) ($error['code'] ?? ($result['ok'] ? '' : (string) $result['status']));
        $message = (string) ($error['message'] ?? '');
        $details = trim((string) ($error['error_data']['details'] ?? ''));
        if ($details !== '' && !str_contains($message, $details)) {
            $message .= ' — ' . $details;
        }
        return [$code, mb_substr($message, 0, 500)];
    }

    private function describe(string $code, string $message): string
    {
        $hint = match ($code) {
            '131047' => ' The 24-hour reply window is closed: send an approved template instead.',
            '131026' => ' The number may not be on WhatsApp.',
            '132001' => ' Check the template name and language in Site settings.',
            default => '',
        };
        return mb_substr('(#' . ($code !== '' ? $code : '?') . ') ' . ($message !== '' ? $message : 'send failed') . $hint, 0, 400);
    }

    private function messageId(array $result): ?string
    {
        $id = (string) ($result['response']['messages'][0]['id'] ?? '');
        return $id !== '' ? $id : null;
    }

    private function json(mixed $value): string
    {
        return (string) json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
