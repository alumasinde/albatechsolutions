<?php

declare(strict_types=1);

use App\Core\Config;
use App\Core\Helpers\Csrf;
use App\Core\Helpers\Sanitizer;
use App\Core\Settings;

if (!function_exists('e')) {
    /**
     * Escape a value for HTML output. Use around every dynamic value
     * printed in a view to prevent XSS.
     */
    function e(mixed $value): string
    {
        return Sanitizer::escape($value);
    }
}

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        return Config::get($key, $default);
    }
}

if (!function_exists('setting')) {
    /**
     * Admin-configurable value (branding, contact info, feature
     * flags, theme colours) — pulled from the DB `settings` table.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return Settings::get($key, $default);
    }
}


if (!function_exists('whatsapp_url')) {
    /**
     * Build a WhatsApp click-to-chat URL from the admin-configured number.
     * Optional message text is URL-encoded so CTAs can carry context.
     */
    function whatsapp_url(string $message = ''): string
    {
        $number = preg_replace('/\D+/', '', (string) setting('whatsapp_number', ''));

        if ($number === '') {
            return '#';
        }

        $url = 'https://wa.me/' . $number;

        return $message !== '' ? $url . '?text=' . rawurlencode($message) : $url;
    }
}


if (!function_exists('whatsapp_message')) {
    /** Build a concise, context-aware WhatsApp message for a CTA. */
    function whatsapp_message(string $intent, string $context = '', string $note = ''): string
    {
        $intent = trim(preg_replace('/\s+/', ' ', $intent));
        $context = trim(preg_replace('/\s+/', ' ', $context));
        $note = trim($note);
        $message = 'Hi ' . setting('site_name', 'AlbaTech Solutions') . ', ' . ($intent !== '' ? $intent : 'I would like to discuss a project.');
        if ($context !== '') {
            $message .= "\n\nContext: " . $context;
        }
        if ($note !== '') {
            $message .= "\n\nWhat I need: " . $note;
        }
        return mb_substr($message, 0, 1800);
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return Csrf::field();
    }
}

if (!function_exists('asset')) {
    /**
     * Build a same-origin asset URL with automatic cache busting.
     *
     * Assets are deliberately root-relative instead of using APP_URL.
     * This prevents www/non-www mismatches and keeps CSP 'self'
     * working correctly.
     *
     * Example:
     *
     * /assets/css/v2/production.css?v=1787398338
     */
    function asset(string $path): string
    {
        $relativePath = ltrim($path, '/');

        $assetPath = 'assets/' . $relativePath;
        $url = '/' . $assetPath;

        if (defined('PUBLIC_PATH')) {
            $diskPath = PUBLIC_PATH . '/' . $assetPath;

            if (is_file($diskPath)) {
                $version = (int) filemtime($diskPath);

                /*
                 * If the entry stylesheet imports component styles,
                 * use the newest component modification time so a
                 * changed component invalidates the cached entry file.
                 */
                if (
                    in_array(
                        $relativePath,
                        [
                            'css/app.css',
                            'css/production.css',
                            'css/v2/app.css',
                            'css/v2/production.css',
                        ],
                        true
                    )
                ) {
                    $componentDirectories = [
                        PUBLIC_PATH . '/assets/css/components',
                        PUBLIC_PATH . '/assets/css/v2/components',
                    ];

                    foreach ($componentDirectories as $componentsDir) {
                        if (!is_dir($componentsDir)) {
                            continue;
                        }

                        foreach (
                            glob($componentsDir . '/*.css') ?: []
                            as $componentFile
                        ) {
                            if (is_file($componentFile)) {
                                $version = max(
                                    $version,
                                    (int) filemtime($componentFile)
                                );
                            }
                        }
                    }
                }

                $url .= '?v=' . $version;
            }
        }

        return $url;
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        return rtrim(config('app.url'), '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('old')) {
    /**
     * Repopulate a form field after a failed validation redirect.
     */
    function old(string $key, mixed $default = ''): mixed
    {
        return \App\Core\Session::getFlash('_old')[$key] ?? $default;
    }
}

if (!function_exists('flash_errors')) {
    function flash_errors(): array
    {
        return \App\Core\Session::getFlash('_errors', []);
    }
}
