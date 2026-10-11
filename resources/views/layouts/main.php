<?php

use AlbaTech\Core\Env;
use AlbaTech\Core\SiteUrl;
use AlbaTech\Core\View;
use AlbaTech\Core\SeoDefaults;
use AlbaTech\Core\WhatsAppLink;
use AlbaTech\Repositories\SiteSettingsRepository;

$siteSettings = (new SiteSettingsRepository())->publicValues();
$siteName = trim((string) ($siteSettings['site_name'] ?? $appName ?? Env::get('APP_NAME', 'AlbaTech Solutions')));
if ($siteName === '') $siteName = 'AlbaTech Solutions';

$pageTitle = SeoDefaults::title((string) ($pageTitle ?? ''), (string) ($siteSettings['default_meta_title'] ?? $siteName));

$defaultDescription = trim((string) ($siteSettings['default_meta_description'] ?? ''));
if ($defaultDescription === '') {
    $defaultDescription = SeoDefaults::DESCRIPTION;
}
$meta = isset($meta) && is_array($meta) ? $meta : [];
$metaDescription = SeoDefaults::description((string) ($metaDescription ?? $meta['meta_description'] ?? ''), $defaultDescription);
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
// Never guess the homepage as a canonical for an unrelated indexable route.
// Controllers must provide the page's trusted, absolute canonical URL.
$metaImage = SeoDefaults::image((string) ($metaImage ?? $siteSettings['default_social_image'] ?? ''), $siteUrl);
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
    $articleSchema = [
        '@type' => 'Article',
        '@id' => $canonicalUrl . '#article',
        'headline' => (string) ($guideData['title'] ?? $pageTitle),
        'description' => $metaDescription,
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $canonicalUrl],
        'author' => ['@type' => 'Organization', '@id' => $organizationId],
        'publisher' => ['@id' => $organizationId],
        'image' => [$metaImage],
        'inLanguage' => 'en-KE',
    ];
    $publishedDate = trim((string) ($guideData['published'] ?? ''));
    $modifiedDate = trim((string) ($guideData['updated'] ?? ''));
    if ($publishedDate !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $publishedDate)) $articleSchema['datePublished'] = $publishedDate;
    if ($modifiedDate !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $modifiedDate)) $articleSchema['dateModified'] = $modifiedDate;
    $structuredGraph[] = $articleSchema;
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
$whatsAppUrl = WhatsAppLink::build($siteSettings);
$footerEmail = trim((string) ($siteSettings['contact_email'] ?? ''));
$footerPhone = trim((string) ($siteSettings['business_phone'] ?? ''));
$footerPhoneHref = preg_replace('/[^0-9+]/', '', $footerPhone) ?? '';
$footerLocation = trim((string) ($siteSettings['business_address'] ?? 'Nairobi, Kenya'));
if ($footerLocation === '') $footerLocation = 'Kenya';

