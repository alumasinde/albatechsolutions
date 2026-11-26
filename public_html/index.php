<?php

declare(strict_types=1);

use AlbaTech\Core\AdminSecurity;
use AlbaTech\Core\Application;
use AlbaTech\Core\Env;

require dirname(__DIR__) . '/vendor/autoload.php';

Env::load(dirname(__DIR__) . '/.env');

date_default_timezone_set(Env::get('APP_TIMEZONE', 'Africa/Nairobi'));

// Per-response nonce allows only the known inline scripts rendered by our templates.
$cspNonce = rtrim(strtr(base64_encode(random_bytes(18)), '+/', '-_'), '=');
$GLOBALS['albatech_csp_nonce'] = $cspNonce;

$isHttps = AdminSecurity::isHttps();

// Headers that are safe over both HTTP (local dev) and HTTPS.
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
header('X-Permitted-Cross-Domain-Policies: none');

// HTTPS-only headers. Over plain HTTP, browsers ignore COOP (untrustworthy origin)
// and HSTS is meaningless, so sending them only produces console noise.
if ($isHttps) {
    header('Cross-Origin-Opener-Policy: same-origin');
    header('Cross-Origin-Resource-Policy: same-site');
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

$csp = "default-src 'self'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'; "
    . "object-src 'none'; img-src 'self' data: https:; "
    . "font-src 'self' data: https://fonts.gstatic.com; "
    . "style-src 'self' https://fonts.googleapis.com; "
    . "script-src 'self' 'nonce-{$cspNonce}' https://plausible.io https://*.plausible.io https://www.googletagmanager.com https://www.google-analytics.com; "
    . "connect-src 'self' https://plausible.io https://*.plausible.io https://*.googletagmanager.com https://*.google-analytics.com https://*.analytics.google.com https://*.g.doubleclick.net";

// upgrade-insecure-requests rewrites every request to https://. Sending it on a
// plain-HTTP origin (e.g. http://albatechsolution.test:8000) breaks CSS/JS with
// ERR_CONNECTION_CLOSED, so only send it when the page itself is served over HTTPS.
if ($isHttps) {
    $csp .= '; upgrade-insecure-requests';
}

header('Content-Security-Policy: ' . $csp);

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
if (str_starts_with($requestPath, '/admin')) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
}

if (Env::get('APP_ENV', 'local') === 'production') {
    if (Env::bool('APP_DEBUG', false)) {
        error_log('[AlbaTech] APP_DEBUG must be false in production.');
        http_response_code(503);
        exit('Service temporarily unavailable.');
    }
    if (strtolower((string) parse_url((string) Env::get('APP_URL', ''), PHP_URL_SCHEME)) !== 'https') {
        error_log('[AlbaTech] APP_URL must use HTTPS in production.');
        http_response_code(503);
        exit('Production configuration is incomplete.');
    }
    if (strtolower((string) Env::get('DB_USERNAME', 'root')) === 'root') {
        error_log('[AlbaTech] Production must use a least-privilege database account, not root.');
        http_response_code(503);
        exit('Production configuration is incomplete.');
    }
    if (!Env::bool('SESSION_SECURE_COOKIE', false)) {
        error_log('[AlbaTech] SESSION_SECURE_COOKIE must be true in production.');
        http_response_code(503);
        exit('Production configuration is incomplete.');
    }
}

$app = new Application();
require dirname(__DIR__) . '/routes/web.php';

$app->run();