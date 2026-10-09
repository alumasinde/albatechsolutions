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
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='8' fill='%23155eef'/%3E%3Ctext x='16' y='23' font-family='Arial' font-size='20' font-weight='700' text-anchor='middle' fill='%23fff'%3EA%3C/text%3E%3C/svg%3E">

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

        <button
            class="mobile-menu-btn"
            type="button"
            aria-label="Toggle navigation menu"
            aria-expanded="false"
            aria-controls="site-nav"
            id="mobile-menu-toggle"
        >
            <i class="fa-solid fa-bars" aria-hidden="true"></i>
        </button>

        <nav class="site-nav" id="site-nav" aria-label="Main navigation">
            <a href="/">Home</a>
            <a href="/services">Services</a>
            <a href="/guides">Guides</a>
            <a href="/about">About</a>
            <a href="/get-help">Contact</a>
        </nav>
    </div>
</header>

<script nonce="<?= View::escape($cspNonce ?? '') ?>">
(function() {
    const menuToggle = document.getElementById('mobile-menu-toggle');
    const siteNav = document.getElementById('site-nav');

    if (!menuToggle || !siteNav) return;

    const menuIcon = menuToggle.querySelector('i') || document.createElement('i');

    menuToggle.addEventListener('click', function() {
        const isOpen = siteNav.classList.toggle('is-open');
        menuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        menuIcon.className = isOpen ? 'fa-solid fa-xmark' : 'fa-solid fa-bars';
    });

    // Close menu when clicking a link
    siteNav.querySelectorAll('a').forEach(function(link) {
        link.addEventListener('click', function() {
            siteNav.classList.remove('is-open');
            menuToggle.setAttribute('aria-expanded', 'false');
            menuIcon.className = 'fa-solid fa-bars';
        });
    });

    // Close menu when clicking outside
    document.addEventListener('click', function(e) {
        if (!siteNav.contains(e.target) && !menuToggle.contains(e.target)) {
            siteNav.classList.remove('is-open');
            menuToggle.setAttribute('aria-expanded', 'false');
            menuIcon.className = 'fa-solid fa-bars';
        }
    });

    // Close menu on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && siteNav.classList.contains('is-open')) {
            siteNav.classList.remove('is-open');
            menuToggle.setAttribute('aria-expanded', 'false');
            menuIcon.className = 'fa-solid fa-bars';
            menuToggle.focus();
        }
    });
})();
</script>

<main id="main-content">
    <?= $content ?? '' ?>
</main>

<?php
$footerEmail = trim((string) ($siteSettings['contact_email'] ?? ''));
$footerPhone = trim((string) ($siteSettings['business_phone'] ?? ''));
$footerPhoneHref = preg_replace('/[^0-9+]/', '', $footerPhone);
$footerAddress = trim((string) ($siteSettings['business_address'] ?? 'Ruaka, Kenya'));
?>
<footer class="site-footer">
    <div class="container">
        <div class="footer-main">
            <div class="footer-brand-block">
                <a class="brand footer-brand" href="/" aria-label="<?= View::escape($siteName) ?> home">
                    <span class="brand-mark" aria-hidden="true">A</span>
                    <span class="brand-wordmark">AlbaTech <span>Solutions</span></span>
                </a>
                <p class="footer-description">Practical digital solutions, reliable IT support and web services for people and businesses in Kenya.</p>
                <a class="footer-contact-cta" href="/get-help">Tell us what you need <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            </div>

            <div class="footer-column">
                <h2>Explore</h2>
                <a href="/services">Our services</a>
                <a href="/about">About AlbaTech</a>
                <a href="/guides">Guides & resources</a>
                <a href="/get-help">Contact us</a>
            </div>

            <div class="footer-column footer-contact-column">
                <h2>Contact & location</h2>
                <?php if ($footerEmail !== '' && filter_var($footerEmail, FILTER_VALIDATE_EMAIL)): ?>
                    <a href="mailto:<?= View::escape($footerEmail) ?>"><i class="fa-regular fa-envelope" aria-hidden="true"></i><span><?= View::escape($footerEmail) ?></span></a>
                <?php endif; ?>
                <?php if ($footerPhone !== '' && $footerPhoneHref !== ''): ?>
                    <a href="tel:<?= View::escape($footerPhoneHref) ?>"><i class="fa-solid fa-phone" aria-hidden="true"></i><span><?= View::escape($footerPhone) ?></span></a>
                <?php endif; ?>
                <p class="footer-location"><i class="fa-solid fa-location-dot" aria-hidden="true"></i><span><?= View::escape($footerAddress !== '' ? $footerAddress : 'Kenya') ?></span></p>
                <a href="/contact-details">Contact details & directions</a>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> <?= View::escape($siteName) ?>. All rights reserved.</p>
            <nav class="footer-legal" aria-label="Legal links">
                <a href="/privacy">Privacy policy</a>
                <a href="/terms">Terms of service</a>
            </nav>
            <p class="footer-independent-note">Independent digital assistance. Not a government agency.</p>
        </div>
    </div>
</footer>
</body>
</html>