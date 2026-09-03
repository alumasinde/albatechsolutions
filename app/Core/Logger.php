<?php

declare(strict_types=1);

namespace App\Core;

final class Logger
{
    private static function path(string $channel): string
    {
        $dir = dirname(__DIR__, 2) . '/storage/logs';

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return $dir . '/' . $channel . '-' . date('Y-m-d') . '.log';
    }

    private static function write(string $channel, string $level, string $message, array $context = []): void
    {
        $line = sprintf(
            "[%s] %s: %s %s\n",
            date('Y-m-d H:i:s'),
            strtoupper($level),
            $message,
            $context ? json_encode($context, JSON_UNESCAPED_SLASHES) : ''
        );

        file_put_contents(self::path($channel), $line, FILE_APPEND | LOCK_EX);
    }

    public static function error(string $message, array $context = []): void
    {
        self::write('error', 'error', $message, $context);
    }

    public static function info(string $message, array $context = []): void
    {
        self::write('app', 'info', $message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::write('app', 'warning', $message, $context);
    }

    public static function security(string $message, array $context = []): void
    {
        self::write('security', 'security', $message, $context);
    }
}
