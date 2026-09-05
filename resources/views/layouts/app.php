<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="<?= e(\App\Core\Helpers\Csrf::token()) ?>">
    <meta name="theme-color" content="<?= e(setting('theme_color_primary', '#078a9a')) ?>">
    <title><?= e($title ?? setting('site_name', 'AlbaTech Solutions')) ?></title>

    <?php
    $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $privatePath = $requestPath === '/get-help/thanks'
        || $requestPath === '/login'
        || str_starts_with($requestPath, '/login/')
        || $requestPath === '/register'
        || $requestPath === '/forgot-password'
        || str_starts_with($requestPath, '/reset-password')
        || str_starts_with($requestPath, '/verify-email')
        || $requestPath === '/dashboard'
        || str_starts_with($requestPath, '/account')
        || str_starts_with($requestPath, '/admin')
        || str_starts_with($requestPath, '/quote/')
        || str_starts_with($requestPath, '/request/')
        || str_starts_with($requestPath, '/review/')
        || str_starts_with($requestPath, '/receipt/');
    $resolvedRobots = $privatePath ? 'noindex, nofollow' : ($robots ?? 'index, follow');
    $pageScopeClass = str_starts_with($requestPath, '/admin') || $requestPath === '/dashboard' ? 'admin-page' : 'public-page';
    ?>

    <?php if (isset($metaDescription) || isset($jsonLd) || $privatePath): ?>
        <?php
        $allJsonLd = array_merge(
            [\App\Core\Seo::organization(), \App\Core\Seo::website()],
            $jsonLd ?? []
        );
        echo \App\Core\Seo::renderHead(
            $title ?? setting('site_name', 'AlbaTech Solutions'),
            $metaDescription ?? null,
            $canonicalUrl ?? null,
            $ogImage ?? null,
            $ogType ?? 'website',
            $allJsonLd,
            $resolvedRobots
        );
        ?>
    <?php else: ?>
        <meta name="robots" content="noindex, nofollow">
    <?php endif; ?>

    <link rel="icon" href="<?= asset('favicon.ico') ?>">
    <meta name="color-scheme" content="light">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/v5/production.css') ?>">
    <style>
        :root {
            --color-primary: <?= e(setting('theme_color_primary', '#078a9a')) ?>;
            --color-secondary: <?= e(setting('theme_color_secondary', '#075e68')) ?>;
            --color-accent: <?= e(setting('theme_color_accent', '#f2b84b')) ?>;
            --color-background: <?= e(setting('theme_color_background', '#f8fafc')) ?>;
            --font-family: <?= e(setting('theme_font_family', "'Inter', sans-serif")) ?>;
            --radius-base: <?= e(setting('theme_radius', '12px')) ?>;
        }
    </style>
    <?php if (isset($extraHead)) echo $extraHead; ?>
</head>
<body class="<?= e($pageScopeClass) ?> <?= setting('whatsapp_number') ? 'has-mobile-cta' : '' ?>">
    <a class="skip-link" href="#main-content">Skip to content</a>
    <div id="toast-container" class="toast-container" aria-live="polite"></div>

    <div id="main-content">
        <?= $content ?? '' ?>
    </div>

    <script src="<?= asset('js/v4/app.js') ?>" defer></script>

    <?php if (setting('whatsapp_number')): ?>
        <div class="mobile-cta-bar" aria-label="Quick contact actions">
            <a href="/get-help" class="mobile-cta-bar__quote">Get Assistance</a>
            <a href="<?= e(whatsapp_url('Hi AlbaTech Solutions, I would like to speak with someone about what I need help with.')) ?>" target="_blank" rel="noopener noreferrer" class="mobile-cta-bar__whatsapp js-whatsapp" data-whatsapp-number="<?= e(preg_replace('/\D+/', '', (string) setting('whatsapp_number'))) ?>">
                <i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WhatsApp
            </a>
        </div>
    <?php endif; ?>
</body>
</html>
