<?php

declare(strict_types=1);

namespace AlbaTech\Core;

use AlbaTech\Repositories\SiteSettingsRepository;
use Throwable;

final class SiteUrl
{
    public static function base(): string
    {
        $settings = [];
        try {
            $settings = (new SiteSettingsRepository())->publicValues();
        } catch (Throwable) {
            // APP_URL remains the fallback during first setup or a database outage.
        }

        $configured = trim((string) (
            $settings['site_url']
            ?? Env::get('APP_URL', 'https://albatechsolutions.co.ke')
        ));
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
            return 'https://albatechsolutions.co.ke';
        }

        // Canonical URLs must point to the site's origin, not a URL with query/fragment.
        $scheme = strtolower((string) $parts['scheme']);
        $host = strtolower((string) $parts['host']);
        $port = isset($parts['port']) ? ':' . (int) $parts['port'] : '';
        return $scheme . '://' . $host . $port;
    }
}
