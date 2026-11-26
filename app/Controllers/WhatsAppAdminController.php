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
use Throwable;

/**
 * Admin WhatsApp inbox: conversations from the webhook, free-text replies inside
 * Meta's 24-hour window, an opt-out list, and a test-template sender.
 */
final class WhatsAppAdminController
{
    public function inbox(Request $request, array $params = []): void
    {
        AdminSecurity::requirePermission('whatsapp.manage');
        $repo = new WhatsAppRepository();
        $number = preg_replace('/\D+/', '', (string) ($params['number'] ?? '')) ?? '';
        $this->render('whatsapp', [
            'pageTitle' => 'WhatsApp inbox',
            'conversations' => $repo->conversations(),
            'optOuts' => $repo->optOuts(),
            'thread' => $number !== '' ? $repo->thread($number) : [],
            'threadNumber' => $number,
            'threadOptedOut' => $number !== '' && $repo->isOptedOut($number),
            'apiConfigured' => WhatsAppApi::configured(),
        ]);
    }

    public function send(Request $request, array $params): void
    {
        AdminSecurity::requirePermission('whatsapp.manage');
        if (!AdminSecurity::verifyCsrf($request->input('_csrf'))) {
            AdminSecurity::flash('error', 'Your session expired. Try again.');
            Response::redirect('/admin/whatsapp');
        }
        $number = preg_replace('/\D+/', '', (string) ($params['number'] ?? '')) ?? '';
        $back = $number !== '' ? '/admin/whatsapp/' . $number : '/admin/whatsapp';
        if (strlen($number) < 8 || strlen($number) > 15) {
            AdminSecurity::flash('error', 'Invalid WhatsApp number.');
            Response::redirect('/admin/whatsapp');
        }
        $body = trim((string) $request->input('body', ''));
        if ($body === '' || mb_strlen($body) > 4000) {
            AdminSecurity::flash('error', 'Enter a message of up to 4,000 characters.');
            Response::redirect($back);
        }
        $repo = new WhatsAppRepository();
        if ($repo->isOptedOut($number)) {
            AdminSecurity::flash('error', 'This number has opted out of WhatsApp messages.');
            Response::redirect($back);
        }
        if (!WhatsAppApi::configured()) {
            AdminSecurity::flash('error', 'WhatsApp API is not configured. Set WHATSAPP_PHONE_NUMBER_ID and WHATSAPP_ACCESS_TOKEN in .env.');
            Response::redirect($back);
        }

        $result = WhatsAppApi::sendText($number, $body);
        $repo->record(
            'out',
            $number,
            $repo->contactNameFor($number),
            'text',
            $body,
            (string) ($result['response']['messages'][0]['id'] ?? ''),
            $result['ok'] ? 'sent' : 'failed',
            (string) json_encode($result['response'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );
        if ($result['ok']) {
            AdminSecurity::flash('success', 'Message sent.');
            $this->audit('whatsapp.message.sent', $number, ['length' => mb_strlen($body)]);
        } else {
            AdminSecurity::flash('error', 'WhatsApp send failed: ' . $this->apiError($result));
        }
        Response::redirect($back);
    }

    public function sendTest(Request $request): void
    {
        AdminSecurity::requirePermission('whatsapp.manage');
        if (!AdminSecurity::verifyCsrf($request->input('_csrf'))) {
            AdminSecurity::flash('error', 'Your session expired. Try again.');
            Response::redirect('/admin/whatsapp');
        }
        $settings = (new SiteSettingsRepository())->publicValues();
        $repo = new WhatsAppRepository();

        $to = WhatsAppApi::normalizeNumber((string) ($settings['whatsapp_notify_number'] ?? ''));
        if ($to === '') {
            $to = WhatsAppApi::normalizeNumber((string) ($settings['whatsapp_number'] ?? ''));
        }
        if ($to === '') {
            AdminSecurity::flash('error', 'Set a WhatsApp notification number in Site settings first.');
            Response::redirect('/admin/whatsapp');
        }
        $template = trim((string) ($settings['whatsapp_template_name'] ?? ''));
        if ($template === '') {
            AdminSecurity::flash('error', 'Set an approved template name in Site settings first.');
            Response::redirect('/admin/whatsapp');
        }
        if (!WhatsAppApi::configured()) {
            AdminSecurity::flash('error', 'WhatsApp API is not configured. Set WHATSAPP_PHONE_NUMBER_ID and WHATSAPP_ACCESS_TOKEN in .env.');
            Response::redirect('/admin/whatsapp');
        }
        if ($repo->isOptedOut($to)) {
            AdminSecurity::flash('error', 'The notification number has opted out of WhatsApp messages.');
            Response::redirect('/admin/whatsapp');
        }

        $language = trim((string) ($settings['whatsapp_template_language'] ?? '')) ?: 'en';
        $result = WhatsAppApi::sendTemplate($to, $template, $language, ['customer_name' => 'Test customer', 'service' => 'a test message']);
        $repo->record(
            'out',
            $to,
            '',
            'template',
            'Test template: ' . $template,
            (string) ($result['response']['messages'][0]['id'] ?? ''),
            $result['ok'] ? 'sent' : 'failed',
            (string) json_encode($result['response'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );
        if ($result['ok']) {
            AdminSecurity::flash('success', 'Test template sent to +' . $to . '.');
        } else {
            AdminSecurity::flash('error', 'Test send failed: ' . $this->apiError($result));
        }
        Response::redirect('/admin/whatsapp');
    }

    /** Meta's error.message plus the more precise error_data.details when present. */
    private function apiError(array $result): string
    {
        $error = $result['response']['error'] ?? [];
        $message = '(#' . (string) ($error['code'] ?? '?') . ') ' . (string) ($error['message'] ?? 'send failed');
        $details = trim((string) ($error['error_data']['details'] ?? ''));
        if ($details !== '' && !str_contains($message, $details)) {
            $message .= ' — ' . $details;
        }
        return mb_substr($message, 0, 300);
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
