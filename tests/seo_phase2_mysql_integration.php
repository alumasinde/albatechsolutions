<?php

declare(strict_types=1);

use AlbaTech\Controllers\SeoController;
use AlbaTech\Controllers\StaticPageController;
use AlbaTech\Core\Database;
use AlbaTech\Core\Env;
use AlbaTech\Core\SiteUrl;

$root = dirname(__DIR__);
if (!is_file($root . '/vendor/autoload.php')) {
    fwrite(STDERR, "FAIL: Run composer install before the MySQL integration test.\n");
    exit(2);
}
require $root . '/vendor/autoload.php';
Env::load($root . '/.env');

try {
    $pdo = Database::connection();
    $pdo->query('SELECT 1')->fetchColumn();
    $cmsCount = (int) $pdo->query("SELECT COUNT(*) FROM cms_pages WHERE status='published'")->fetchColumn();
    $guideCount = (int) $pdo->query("SELECT COUNT(*) FROM guides WHERE status='published'")->fetchColumn();
    $serviceCount = (int) $pdo->query("SELECT COUNT(*) FROM services WHERE status='published'")->fetchColumn();
    $projectCount = (int) $pdo->query("SELECT COUNT(*) FROM work_projects WHERE status='published'")->fetchColumn();
} catch (Throwable $e) {
    fwrite(STDERR, "FAIL: MySQL is not available or migrations/seeds are incomplete: " . $e->getMessage() . "\n");
    exit(2);
}

// Buffer the test runner's PASS/FAIL output until all endpoint assertions finish.
// Otherwise earlier echo statements mark headers as sent, and controller header() calls
// emit warnings into the captured sitemap body, making valid XML appear malformed.
ob_start();

$checks = 0;
$failures = 0;
$assert = static function (bool $condition, string $message) use (&$checks, &$failures): void {
    $checks++;
    echo ($condition ? 'PASS: ' : 'FAIL: ') . $message . PHP_EOL;
    if (!$condition) $failures++;
};

$assert($cmsCount >= 5, 'Seeded database contains the five standard CMS pages');
$assert($guideCount >= 4, 'Seeded database contains the four published guide records');
$assert($serviceCount >= 1, 'Database has at least one published service to exercise dynamic sitemap URLs');
$assert($projectCount >= 0, 'Portfolio query is available');

$base = SiteUrl::base();
ob_start();
(new SeoController())->sitemap();
$sitemap = (string) ob_get_clean();
$sitemapStatus = http_response_code();
$assert($sitemapStatus === 200, 'Sitemap returns HTTP 200 when all dynamic sources are available');
$assert(str_starts_with(trim($sitemap), '<?xml'), 'Sitemap response is XML');
$locations = [];
if (preg_match_all('~<loc>(.*?)</loc>~s', $sitemap, $matches)) {
    $locations = array_map(static fn(string $loc): string => html_entity_decode($loc, ENT_QUOTES | ENT_XML1, 'UTF-8'), $matches[1]);
}
$assert(in_array($base . '/', $locations, true), 'Sitemap contains the canonical homepage URL');
$assert(in_array($base . '/about', $locations, true), 'Sitemap contains a published CMS page');
$assert(in_array($base . '/services', $locations, true), 'Sitemap contains the services index');
$assert(in_array($base . '/guides', $locations, true), 'Sitemap contains the guides index');
$assert(count($locations) === count(array_unique($locations)), 'Sitemap contains no duplicate loc entries');

ob_start();
(new StaticPageController())->about();
$aboutHtml = (string) ob_get_clean();
$assert(str_contains($aboutHtml, '<link rel="canonical" href="' . htmlspecialchars($base . '/about', ENT_QUOTES, 'UTF-8') . '">'), 'Rendered About page canonical matches configured site origin and route');
$assert(preg_match('~<title>\s*[^<]+</title>~i', $aboutHtml) === 1, 'Rendered CMS page has a non-empty title');
$assert(preg_match('~<meta\s+name="description"\s+content="[^"]+"~i', $aboutHtml) === 1, 'Rendered CMS page has a non-empty meta description');
$assert(str_contains($aboutHtml, 'property="og:image"'), 'Rendered CMS page includes social-sharing image metadata');
if (preg_match('~<script\s+type="application/ld\+json"[^>]*>(.*?)</script>~is', $aboutHtml, $schemaMatch) === 1) {
    $schema = json_decode(trim($schemaMatch[1]), true);
    $assert(is_array($schema) && ($schema['@context'] ?? '') === 'https://schema.org' && is_array($schema['@graph'] ?? null), 'Rendered structured data is valid JSON-LD with a graph');
} else {
    $assert(false, 'Rendered page includes a JSON-LD script block');
}

$routerSource = file_get_contents($root . '/app/Core/Router.php') ?: '';
$assert(str_contains($routerSource, 'Response::redirect($targetPath') && str_contains($routerSource, 'Response::redirect($target'), 'Canonical-case and trailing-slash redirects remain enabled');

if ($failures > 0) {
    fwrite(STDERR, "{$failures} of {$checks} MySQL SEO integration checks failed.\n");
    if (ob_get_level() > 0) {
        ob_end_flush();
    }
    exit(1);
}
echo "PASS: {$checks} seeded-MySQL SEO integration checks (CMS {$cmsCount}, guides {$guideCount}, services {$serviceCount}, projects {$projectCount}).\n";
if (ob_get_level() > 0) {
    ob_end_flush();
}
