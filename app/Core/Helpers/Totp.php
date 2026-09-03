<?php

declare(strict_types=1);

namespace App\Core\Helpers;

/**
 * Totp - Time-based One-Time Password (RFC 6238), hand-rolled to
 * avoid adding an extra Composer dependency for something this small.
 * Compatible with Google Authenticator, Authy, 1Password, etc.
 */
final class Totp
{
    private const PERIOD = 30;
    private const DIGITS = 6;
    private const ALGO = 'sha1';

    public static function generateSecret(int $length = 20): string
    {
        return self::base32Encode(random_bytes($length));
    }

    public static function currentCode(string $base32Secret, ?int $timestamp = null): string
    {
        $timestamp ??= time();
        $counter = intdiv($timestamp, self::PERIOD);

        return self::hotp($base32Secret, $counter);
    }

    /**
     * Verify a submitted code, allowing +/- 1 time-step of clock drift.
     */
    public static function verify(string $base32Secret, string $code, int $window = 1): bool
    {
        $code = trim($code);
        $timestamp = time();
        $counter = intdiv($timestamp, self::PERIOD);

        for ($i = -$window; $i <= $window; $i++) {
            if (hash_equals(self::hotp($base32Secret, $counter + $i), $code)) {
                return true;
            }
        }

        return false;
    }

    public static function provisioningUri(string $base32Secret, string $accountLabel, string $issuer): string
    {
        $label = rawurlencode($issuer . ':' . $accountLabel);
        $params = http_build_query([
            'secret'  => $base32Secret,
            'issuer'  => $issuer,
            'algorithm' => 'SHA1',
            'digits'  => self::DIGITS,
            'period'  => self::PERIOD,
        ]);

        return "otpauth://totp/{$label}?{$params}";
    }

    /**
     * @return string[] Ten single-use recovery codes.
     */
    public static function generateRecoveryCodes(int $count = 10): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(4))) . '-' . strtoupper(bin2hex(random_bytes(4)));
        }

        return $codes;
    }

    private static function hotp(string $base32Secret, int $counter): string
    {
        $key = self::base32Decode($base32Secret);
        $binaryCounter = pack('N*', 0, $counter); // 8-byte big-endian counter

        $hash = hash_hmac(self::ALGO, $binaryCounter, $key, true);
        $offset = ord($hash[strlen($hash) - 1]) & 0x0F;

        $truncated = ((ord($hash[$offset]) & 0x7F) << 24)
            | ((ord($hash[$offset + 1]) & 0xFF) << 16)
            | ((ord($hash[$offset + 2]) & 0xFF) << 8)
            | (ord($hash[$offset + 3]) & 0xFF);

        $code = $truncated % (10 ** self::DIGITS);

        return str_pad((string) $code, self::DIGITS, '0', STR_PAD_LEFT);
    }

    private static function base32Encode(string $data): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $bits = '';
        foreach (str_split($data) as $char) {
            $bits .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }

        $output = '';
        foreach (str_split($bits, 5) as $chunk) {
            $chunk = str_pad($chunk, 5, '0', STR_PAD_RIGHT);
            $output .= $alphabet[bindec($chunk)];
        }

        return $output;
    }

    private static function base32Decode(string $base32): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $base32 = strtoupper(rtrim($base32, '='));

        $bits = '';
        foreach (str_split($base32) as $char) {
            $pos = strpos($alphabet, $char);
            if ($pos === false) {
                continue;
            }
            $bits .= str_pad(decbin($pos), 5, '0', STR_PAD_LEFT);
        }

        $bytes = '';
        foreach (str_split($bits, 8) as $byte) {
            if (strlen($byte) < 8) {
                continue;
            }
            $bytes .= chr(bindec($byte));
        }

        return $bytes;
    }
}
