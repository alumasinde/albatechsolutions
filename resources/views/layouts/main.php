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
$googleVerification = trim((string) ($siteSettings['google_site_verification'] ?? ''));

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
$projectData = isset($schemaProject) && is_array($schemaProject) ? $schemaProject : null;
$productData = isset($schemaProduct) && is_array($schemaProduct) ? $schemaProduct : null;

$founderName = trim((string) ($siteSettings['founder_name'] ?? ''));
$founderTitle = trim((string) ($siteSettings['founder_title'] ?? ''));
$founderBio = trim((string) ($siteSettings['founder_bio'] ?? ''));
$founderLinkedin = trim((string) ($siteSettings['founder_linkedin'] ?? ''));
$founderId = $siteUrl . '/about#founder';
$schemaPageType = (string) ($schemaPageType ?? 'WebPage');
if (!in_array($schemaPageType, ['WebPage', 'ContactPage', 'AboutPage', 'CollectionPage'], true)) {
    $schemaPageType = 'WebPage';
}

$organizationId = $siteUrl . '/#organization';
$websiteId = $siteUrl . '/#website';
$localBusinessId = $siteUrl . '/#localbusiness';
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

// Build LocalBusiness schema
$localBusinessSchema = [
    '@type' => 'ProfessionalService',
    '@id' => $localBusinessId,
    'name' => $siteName,
    'url' => $siteUrl . '/',
    'description' => $defaultDescription,
    'logo' => ['@type' => 'ImageObject', 'url' => $siteUrl . '/assets/images/albatech-logo.png'],
    'areaServed' => ['@type' => 'Country', 'name' => 'Kenya'],
    'priceRange' => 'KSh',
];

if ($orgAddress !== '') {
    $localBusinessSchema['address'] = [
        '@type' => 'PostalAddress',
        'addressLocality' => trim(explode(',', $orgAddress)[0]),
        'addressCountry' => 'KE',
    ];
}
if ($orgEmail !== '' && filter_var($orgEmail, FILTER_VALIDATE_EMAIL)) {
    $localBusinessSchema['email'] = $orgEmail;
}
if ($orgPhone !== '') {
    $localBusinessSchema['telephone'] = $orgPhone;
}

// Add geo coordinates if available
$lat = trim((string) ($siteSettings['business_latitude'] ?? ''));
$lng = trim((string) ($siteSettings['business_longitude'] ?? ''));
if ($lat !== '' && $lng !== '' && is_numeric($lat) && is_numeric($lng)) {
    $localBusinessSchema['geo'] = [
        '@type' => 'GeoCoordinates',
        'latitude' => (float) $lat,
        'longitude' => (float) $lng,
    ];
}

// Add sameAs social links
$sameAs = [];
$socialLinks = [
    'social_facebook' => 'facebook.com',
    'social_twitter' => 'twitter.com',
    'social_linkedin' => 'linkedin.com',
    'social_instagram' => 'instagram.com',
];
foreach ($socialLinks as $key => $domain) {
    $url = trim((string) ($siteSettings[$key] ?? ''));
    if ($url !== '' && filter_var($url, FILTER_VALIDATE_URL)) {
        $sameAs[] = $url;
    }
}
if (!empty($sameAs)) {
    $localBusinessSchema['sameAs'] = $sameAs;
}

$structuredGraph[] = $localBusinessSchema;

if ($founderName !== '') {
    $personSchema = [
        '@type' => 'Person',
        '@id' => $founderId,
        'name' => $founderName,
        'url' => $siteUrl . '/about',
        'worksFor' => ['@id' => $organizationId],
    ];
    if ($founderTitle !== '') {
        $personSchema['jobTitle'] = $founderTitle;
    }
    if ($founderBio !== '') {
        $personSchema['description'] = $founderBio;
    }
    if ($founderLinkedin !== '' && filter_var($founderLinkedin, FILTER_VALIDATE_URL) && preg_match('~^https://~i', $founderLinkedin) === 1) {
        $personSchema['sameAs'] = [$founderLinkedin];
    }
    $structuredGraph[] = $personSchema;
    $structuredGraph[0]['founder'] = ['@id' => $founderId];
}

