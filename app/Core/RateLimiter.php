<?php

declare(strict_types=1);

namespace AlbaTech\Core;

/** Small file-backed limiter for low-volume admin authentication endpoints. */
final class RateLimiter
{
    public static function key(string $scope, string $identity): string
    {
        return hash('sha256', $scope . '|' . self::clientIp() . '|' . strtolower(trim($identity)));
    }

    public static function clientIp(): string
    {
        // Do not trust X-Forwarded-For unless the application is explicitly configured
        // behind a trusted proxy. REMOTE_ADDR is the only source used here.
        $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : 'unknown';
    }

    public static function tooManyAttempts(string $key, int $maximum, int $windowSeconds): bool
    {
        $state = self::read($key);
        $now = time();
        $attempts = array_values(array_filter($state['attempts'] ?? [], static fn ($t) => is_int($t) && $t > $now - $windowSeconds));
        self::write($key, ['attempts' => $attempts, 'blocked_until' => (int) ($state['blocked_until'] ?? 0)]);
        return (int) ($state['blocked_until'] ?? 0) > $now || count($attempts) >= $maximum;
    }

    public static function hit(string $key, int $windowSeconds, int $blockSeconds = 0): void
    {
        $state = self::read($key);
        $now = time();
        $attempts = array_values(array_filter($state['attempts'] ?? [], static fn ($t) => is_int($t) && $t > $now - $windowSeconds));
        $attempts[] = $now;
        self::write($key, ['attempts' => $attempts, 'blocked_until' => $blockSeconds > 0 ? $now + $blockSeconds : (int) ($state['blocked_until'] ?? 0)]);
    }

    public static function clear(string $key): void
    {
        $path = self::path($key);
        if (is_file($path)) @unlink($path);
    }

    private static function read(string $key): array
    {
        $path = self::path($key);
        if (!is_file($path)) return [];
        $handle = @fopen($path, 'rb');
        if (!$handle) return [];
        flock($handle, LOCK_SH);
        $raw = stream_get_contents($handle);
        flock($handle, LOCK_UN);
        fclose($handle);
        $data = json_decode((string) $raw, true);
        return is_array($data) ? $data : [];
    }

    private static function write(string $key, array $data): void
    {
        $directory = dirname(__DIR__, 2) . '/storage/cache/rate-limits';
        if (!is_dir($directory) && !@mkdir($directory, 0700, true) && !is_dir($directory)) return;
        $path = $directory . '/' . $key . '.json';
        $handle = @fopen($path, 'c+b');
        if (!$handle) return;
        flock($handle, LOCK_EX);
        ftruncate($handle, 0);
        rewind($handle);
        fwrite($handle, json_encode($data, JSON_UNESCAPED_SLASHES) ?: '{}');
        fflush($handle);
        flock($handle, LOCK_UN);
        fclose($handle);
        @chmod($path, 0600);
    }

    private static function path(string $key): string
    {
        return dirname(__DIR__, 2) . '/storage/cache/rate-limits/' . $key . '.json';
    }
}
