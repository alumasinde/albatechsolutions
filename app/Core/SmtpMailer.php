<?php

declare(strict_types=1);

namespace AlbaTech\Core;

use RuntimeException;

/** Minimal authenticated SMTP sender using verified TLS; no third-party runtime dependency. */
final class SmtpMailer
{
    public static function send(string $to, string $subject, string $body, string $replyToEmail, string $replyToName): void
    {
        $host = trim((string) Env::get('MAIL_HOST', ''));
        $port = (int) Env::get('MAIL_PORT', '587');
        $encryption = strtolower(trim((string) Env::get('MAIL_ENCRYPTION', 'tls')));
        $username = (string) Env::get('MAIL_USERNAME', '');
        $password = (string) Env::get('MAIL_PASSWORD', '');
        $from = trim((string) Env::get('MAIL_FROM_ADDRESS', ''));
        $fromName = self::headerText((string) Env::get('MAIL_FROM_NAME', Env::get('APP_NAME', 'AlbaTech Solutions')));

        if ($host === '' || !preg_match('/^[a-zA-Z0-9.-]+$/', $host) || !in_array($port, [25, 465, 587, 2525], true)) {
            throw new RuntimeException('SMTP host or port is not configured correctly.');
        }
        if (!in_array($encryption, ['tls', 'ssl', 'none'], true)) {
            throw new RuntimeException('SMTP encryption must be tls, ssl, or none.');
        }
        if (!filter_var($to, FILTER_VALIDATE_EMAIL) || !filter_var($from, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('SMTP recipient or sender email is invalid.');
        }
        if ($username === '' || $password === '') {
            throw new RuntimeException('SMTP authentication credentials are not configured.');
        }

        $target = ($encryption === 'ssl' ? 'ssl://' : 'tcp://') . $host . ':' . $port;
        $context = stream_context_create(['ssl' => [
            'verify_peer' => true,
            'verify_peer_name' => true,
            'peer_name' => $host,
            'SNI_enabled' => true,
        ]]);
        $socket = @stream_socket_client($target, $errno, $errstr, 10, STREAM_CLIENT_CONNECT, $context);
        if (!is_resource($socket)) {
            throw new RuntimeException('Could not connect to configured SMTP server.');
        }
        stream_set_timeout($socket, 10);

        try {
            self::expect($socket, [220]);
            self::command($socket, 'EHLO ' . self::safeEhlo((string) parse_url((string) Env::get('APP_URL', 'localhost'), PHP_URL_HOST)), [250]);
            if ($encryption === 'tls') {
                self::command($socket, 'STARTTLS', [220]);
                $crypto = stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                if ($crypto !== true) {
                    throw new RuntimeException('Could not establish TLS for SMTP.');
                }
                self::command($socket, 'EHLO ' . self::safeEhlo((string) parse_url((string) Env::get('APP_URL', 'localhost'), PHP_URL_HOST)), [250]);
            } elseif ($encryption === 'none' && Env::get('APP_ENV', 'local') === 'production') {
                throw new RuntimeException('Unencrypted SMTP is not allowed in production.');
            }

            self::command($socket, 'AUTH LOGIN', [334]);
            self::command($socket, base64_encode($username), [334]);
            self::command($socket, base64_encode($password), [235]);
            self::command($socket, 'MAIL FROM:<' . $from . '>', [250]);
            self::command($socket, 'RCPT TO:<' . $to . '>', [250, 251]);
            self::command($socket, 'DATA', [354]);

            $subject = self::headerText($subject);
            $replyToName = self::headerText($replyToName);
            $replyToEmail = filter_var($replyToEmail, FILTER_VALIDATE_EMAIL) ? $replyToEmail : $from;
            $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
            $encodedFromName = '=?UTF-8?B?' . base64_encode($fromName) . '?=';
            $encodedReplyName = '=?UTF-8?B?' . base64_encode($replyToName) . '?=';
            $message = "Date: " . date(DATE_RFC2822) . "\r\n"
                . "From: {$encodedFromName} <{$from}>\r\n"
                . "To: <{$to}>\r\n"
                . "Reply-To: {$encodedReplyName} <{$replyToEmail}>\r\n"
                . "Subject: {$encodedSubject}\r\n"
                . "MIME-Version: 1.0\r\n"
                . "Content-Type: text/plain; charset=UTF-8\r\n"
                . "Content-Transfer-Encoding: 8bit\r\n"
                . "X-Mailer: AlbaTech-Solutions\r\n\r\n"
                . str_replace(["\r\n", "\r"], "\n", $body);
            $message = str_replace("\n", "\r\n", $message);
            // SMTP DATA lines beginning with a dot must be dot-stuffed.
            $message = preg_replace('/(^|\r\n)\./', '$1..', $message) ?? $message;
            self::writeAll($socket, $message . "\r\n.\r\n");
            self::expect($socket, [250]);
            self::command($socket, 'QUIT', [221]);
        } finally {
            fclose($socket);
        }
    }

    private static function command($socket, string $command, array $expected): string
    {
        if (str_contains($command, "\r") || str_contains($command, "\n")) {
            throw new RuntimeException('Invalid SMTP command.');
        }
        self::writeAll($socket, $command . "\r\n");
        return self::expect($socket, $expected);
    }

    private static function expect($socket, array $expected): string
    {
        $response = '';
        do {
            $line = fgets($socket, 2048);
            if ($line === false) throw new RuntimeException('SMTP server closed the connection or timed out.');
            $meta = stream_get_meta_data($socket);
            if (!empty($meta['timed_out'])) throw new RuntimeException('SMTP server response timed out.');
            $response .= $line;
            if (preg_match('/^(\d{3})([ -])/', $line, $matches) !== 1) {
                throw new RuntimeException('Invalid SMTP response.');
            }
            $code = (int) $matches[1];
            $more = $matches[2] === '-';
        } while ($more);
        if (!in_array($code, $expected, true)) {
            throw new RuntimeException('SMTP server rejected a mail operation with code ' . $code . '.');
        }
        return $response;
    }

    private static function writeAll($socket, string $data): void
    {
        $length = strlen($data);
        $offset = 0;
        while ($offset < $length) {
            $written = fwrite($socket, substr($data, $offset));
            if ($written === false || $written === 0) {
                throw new RuntimeException('SMTP command write failed.');
            }
            $offset += $written;
        }
    }

    private static function headerText(string $value): string
    {
        return trim(str_replace(["\r", "\n", "\0"], ' ', $value));
    }

    private static function safeEhlo(string $host): string
    {
        return preg_match('/^[a-zA-Z0-9.-]{1,253}$/', $host) ? $host : 'localhost';
    }
}
