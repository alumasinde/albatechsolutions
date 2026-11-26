<?php

declare(strict_types=1);

namespace AlbaTech\Controllers;

use AlbaTech\Core\AdminSecurity;
use AlbaTech\Core\Database;
use AlbaTech\Core\Env;
use AlbaTech\Core\RateLimiter;
use AlbaTech\Core\Request;
use AlbaTech\Core\Response;
use AlbaTech\Core\SmtpMailer;
use AlbaTech\Core\WhatsAppApi;
use AlbaTech\Repositories\ServiceRepository;
use AlbaTech\Repositories\SiteSettingsRepository;
use AlbaTech\Repositories\WhatsAppRepository;
use Throwable;

final class EnquiryController
{
    public function submit(Request $request): void
    {
        if ($request->method() !== 'POST') {
            Response::redirect('/get-help', 303);
        }

        if (!AdminSecurity::verifyCsrf($request->input('_csrf'))) {
            http_response_code(419);
            Response::html('Your form session expired. Please go back, refresh the page, and try again.', 419);
            return;
        }

        // Quietly accept bot submissions into a generic success redirect without sending email.
        if (trim((string) $request->input('website_url', '')) !== '') {
            Response::redirect('/get-help?sent=1', 303);
        }

        $name = trim((string) $request->input('name', ''));
        $senderEmail = strtolower(trim((string) $request->input('email', '')));
        $phone = trim((string) $request->input('phone', ''));
        $serviceKey = trim((string) $request->input('service', ''));
        $message = trim((string) $request->input('message', ''));

        $rateKey = RateLimiter::key('public-enquiry', '*');
        try {
            if (RateLimiter::tooManyAttempts($rateKey, 5, 900)) {
                http_response_code(429);
                header('Retry-After: 900');
                Response::html('Too many enquiries were submitted from this connection. Please wait 15 minutes and try again.', 429);
                return;
            }
            RateLimiter::hit($rateKey, 900);
        } catch (Throwable $e) {
            error_log('[AlbaTech enquiry rate limit] ' . $e->getMessage());
            Response::redirect('/get-help?sent=0', 303);
        }

        $phoneDigits = preg_replace('/\D+/', '', $phone) ?? '';
        $phoneValid = $phone !== ''
            && preg_match('/^[+0-9() .-]+$/', $phone) === 1
            && strlen($phoneDigits) >= 8
            && strlen($phoneDigits) <= 15;
        $emailValid = $senderEmail === '' || (filter_var($senderEmail, FILTER_VALIDATE_EMAIL) && strlen($senderEmail) <= 254);

        if ($name === '' || mb_strlen($name) > 100 || preg_match('/[\r\n\0]/', $name)
            || !$phoneValid || mb_strlen($phone) > 30 || preg_match('/[\r\n\0]/', $phone)
            || !$emailValid
            || mb_strlen($message) < 5 || mb_strlen($message) > 5000) {
            Response::redirect('/get-help?sent=0', 303);
        }

        $serviceName = (new ServiceRepository())->enquiryOptions()[$serviceKey] ?? ($serviceKey === '' ? 'Not specified' : 'Other');
        $settings = (new SiteSettingsRepository())->publicValues();
        $recipient = trim((string) ($settings['contact_email'] ?? ''));
        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            $recipient = trim((string) ($settings['support_email'] ?? ''));
        }
        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            $recipient = trim((string) Env::get('MAIL_TO', ''));
        }
        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            error_log('[AlbaTech enquiry] No valid recipient configured. Set contact_email/support_email in Admin Settings or MAIL_TO in .env.');
            Response::redirect('/get-help?sent=0', 303);
        }

        $siteName = trim((string) ($settings['site_name'] ?? Env::get('APP_NAME', 'AlbaTech Solutions')));
        $safeName = str_replace(["\r", "\n"], ' ', $name);
        $body = "A new website enquiry was submitted.\n\n"
            . "Name: {$safeName}\n"
            . "Phone / WhatsApp: {$phone}\n"
            . "Email: " . ($senderEmail !== '' ? $senderEmail : 'Not provided') . "\n"
            . "Service: {$serviceName}\n\n"
            . "Message:\n{$message}\n\n"
            . "Received: " . date(DATE_RFC2822) . "\n"
            . "Source: " . (string) Env::get('APP_URL', '') . "/get-help\n";

        try {
            SmtpMailer::send($recipient, 'Website enquiry: ' . $siteName, $body, $senderEmail, $safeName);
            $whatsAppConsent = (string) $request->input('whatsapp_consent', '') === '1';
            if ($whatsAppConsent) {
                $this->sendWhatsAppAcknowledgment($phoneDigits, $safeName, $serviceName, $settings);
            }
            // Keep an audit trail without storing the enquiry body or personal data.
            try {
                Database::connection()->prepare(
                    "INSERT INTO admin_audit_logs (admin_user_id, action, entity_type, entity_id, details, ip_address) VALUES (NULL, 'public.enquiry.sent', 'enquiry', NULL, :details, :ip)"
                )->execute([
                    'details' => json_encode(['service' => $serviceName, 'whatsapp_consent' => $whatsAppConsent], JSON_UNESCAPED_SLASHES),
                    'ip' => substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45),
                ]);
            } catch (Throwable $auditError) {
                error_log('[AlbaTech enquiry audit] ' . $auditError->getMessage());
            }
            Response::redirect('/get-help?sent=1', 303);
        } catch (Throwable $e) {
            // Do not reveal SMTP host, credentials, or server diagnostics to visitors.
            error_log('[AlbaTech enquiry email] ' . $e->getMessage());
            Response::redirect('/get-help?sent=0', 303);
        }
    }

    /**
     * Send the approved acknowledgment template to a consented submitter.
     * Never fatal: failures are logged and recorded so the admin can retry.
     *
     * @param array<string, string> $settings
     */
    private function sendWhatsAppAcknowledgment(string $phoneDigits, string $name, string $serviceName, array $settings): void
    {
        try {
            if (($settings['whatsapp_api_enabled'] ?? '0') !== '1' || ($settings['whatsapp_ack_enabled'] ?? '1') !== '1' || !WhatsAppApi::configured()) {
                return;
            }
            $template = trim((string) ($settings['whatsapp_template_name'] ?? ''));
            if ($template === '') {
                return;
            }
            $to = WhatsAppApi::normalizeNumber($phoneDigits);
            $repo = new WhatsAppRepository();
            if ($repo->isOptedOut($to)) {
                return;
            }
            $language = trim((string) ($settings['whatsapp_template_language'] ?? '')) ?: 'en';
            $result = WhatsAppApi::sendTemplate($to, $template, $language, ['customer_name' => $name, 'service' => $serviceName]);
            $repo->record(
                'out',
                $to,
                $name,
                'template',
                'Template: ' . $template,
                (string) ($result['response']['messages'][0]['id'] ?? ''),
                $result['ok'] ? 'sent' : 'failed',
                (string) json_encode($result['response'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            );
        } catch (Throwable $e) {
            error_log('[AlbaTech whatsapp ack] ' . $e->getMessage());
        }
    }
}
