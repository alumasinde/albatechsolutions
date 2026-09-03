<?php

declare(strict_types=1);

namespace App\Modules\Growth\Service;

use App\Core\AuditLog;
use App\Core\BaseService;
use App\Core\Config;
use App\Core\Logger;
use App\Core\Settings;
use App\Core\Helpers\Validator;
use App\Modules\Growth\Repository\LeadRepository;
use PHPMailer\PHPMailer\PHPMailer;

final class LeadService extends BaseService
{
    public function __construct(private readonly LeadRepository $leads) {}

    public function submit(array $data, string $ip, string $userAgent): array
    {
        // Quietly accept honeypot hits so simple bots do not learn the
        // anti-spam rule from the response. Do not create a lead.
        if (trim((string)($data['website'] ?? '')) !== '') {
            return ['success' => true, 'id' => 0];
        }

        $validator = new Validator($data, [
            'name' => 'required|min:2|max:150',
            'email' => 'required|email',
            'message' => 'required|min:10|max:5000',
        ]);
        if ($validator->fails()) return ['success' => false, 'errors' => $validator->errors()];

        $id = $this->leads->create([
            'name' => trim((string)$data['name']),
            'business_name' => trim((string)($data['business_name'] ?? '')) ?: null,
            'email' => trim((string)$data['email']),
            'phone' => trim((string)($data['phone'] ?? '')) ?: null,
            'service_id' => !empty($data['service_id']) ? (int)$data['service_id'] : null,
            'budget' => trim((string)($data['budget'] ?? '')) ?: null,
            'project_type' => trim((string)($data['project_type'] ?? '')) ?: null,
            'message' => trim((string)$data['message']),
            'source' => trim((string)($data['source'] ?? 'website')) ?: 'website',
            'status' => 'new',
            'ip_address' => $ip,
            'user_agent' => substr($userAgent, 0, 255),
        ]);

        AuditLog::record('quote_request.received', 'quote_request', $id);
        $this->notifyAdmin($data);
        return ['success' => true, 'id' => $id];
    }

    private function notifyAdmin(array $data): void
    {
        $recipient = Settings::get('contact_email');
        if (!$recipient || empty($_ENV['MAIL_HOST'] ?? null)) return;
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->Port = (int)($_ENV['MAIL_PORT'] ?? 587);
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USER'] ?? '';
            $mail->Password = $_ENV['MAIL_PASS'] ?? '';
            $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'] ?? 'tls';
            $mail->setFrom($_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@' . parse_url(Config::get('app.url'), PHP_URL_HOST), $_ENV['MAIL_FROM_NAME'] ?? Settings::get('site_name', 'Website'));
            $mail->addAddress($recipient);
            $mail->addReplyTo((string)$data['email'], (string)$data['name']);
            $mail->Subject = 'New quote request — ' . ($data['project_type'] ?? 'Website enquiry');
            $mail->Body = sprintf("New quote request\n\nName: %s\nBusiness: %s\nEmail: %s\nPhone: %s\nBudget: %s\nProject: %s\n\n%s", $data['name'], $data['business_name'] ?? '—', $data['email'], $data['phone'] ?? '—', $data['budget'] ?? '—', $data['project_type'] ?? '—', $data['message']);
            $mail->send();
        } catch (\Throwable $e) {
            Logger::warning('Quote request email notification failed: ' . $e->getMessage());
        }
    }
}
