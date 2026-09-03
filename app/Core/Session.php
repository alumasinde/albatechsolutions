<?php

declare(strict_types=1);

namespace App\Core;

final class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $config = Config::get('session');

        ini_set('session.use_strict_mode', $config['strict_mode'] ? '1' : '0');
        ini_set('session.use_only_cookies', $config['use_only_cookies'] ? '1' : '0');
        ini_set('session.use_trans_sid', $config['use_trans_sid'] ? '1' : '0');

        session_set_cookie_params([
            'lifetime' => $config['lifetime'] * 60,
            'path'     => '/',
            'domain'   => '',
            'secure'   => $config['secure'],
            'httponly' => $config['httponly'],
            'samesite' => $config['samesite'],
        ]);

        session_name($config['name']);
        session_start();

        self::regenerateIfStale();
    }

    /**
     * Rotate the session ID periodically to mitigate fixation, without
     * disrupting an active session's data.
     */
    private static function regenerateIfStale(): void
    {
        $intervalSeconds = 900; // 15 minutes

        if (!isset($_SESSION['_last_regen'])) {
            $_SESSION['_last_regen'] = time();
            return;
        }

        if (time() - $_SESSION['_last_regen'] > $intervalSeconds) {
            session_regenerate_id(true);
            $_SESSION['_last_regen'] = time();
        }
    }

    public static function put(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public static function getFlash(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);

        return $value;
    }

    public static function destroy(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $params['path'],
                'domain' => $params['domain'],
                'secure' => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => $params['samesite'] ?? 'Lax',
            ]);
        }

        session_destroy();
    }
}
