<?php

use AlbaTech\Core\Env;
use AlbaTech\Core\View;
use AlbaTech\Repositories\SiteSettingsRepository;

$siteSettings = (new SiteSettingsRepository())->publicValues();

$siteName = (string) (
    $siteSettings['site_name']
    ?? $appName
    ?? Env::get('APP_NAME', 'AlbaTech Solutions')
);

$siteName = trim($siteName) !== ''
    ? trim($siteName)
    : 'AlbaTech Solutions';

$pageTitle = trim((string) ($pageTitle ?? $siteName));

if ($pageTitle === '') {
    $pageTitle = $siteName;
}

$defaultDescription = (string) (
    $siteSettings['default_meta_description']
    ?? 'Practical IT support, web design and digital solutions for individuals and businesses in Kenya.'
);

// Support both the direct variable and the optional $meta array.
$meta = isset($meta) && is_array($meta) ? $meta : [];

$metaDescription = $metaDescription
    ?? $meta['meta_description']
    ?? $defaultDescription;

$metaDescription = trim((string) $metaDescription);

if ($metaDescription === '') {
    $metaDescription = $defaultDescription;
}

$siteUrl = rtrim(
    trim((string) (
        $siteSettings['site_url']
        ?? Env::get('APP_URL', 'http://albatechsolution.test:8000')
    )),
    '/'
);

if ($siteUrl === '') {
    $siteUrl = 'http://albatechsolution.test:8000';
}

$canonicalUrl = trim((string) ($canonicalUrl ?? ''));

if ($canonicalUrl === '') {
    $canonicalUrl = $siteUrl . '/';
}

$metaImage = trim((string) ($metaImage ?? ''));
$metaRobots = trim((string) ($metaRobots ?? 'index, follow, max-image-preview:large'));

$author = trim((string) (
    $author
    ?? $siteSettings['default_author']
    ?? 'Albert Masinde'
));

$serviceData = isset($service) && is_array($service)
    ? $service
    : null;

$structuredData = [
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => $siteName,
    'url' => $siteUrl . '/',
    'description' => $metaDescription,
    'areaServed' => [
        '@type' => 'Country',
        'name' => 'Kenya',
    ],
];

if ($serviceData !== null) {
    $serviceName = trim((string) ($serviceData['name'] ?? $siteName));

    $serviceDescription = trim((string) (
        $serviceData['meta_description']
        ?? $serviceData['short_description']
        ?? $metaDescription
    ));

    if ($serviceDescription === '') {
        $serviceDescription = $metaDescription;
    }

    $structuredData = [
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => $serviceName !== '' ? $serviceName : $siteName,
        'description' => $serviceDescription,
        'url' => $canonicalUrl,
        'provider' => [
            '@type' => 'Organization',
            'name' => $siteName,
            'url' => $siteUrl . '/',
        ],
        'areaServed' => [
            '@type' => 'Country',
            'name' => 'Kenya',
        ],
    ];
}

$whatsAppRaw = (string) (
    $siteSettings['whatsapp_number']
    ?? Env::get('WHATSAPP_NUMBER', '')
);

$whatsAppNumber = preg_replace('/\D+/', '', $whatsAppRaw) ?? '';

$whatsAppUrl = $whatsAppNumber !== ''
    ? 'https://wa.me/' . $whatsAppNumber
        . '?text=' . rawurlencode(
            'Hi ' . $siteName . ', I would like to make an enquiry.'
        )
    : '/get-help';

?>
<!doctype html>
<html lang="en-KE">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= View::escape($pageTitle) ?></title>

    <meta
        name="description"
        content="<?= View::escape($metaDescription) ?>"
    >

    <?php if ($author !== ''): ?>
        <meta name="author" content="<?= View::escape($author) ?>">
    <?php endif; ?>

    <meta name="robots" content="<?= View::escape($metaRobots) ?>">
    <meta name="color-scheme" content="light">
    <meta name="theme-color" content="#0F4C81">

    <link rel="canonical" href="<?= View::escape($canonicalUrl) ?>">
    <link rel="sitemap" type="application/xml" href="/sitemap.xml">
    <link
        rel="alternate"
        type="text/plain"
        href="/llms.txt"
        title="AI and automated-system site guide"
    >

    <meta property="og:type" content="<?= $serviceData !== null ? 'website' : 'website' ?>">
    <meta property="og:site_name" content="<?= View::escape($siteName) ?>">
    <meta property="og:title" content="<?= View::escape($pageTitle) ?>">
    <meta property="og:description" content="<?= View::escape($metaDescription) ?>">
    <meta property="og:url" content="<?= View::escape($canonicalUrl) ?>">

    <?php if ($metaImage !== ''): ?>
        <meta property="og:image" content="<?= View::escape($metaImage) ?>">
    <?php endif; ?>

    <meta name="twitter:card" content="<?= $metaImage !== '' ? 'summary_large_image' : 'summary' ?>">
    <meta name="twitter:title" content="<?= View::escape($pageTitle) ?>">
    <meta name="twitter:description" content="<?= View::escape($metaDescription) ?>">

    <?php if ($metaImage !== ''): ?>
        <meta name="twitter:image" content="<?= View::escape($metaImage) ?>">
    <?php endif; ?>

    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    >

    <link rel="stylesheet" href="/assets/css/app.css">

    <script type="application/ld+json" nonce="<?= View::escape($cspNonce ?? '') ?>"><?=
        json_encode(
            $structuredData,
            JSON_UNESCAPED_SLASHES
            | JSON_UNESCAPED_UNICODE
            | JSON_HEX_TAG
            | JSON_HEX_APOS
            | JSON_HEX_AMP
            | JSON_HEX_QUOT
        )
    ?></script>
</head>

<body>
<a class="skip-link" href="#main-content">Skip to content</a>

<header class="site-header">
    <div class="container header-inner">
        <a
            class="brand"
            href="/"
            aria-label="<?= View::escape($siteName) ?> home"
        >
            AlbaTech <span>Solutions</span>
        </a>

        <nav class="site-nav" aria-label="Main navigation">
            <a href="/">Home</a>
            <a href="/services">Services</a>
            <a href="/guides">Guides</a>
            <a href="/about">About</a>
            <a href="/get-help">Contact</a>
        </nav>
    </div>
</header>

<main id="main-content">
    <?= $content ?? '' ?>
</main>

<footer class="site-footer">
    <div class="container footer-inner">
        <div>
            <a class="brand" href="/">
                AlbaTech <span>Solutions</span>
            </a>

            <p>
                Practical digital solutions for people and businesses in Kenya.
            </p>
        </div>

        <div class="footer-links">
            <a href="/services">Services</a>
            <a href="/about">About</a>
            <a href="/get-help">Contact</a>
            <a href="/guides">Guides</a>
        </div>

        <p>
            &copy; <?= date('Y') ?>
            <?= View::escape($siteName) ?>. All rights reserved.
        </p>
    </div>
</footer>
</body>
</html>