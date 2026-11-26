<?php

declare(strict_types=1);

namespace AlbaTech\Controllers;

use AlbaTech\Core\AdminSecurity;
use AlbaTech\Core\Database;
use AlbaTech\Core\Request;
use AlbaTech\Core\Response;
use AlbaTech\Core\View;
use AlbaTech\Core\WhatsAppApi;
use AlbaTech\Repositories\SiteSettingsRepository;
use AlbaTech\Repositories\WhatsAppRepository;
use AlbaTech\Services\WhatsAppInbound;
use AlbaTech\Services\WhatsAppMessenger;
use Throwable;

/**
 * Admin WhatsApp inbox: conversations, 24-hour window state, free-text replies inside the
 * window, a follow-up template when it is closed, bot takeover, customer files, health,
 * opt-outs and conversation deletion.
 */
final class WhatsAppAdminController
{
    public function inbox(Request $request, array $params = []): void
    {
        AdminSecurity::requirePermission('whatsapp.manage');
        $repo = new WhatsAppRepository();
        $settings = (new SiteSettingsRepository())->publicValues();
        $number = $this->number($params);
        $this->render('whatsapp', [
            'pageTitle' => 'WhatsApp inbox',
            'conversations' => $repo->conversations(),
            'optOuts' => $repo->optOuts(),
            'thread' => $number !== '' ? $repo->thread($number) : [],
            'threadNumber' => $number,
            'threadOptedOut' => $number !== '' && $repo->isOptedOut($number),
            'contact' => $number !== '' ? $repo->contact($number) : null,
            'consent' => $number !== '' ? $repo->latestConsent($number) : null,
            'health' => $repo->health(),
            'apiConfigured' => WhatsAppApi::configured(),
            'apiEnabled' => ($settings['whatsapp_api_enabled'] ?? '0') === '1',
            'reopenTemplate' => trim((string) ($settings['whatsapp_reopen_template'] ?? '')),
        ]);
    }

    public function send(Request $request, array $params): void
    {
        $user = $this->guard($request);
        $number = $this->number($params);
        $back = $this->back($number);
        $body = trim((string) $request->input('body', ''));
        if ($body === '' || mb_strlen($body) > 4000) {
            $this->fail('Enter a message of up to 4,000 characters.', $back);
        }
        $repo = new WhatsAppRepository();
        $contact = $repo->contact($number);
        // Free text only works inside Meta's 24-hour customer-service window. Inside it, a
        // personal reply is allowed even to opted-out numbers: they messaged us first.
        if (empty($contact['window_open'])) {
            $this->fail('The 24-hour reply window is closed. Send the follow-up template instead and reply once they answer.', $back);
        }
        $this->requireApi($back);

        $result = (new WhatsAppMessenger($repo))->sendText($number, $body, 'agent', (int) $user['id']);
        $repo->pauseBot($number, WhatsAppInbound::AGENT_PAUSE_HOURS);
        $this->report($result, 'Message sent. Automatic replies are paused for this chat for 24 hours.', $back);
        $this->audit('whatsapp.message.sent', $number, ['length' => mb_strlen($body), 'ok' => $result['ok']]);
        Response::redirect($back);
    }

    public function sendTemplate(Request $request, array $params): void
    {
        $user = $this->guard($request);
        $number = $this->number($params);
        $back = $this->back($number);
        $settings = (new SiteSettingsRepository())->publicValues();
        $template = trim((string) ($settings['whatsapp_reopen_template'] ?? ''));
        if ($template === '') {
            $this->fail('Set a follow-up template name in Site settings first.', $back);
        }
        $repo = new WhatsAppRepository();
        if ($repo->isOptedOut($number)) {
            $this->fail('This number opted out. Templates cannot be sent until they message you again or reply START.', $back);
        }
        $this->requireApi($back);
        $name = $repo->contactNameFor($number);
        $result = (new WhatsAppMessenger($repo))->sendTemplate(
            $number,
            $template,
            trim((string) ($settings['whatsapp_template_language'] ?? '')) ?: 'en',
            ['customer_name' => $name !== '' ? (string) preg_split('/\s+/', $name)[0] : 'there'],
            'agent',
            (int) $user['id'],
            'Follow-up template: ' . $template
        );
        $repo->pauseBot($number, WhatsAppInbound::AGENT_PAUSE_HOURS);
        $this->report($result, 'Follow-up template sent. You can reply freely once they answer.', $back);
        $this->audit('whatsapp.template.sent', $number, ['template' => $template, 'ok' => $result['ok']]);
        Response::redirect($back);
    }

    public function resumeBot(Request $request, array $params): void
    {
        $this->guard($request);
        $number = $this->number($params);
        (new WhatsAppRepository())->resumeBot($number);
        AdminSecurity::flash('success', 'Automatic replies resumed for this chat.');
        Response::redirect($this->back($number));
    }

    public function deleteConversation(Request $request, array $params): void
    {
        $this->guard($request);
        $number = $this->number($params);
        $paths = (new WhatsAppRepository())->deleteConversation($number);
        foreach ($paths as $path) {
            $this->deleteStoredFile($path);
        }
        $this->audit('whatsapp.conversation.deleted', $number, ['files' => count($paths)]);
        AdminSecurity::flash('success', 'Conversation, contact details, consent records and files deleted. The opt-out (if any) is kept so the number is never messaged automatically.');
        Response::redirect('/admin/whatsapp');
    }