// Add BreadcrumbList if provided by controller
if (isset($breadcrumbs) && is_array($breadcrumbs) && !empty($breadcrumbs)) {
    $breadcrumbList = [
        '@type' => 'BreadcrumbList',
        'itemListElement' => [],
    ];
    foreach ($breadcrumbs as $index => $crumb) {
        $breadcrumbList['itemListElement'][] = [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'name' => $crumb['name'] ?? '',
            'item' => isset($crumb['url']) && $crumb['url'] !== '' ? $siteUrl . $crumb['url'] : null,
        ];
    }
    $structuredGraph[] = $breadcrumbList;
}

if ($guideData !== null && $canonicalUrl !== '') {
    $articleSchema = [
        '@type' => 'Article',
        '@id' => $canonicalUrl . '#article',
        'headline' => (string) ($guideData['title'] ?? $pageTitle),
        'description' => $metaDescription,
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $canonicalUrl],
        'author' => $founderName !== '' ? ['@id' => $founderId] : ['@type' => 'Organization', '@id' => $organizationId],
        'publisher' => ['@id' => $organizationId],
        'image' => [$metaImage],
        'inLanguage' => 'en-KE',
    ];
    $publishedDate = trim((string) ($guideData['published'] ?? ''));
    $modifiedDate = trim((string) ($guideData['updated'] ?? ''));
    if ($publishedDate !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $publishedDate)) $articleSchema['datePublished'] = $publishedDate;
    if ($modifiedDate !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $modifiedDate)) $articleSchema['dateModified'] = $modifiedDate;
    $structuredGraph[] = $articleSchema;
} elseif ($projectData !== null && $canonicalUrl !== '') {
    $projectSchema = [
        '@type' => 'CreativeWork',
        '@id' => $canonicalUrl . '#creative-work',
        'name' => (string) ($projectData['title'] ?? $pageTitle),
        'description' => $metaDescription,
        'url' => $canonicalUrl,
        'author' => $founderName !== '' ? ['@id' => $founderId] : ['@type' => 'Organization', '@id' => $organizationId],
        'publisher' => ['@id' => $organizationId],
        'image' => [$metaImage],
        'inLanguage' => 'en-KE',
    ];
    if (!empty($projectData['client_name'])) {
        $projectSchema['about'] = [
            '@type' => 'Thing',
            'name' => (string) $projectData['client_name'],
        ];
    }
    if (!empty($projectData['category_name'])) {
        $projectSchema['genre'] = (string) $projectData['category_name'];
    }
    $structuredGraph[] = $projectSchema;
} elseif ($productData !== null && $canonicalUrl !== '') {
    $productSchema = [
        '@type' => 'SoftwareApplication',
        '@id' => $canonicalUrl . '#software',
        'name' => (string) ($productData['title'] ?? $pageTitle),
        'description' => $metaDescription,
        'url' => $canonicalUrl,
        'applicationCategory' => 'BusinessApplication',
        'publisher' => ['@id' => $organizationId],
        'image' => [$metaImage],
        'inLanguage' => 'en-KE',
    ];
    $platform = trim((string) ($productData['platform'] ?? ''));
    if ($platform !== '') {
        $productSchema['operatingSystem'] = $platform;
    }
    $structuredGraph[] = $productSchema;
} elseif ($serviceData !== null && $canonicalUrl !== '') {
    $serviceName = trim((string) ($serviceData['name'] ?? $siteName));
    $serviceDescription = trim((string) ($serviceData['meta_description'] ?? $serviceData['short_description'] ?? $metaDescription));
    $serviceSchema = [
        '@type' => 'Service',
        '@id' => $canonicalUrl . '#service',
        'name' => $serviceName !== '' ? $serviceName : $siteName,
        'description' => $serviceDescription !== '' ? $serviceDescription : $metaDescription,
        'url' => $canonicalUrl,
        'provider' => ['@id' => $organizationId],
        'areaServed' => ['@type' => 'Country', 'name' => 'Kenya'],
    ];
    $pricingType = (string) ($serviceData['pricing_type'] ?? '');
    $pricingAmount = $serviceData['pricing_amount'] ?? null;
    if (in_array($pricingType, ['fixed', 'starting_at'], true) && $pricingAmount !== null && $pricingAmount !== '' && is_numeric($pricingAmount)) {
        $serviceSchema['offers'] = [
            '@type' => 'Offer',
            'url' => $canonicalUrl,
            'price' => (float) $pricingAmount,
            'priceCurrency' => 'KES',
            'availability' => 'https://schema.org/InStock',
        ];
    }
    $structuredGraph[] = $serviceSchema;
} elseif ($canonicalUrl !== '') {
    $webPageSchema = [
        '@type' => $schemaPageType,
        '@id' => $canonicalUrl . '#webpage',
        'url' => $canonicalUrl,
        'name' => $pageTitle,
        'description' => $metaDescription,
        'isPartOf' => ['@id' => $websiteId],
        'about' => ['@id' => $organizationId],
        'inLanguage' => 'en-KE',
    ];
    if ($schemaPageType === 'ContactPage') {
        $webPageSchema['mainEntity'] = ['@id' => $organizationId];
    }
    $structuredGraph[] = $webPageSchema;
}

