<?php

declare(strict_types=1);

namespace AlbaTech\Repositories;

use AlbaTech\Core\Database;
use Throwable;

final class SiteSettingsRepository
{
    /** @var array<string, string>|null Request-level cache: the table is read by controllers, the layout and helpers on every page. */
    private static ?array $publicCache = null;

    public function publicValues(): array
    {
        if (self::$publicCache !== null) {
            return self::$publicCache;
        }

        try {
            $rows = Database::connection()->query('SELECT setting_key, setting_value FROM site_settings WHERE is_public = 1')->fetchAll();
            $values = [];
            foreach ($rows as $row) $values[(string)$row['setting_key']] = (string)($row['setting_value'] ?? '');
            self::$publicCache = $values;
            return $values;
        } catch (Throwable $e) {
            return [];
        }
    }
}
