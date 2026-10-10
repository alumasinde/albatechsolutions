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
use AlbaTech\Repositories\SiteSettingsRepository;
use Throwable;

final class EnquiryController
{
    private const SERVICES = [
        'website-design' => 'Website Design',
        'landing-page' => 'Landing Page',
        'google-business' => 'Google Business Profile',
        'kra-returns' => 'KRA Returns Filing',
        'business-registration' => 'Business Registration',
        'cv-writing' => 'CV Writing',
        'other' => 'Something else',
    ];

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

        if ($name === '' || mb_strlen($name) > 100 || preg_match('/[\r\n\0]/', $name)
            || !filter_var($senderEmail, FILTER_VALIDATE_EMAIL) || strlen($senderEmail) > 254
            || mb_strlen($phone) > 30 || preg_match('/[\r\n\0]/', $phone)
            || mb_strlen($message) < 5 || mb_strlen($message) > 5000) {
            Response::redirect('/get-help?sent=0', 303);
        }

        $serviceName = self::SERVICES[$serviceKey] ?? ($serviceKey === '' ? 'Not specified' : 'Other');
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
            . "Email: {$senderEmail}\n"
            . "Phone: " . ($phone !== '' ? $phone : 'Not provided') . "\n"
            . "Service: {$serviceName}\n\n"
            . "Message:\n{$message}\n\n"
            . "Received: " . date(DATE_RFC2822) . "\n"
            . "Source: " . (string) Env::get('APP_URL', '') . "/get-help\n";

        try {
            SmtpMailer::send($recipient, 'Website enquiry — ' . $siteName, $body, $senderEmail, $safeName);
            // Keep an audit trail without storing the enquiry body or personal data.
            try {
                Database::connection()->prepare(
                    "INSERT INTO admin_audit_logs (admin_user_id, action, entity_type, entity_id, details, ip_address) VALUES (NULL, 'public.enquiry.sent', 'enquiry', NULL, :details, :ip)"
                )->execute([
                    'details' => json_encode(['service' => $serviceName], JSON_UNESCAPED_SLASHES),
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
}
