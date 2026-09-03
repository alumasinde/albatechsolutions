<?php

declare(strict_types=1);

namespace App\Modules\Cms\Service;

use App\Core\AuditLog;
use App\Core\BaseService;
use App\Core\Config;
use App\Core\Helpers\Validator;
use App\Core\Logger;
use App\Core\Settings;
use App\Modules\Cms\Repository\ContactMessageRepository;
use PHPMailer\PHPMailer\PHPMailer;

final class ContactService extends BaseService
{
    public function __construct(
        private readonly ContactMessageRepository $messages
    ) {
    }

    /**
     * @return array{success: bool, errors?: array}
     */
    public function submit(array $data, string $ip): array
    {
        $validator = new Validator($data, [
            'name'    => 'required|min:2|max:150',
            'email'   => 'required|email',
            'message' => 'required|min:10',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()];
        }

        $id = $this->messages->create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'subject'    => $data['subject'] ?? null,
            'message'    => $data['message'],
            'status'     => 'new',
            'ip_address' => $ip,
        ]);

        AuditLog::record('contact_message.received', 'contact_message', $id);

        // Best-effort email notification — a misconfigured mail server
        // should never prevent the message from being saved.
        $this->notifyAdmin($data);

        return ['success' => true];
    }

    private function notifyAdmin(array $data): void
    {
        $recipient = Settings::get('contact_email');

        if (!$recipient || empty($_ENV['MAIL_HOST'] ?? null)) {
            return;
        }

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->Port = (int) ($_ENV['MAIL_PORT'] ?? 587);
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USER'] ?? '';
            $mail->Password = $_ENV['MAIL_PASS'] ?? '';
            $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'] ?? 'tls';

            $mail->setFrom(
                $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@' . parse_url(Config::get('app.url'), PHP_URL_HOST),
                $_ENV['MAIL_FROM_NAME'] ?? Settings::get('site_name', 'Website')
            );
            $mail->addAddress($recipient);
            $mail->addReplyTo($data['email'], $data['name']);

            $mail->Subject = 'New contact form message: ' . ($data['subject'] ?: 'General enquiry');
            $mail->Body = sprintf(
                "New message from the contact form:\n\nName: %s\nEmail: %s\nPhone: %s\n\nMessage:\n%s",
                $data['name'],
                $data['email'],
                $data['phone'] ?? 'not provided',
                $data['message']
            );

            $mail->send();
        } catch (\Throwable $e) {
            Logger::warning('Contact form email notification failed: ' . $e->getMessage());
        }
    }
}
