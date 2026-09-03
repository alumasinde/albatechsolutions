<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

/**
 * Auth - session-based authentication + DB-driven RBAC checks.
 *
 * Roles and permissions are stored in the DB (roles, permissions,
 * role_permissions, user_roles tables) so access control is fully
 * admin-configurable, never hardcoded.
 */
final class Auth
{
    private const SESSION_KEY = '_auth_user_id';
    private const PENDING_2FA_KEY = '_auth_pending_2fa_user_id';

    public static function login(int $userId): void
    {
        session_regenerate_id(true);
        Session::put(self::SESSION_KEY, $userId);
        Session::forget(self::PENDING_2FA_KEY);
    }

    public static function markPendingTwoFactor(int $userId): void
    {
        session_regenerate_id(true);
        Session::forget(self::SESSION_KEY);
        Session::put(self::PENDING_2FA_KEY, $userId);
    }

    public static function pendingTwoFactorUserId(): ?int
    {
        $id = Session::get(self::PENDING_2FA_KEY);
        return $id === null ? null : (int) $id;
    }

    public static function confirmTwoFactor(): void
    {
        $userId = self::pendingTwoFactorUserId();
        if ($userId !== null) self::login($userId);
    }

    public static function logout(): void
    {
        self::clearAuthentication();
        Session::destroy();
    }

    public static function check(): bool
    {
        return Session::has(self::SESSION_KEY);
    }

    public static function id(): ?int
    {
        $id = Session::get(self::SESSION_KEY);
        return $id === null ? null : (int) $id;
    }

    public static function user(): ?array
    {
        $id = self::id();
        if ($id === null) return null;

        static $cached = null;
        if ($cached !== null && (int) $cached['id'] === $id) return $cached;

        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE id = :id AND deleted_at IS NULL LIMIT 1');
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        $cached = $user;

        return $user;
    }

    public static function can(string $permissionSlug): bool
    {
        $userId = self::id();
        if ($userId === null) return false;

        $stmt = Database::connection()->prepare(
            'SELECT COUNT(*) FROM user_roles ur
             INNER JOIN role_permissions rp ON rp.role_id = ur.role_id
             INNER JOIN permissions p ON p.id = rp.permission_id
             WHERE ur.user_id = :user_id AND p.slug = :slug'
        );
        $stmt->execute(['user_id' => $userId, 'slug' => $permissionSlug]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public static function hasRole(string $roleSlug): bool
    {
        $userId = self::id();
        if ($userId === null) return false;

        $stmt = Database::connection()->prepare(
            'SELECT COUNT(*) FROM user_roles ur
             INNER JOIN roles r ON r.id = ur.role_id
             WHERE ur.user_id = :user_id AND r.slug = :slug'
        );
        $stmt->execute(['user_id' => $userId, 'slug' => $roleSlug]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public static function hasStaffRole(): bool
    {
        $userId = self::id();
        if ($userId === null) return false;

        $stmt = Database::connection()->prepare(
            'SELECT COUNT(*) FROM user_roles ur
             INNER JOIN roles r ON r.id = ur.role_id
             WHERE ur.user_id = :user_id AND r.slug <> :customer'
        );
        $stmt->execute(['user_id' => $userId, 'customer' => 'customer']);

        return (int) $stmt->fetchColumn() > 0;
    }

    private static function clearAuthentication(): void
    {
        Session::forget(self::SESSION_KEY);
        Session::forget(self::PENDING_2FA_KEY);
    }
}
