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
        Session::put(self::SESSION_KEY, $userId);
        Session::forget(self::PENDING_2FA_KEY);
        session_regenerate_id(true);
    }

    /**
     * First stage of a two-factor login: password verified, but the
     * user is not yet fully authenticated until they pass the TOTP
     * challenge. Kept in a separate session key so route middleware
     * (which checks SESSION_KEY) never treats this as a real session.
     */
    public static function markPendingTwoFactor(int $userId): void
    {
        Session::forget(self::SESSION_KEY);
        Session::put(self::PENDING_2FA_KEY, $userId);
        session_regenerate_id(true);
    }

    public static function pendingTwoFactorUserId(): ?int
    {
        return Session::get(self::PENDING_2FA_KEY);
    }

    public static function confirmTwoFactor(): void
    {
        $userId = self::pendingTwoFactorUserId();

        if ($userId !== null) {
            self::login($userId);
        }
    }

    public static function logout(): void
    {
        Session::forget(self::SESSION_KEY);
        Session::destroy();
    }

    public static function check(): bool
    {
        return Session::has(self::SESSION_KEY);
    }

    public static function id(): ?int
    {
        return Session::get(self::SESSION_KEY);
    }

    public static function user(): ?array
    {
        $id = self::id();

        if ($id === null) {
            return null;
        }

        static $cached = null;

        if ($cached !== null && $cached['id'] === $id) {
            return $cached;
        }

        $stmt = Database::connection()->prepare(
            'SELECT * FROM users WHERE id = :id AND deleted_at IS NULL LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

        $cached = $user;

        return $user;
    }

    /**
     * Check whether the current user holds a permission slug
     * (e.g. 'services.view', 'pages.manage') via their assigned roles.
     */
    public static function can(string $permissionSlug): bool
    {
        $userId = self::id();

        if ($userId === null) {
            return false;
        }

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

        if ($userId === null) {
            return false;
        }

        $stmt = Database::connection()->prepare(
            'SELECT COUNT(*) FROM user_roles ur
             INNER JOIN roles r ON r.id = ur.role_id
             WHERE ur.user_id = :user_id AND r.slug = :slug'
        );
        $stmt->execute(['user_id' => $userId, 'slug' => $roleSlug]);

        return (int) $stmt->fetchColumn() > 0;
    }
}
