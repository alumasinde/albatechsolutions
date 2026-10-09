<?php

declare(strict_types=1);

namespace AlbaTech\Core;

final class AdminSecurity
{
    public static function startSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_name('albatech_admin');
            session_set_cookie_params([
                'httponly' => true,
                'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
                'samesite' => 'Lax',
                'path' => '/',
            ]);
            session_start();
        }
    }

    public static function csrfToken(): string
    {
        self::startSession();
        if (!isset($_SESSION['_csrf']) || !is_string($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    public static function verifyCsrf(mixed $token): bool
    {
        self::startSession();
        return is_string($token) && isset($_SESSION['_csrf']) && hash_equals((string) $_SESSION['_csrf'], $token);
    }

    public static function user(): ?array
    {
        self::startSession();
        return isset($_SESSION['admin_user']) && is_array($_SESSION['admin_user']) ? $_SESSION['admin_user'] : null;
    }

    public static function requireUser(): array
    {
        $user = self::user();
        if ($user === null) {
            Response::redirect('/admin/login');
        }
        return $user;
    }

    public static function flash(string $type, string $message): void
    {
        self::startSession();
        $_SESSION['_flash'] = ['type' => $type, 'message' => $message];
    }

    public static function pullFlash(): ?array
    {
        self::startSession();
        $flash = $_SESSION['_flash'] ?? null;
        unset($_SESSION['_flash']);
        return is_array($flash) ? $flash : null;
    }
}
