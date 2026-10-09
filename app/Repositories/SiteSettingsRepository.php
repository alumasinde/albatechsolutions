<?php

declare(strict_types=1);

namespace AlbaTech\Repositories;

use AlbaTech\Core\Database;
use Throwable;

final class SiteSettingsRepository
{
    public function publicValues(): array
    {
        try {
            $rows = Database::connection()->query('SELECT setting_key, setting_value FROM site_settings WHERE is_public = 1')->fetchAll();
            $values = [];
            foreach ($rows as $row) $values[(string)$row['setting_key']] = (string)($row['setting_value'] ?? '');
            return $values;
        } catch (Throwable $e) {
            return [];
        }
    }
}
