<?php

use AlbaTech\Core\Env;
use AlbaTech\Core\SiteUrl;
use AlbaTech\Core\View;
use AlbaTech\Repositories\SiteSettingsRepository;

$siteSettings = (new SiteSettingsRepository())->publicValues();
$siteName = trim((string) ($siteSettings['site_name'] ?? $appName ?? Env::get('APP_NAME', 'AlbaTech Solutions')));
if ($siteName === '') $siteName = 'AlbaTech Solutions';

$pageTitle = trim((string) ($pageTitle ?? $siteName));
if ($pageTitle === '') $pageTitle = $siteName;

$defaultDescription = trim((string) ($siteSettings['default_meta_description'] ?? ''));
if ($defaultDescription === '') {
    $defaultDescription = 'Website design, online business presence and practical digital assistance for people and businesses in Kenya.';
}
$meta = isset($meta) && is_array($meta) ? $meta : [];
$metaDescription = trim((string) ($metaDescription ?? $meta['meta_description'] ?? $defaultDescription));
if ($metaDescription === '') $metaDescription = $defaultDescription;
// Keep snippets useful; avoid outputting excessively long descriptions by mistake.
if (function_exists('mb_strlen') && mb_strlen($metaDescription, 'UTF-8') > 320) {
    $metaDescription = rtrim(mb_substr($metaDescription, 0, 317, 'UTF-8')) . '...';
} elseif (strlen($metaDescription) > 320) {
    $metaDescription = rtrim(substr($metaDescription, 0, 317)) . '...';
}

$siteUrl = SiteUrl::base();
$canonicalUrl = trim((string) ($canonicalUrl ?? ''));
$shouldIndex = !str_contains(strtolower((string) ($metaRobots ?? 'index, follow')), 'noindex');
if ($canonicalUrl === '' && $shouldIndex) $canonicalUrl = $siteUrl . '/';
$metaImage = trim((string) ($metaImage ?? ($siteUrl . '/assets/images/albatech-social-card.png')));
if ($metaImage !== '' && !preg_match('~^https://~i', $metaImage)) $metaImage = $siteUrl . '/' . ltrim($metaImage, '/');
$metaRobots = trim((string) ($metaRobots ?? 'index, follow, max-image-preview:large'));
$author = trim((string) ($author ?? $siteSettings['default_author'] ?? 'AlbaTech Solutions'));
// Only a controller may declare a service page. Reading plain `$service` leaked loop variables
// from listing views (home, /services) into the layout and emitted a bogus Service node.
$serviceData = isset($schemaService) && is_array($schemaService) ? $schemaService : null;
$guideData = isset($guideArticle) && is_array($guideArticle) ? $guideArticle : null;

$organizationId = $siteUrl . '/#organization';
$websiteId = $siteUrl . '/#website';
$structuredGraph = [[
    '@type' => 'Organization',
    '@id' => $organizationId,
    'name' => $siteName,
    'url' => $siteUrl . '/',
    'description' => $defaultDescription,
    'logo' => ['@type' => 'ImageObject', 'url' => $siteUrl . '/assets/images/albatech-logo.png'],
    'areaServed' => ['@type' => 'Country', 'name' => 'Kenya'],
], [
    '@type' => 'WebSite',
    '@id' => $websiteId,
    'url' => $siteUrl . '/',
    'name' => $siteName,
    'inLanguage' => 'en-KE',
    'publisher' => ['@id' => $organizationId],
]];

// Enrich Organization with real contact data when it is configured (nothing is invented).
$orgAddress = trim((string) ($siteSettings['business_address'] ?? ''));
if ($orgAddress !== '') {
    $structuredGraph[0]['address'] = [
        '@type' => 'PostalAddress',
        'addressLocality' => trim(explode(',', $orgAddress)[0]),
        'addressCountry' => 'KE',
    ];
}
$orgEmail = trim((string) ($siteSettings['contact_email'] ?? ''));
if ($orgEmail !== '' && filter_var($orgEmail, FILTER_VALIDATE_EMAIL)) {
    $structuredGraph[0]['email'] = $orgEmail;
}
$orgPhone = preg_replace('/[^0-9+]/', '', (string) ($siteSettings['business_phone'] ?? '')) ?? '';
if ($orgPhone !== '') {
    $structuredGraph[0]['telephone'] = $orgPhone;
}

