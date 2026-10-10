<?php

declare(strict_types=1);

use AlbaTech\Controllers\GuideController;
use AlbaTech\Controllers\SeoController;
use AlbaTech\Core\Database;
use AlbaTech\Core\Env;
use AlbaTech\Core\Response;
use AlbaTech\Core\SiteUrl;
use AlbaTech\Core\View;
use AlbaTech\Repositories\ServiceRepository;
use AlbaTech\Repositories\SiteSettingsRepository;

require dirname(__DIR__) . '/app/Core/Env.php';
require dirname(__DIR__) . '/app/Core/Database.php';
require dirname(__DIR__) . '/app/Core/Response.php';
require dirname(__DIR__) . '/app/Core/View.php';
require dirname(__DIR__) . '/app/Core/SiteUrl.php';
require dirname(__DIR__) . '/app/Repositories/SiteSettingsRepository.php';
require dirname(__DIR__) . '/app/Repositories/ServiceRepository.php';
require dirname(__DIR__) . '/app/Controllers/GuideController.php';
require dirname(__DIR__) . '/app/Controllers/SeoController.php';

$checks = 0;
$assert = static function (bool $condition, string $message) use (&$checks): void {
    $checks++;
    if (!$condition) {
        fwrite(STDERR, "FAIL: {$message}\n");
        exit(1);
    }
};

$articles = GuideController::articles();
$assert(count($articles) >= 5, 'Guides catalogue has at least five useful articles');
$assert(!str_contains(file_get_contents(dirname(__DIR__) . '/resources/views/pages/guides.php') ?: '', 'coming soon'), 'Guides page no longer uses a coming-soon notice');
$assert(isset($articles['how-much-does-a-website-cost-in-kenya']), 'Website cost guide has a stable slug');
$assert(isset($articles['domain-and-hosting-explained']), 'Domain and hosting guide is present');
$assert(isset($articles['business-registration-assistance-what-to-prepare']), 'Business preparation guide is present');
foreach ($articles as $slug => $article) {
    $assert(preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug) === 1, 'Guide slug is URL safe: ' . $slug);
    $assert(trim((string) ($article['meta_title'] ?? '')) !== '', 'Guide has SEO title: ' . $slug);
    $assert(trim((string) ($article['meta_description'] ?? '')) !== '', 'Guide has meta description: ' . $slug);
}

$baseUrl = SiteUrl::base();
$assert(str_starts_with($baseUrl, 'https://') || str_starts_with($baseUrl, 'http://'), 'Canonical base URL has a valid HTTP scheme');
$article = $articles['how-much-does-a-website-cost-in-kenya'];
$html = View::render('pages/guide-article', [
    'pageTitle' => $article['meta_title'] . ' | AlbaTech Solutions',
    'metaDescription' => $article['meta_description'],
    'canonicalUrl' => $baseUrl . '/guides/how-much-does-a-website-cost-in-kenya',
    'metaImage' => $baseUrl . '/assets/images/albatech-social-card.png',
    'guideArticle' => $article,
    'guideSlug' => 'how-much-does-a-website-cost-in-kenya',
]);
$assert(str_contains($html, '<link rel="canonical" href="' . $baseUrl . '/guides/how-much-does-a-website-cost-in-kenya">'), 'Article has an absolute canonical URL');
$assert(str_contains($html, 'property="og:image"'), 'Social sharing image metadata is present');
$assert(str_contains($html, 'name="twitter:card" content="summary_large_image"'), 'Twitter card metadata is present');
$assert(str_contains($html, '"@type":"Article"'), 'Article JSON-LD schema is present');
if (preg_match('~<script type="application/ld\+json"[^>]*>(.*?)</script>~s', $html, $jsonMatch) === 1) {
    $schema = json_decode(trim($jsonMatch[1]), true);
    $assert(is_array($schema) && ($schema['@context'] ?? '') === 'https://schema.org', 'JSON-LD is valid JSON with schema.org context');
    $assert(isset($schema['@graph']) && is_array($schema['@graph']), 'JSON-LD graph contains structured entities');
} else {
    $assert(false, 'JSON-LD script block exists');
}
$assert(is_file(dirname(__DIR__) . '/public/assets/images/albatech-social-card.png'), 'Social preview image asset exists');
$assert(is_file(dirname(__DIR__) . '/public/assets/images/albatech-logo.png'), 'Structured-data logo asset exists');
foreach (['pages/terms', 'pages/privacy', 'pages/contact-details'] as $view) {
    $legalHtml = View::render($view, ['pageTitle' => 'AlbaTech Solutions | ' . basename($view), 'metaDescription' => 'Public information for AlbaTech Solutions.', 'canonicalUrl' => $baseUrl . '/' . basename($view)]);
    $assert(str_contains($legalHtml, '<link rel="canonical"'), 'Legal/contact page renders canonical metadata: ' . $view);
}

http_response_code(200);
ob_start();
(new SeoController())->sitemap();
$sitemap = (string) ob_get_clean();
$sitemapStatus = http_response_code();
if ($sitemapStatus === 503) {
    $assert(!str_contains($sitemap, '<urlset'), 'Sitemap does not serve a partial URL set when dynamic sources are unavailable');
} else {
    $assert($sitemapStatus === 200, 'Complete sitemap returns HTTP 200');
    $assert(str_contains($sitemap, '<urlset'), 'Sitemap responds with XML urlset');
    foreach (array_keys($articles) as $slug) {
        $assert(str_contains($sitemap, '/guides/' . $slug), 'Sitemap includes guide URL: ' . $slug);
    }
}

ob_start();
(new SeoController())->robots();
$robots = (string) ob_get_clean();
$assert(str_contains($robots, 'Sitemap: ' . $baseUrl . '/sitemap.xml'), 'Robots file references the canonical sitemap URL');
$assert(str_contains($robots, 'Disallow: /admin'), 'Robots file disallows admin routes');
if ($sitemapStatus !== 503) {
    foreach (['/terms', '/privacy', '/get-help'] as $path) {
        $assert(str_contains($sitemap, $baseUrl . $path), 'Sitemap includes canonical public information page: ' . $path);
    }
    $assert(!str_contains($sitemap, $baseUrl . '/contact-details'), 'Sitemap excludes redirected contact-details URL');
}

fwrite(STDOUT, "PASS: {$checks} Phase 4 SEO and content regression checks.\n");
