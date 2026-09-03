<?php

declare(strict_types=1);

namespace App\Modules\Admin\Service;

use App\Core\AuditLog;
use App\Core\BaseService;
use App\Core\Settings;

final class SettingsService extends BaseService
{
    /**
     * Whitelist of settings the admin panel is allowed to write, so
     * an unexpected POST field can never create arbitrary DB rows.
     */
    private const EDITABLE_KEYS = [
        'site_name', 'site_tagline',
        'theme_color_primary', 'theme_color_secondary', 'theme_color_accent', 'theme_color_background',
        'theme_font_family', 'theme_radius',
        'contact_email', 'contact_phone', 'contact_address', 'whatsapp_number', 'bank_account_details',
        'social_facebook', 'social_twitter', 'social_linkedin', 'social_instagram',
        'seo_default_title', 'seo_default_description', 'seo_default_keywords',
        'homepage_eyebrow', 'homepage_hero_title', 'homepage_hero_subtitle',
        'homepage_primary_cta_label', 'homepage_primary_cta_url',
        'homepage_secondary_cta_label', 'homepage_secondary_cta_url',
        'homepage_services_heading', 'homepage_industries_heading', 'homepage_process_heading',
        'analytics_ga4_id', 'analytics_google_site_verification',
        'site_logo_media_id',
    ];

    public function update(array $data): void
    {
        foreach (self::EDITABLE_KEYS as $key) {
            if (array_key_exists($key, $data)) {
                Settings::set($key, (string) $data[$key]);
            }
        }

        AuditLog::record('settings.updated', 'settings', null, ['keys' => array_keys($data)]);
    }
}