if ($guideData !== null && $canonicalUrl !== '') {
    $structuredGraph[] = [
        '@type' => 'Article',
        '@id' => $canonicalUrl . '#article',
        'headline' => (string) ($guideData['title'] ?? $pageTitle),
        'description' => $metaDescription,
        'datePublished' => (string) ($guideData['updated'] ?? date('Y-m-d')),
        'dateModified' => (string) ($guideData['updated'] ?? date('Y-m-d')),
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $canonicalUrl],
        'author' => ['@type' => 'Organization', '@id' => $organizationId],
        'publisher' => ['@id' => $organizationId],
        'image' => [$metaImage],
        'inLanguage' => 'en-KE',
    ];
} elseif ($serviceData !== null && $canonicalUrl !== '') {
    $serviceName = trim((string) ($serviceData['name'] ?? $siteName));
    $serviceDescription = trim((string) ($serviceData['meta_description'] ?? $serviceData['short_description'] ?? $metaDescription));
    $structuredGraph[] = [
        '@type' => 'Service',
        '@id' => $canonicalUrl . '#service',
        'name' => $serviceName !== '' ? $serviceName : $siteName,
        'description' => $serviceDescription !== '' ? $serviceDescription : $metaDescription,
        'url' => $canonicalUrl,
        'provider' => ['@id' => $organizationId],
        'areaServed' => ['@type' => 'Country', 'name' => 'Kenya'],
    ];
} elseif ($canonicalUrl !== '') {
    $structuredGraph[] = [
        '@type' => 'WebPage',
        '@id' => $canonicalUrl . '#webpage',
        'url' => $canonicalUrl,
        'name' => $pageTitle,
        'description' => $metaDescription,
        'isPartOf' => ['@id' => $websiteId],
        'about' => ['@id' => $organizationId],
        'inLanguage' => 'en-KE',
    ];
}

$structuredData = $structuredData ?? ['@context' => 'https://schema.org', '@graph' => $structuredGraph];
$whatsAppRaw = (string) ($siteSettings['whatsapp_number'] ?? '');
$whatsAppNumber = preg_replace('/\D+/', '', $whatsAppRaw) ?? '';
$whatsAppMessage = trim((string) ($siteSettings['whatsapp_prefill_message'] ?? ''));
if ($whatsAppMessage === '') $whatsAppMessage = 'Hi ' . $siteName . ', I would like to make an enquiry. Please guide me on the next steps.';
$whatsAppUrl = strlen($whatsAppNumber) >= 8 && strlen($whatsAppNumber) <= 15 && !str_starts_with($whatsAppNumber, '0')
    ? 'https://wa.me/' . $whatsAppNumber . '?text=' . rawurlencode($whatsAppMessage)
    : '/get-help';
$footerEmail = trim((string) ($siteSettings['contact_email'] ?? ''));
$footerPhone = trim((string) ($siteSettings['business_phone'] ?? ''));
$footerPhoneHref = preg_replace('/[^0-9+]/', '', $footerPhone) ?? '';
$footerLocation = trim((string) ($siteSettings['business_address'] ?? 'Nairobi, Kenya'));
if ($footerLocation === '') $footerLocation = 'Kenya';

?>
<!doctype html>
<html lang="en-KE">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

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
    <meta name="theme-color" content="#078C99">

    <?php if ($canonicalUrl !== ""): ?>
        <link rel="canonical" href="<?= View::escape($canonicalUrl) ?>">
    <?php endif; ?>
    <link rel="sitemap" type="application/xml" href="/sitemap.xml">
    <link
        rel="alternate"
        type="text/plain"
        href="/llms.txt"
        title="AI and automated-system site guide"
    >

    <meta property="og:type" content="<?= $guideData !== null ? 'article' : 'website' ?>">
    <meta property="og:site_name" content="<?= View::escape($siteName) ?>">
    <meta property="og:title" content="<?= View::escape($pageTitle) ?>">
    <meta property="og:description" content="<?= View::escape($metaDescription) ?>">
    <meta property="og:locale" content="en_KE">
    <?php if ($canonicalUrl !== ""): ?>
        <meta property="og:url" content="<?= View::escape($canonicalUrl) ?>">
    <?php endif; ?>

    <?php
    $defaultImage = $siteUrl . '/assets/images/albatech-social-card.png';
    $finalImage = $metaImage !== '' ? $metaImage : $defaultImage;
    ?>
    <meta property="og:image" content="<?= View::escape($finalImage) ?>">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="<?= View::escape($siteName . ' — practical digital services in Kenya') ?>">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= View::escape($pageTitle) ?>">
    <meta name="twitter:description" content="<?= View::escape($metaDescription) ?>">
    <meta name="twitter:image" content="<?= View::escape($finalImage) ?>">
    <meta name="twitter:image:alt" content="<?= View::escape($siteName . ' — practical digital services in Kenya') ?>">

    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="icon" type="image/png" sizes="512x512" href="/assets/images/albatech-logo.png">
    <link rel="apple-touch-icon" href="/assets/images/albatech-logo.png">

    <link rel="stylesheet" href="<?= View::asset('css/tokens.css') ?>">
    <link rel="stylesheet" href="<?= View::asset('css/app.css') ?>">

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
            <img src="/assets/images/albatech-logo.png" alt="<?= View::escape($siteName) ?>" class="brand-logo" width="180" height="48">
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

