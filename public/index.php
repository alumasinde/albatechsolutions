<?php

declare(strict_types=1);

use AlbaTech\Core\Application;
use AlbaTech\Core\Env;

require dirname(__DIR__) . '/vendor/autoload.php';

Env::load(dirname(__DIR__) . '/.env');

date_default_timezone_set(Env::get('APP_TIMEZONE', 'Africa/Nairobi'));

// Baseline browser security headers. HSTS is only emitted on HTTPS requests.
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
header('X-Permitted-Cross-Domain-Policies: none');
header("Content-Security-Policy: default-src 'self'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'; object-src 'none'; img-src 'self' data: https:; font-src 'self' data: https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; script-src 'self' 'unsafe-inline'; connect-src 'self'");
if (\AlbaTech\Core\AdminSecurity::isHttps()) {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}
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
    if (Env::get('DB_USERNAME', 'root') === 'root') {
        error_log('[AlbaTech] Production must use a least-privilege database account, not root.');
        http_response_code(503);
        exit('Production configuration is incomplete.');
    }
}

$app = new Application();
require dirname(__DIR__) . '/routes/web.php';

$app->run();
