<?php

declare(strict_types=1);

namespace App\Core;

final class SecurityHeaders
{
    /**
     * Return all security headers for the current request.
     *
     * @return array<string, string>
     */
    public static function all(): array
    {
        $headers = [
            'X-Content-Type-Options' => 'nosniff',

            /*
             * Prevent the site from being embedded in an iframe
             * by another origin.
             */
            'X-Frame-Options' => 'SAMEORIGIN',

            /*
             * Prevent browsers from sending the full URL as the
             * Referer when navigating from HTTPS to less secure
             * destinations.
             */
            'Referrer-Policy' => 'strict-origin-when-cross-origin',

            /*
             * X-XSS-Protection is obsolete in modern browsers.
             * Explicitly disabling it is preferable to relying
             * on legacy browser behaviour.
             */
            'X-XSS-Protection' => '0',

            /*
             * Prevent Flash/PDF-style cross-domain policy files.
             */
            'X-Permitted-Cross-Domain-Policies' => 'none',

            // Modern cross-origin isolation defaults for a normal business site.
            'Cross-Origin-Opener-Policy' => 'same-origin',
            'Cross-Origin-Resource-Policy' => 'same-origin',
            'Origin-Agent-Cluster' => '?1',
            'X-DNS-Prefetch-Control' => 'off',

            /*
             * AlbaTech currently doesn't require these browser APIs.
             */
            'Permissions-Policy' => implode(', ', [
                'camera=()',
                'microphone=()',
                'geolocation=()',
                'payment=(self)',
                'usb=()',
                'magnetometer=()',
                'gyroscope=()',
                'accelerometer=()',
            ]),

            /*
             * Content Security Policy.
             */
            'Content-Security-Policy' => self::contentSecurityPolicy(),
        ];

        /*
         * HSTS is only added when the current request is HTTPS.
         *
         * This prevents local HTTP development from accidentally
         * receiving an HSTS policy.
         */
        if (self::isHttps()) {
            $headers['Strict-Transport-Security'] =
                'max-age=31536000; includeSubDomains';
            $headers['Content-Security-Policy'] .= '; upgrade-insecure-requests';
        }

        return $headers;
    }

    /**
     * Build AlbaTech's Content Security Policy.
     *
     * @return string
     */
    public static function contentSecurityPolicy(): string
    {
        $directives = [
            /*
             * Default fallback.
             */
            'default-src' => [
                "'self'",
            ],

            /*
             * Prevent <base> from changing relative URL resolution.
             */
            'base-uri' => [
                "'self'",
            ],

            /*
             * Disable object/embed/applet content.
             */
            'object-src' => [
                "'none'",
            ],

            /*
             * Only this site can frame itself.
             */
            'frame-ancestors' => [
                "'self'",
            ],

            /*
             * Forms may only submit to this origin.
             */
            'form-action' => [
                "'self'",
            ],

            /*
             * Images can come from:
             *
             * - AlbaTech itself
             * - data URLs
             * - blob URLs
             * - HTTPS sources
             */
            'img-src' => [
                "'self'",
                'data:',
                'blob:',
                'https:',
            ],

            /*
             * JavaScript.
             *
             * No unsafe-inline here.
             *
             * This is important because we don't want to solve
             * JavaScript CSP problems by simply weakening the policy.
             */
            'script-src' => [
                "'self'",
                'https://cdnjs.cloudflare.com',
                'https://www.googletagmanager.com',
                'https://www.google-analytics.com',
                'https://js.paystack.co',
            ],

            /*
             * Your current theme tokens are rendered as an inline
             * <style> block from the database.
             *
             * Until that is moved to a nonce/hash-based system,
             * unsafe-inline is required for that specific use.
             */
            'style-src' => [
                "'self'",
                "'unsafe-inline'",
                'https://cdnjs.cloudflare.com',
            ],

            /*
             * Font Awesome currently loads its font files from
             * Cloudflare CDN.
             */
            'font-src' => [
                "'self'",
                'https://cdnjs.cloudflare.com',
                'data:',
            ],

            /*
             * Paystack checkout and any other application frames
             * explicitly approved by AlbaTech.
             */
            'frame-src' => [
                "'self'",
                'https://checkout.paystack.com',
                'https://standard.paystack.co',
            ],

            /*
             * AJAX/fetch/XHR/WebSocket connections.
             */
            'connect-src' => [
                "'self'",
                'https://www.google-analytics.com',
                'https://analytics.google.com',
                'https://api.paystack.co',
                'https://checkout.paystack.com',
            ],

            /*
             * Restrict audio/video to AlbaTech or HTTPS sources.
             */
            'media-src' => [
                "'self'",
                'https:',
            ],

            /*
             * Workers used by browser-side functionality.
             */
            'worker-src' => [
                "'self'",
                'blob:',
            ],

            /*
             * Browser manifest.
             */
            'manifest-src' => [
                "'self'",
            ],

            /*
             * Prevent the browser from sending CSP violation reports
             * or other resource requests to arbitrary origins.
             */
            'child-src' => [
                "'self'",
                'blob:',
            ],
        ];

        return self::buildPolicy($directives);
    }

    /**
     * Convert directive arrays into a valid CSP header.
     *
     * @param array<string, array<int, string>> $directives
     */
    private static function buildPolicy(array $directives): string
    {
        $parts = [];

        foreach ($directives as $directive => $sources) {
            $parts[] = $directive . ' ' . implode(' ', $sources);
        }

        return implode('; ', $parts);
    }

    /**
     * Detect HTTPS safely, including reverse proxies.
     */
    private static function isHttps(): bool
    {
        /*
         * Direct Apache/PHP HTTPS.
         */
        if (
            isset($_SERVER['HTTPS'])
            && strtolower((string) $_SERVER['HTTPS']) !== ''
            && strtolower((string) $_SERVER['HTTPS']) !== 'off'
            && (string) $_SERVER['HTTPS'] !== '0'
        ) {
            return true;
        }

        /*
         * Reverse proxy / Cloudflare / load balancer.
         *
         * X-Forwarded-Proto can technically contain:
         *
         * https,http
         *
         * so we inspect the first value.
         */
        $forwardedProto = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '';

        if ($forwardedProto !== '') {
            $protocol = strtolower(
                trim(
                    explode(',', (string) $forwardedProto)[0]
                )
            );

            if ($protocol === 'https') {
                return true;
            }
        }

        /*
         * Cloudflare sends CF-Visitor as JSON:
         *
         * {"scheme":"https"}
         */
        $cfVisitor = $_SERVER['HTTP_CF_VISITOR'] ?? '';

        if ($cfVisitor !== '') {
            $decoded = json_decode(
                (string) $cfVisitor,
                true
            );

            if (
                is_array($decoded)
                && ($decoded['scheme'] ?? null) === 'https'
            ) {
                return true;
            }
        }

        /*
         * RFC 7239 Forwarded header.
         *
         * Example:
         * Forwarded: proto=https;host=example.com
         */
        $forwarded = $_SERVER['HTTP_FORWARDED'] ?? '';

        if ($forwarded !== '') {
            if (
                preg_match(
                    '/(?:^|[;,\\s])proto=https(?:[;,\\s]|$)/i',
                    (string) $forwarded
                ) === 1
            ) {
                return true;
            }
        }

        return false;
    }
}