<footer class="site-footer">
    <div class="container footer-main">
        <div class="footer-brand-block">
            <a class="brand" href="/" aria-label="<?= View::escape($siteName) ?> home">AlbaTech <span>Solutions</span></a>
            <p class="footer-description">Practical website, online presence and digital assistance for people and businesses in Kenya. Clear scope, straightforward communication and realistic next steps.</p>
            <a class="footer-contact-cta" href="/get-help">Tell us what you need <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </div>
        <nav class="footer-column" aria-label="Explore">
            <h2>Explore</h2>
            <a href="/services">Our services</a>
            <a href="/guides">Guides & resources</a>
            <a href="/about">About us</a>
        </nav>
        <div class="footer-column footer-column--contact">
            <h2>Contact & location</h2>
            <p class="footer-location"><i class="fa-solid fa-location-dot" aria-hidden="true"></i><span><?= View::escape($footerLocation) ?><br><small>Service area; please contact us before visiting.</small></span></p>
            <?php if ($footerEmail !== '' && filter_var($footerEmail, FILTER_VALIDATE_EMAIL)): ?><a href="mailto:<?= View::escape($footerEmail) ?>"><i class="fa-regular fa-envelope" aria-hidden="true"></i><?= View::escape($footerEmail) ?></a><?php endif; ?>
            <?php if ($footerPhone !== '' && $footerPhoneHref !== ''): ?><a href="tel:<?= View::escape($footerPhoneHref) ?>"><i class="fa-solid fa-phone" aria-hidden="true"></i><?= View::escape($footerPhone) ?></a><?php endif; ?>
            <a href="/contact-details">All contact details</a>
        </div>
        <nav class="footer-column" aria-label="Information">
            <h2>Information</h2>
            <a href="/privacy">Privacy policy</a>
            <a href="/terms">Terms of service</a>
            <a href="/get-help">Make an enquiry</a>
        </nav>
    </div>
    <div class="container footer-bottom">
        <p>&copy; <?= date('Y') ?> <?= View::escape($siteName) ?>. All rights reserved.</p>
        <p class="footer-independent-note">AlbaTech Solutions is an independent service provider, not a government agency. Official decisions remain with the relevant authority.</p>
    </div>
</footer>

<?php if (strlen($whatsAppNumber) >= 8 && strlen($whatsAppNumber) <= 15 && !str_starts_with($whatsAppNumber, '0')): ?>
<div class="whatsapp-float" data-whatsapp-widget>
  <div class="whatsapp-float__panel" data-whatsapp-panel hidden>
    <button class="whatsapp-float__close" type="button" data-whatsapp-close aria-label="Close WhatsApp chat">&times;</button>
    <span class="whatsapp-float__eyebrow">AlbaTech Solutions</span>
    <h2>Need guidance?</h2>
    <p>Send us a message on WhatsApp and tell us what you need help with.</p>
    <a class="button button--whatsapp" href="<?= View::escape($whatsAppUrl) ?>" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Start a chat</a>
  </div>
  <button class="whatsapp-float__trigger" type="button" data-whatsapp-open aria-expanded="false" aria-label="Need guidance? Open WhatsApp chat panel"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i><span>Need guidance?</span></button>
</div>
<script nonce="<?= View::escape($cspNonce ?? '') ?>">
document.addEventListener('DOMContentLoaded', function () {
 const widget = document.querySelector('[data-whatsapp-widget]'); if (!widget) return;
 const panel = widget.querySelector('[data-whatsapp-panel]'); const open = widget.querySelector('[data-whatsapp-open]'); const close = widget.querySelector('[data-whatsapp-close]');
 open.addEventListener('click', function () { panel.hidden = !panel.hidden; open.setAttribute('aria-expanded', panel.hidden ? 'false' : 'true'); });
 close.addEventListener('click', function () { panel.hidden = true; open.setAttribute('aria-expanded', 'false'); open.focus(); });
});
</script>
<?php endif; ?>

</body>
</html>