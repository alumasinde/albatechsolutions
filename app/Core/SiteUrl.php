<?php

declare(strict_types=1);

namespace AlbaTech\Core;

use AlbaTech\Repositories\SiteSettingsRepository;
use Throwable;

final class SiteUrl
{
    private static ?string $cached = null;

    public static function base(): string
    {
        if (self::$cached !== null) {
            return self::$cached;
        }

        $settings = [];
        try {
            $settings = (new SiteSettingsRepository())->publicValues();
        } catch (Throwable) {
            // APP_URL remains the fallback during first setup or a database outage.
        }

        // In local/staging environments APP_URL must win so canonical URLs do not
        // accidentally point crawlers and test assertions at the production domain.
        $appUrl = trim((string) Env::get('APP_URL', ''));
        $environment = strtolower((string) Env::get('APP_ENV', 'local'));
        $configured = ($environment !== 'production' && $appUrl !== '')
            ? $appUrl
            : trim((string) ($settings['site_url'] ?? ($appUrl !== '' ? $appUrl : 'https://albatechsolutions.co.ke')));
        $parts = parse_url($configured);

        if (!is_array($parts)
            || !in_array(strtolower((string) ($parts['scheme'] ?? '')), ['http', 'https'], true)
            || empty($parts['host'])
            || isset($parts['user'])
            || isset($parts['pass'])
        ) {
            $configured = trim((string) Env::get('APP_URL', 'https://albatechsolutions.co.ke'));
            $parts = parse_url($configured);
        }

        if (!is_array($parts)
            || !in_array(strtolower((string) ($parts['scheme'] ?? '')), ['http', 'https'], true)
            || empty($parts['host'])
        ) {
            return self::$cached = 'https://albatechsolutions.co.ke';
        }

        // Canonical URLs must point to the site's origin, not a URL with query/fragment.
        $scheme = strtolower((string) $parts['scheme']);
        $host = strtolower((string) $parts['host']);
        $port = isset($parts['port']) ? ':' . (int) $parts['port'] : '';
        return self::$cached = $scheme . '://' . $host . $port;
    }
}
