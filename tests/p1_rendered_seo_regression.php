<?php

declare(strict_types=1);

use AlbaTech\Controllers\GuideController;
use AlbaTech\Core\SiteUrl;
use AlbaTech\Core\View;

require dirname(__DIR__) . '/app/Core/Env.php';
require dirname(__DIR__) . '/app/Core/Database.php';
AlbaTech\Core\Env::load(dirname(__DIR__) . '/.env');
require dirname(__DIR__) . '/app/Core/Response.php';
require dirname(__DIR__) . '/app/Core/View.php';
require dirname(__DIR__) . '/app/Core/SiteUrl.php';
require dirname(__DIR__) . '/app/Core/SeoDefaults.php';
require dirname(__DIR__) . '/app/Core/WhatsAppLink.php';
require dirname(__DIR__) . '/app/Repositories/SiteSettingsRepository.php';
require dirname(__DIR__) . '/app/Repositories/CmsRepository.php';
require dirname(__DIR__) . '/app/Repositories/ServiceRepository.php';
require dirname(__DIR__) . '/app/Controllers/GuideController.php';

$checks = 0;
$assert = static function (bool $condition, string $message) use (&$checks): void {
    $checks++;
    if (!$condition) {
        fwrite(STDERR, "FAIL: {$message}\n");
        exit(1);
    }
};

$base = SiteUrl::base();
$articles = GuideController::articlesMeta();
$renderedPages = [];
$renderedPages['/guides'] = View::render('pages/guides', [
    'pageTitle' => 'Practical Business & Website Guides in Kenya | AlbaTech Solutions',
    'metaDescription' => 'Straightforward guides on website costs, domains and hosting, business website preparation, online visibility and business registration assistance in Kenya.',
    'canonicalUrl' => $base . '/guides',
    'guideArticles' => $articles,
]);
foreach ($articles as $slug => $article) {
    $renderedPages['/guides/' . $slug] = View::render('pages/guide-article', [
        'pageTitle' => $article['meta_title'] . ' | AlbaTech Solutions',
        'metaDescription' => $article['meta_description'],
        'canonicalUrl' => $base . '/guides/' . rawurlencode($slug),
        'guideArticle' => $article,
        'guideSlug' => $slug,
    ]);
    $assert(!empty($article['published']), 'Guide has an explicit publication date: ' . $slug);
    $assert(!empty($article['updated']), 'Guide has an explicit modification date: ' . $slug);
    $assert(strtotime((string)$article['published']) !== false, 'Guide publication date is parseable: ' . $slug);
    $assert(strtotime((string)$article['updated']) !== false, 'Guide modification date is parseable: ' . $slug);
}

foreach ($renderedPages as $path => $html) {
    preg_match_all('~<h1\b[^>]*>(.*?)</h1>~is', $html, $h1Matches);
    $assert(count($h1Matches[0]) === 1, 'Exactly one H1 in rendered page ' . $path);
    $assert(trim(strip_tags($h1Matches[1][0] ?? '')) !== '', 'H1 is not empty on ' . $path);
    preg_match_all('~<title\b[^>]*>(.*?)</title>~is', $html, $titleMatches);
    $assert(count($titleMatches[0]) === 1 && trim(strip_tags($titleMatches[1][0] ?? '')) !== '', 'One non-empty title on ' . $path);
    preg_match_all('~<meta\b(?=[^>]*\bname=["\']description["\'])(?=[^>]*\bcontent=["\']([^"\']*)["\'])[^>]*>~is', $html, $descriptionMatches);
    $assert(count($descriptionMatches[0]) === 1 && trim((string)($descriptionMatches[1][0] ?? '')) !== '', 'One non-empty meta description on ' . $path);
    preg_match_all('~<link\b(?=[^>]*\brel=["\']canonical["\'])(?=[^>]*\bhref=["\']([^"\']+)["\'])[^>]*>~is', $html, $canonicalMatches);
    $assert(count($canonicalMatches[0]) === 1, 'Exactly one canonical link on ' . $path);
    $canonical = html_entity_decode((string)($canonicalMatches[1][0] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $assert($canonical === $base . $path, 'Canonical matches intended page URL on ' . $path);
    $assert(!str_contains(strtolower($html), 'noindex'), 'Indexable guide page is not accidentally noindex: ' . $path);

    preg_match_all('~<img\b([^>]*)>~is', $html, $images);
    foreach ($images[1] as $index => $attributes) {
        $assert(preg_match('~\balt\s*=~i', $attributes) === 1, 'Image has an alt attribute on ' . $path . ' image ' . ($index + 1));
    }

    preg_match_all('~\bhref=["\']([^"\']+)["\']~i', $html, $hrefs);
    foreach ($hrefs[1] as $href) {
        $href = html_entity_decode(trim($href), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if ($href === '' || str_starts_with($href, '#') || preg_match('~^(?:https?:|mailto:|tel:|javascript:)~i', $href)) {
            continue;
        }
        $pathOnly = parse_url($href, PHP_URL_PATH) ?: '/';
        $known = $pathOnly === '/' || in_array($pathOnly, ['/services','/work','/products','/solutions','/about','/guides','/get-help','/terms','/privacy','/data-deletion','/sitemap.xml','/robots.txt','/llms.txt'], true)
            || str_starts_with($pathOnly, '/assets/')
            || preg_match('~^/(?:services|work|products|guides)/[a-z0-9]+(?:-[a-z0-9]+)*$~', $pathOnly) === 1;
        $assert($known, 'Internal link uses a known public route pattern: ' . $path . ' -> ' . $href);
    }

    preg_match('~<script\s+type=["\']application/ld\+json["\'][^>]*>(.*?)</script>~is', $html, $schemaMatch);
    $schema = json_decode(trim((string)($schemaMatch[1] ?? '')), true);
    $assert(is_array($schema) && ($schema['@context'] ?? '') === 'https://schema.org', 'Valid JSON-LD context on ' . $path);
    $assert(is_array($schema['@graph'] ?? null), 'JSON-LD graph exists on ' . $path);
    if (str_starts_with($path, '/guides/')) {
        $articleNode = null;
        foreach (($schema['@graph'] ?? []) as $node) {
            if (($node['@type'] ?? '') === 'Article') $articleNode = $node;
        }
        $assert(is_array($articleNode), 'Article schema node exists on ' . $path);
        $assert(($articleNode['datePublished'] ?? '') !== '', 'Article schema has datePublished on ' . $path);
        $assert(($articleNode['dateModified'] ?? '') !== '', 'Article schema has dateModified on ' . $path);
        $assert(($articleNode['datePublished'] ?? null) === ($articles[basename($path)]['published'] ?? null), 'Schema publication date matches content on ' . $path);
        $assert(($articleNode['dateModified'] ?? null) === ($articles[basename($path)]['updated'] ?? null), 'Schema modification date matches content on ' . $path);
    }
}

fwrite(STDOUT, "PASS: {$checks} P1 rendered SEO, content-date, schema, image-alt and internal-link checks.\n");
