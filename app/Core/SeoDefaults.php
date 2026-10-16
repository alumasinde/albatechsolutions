<?php

declare(strict_types=1);

namespace AlbaTech\Core;

/** Central SEO fallback policy shared by page controllers and the HTML layout. */
final class SeoDefaults
{
    public const SOCIAL_IMAGE = '/assets/images/albatech-social-card.png';
    public const DESCRIPTION = 'Website design, business registration, KRA filing help and practical digital services for businesses in Nairobi and across Kenya. Clear pricing, WhatsApp support, no unnecessary complexity.';

    public static function title(string $candidate, string $fallback): string
    {
        $candidate = self::clean($candidate, 255);
        return $candidate !== '' ? $candidate : self::clean($fallback, 255);
    }

    /** Title with the brand suffix, guarding against stored meta_titles that already include it. */
    public static function brandedTitle(string $candidate, string $fallback): string
    {
        $title = self::title($candidate, $fallback);
        if (preg_match('/\bAlbaTech Solutions\s*$/iu', $title) === 1) {
            return $title;
        }
        return self::clean($title . ' | AlbaTech Solutions', 255);
    }

    public static function description(string $candidate, string $fallback = self::DESCRIPTION): string
    {
        $candidate = self::clean($candidate, 320);
        return $candidate !== '' ? $candidate : self::clean($fallback, 320);
    }

    /** Return a safe absolute URL for a same-site path or an HTTPS image URL. */
    public static function image(string $candidate, ?string $baseUrl = null): string
    {
        $baseUrl ??= SiteUrl::base();
        $candidate = trim($candidate);
        if ($candidate === '') {
            try {
                $settings = (new \AlbaTech\Repositories\SiteSettingsRepository())->publicValues();
                $candidate = trim((string) ($settings['default_social_image'] ?? ''));
            } catch (\Throwable) {
                $candidate = '';
            }
        }
        if ($candidate !== '' && preg_match('~^https://[^\s]+$~i', $candidate) === 1) {
            return $candidate;
        }
        // Reject protocol-relative URLs and javascript/data URLs; social images must be HTTPS or local paths.
        if ($candidate !== '' && (str_starts_with($candidate, '//') || preg_match('~^[a-z][a-z0-9+.-]*:~i', $candidate) === 1)) {
            $candidate = '';
        }
        $path = '/' . ltrim($candidate !== '' ? $candidate : self::SOCIAL_IMAGE, '/');
        return rtrim($baseUrl, '/') . $path;
    }

    public static function clean(string $value, int $maxLength): string
    {
        $value = trim(preg_replace('/\s+/u', ' ', strip_tags($value)) ?? strip_tags($value));
        if (function_exists('mb_substr')) {
            return mb_substr($value, 0, $maxLength, 'UTF-8');
        }
        return substr($value, 0, $maxLength);
    }
}
