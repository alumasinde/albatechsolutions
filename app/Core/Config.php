<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Config - loads config/*.php files (env-backed) via dot notation.
 * Site-editable branding/content settings live in the DB `settings`
 * table and are accessed separately via App\Core\Settings, so the
 * admin panel can change them without touching code or these files.
 */
final class Config
{
    private static array $items = [];
    private static bool $loaded = false;

    private static function load(): void
    {
        if (self::$loaded) {
            return;
        }

        $configPath = dirname(__DIR__, 2) . '/config';

        foreach (glob($configPath . '/*.php') as $file) {
            $key = basename($file, '.php');
            self::$items[$key] = require $file;
        }

        self::$loaded = true;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::load();

        $segments = explode('.', $key);
        $value = self::$items;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }
}
