<?php

declare(strict_types=1);

use AlbaTech\Core\Env;
use AlbaTech\Core\SeoDefaults;
use AlbaTech\Core\SiteUrl;
use AlbaTech\Core\WhatsAppLink;

$root = dirname(__DIR__);
require $root . '/app/Core/Env.php';
require $root . '/app/Core/Database.php';
require $root . '/app/Repositories/SiteSettingsRepository.php';
require $root . '/app/Core/SiteUrl.php';
require $root . '/app/Core/SeoDefaults.php';
require $root . '/app/Core/WhatsAppLink.php';

$checks = 0;
$assert = static function (bool $condition, string $label) use (&$checks): void {
    $checks++;
    echo ($condition ? 'PASS: ' : 'FAIL: ') . $label . PHP_EOL;
    if (!$condition) $GLOBALS['seo_phase2_failed'] = true;
};

$assert(SeoDefaults::title('', 'Fallback title') === 'Fallback title', 'SEO title fallback is centralized');
$assert(SeoDefaults::title('  Custom <b>title</b>  ', 'Fallback') === 'Custom title', 'SEO titles are trimmed and stripped of tags');
$assert(strlen(SeoDefaults::description(str_repeat('a', 400))) === 320, 'Meta descriptions are capped at 320 characters');
$assert(SeoDefaults::image('/uploads/social.jpg', 'https://example.test') === 'https://example.test/uploads/social.jpg', 'Relative social images become absolute canonical-origin URLs');
$assert(SeoDefaults::image('//evil.test/image.png', 'https://example.test') === 'https://example.test/assets/images/albatech-social-card.png', 'Protocol-relative social image URLs are rejected');
$assert(WhatsAppLink::build(['whatsapp_number' => '254712345678', 'site_name' => 'AlbaTech', 'whatsapp_prefill_message' => 'Hello there']) === 'https://wa.me/254712345678?text=Hello%20there', 'WhatsApp builder uses the configured number and message');
$assert(WhatsAppLink::build(['whatsapp_number' => '0712345678']) === '/get-help', 'Invalid local-format WhatsApp number falls back safely');

putenv('APP_ENV=local');
putenv('APP_URL=https://phase2-local.test');
$assert(SiteUrl::base() === 'https://phase2-local.test', 'Local APP_URL wins over production site_url for canonical tests');

$layout = file_get_contents($root . '/resources/views/layouts/main.php') ?: '';
$seoController = file_get_contents($root . '/app/Controllers/SeoController.php') ?: '';
$workController = file_get_contents($root . '/app/Controllers/WorkController.php') ?: '';
$staticController = file_get_contents($root . '/app/Controllers/StaticPageController.php') ?: '';
$assert(str_contains($layout, 'SeoDefaults::description') && str_contains($layout, 'SeoDefaults::image'), 'Shared layout uses centralized SEO fallback policy');
$assert(str_contains($seoController, "WHERE status='published'") && str_contains($seoController, "FROM cms_pages WHERE status='published'"), 'Sitemap and llms output query published CMS pages only');
$assert(str_contains($seoController, "'about' => '/about'") && str_contains($seoController, "'privacy' => '/privacy'") && !str_contains($seoController, "'contact-details' => '/contact-details'"), 'Sitemap only emits CMS pages selected for indexing');
$assert(str_contains($workController, 'WhatsAppLink::build'), 'Portfolio uses the shared WhatsApp URL builder');
$assert(str_contains($staticController, 'SeoDefaults::title') && str_contains($staticController, 'SeoDefaults::image'), 'CMS pages use centralized SEO defaults');
$router = file_get_contents($root . '/app/Core/Router.php') ?: '';
$assert(str_contains($router, 'Response::redirect($targetPath') && str_contains($router, 'str_ends_with($targetPath, \'/\')') && str_contains($router, 'Response::redirect($target .'), 'Case and trailing-slash redirects remain permanent 301s');
$assert(str_contains($router, 'avoid redirect chains'), 'Case plus trailing slash normalize in one redirect hop');
$assert(is_file($root . '/database/migrations/010_seo_defaults.sql'), 'SEO defaults migration exists');

if (!empty($GLOBALS['seo_phase2_failed'])) exit(1);
echo "PASS: {$checks} Phase 2 SEO regression checks." . PHP_EOL;
