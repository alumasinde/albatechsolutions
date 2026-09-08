<?php

declare(strict_types=1);

/**
 * AlbaTech SMTP diagnostic.
 *
 * Usage:
 *   php bin/test-email.php recipient@example.com
 *
 * This file does not print MAIL_PASS.
 */

require dirname(__DIR__) . '/vendor/autoload.php';

use Dotenv\Dotenv;
use PHPMailer\PHPMailer\PHPMailer;

$root = dirname(__DIR__);
Dotenv::createImmutable($root)->safeLoad();

$recipient = $argv[1] ?? '';
if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Usage: php bin/test-email.php recipient@example.com\n");
    exit(1);
}

$host = trim((string) ($_ENV['MAIL_HOST'] ?? ''));
$port = (int) ($_ENV['MAIL_PORT'] ?? 587);
$user = trim((string) ($_ENV['MAIL_USER'] ?? ''));
$pass = (string) ($_ENV['MAIL_PASS'] ?? '');
$from = trim((string) ($_ENV['MAIL_FROM_ADDRESS'] ?? ''));
$fromName = trim((string) ($_ENV['MAIL_FROM_NAME'] ?? 'AlbaTech Solutions'));
$encryption = strtolower(trim((string) ($_ENV['MAIL_ENCRYPTION'] ?? 'tls')));

echo "AlbaTech SMTP diagnostic\n";
echo "========================\n";
echo "Host: {$host}\n";
echo "Port: {$port}\n";
echo "Encryption: {$encryption}\n";
echo "User configured: " . ($user !== '' ? 'yes' : 'no') . "\n";
echo "Password configured: " . ($pass !== '' ? 'yes' : 'no') . "\n";
echo "From: {$from}\n";
echo "Recipient: {$recipient}\n\n";

if ($host === '' || $from === '') {
    fwrite(STDERR, "MAIL_HOST and MAIL_FROM_ADDRESS must be configured in .env.\n");
    exit(1);
}

$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = $host;
$mail->Port = $port;
$mail->SMTPAuth = true;
$mail->Username = $user;
$mail->Password = $pass;
$mail->CharSet = 'UTF-8';

if ($encryption === 'tls') {
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
} elseif ($encryption === 'ssl') {
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
} elseif ($encryption !== '' && $encryption !== 'none') {
    fwrite(STDERR, "Unsupported MAIL_ENCRYPTION value: {$encryption}\n");
    exit(1);
}

$mail->SMTPDebug = 2;
$mail->Debugoutput = static function (string $message, int $level): void {
    echo "[SMTP {$level}] {$message}\n";
};

try {
    $mail->setFrom($from, $fromName);
    $mail->addAddress($recipient);
    $mail->isHTML(true);
    $mail->Subject = 'AlbaTech SMTP Diagnostic Test';
    $mail->Body = '<h2>SMTP is working</h2><p>This diagnostic email was sent by AlbaTech Solutions.</p>';
    $mail->AltBody = 'SMTP is working. This diagnostic email was sent by AlbaTech Solutions.';

    $mail->send();

    echo "\nSUCCESS: Email accepted by SMTP server.\n";
    echo "Message ID: " . $mail->getLastMessageID() . "\n";
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, "\nFAILED: " . $e->getMessage() . "\n");
    exit(1);
}
