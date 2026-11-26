<?php

declare(strict_types=1);

/**
 * Router for the PHP built-in dev server. Apache already maps real files first
 * via .htaccess, so this file exists only for `php -S` usage:
 *
 *   php -S 127.0.0.1:8080 -t public_html dev-router.php
 *
 * Existing files under public_html/ are served directly; everything else goes
 * through the front controller.
 */

$path = __DIR__ . '/public' . (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

if (is_file($path)) {
    return false;
}

require __DIR__ . '/public_html/index.php';
