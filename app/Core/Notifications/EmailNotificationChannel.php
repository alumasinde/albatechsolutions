<?php

declare(strict_types=1);

namespace App\Core\Notifications;

use App\Core\Config;
use App\Core\Logger;
use App\Core\Settings;
use PHPMailer\PHPMailer\PHPMailer;

final class EmailNotificationChannel implements NotificationChannel
{
    public function name(): string { return 'email'; }

    public function available(): bool
    {
        return !empty($_ENV['MAIL_HOST'] ?? null) && !empty($_ENV['MAIL_FROM_ADDRESS'] ?? null);
    }

    public function send(NotificationMessage $message): NotificationResult
    {
        if (!$this->available()) return NotificationResult::failure('Email provider is not configured.');
        if (!filter_var($message->recipient, FILTER_VALIDATE_EMAIL)) return NotificationResult::failure('Invalid email recipient.');

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = (string)$_ENV['MAIL_HOST'];
            $mail->Port = (int)($_ENV['MAIL_PORT'] ?? 587);
            $mail->SMTPAuth = true;
            $mail->Username = (string)($_ENV['MAIL_USER'] ?? '');
            $mail->Password = (string)($_ENV['MAIL_PASS'] ?? '');
            $mail->SMTPSecure = (string)($_ENV['MAIL_ENCRYPTION'] ?? 'tls');
            $mail->setFrom(
                (string)($_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@' . parse_url(Config::get('app.url'), PHP_URL_HOST)),
                (string)($_ENV['MAIL_FROM_NAME'] ?? Settings::get('site_name', 'AlbaTech Solutions'))
            );
            $mail->addAddress($message->recipient);
            $mail->Subject = $message->subject;
            $mail->Body = $message->body;
            $mail->send();
            return NotificationResult::success((string)$mail->getLastMessageID());
        } catch (\Throwable $e) {
            Logger::warning('Email notification failed: ' . $e->getMessage(), ['to' => $message->recipient]);
            return NotificationResult::failure('Email delivery failed.');
        }
    }
}
