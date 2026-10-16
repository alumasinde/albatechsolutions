<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$checks = 0;
$failures = [];
$assert = static function (bool $condition, string $message) use (&$checks, &$failures): void {
    $checks++;
    if (!$condition) $failures[] = $message;
};

$htaccess = (string) file_get_contents($root . '/public/.htaccess');
$layout = (string) file_get_contents($root . '/resources/views/layouts/main.php');
$home = (string) file_get_contents($root . '/resources/views/pages/home.php');
$view = (string) file_get_contents($root . '/app/Core/View.php');
$workCss = (string) file_get_contents($root . '/public/assets/css/work.css');
$appCss = (string) file_get_contents($root . '/public/assets/css/app.css');

$assert(str_contains($view, 'filemtime($file)'), 'Asset URLs must be versioned when files change.');
$assert(str_contains($htaccess, 'max-age=31536000, immutable'), 'Versioned static assets should use long-lived immutable caching.');
$assert(str_contains($htaccess, 'ExpiresByType image/webp'), 'WebP assets should have an expiry policy.');
$assert(str_contains($htaccess, 'ExpiresByType image/avif'), 'AVIF assets should have an expiry policy.');
$assert(str_contains($htaccess, 'mod_deflate.c'), 'Compression should remain configured.');
$assert(!str_contains($layout, 'rel="preload" as="image"'), 'CMS-configurable hero should not preload a hardcoded fallback image URL.');
$home = file_get_contents(__DIR__ . '/../resources/views/pages/home.php');
$assert(str_contains($home, 'fetchpriority="high"'), 'Hero image should carry high fetch priority on the image element.');
$assert(str_contains($home, 'loading="eager" fetchpriority="high"'), 'Homepage hero image should load eagerly with high priority.');
$assert(str_contains($home, 'width="1200" height="630"'), 'Homepage hero image should declare dimensions to reduce layout shift.');
$assert(str_contains($workCss, 'aspect-ratio:16/10'), 'Portfolio card images should reserve an aspect-ratio box.');
$assert(str_contains($appCss, 'prefers-reduced-motion'), 'Motion-sensitive behaviour should be covered in the public stylesheet.');
$assert(str_contains($layout, "View::asset('css/bundle.css')"), 'Public pages should load the single versioned CSS bundle.');
$assert(filesize($root . '/public/assets/css/bundle.css') > 0, 'bundle.css must be built and non-empty.');

if ($failures) {
    fwrite(STDERR, "P2 performance regression: FAIL\n");
    foreach ($failures as $failure) fwrite(STDERR, " - {$failure}\n");
    exit(1);
}
printf("P2 performance regression: PASS (%d assertions)\n", $checks);