if (!empty($faqItems) && is_array($faqItems) && $canonicalUrl !== '') {
    $faqEntities = [];
    foreach ($faqItems as $faqItem) {
        $questionText = trim((string) ($faqItem['question'] ?? ''));
        $answerText = trim((string) ($faqItem['answer'] ?? ''));
        if ($questionText === '' || $answerText === '') {
            continue;
        }
        $faqEntities[] = [
            '@type' => 'Question',
            'name' => $questionText,
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $answerText],
        ];
    }
    if (!empty($faqEntities)) {
        $structuredGraph[] = [
            '@type' => 'FAQPage',
            '@id' => $canonicalUrl . '#faq',
            'mainEntity' => $faqEntities,
        ];
    }
}

$structuredData = $structuredData ?? ['@context' => 'https://schema.org', '@graph' => $structuredGraph];
$whatsAppUrl = WhatsAppLink::build($siteSettings);
$footerEmail = trim((string) ($siteSettings['contact_email'] ?? ''));
$footerPhone = trim((string) ($siteSettings['business_phone'] ?? ''));
$footerPhoneHref = preg_replace('/[^0-9+]/', '', $footerPhone) ?? '';
$footerLocation = trim((string) ($siteSettings['business_address'] ?? 'Nairobi, Kenya'));
if ($footerLocation === '') $footerLocation = 'Kenya';

$headerNavigation = []; $footerExploreNavigation = []; $footerInfoNavigation = []; $layoutCtas = [];
$cms = new \AlbaTech\Repositories\CmsRepository();
$navByPlacement = $cms->navigationByPlacement();
$headerNavigation = $navByPlacement['header'] ?? [];
$footerExploreNavigation = $navByPlacement['footer_explore'] ?? [];
$footerInfoNavigation = $navByPlacement['footer_information'] ?? [];
$layoutCtas = $cms->ctas();
if (!$headerNavigation) $headerNavigation = [['label'=>'Work','url'=>'/work','open_new_tab'=>0],['label'=>'Products','url'=>'/products','open_new_tab'=>0],['label'=>'Solutions','url'=>'/solutions','open_new_tab'=>0],['label'=>'Services','url'=>'/services','open_new_tab'=>0],['label'=>'Guides','url'=>'/guides','open_new_tab'=>0],['label'=>'Contact','url'=>'/get-help','open_new_tab'=>0]];

