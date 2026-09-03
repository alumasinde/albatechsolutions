<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

/**
 * Settings - DB-backed key/value store for everything the admin
 * panel should be able to change without a code deploy: branding,
 * theme/colour palette, typography, contact details, social links,
 * SEO defaults, payment toggles, feature flags, etc.
 *
 * Cached in-memory per request. Call Settings::flush() after an
 * admin write so the next read picks up fresh values.
 */
final class Settings
{
    private static ?array $cache = null;

    public static function all(): array
    {
        if (self::$cache === null) {
            self::$cache = self::loadFromDb();
        }

        return self::$cache;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $all = self::all();

        return array_key_exists($key, $all) ? $all[$key] : $default;
    }

    public static function set(string $key, string $value, string $type = 'string'): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'INSERT INTO settings (`key`, `value`, `type`, updated_at)
             VALUES (:key, :value, :type, NOW())
             ON DUPLICATE KEY UPDATE `value` = VALUES(`value`),
                                      `type` = VALUES(`type`),
                                      updated_at = NOW()'
        );
        $stmt->execute(['key' => $key, 'value' => $value, 'type' => $type]);

        self::flush();
    }

    public static function flush(): void
    {
        self::$cache = null;
    }

    private static function loadFromDb(): array
    {
        try {
            $pdo = Database::connection();
            $rows = $pdo->query('SELECT `key`, `value`, `type` FROM settings')
                ->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            // Settings table may not exist yet (fresh install before migration).
            return [];
        }

        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['key']] = self::castValue($row['value'], $row['type']);
        }

        return $settings;
    }

    private static function castValue(?string $value, string $type): mixed
    {
        return match ($type) {
            'bool', 'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'int', 'integer'  => (int) $value,
            'float', 'double' => (float) $value,
            'json'            => json_decode((string) $value, true),
            default           => $value,
        };
    }
}
