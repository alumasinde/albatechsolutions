<?php

declare(strict_types=1);

namespace AlbaTech\Core;

use PDO;

final class AdminSecurity
{
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) return;
        $sessionName = Env::get('SESSION_NAME', 'albatech_admin') ?: 'albatech_admin';
        if (!preg_match('/^[A-Za-z][A-Za-z0-9_-]{0,63}$/', $sessionName)) {
            $sessionName = 'albatech_admin';
        }
        session_name($sessionName);
        ini_set('session.use_trans_sid', '0');
        ini_set('session.use_strict_mode', '1');
        ini_set('session.sid_length', '48');
        ini_set('session.sid_bits_per_character', '6');
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => self::isHttps() || Env::bool('SESSION_SECURE_COOKIE', false),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_httponly', '1');
        session_start();
        self::enforceSessionExpiry();
    }

    public static function isHttps(): bool
    {
        // Never trust forwarded headers here; TLS termination must be reflected by the web server.
        return (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);
    }

    private static function enforceSessionExpiry(): void
    {
        if (!isset($_SESSION['admin_user'])) return;
        $now = time();
        $idle = max(300, (int) Env::get('SESSION_IDLE_TIMEOUT', '1800'));
        $absolute = max($idle, (int) Env::get('SESSION_ABSOLUTE_TIMEOUT', '28800'));
        $created = (int) ($_SESSION['_created_at'] ?? $now);
        $last = (int) ($_SESSION['_last_activity'] ?? $now);
        if (($now - $last) > $idle || ($now - $created) > $absolute) {
            $_SESSION = [];
            self::destroySession();
            Response::redirect('/admin/login?expired=1');
        }
        $_SESSION['_last_activity'] = $now;
    }

    public static function csrfToken(): string
    {
        self::startSession();
        if (!isset($_SESSION['_csrf']) || !is_string($_SESSION['_csrf'])) $_SESSION['_csrf'] = bin2hex(random_bytes(32));
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
        if ($user === null) Response::redirect('/admin/login');
        // Recheck account state on every protected request so deactivation takes effect immediately.
        $stmt = Database::connection()->prepare("SELECT id, first_name, last_name, email FROM admin_users WHERE id = :id AND status = 'active' LIMIT 1");
        $stmt->execute(['id' => (int) ($user['id'] ?? 0)]);
        $fresh = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$fresh) {
            $_SESSION = [];
            self::destroySession();
            Response::redirect('/admin/login');
        }
        $_SESSION['admin_user'] = $fresh;
        return $fresh;
    }

    public static function requirePermission(string $permission): array
    {
        $user = self::requireUser();
        $stmt = Database::connection()->prepare('SELECT 1 FROM admin_user_roles ur JOIN role_permissions rp ON rp.role_id = ur.role_id JOIN permissions p ON p.id = rp.permission_id WHERE ur.admin_user_id = :user_id AND p.slug = :permission LIMIT 1');
        $stmt->execute(['user_id' => (int) $user['id'], 'permission' => $permission]);
        if (!$stmt->fetchColumn()) {
            http_response_code(403);
            Response::html(View::render('errors/403', ['pageTitle' => 'Access denied']), 403);
            exit;
        }
        return $user;
    }


    public static function destroySession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }

        $_SESSION = [];
        $params = session_get_cookie_params();
        if (ini_get('session.use_cookies')) {
            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $params['path'] ?: '/',
                'domain' => $params['domain'] ?? '',
                'secure' => (bool) ($params['secure'] ?? false),
                'httponly' => true,
                'samesite' => $params['samesite'] ?? 'Lax',
            ]);
        }
        session_destroy();
    }

    public static function rotateCsrfToken(): string
    {
        self::startSession();
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        return $_SESSION['_csrf'];
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