$headerNavigation = []; $footerExploreNavigation = []; $footerInfoNavigation = []; $layoutCtas = [];
try {
    $navRows = \AlbaTech\Core\Database::connection()->query('SELECT label,url,placement,open_new_tab FROM site_navigation WHERE is_enabled=1 ORDER BY sort_order,id')->fetchAll();
    foreach ($navRows as $navRow) { if ($navRow['placement']==='header') $headerNavigation[]=$navRow; elseif ($navRow['placement']==='footer_explore') $footerExploreNavigation[]=$navRow; elseif ($navRow['placement']==='footer_information') $footerInfoNavigation[]=$navRow; }
    foreach (\AlbaTech\Core\Database::connection()->query('SELECT cta_key,label,url,placement,style_class FROM site_ctas WHERE is_enabled=1')->fetchAll() as $ctaRow) $layoutCtas[$ctaRow['cta_key']]=$ctaRow;
} catch (\Throwable $e) { error_log('[AlbaTech layout CMS] '.$e->getMessage()); }
if (!$headerNavigation) $headerNavigation = [['label'=>'Home','url'=>'/','open_new_tab'=>0],['label'=>'Services','url'=>'/services','open_new_tab'=>0],['label'=>'Guides','url'=>'/guides','open_new_tab'=>0],['label'=>'About','url'=>'/about','open_new_tab'=>0],['label'=>'Contact','url'=>'/get-help','open_new_tab'=>0]];
if (!$footerExploreNavigation) $footerExploreNavigation = [['label'=>'Our services','url'=>'/services','open_new_tab'=>0],['label'=>'Guides & resources','url'=>'/guides','open_new_tab'=>0],['label'=>'About us','url'=>'/about','open_new_tab'=>0]];
if (!$footerInfoNavigation) $footerInfoNavigation = [['label'=>'Privacy policy','url'=>'/privacy','open_new_tab'=>0],['label'=>'Terms of service','url'=>'/terms','open_new_tab'=>0],['label'=>'Make an enquiry','url'=>'/get-help','open_new_tab'=>0]];

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
    <meta property="og:image:alt" content="<?= View::escape($siteName . ' — practical digital services in Kenya') ?>">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= View::escape($pageTitle) ?>">
    <meta name="twitter:description" content="<?= View::escape($metaDescription) ?>">
    <meta name="twitter:image" content="<?= View::escape($finalImage) ?>">
    <meta name="twitter:image:alt" content="<?= View::escape($siteName . ' — practical digital services in Kenya') ?>">

    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <?php if ($canonicalUrl !== '' && rtrim($canonicalUrl, '/') === rtrim($siteUrl, '/')): ?>
        <link rel="preload" as="image" href="/assets/images/albatech-social-card.png" fetchpriority="high">
    <?php endif; ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="icon" type="image/png" sizes="512x512" href="/assets/images/albatech-logo.png">
    <link rel="apple-touch-icon" href="/assets/images/albatech-logo.png">

    <link rel="stylesheet" href="<?= View::asset('css/tokens.css') ?>">
    <link rel="stylesheet" href="<?= View::escape(View::asset('css/app.css')) ?>">
    <link rel="stylesheet" href="<?= View::escape(View::asset('css/work.css')) ?>">

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
            <?php foreach ($headerNavigation as $nav): ?><a href="<?= View::escape($nav['url']) ?>"<?= !empty($nav['open_new_tab']) ? ' target="_blank" rel="noopener noreferrer"' : '' ?>><?= View::escape($nav['label']) ?></a><?php endforeach; ?>
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
            <p class="footer-description"><?= View::escape($siteSettings['footer_description'] ?? 'Practical website, online presence and digital assistance for people and businesses in Kenya. Clear scope, straightforward communication and realistic next steps.') ?></p>
            <?php $footerCta = $layoutCtas['primary_enquiry'] ?? ['label'=>'Tell us what you need','url'=>'/get-help']; ?><a class="footer-contact-cta" href="<?= View::escape($footerCta['url']) ?>"><?= View::escape($footerCta['label']) ?> <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </div>
        <nav class="footer-column" aria-label="Explore">
            <h2>Explore</h2>
            <?php foreach ($footerExploreNavigation as $nav): ?><a href="<?= View::escape($nav['url']) ?>"<?= !empty($nav['open_new_tab']) ? ' target="_blank" rel="noopener noreferrer"' : '' ?>><?= View::escape($nav['label']) ?></a><?php endforeach; ?>
        </nav>
        <div class="footer-column">
            <h2>Contact & location</h2>
            <p class="footer-location"><i class="fa-solid fa-location-dot" aria-hidden="true"></i><span><?= View::escape($footerLocation) ?></span></p>
            <?php if ($footerEmail !== '' && filter_var($footerEmail, FILTER_VALIDATE_EMAIL)): ?><a href="mailto:<?= View::escape($footerEmail) ?>"><i class="fa-regular fa-envelope" aria-hidden="true"></i><?= View::escape($footerEmail) ?></a><?php endif; ?>
            <?php if ($footerPhone !== '' && $footerPhoneHref !== ''): ?><a href="tel:<?= View::escape($footerPhoneHref) ?>"><i class="fa-solid fa-phone" aria-hidden="true"></i><?= View::escape($footerPhone) ?></a><?php endif; ?>
            <a href="/get-help">All contact details</a>
        </div>
        <nav class="footer-column" aria-label="Information">
            <h2>Information</h2>
            <?php foreach ($footerInfoNavigation as $nav): ?><a href="<?= View::escape($nav['url']) ?>"<?= !empty($nav['open_new_tab']) ? ' target="_blank" rel="noopener noreferrer"' : '' ?>><?= View::escape($nav['label']) ?></a><?php endforeach; ?>
        </nav>
    </div>
    <div class="container footer-bottom">
        <p>&copy; <?= date('Y') ?> <?= View::escape($siteName) ?>. All rights reserved.</p>
        <div class="footer-legal"><a href="/privacy">Privacy</a><a href="/terms">Terms</a><a href="/get-help">Contact details</a></div>
        <p class="footer-independent-note">AlbaTech Solutions is an independent service provider, not a government agency. Official decisions remain with the relevant authority.</p>
    </div>
</footer>

<?php if (str_starts_with($whatsAppUrl, 'https://wa.me/')): ?>
<div class="whatsapp-float">
  <a class="whatsapp-float__trigger" href="<?= View::escape($whatsAppUrl) ?>" target="_blank" rel="noopener noreferrer" aria-label="Open WhatsApp chat with AlbaTech Solutions">
    <i class="fa-brands fa-whatsapp" aria-hidden="true"></i><span></span>
  </a>
</div>
<?php endif; ?>

<script src="<?= View::escape(View::asset('js/work-carousel.js')) ?>" defer></script>
</body>
</html>