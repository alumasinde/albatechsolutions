<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$checks = 0;
$assert = static function (bool $condition, string $message) use (&$checks): void {
    $checks++;
    if (!$condition) {
        fwrite(STDERR, "FAIL: {$message}\n");
        exit(1);
    }
};
$read = static fn (string $file): string => (string) file_get_contents($root . '/' . $file);

$layout = $read('resources/views/layouts/main.php');
$assert(!str_contains($layout, "if (\$canonicalUrl === '' && \$shouldIndex) \$canonicalUrl = \$siteUrl . '/';"), 'Layout no longer silently canonicalizes every missing canonical to homepage');
$assert(str_contains($layout, "Controllers must provide the page's trusted, absolute canonical URL."), 'Canonical requirement is documented in layout');

$router = $read('app/Core/Router.php');
$assert(str_contains($router, '$this->redirectCanonicalCase($method);'), 'Router normalizes safe uppercase GET paths');
$assert(str_contains($router, 'Response::redirect($lowerPath'), 'Case normalization uses permanent redirect');
$assert(str_contains($router, '$this->redirectTrailingSlash($method);'), 'Router normalizes trailing slash');

$routes = $read('routes/web.php');
$assert(str_contains($routes, "Response::redirect('/get-help', 301)"), 'Contact aliases permanently redirect to /get-help');
$assert(!str_contains($routes, "get('/contact', [new StaticPageController(), 'getHelp'])"), '/contact no longer renders duplicate content');

$sitemap = $read('app/Controllers/SeoController.php');
$assert(!str_contains($sitemap, "\$baseUrl . '/contact-details'"), 'Sitemap omits redirected contact-details URL');
$assert(!str_contains($sitemap, "'/contact-details): Public contact"), 'LLMs listing omits redirected contact-details URL');

$error500 = $read('resources/views/errors/500.php');
$assert(str_contains($error500, 'View::escape($errorMessage'), '500 error output is HTML-escaped');
$error403 = $read('resources/views/errors/403.php');
$assert(!str_contains($error403, 'class="container" class='), '403 template has no duplicate class attributes');
$app = $read('app/Core/Application.php');
$assert(str_contains($app, "Response::html(View::render('errors/500'"), 'Application renders central 500 template');
$assert(str_contains($app, '), 500);'), 'Application sets HTTP 500 status');
$router = $read('app/Core/Router.php');
$assert(str_contains($router, "]), 404);"), 'Router returns HTTP 404 for unknown routes');

fwrite(STDOUT, "PASS: {$checks} P0 hardening regression checks.\n");