$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$currentPath = rtrim($currentPath, '/') ?: '/';
$navIsActive = static function (string $url) use ($currentPath): bool {
    $path = parse_url($url, PHP_URL_PATH) ?: '/';
    $path = rtrim($path, '/') ?: '/';
    return $path === '/' ? $currentPath === '/' : ($currentPath === $path || str_starts_with($currentPath, $path . '/'));
};
if (!$footerExploreNavigation) $footerExploreNavigation = [['label'=>'Our work','url'=>'/work','open_new_tab'=>0],['label'=>'Products','url'=>'/products','open_new_tab'=>0],['label'=>'Solutions','url'=>'/solutions','open_new_tab'=>0],['label'=>'Services','url'=>'/services','open_new_tab'=>0],['label'=>'Guides','url'=>'/guides','open_new_tab'=>0],['label'=>'About us','url'=>'/about','open_new_tab'=>0]];
if (!$footerInfoNavigation) $footerInfoNavigation = [['label'=>'Privacy policy','url'=>'/privacy','open_new_tab'=>0],['label'=>'Terms of service','url'=>'/terms','open_new_tab'=>0],['label'=>'Data deletion','url'=>'/data-deletion','open_new_tab'=>0],['label'=>'Make an enquiry','url'=>'/get-help','open_new_tab'=>0]];

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
    <?php if ($googleVerification !== '' && preg_match('/^[A-Za-z0-9_-]{8,120}$/', $googleVerification) === 1): ?>
        <meta name="google-site-verification" content="<?= View::escape($googleVerification) ?>">
    <?php endif; ?>
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
    $defaultImage = $siteUrl . SeoDefaults::SOCIAL_IMAGE;
    $finalImage = $metaImage !== '' ? $metaImage : $defaultImage;
    ?>
    <meta property="og:image" content="<?= View::escape($finalImage) ?>">
    <?php if ($finalImage === $defaultImage): ?>
        <meta property="og:image:type" content="image/jpeg">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
    <?php endif; ?>
    <meta property="og:image:alt" content="<?= View::escape($siteName . ': software and websites for Kenyan businesses') ?>">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= View::escape($pageTitle) ?>">
    <meta name="twitter:description" content="<?= View::escape($metaDescription) ?>">
    <meta name="twitter:image" content="<?= View::escape($finalImage) ?>">
    <meta name="twitter:image:alt" content="<?= View::escape($siteName . ': software and websites for Kenyan businesses') ?>">

    <!-- The hero image is CMS-configurable. Its eager, high-priority image element in the page is the source of truth;
         avoid preloading a hardcoded fallback URL when an administrator may choose another image. -->

    <!-- Favicons -->
    <link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">
    <link rel="icon" type="image/png" sizes="48x48" href="/assets/images/favicon-48x48.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/assets/images/favicon-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/assets/images/favicon-512x512.png">
    <link rel="apple-touch-icon" sizes="192x192" href="/assets/images/favicon-192x192.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap">
    <link rel="stylesheet" href="<?= View::escape(View::asset('css/bundle.css')) ?>">
    <!-- SVG sprite is the primary icon system. Font Awesome is trimmed to a subset containing only the icons the public site uses (bin/subset-fontawesome.php); admin loads the full file. -->
    <link rel="stylesheet" href="<?= View::escape(View::asset('vendor/fontawesome/css/fa-subset.min.css')) ?>">

    <?php
    // Analytics: Plausible is cookieless and loads immediately; GA4 waits for consent.
    $analyticsEnabled = ($siteSettings['analytics_enabled'] ?? '') === '1';
    $analyticsProvider = trim((string) ($siteSettings['analytics_provider'] ?? ''));
    $analyticsDomain = trim((string) ($siteSettings['analytics_domain'] ?? ''));
    $analyticsScriptUrl = trim((string) ($siteSettings['analytics_script_url'] ?? ''));
    $ga4Active = $analyticsEnabled && $analyticsProvider === 'ga4' && $analyticsScriptUrl !== '';
    $consentRequired = ($siteSettings['analytics_consent_enabled'] ?? '1') !== '0';

    if ($analyticsEnabled && $analyticsProvider === 'plausible' && $analyticsDomain !== '' && $analyticsScriptUrl !== ''):
    ?>
        <script defer data-domain="<?= View::escape($analyticsDomain) ?>" src="<?= View::escape($analyticsScriptUrl) ?>"></script>
    <?php elseif ($ga4Active): ?>
        <script nonce="<?= View::escape($cspNonce ?? '') ?>">
            window.albaAnalytics = {
                provider: 'ga4',
                scriptUrl: <?= json_encode($analyticsScriptUrl, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
                measurementId: <?= json_encode($analyticsDomain, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
                consentRequired: <?= $consentRequired ? 'true' : 'false' ?>
            };
        </script>
    <?php endif; ?>

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
    <div class="shell">
        <a
            class="brand"
            href="/"
            aria-label="<?= View::escape($siteName) ?> home"
        >
            <img src="<?= View::escape(View::asset('images/albatech-logo-sm.png')) ?>" alt="<?= View::escape($siteName) ?>" class="brand-logo" width="352" height="352">
        </a>

        <div class="flex items-center gap-1">
            <nav class="site-nav" id="site-nav" aria-label="Main navigation">
                <?php foreach ($headerNavigation as $nav): ?><a href="<?= View::escape($nav['url']) ?>"<?= $navIsActive((string) $nav['url']) ? ' aria-current="page"' : '' ?><?= !empty($nav['open_new_tab']) ? ' target="_blank" rel="noopener noreferrer"' : '' ?>><?= View::escape($nav['label']) ?></a><?php endforeach; ?>
            </nav>
            <a class="btn btn-primary btn-sm header-cta" href="/get-help">Get help</a>
            <button
                class="mobile-menu-btn"
                type="button"
                aria-label="Toggle navigation menu"
                aria-expanded="false"
                aria-controls="site-nav"
                id="mobile-menu-toggle"
            >
                <svg class="icon icon-bars" aria-hidden="true"><use href="/assets/images/icons.svg#icon-bars"></use></svg>
            </button>
        </div>
    </div>
</header>

<main id="main-content">
    <?= $content ?? '' ?>
</main>

<footer class="site-footer mt-auto py-12">
    <div class="shell grid gap-10 pb-10 md:grid-cols-2 lg:grid-cols-[1.7fr_1fr_1fr_1fr]">
        <div class="md:col-span-2 lg:col-span-1">
            <a class="brand" href="/" aria-label="<?= View::escape($siteName) ?> home">AlbaTech <span class="text-primary">Solutions</span></a>
            <p class="mt-4 mb-4 max-w-sm text-sm leading-relaxed"><?= View::escape($siteSettings['footer_description'] ?? 'A software studio in Nairobi building products and systems for Kenyan businesses, alongside websites and practical digital assistance.') ?></p>
            <?php $footerCta = $layoutCtas['primary_enquiry'] ?? ['label'=>'Tell us what you need','url'=>'/get-help']; ?><a class="footer-contact-cta inline-flex items-center gap-2 font-bold" href="<?= View::escape($footerCta['url']) ?>"><?= View::escape($footerCta['label']) ?> <i class="fa-solid fa-arrow-right text-sm transition-transform duration-150" aria-hidden="true"></i></a>
        </div>
        <nav class="flex flex-col items-start gap-3" aria-label="Explore">
            <h2 class="text-sm font-extrabold tracking-normal">Explore</h2>
            <?php foreach ($footerExploreNavigation as $nav): ?><a class="text-sm leading-normal" href="<?= View::escape($nav['url']) ?>"<?= !empty($nav['open_new_tab']) ? ' target="_blank" rel="noopener noreferrer"' : '' ?>><?= View::escape($nav['label']) ?></a><?php endforeach; ?>
        </nav>
        <div class="flex flex-col items-start gap-3">
            <h2 class="text-sm font-extrabold tracking-normal">Contact &amp; location</h2>
            <p class="m-0 inline-flex items-start gap-2 text-sm leading-normal"><i class="fa-solid fa-location-dot mt-1 w-4 text-center text-accent" aria-hidden="true"></i><span><?= View::escape($footerLocation) ?></span></p>
            <?php if ($footerEmail !== '' && filter_var($footerEmail, FILTER_VALIDATE_EMAIL)): ?><a class="inline-flex items-start gap-2 text-sm leading-normal" href="mailto:<?= View::escape($footerEmail) ?>"><i class="fa-regular fa-envelope mt-1 w-4 text-center text-accent" aria-hidden="true"></i><?= View::escape($footerEmail) ?></a><?php endif; ?>
            <?php if ($footerPhone !== '' && $footerPhoneHref !== ''): ?><a class="inline-flex items-start gap-2 text-sm leading-normal" href="tel:<?= View::escape($footerPhoneHref) ?>"><i class="fa-solid fa-phone mt-1 w-4 text-center text-accent" aria-hidden="true"></i><?= View::escape($footerPhone) ?></a><?php endif; ?>
            <a class="text-sm leading-normal" href="/get-help">All contact details</a>
        </div>
        <nav class="flex flex-col items-start gap-3" aria-label="Information">
            <h2 class="text-sm font-extrabold tracking-normal">Information</h2>
            <?php foreach ($footerInfoNavigation as $nav): ?><a class="text-sm leading-normal" href="<?= View::escape($nav['url']) ?>"<?= !empty($nav['open_new_tab']) ? ' target="_blank" rel="noopener noreferrer"' : '' ?>><?= View::escape($nav['label']) ?></a><?php endforeach; ?>
        </nav>
    </div>
    <div class="shell flex flex-col gap-3 border-t border-footer-border pt-5 text-xs">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="m-0">&copy; <?= date('Y') ?> <?= View::escape($siteName) ?>. All rights reserved.</p>
            <div class="flex flex-wrap gap-4"><a href="/privacy">Privacy</a><a href="/terms">Terms</a><a href="/data-deletion">Data deletion</a><a href="/get-help">Contact details</a><?php if ($ga4Active && $consentRequired): ?><a href="#cookie-settings" data-consent-settings>Cookie settings</a><?php endif; ?></div>
        </div>
        <p class="m-0 leading-normal">AlbaTech Solutions is an independent service provider, not a government agency. Official decisions remain with the relevant authority.</p>
    </div>
</footer>

<?php if (str_starts_with($whatsAppUrl, 'https://wa.me/')): ?>
<a class="whatsapp-float" href="<?= View::escape($whatsAppUrl) ?>" target="_blank" rel="noopener noreferrer" aria-label="Chat with AlbaTech Solutions on WhatsApp">
  <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
</a>
<?php endif; ?>

<?php if ($ga4Active && $consentRequired): ?>
<div id="consent-banner" class="fixed inset-x-0 bottom-0 z-[60] p-4 sm:p-6" hidden>
  <div class="mx-auto flex max-w-3xl flex-col gap-4 rounded-2xl border border-border bg-surface p-4 shadow-pop sm:flex-row sm:items-center sm:justify-between sm:p-5">
    <span class="block text-sm leading-relaxed text-muted">We'd like to use Google Analytics to see which pages visitors find useful. Nothing loads unless you accept. <a href="/privacy" class="underline">Privacy policy</a></span>
    <div class="flex shrink-0 gap-2">
      <button type="button" data-consent="denied" class="inline-flex min-h-11 items-center justify-center rounded-md border border-border-strong bg-surface px-4 text-sm font-semibold text-ink hover:bg-surface-muted">Decline</button>
      <button type="button" data-consent="granted" class="inline-flex min-h-11 items-center justify-center rounded-md bg-primary px-4 text-sm font-semibold text-white hover:bg-primary-hover">Accept analytics</button>
    </div>
  </div>
</div>
<?php endif; ?>

<script src="<?= View::escape(View::asset('js/site.js')) ?>" defer></script>
<?php if (!empty($needsWorkCarousel)): ?>
<script src="<?= View::escape(View::asset('js/work-carousel.js')) ?>" defer></script>
<?php endif; ?>
</body>
</html>