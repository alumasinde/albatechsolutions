<?php

declare(strict_types=1);

namespace AlbaTech\Repositories;

use AlbaTech\Core\Database;
use Throwable;

/** CMS navigation and CTA rows, cached per request because the layout and controllers both need them. */
final class CmsRepository
{
    /** @var array<string, list<array<string, mixed>>>|null */
    private static ?array $navCache = null;

    /** @var array<string, array<string, mixed>>|null */
    private static ?array $ctaCache = null;

    /** @return array<string, list<array<string, mixed>>> Rows grouped by placement. */
    public function navigationByPlacement(): array
    {
        if (self::$navCache !== null) {
            return self::$navCache;
        }

        $grouped = [];
        try {
            $rows = Database::connection()->query(
                'SELECT label,url,placement,open_new_tab FROM site_navigation WHERE is_enabled=1 ORDER BY sort_order,id'
            )->fetchAll();
            foreach ($rows as $row) $grouped[(string) $row['placement']][] = $row;
        } catch (Throwable $e) {
            error_log('[AlbaTech CMS nav] ' . $e->getMessage());
        }

        return self::$navCache = $grouped;
    }

    /** @return array<string, array<string, mixed>> Rows keyed by cta_key. */
    public function ctas(): array
    {
        if (self::$ctaCache !== null) {
            return self::$ctaCache;
        }

        $out = [];
        try {
            $rows = Database::connection()->query(
                'SELECT cta_key,label,url,placement,style_class FROM site_ctas WHERE is_enabled=1'
            )->fetchAll();
            foreach ($rows as $row) $out[(string) $row['cta_key']] = $row;
        } catch (Throwable $e) {
            error_log('[AlbaTech CMS ctas] ' . $e->getMessage());
        }

        return self::$ctaCache = $out;
    }
}