    /** Stream a customer file to signed-in admins only. Files live outside the web root. */
    public function media(Request $request, array $params): void
    {
        AdminSecurity::requirePermission('whatsapp.manage');
        $message = (new WhatsAppRepository())->message((int) ($params['id'] ?? 0));
        $relative = (string) ($message['media_path'] ?? '');
        $path = $relative !== '' ? realpath(WhatsAppInbound::storagePath($relative)) : false;
        $root = realpath(WhatsAppInbound::storagePath('whatsapp-media'));
        if ($path === false || $root === false || !str_starts_with($path, $root . DIRECTORY_SEPARATOR) || !is_file($path)) {
            Response::text('File not found', 404);
            return;
        }
        $mime = (string) ($message['media_mime'] ?? '') ?: 'application/octet-stream';
        $inline = preg_match('~^(image/(jpeg|png|webp)|application/pdf|audio/|video/)~', $mime) === 1;
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($path));
        header('Content-Disposition: ' . ($inline ? 'inline' : 'attachment') . '; filename="' . basename($path) . '"');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, no-store');
        header("Content-Security-Policy: default-src 'none'; img-src 'self'; media-src 'self'; style-src 'unsafe-inline'; sandbox");
        readfile($path);
    }

    public function sendTest(Request $request): void
    {
        $this->guard($request);
        $settings = (new SiteSettingsRepository())->publicValues();
        $to = WhatsAppApi::normalizeNumber((string) ($settings['whatsapp_notify_number'] ?? ''));
        if ($to === '') {
            $to = WhatsAppApi::normalizeNumber((string) ($settings['whatsapp_number'] ?? ''));
        }
        if ($to === '') {
            $this->fail('Set a WhatsApp notification number in Site settings first.', '/admin/whatsapp');
        }
        $template = trim((string) ($settings['whatsapp_template_name'] ?? ''));
        if ($template === '') {
            $this->fail('Set an approved template name in Site settings first.', '/admin/whatsapp');
        }
        $this->requireApi('/admin/whatsapp');
        $result = (new WhatsAppMessenger())->sendTemplate($to, $template, trim((string) ($settings['whatsapp_template_language'] ?? '')) ?: 'en', ['customer_name' => 'Test customer', 'service' => 'a test message'], 'system', null, 'Test template: ' . $template);
        $this->report($result, 'Test template sent to +' . $to . '.', '/admin/whatsapp');
        Response::redirect('/admin/whatsapp');
    }

    /** @return array<string, mixed> admin user */
    private function guard(Request $request): array
    {
        $user = AdminSecurity::requirePermission('whatsapp.manage');
        if (!AdminSecurity::verifyCsrf($request->input('_csrf'))) {
            $this->fail('Your session expired. Try again.', '/admin/whatsapp');
        }
        return $user;
    }

    private function number(array $params): string
    {
        $number = preg_replace('/\D+/', '', (string) ($params['number'] ?? '')) ?? '';
        return strlen($number) >= 8 && strlen($number) <= 15 ? $number : '';
    }

    private function back(string $number): string
    {
        if ($number === '') {
            $this->fail('Invalid WhatsApp number.', '/admin/whatsapp');
        }
        return '/admin/whatsapp/' . $number;
    }

    private function requireApi(string $back): void
    {
        if (!WhatsAppApi::configured()) {
            $this->fail('WhatsApp API is not configured. Set WHATSAPP_PHONE_NUMBER_ID and WHATSAPP_ACCESS_TOKEN in .env.', $back);
        }
    }

    /** @param array{ok: bool, queued: bool, error: string} $result */
    private function report(array $result, string $success, string $back): void
    {
        if ($result['ok']) {
            AdminSecurity::flash('success', $success);
        } elseif ($result['queued']) {
            AdminSecurity::flash('success', 'WhatsApp is busy right now. The message is queued and will be retried automatically. ' . $result['error']);
        } else {
            AdminSecurity::flash('error', 'WhatsApp send failed: ' . $result['error']);
        }
    }

    private function fail(string $message, string $back): never
    {
        AdminSecurity::flash('error', $message);
        Response::redirect($back);
    }

    private function deleteStoredFile(string $relative): void
    {
        $path = realpath(WhatsAppInbound::storagePath($relative));
        $root = realpath(WhatsAppInbound::storagePath('whatsapp-media'));
        if ($path !== false && $root !== false && str_starts_with($path, $root . DIRECTORY_SEPARATOR)) {
            @unlink($path);
        }
    }

    private function render(string $view, array $data): void
    {
        $data['csrf'] = AdminSecurity::csrfToken();
        $data['adminUser'] = AdminSecurity::user();
        $data['flash'] = AdminSecurity::pullFlash();
        Response::html(View::render('admin/' . $view, $data, 'layouts/admin'));
    }

    /** @param array<string, mixed> $details */
    private function audit(string $action, ?string $entityId, array $details): void
    {
        try {
            $user = AdminSecurity::user();
            Database::connection()->prepare('INSERT INTO admin_audit_logs (admin_user_id, action, entity_type, entity_id, details, ip_address) VALUES (:user_id,:action,:type,:entity_id,:details,:ip)')->execute([
                'user_id' => $user['id'] ?? null,
                'action' => $action,
                'type' => 'whatsapp',
                'entity_id' => $entityId,
                'details' => json_encode($details, JSON_UNESCAPED_SLASHES),
                'ip' => substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45),
            ]);
        } catch (Throwable $e) {
            error_log('[WhatsApp audit] ' . $e->getMessage());
        }
    }
}
