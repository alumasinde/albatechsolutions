<?php

use App\Modules\Cms\Repository\MenuRepository;

$menuRepo = new MenuRepository();

$footerItems = $menuRepo->itemsForSlug('footer');

$primaryItems = [
    ['label' => 'Services', 'url' => '/services'],
    ['label' => 'How It Works', 'url' => '/#how-it-works'],
    ['label' => 'Guides', 'url' => '/blog'],
    ['label' => 'About', 'url' => '/about'],
    ['label' => 'Contact', 'url' => '/contact'],
];

$databasePrimaryItems = $menuRepo->itemsForSlug('primary');
if ($databasePrimaryItems !== []) {
    $allowedPrimaryUrls = array_column($primaryItems, 'url');
    $configured = array_values(array_filter($databasePrimaryItems, static function (array $item) use ($allowedPrimaryUrls): bool {
        return in_array((string) ($item['url'] ?? ''), $allowedPrimaryUrls, true);
    }));

    if ($configured !== []) {
        $configuredByUrl = [];
        foreach ($configured as $item) {
            $configuredByUrl[(string) $item['url']] = $item;
        }

        foreach ($primaryItems as &$item) {
            if (isset($configuredByUrl[$item['url']])) {
                $item['label'] = (string) ($configuredByUrl[$item['url']]['label'] ?? $item['label']);
            }
        }
        unset($item);
    }
}

ob_start();
?>

<header class="site-header" data-site-header>
    <div class="site-header-inner">
        <a href="/" class="site-logo" aria-label="<?= e(setting('site_name', 'AlbaTech Solutions')) ?> Home">
            <?php if ($logoPath = setting('site_logo_path')): ?>
                <img src="<?= e(url($logoPath)) ?>" alt="<?= e(setting('site_name', 'AlbaTech Solutions')) ?> logo" width="180" height="38" decoding="async">
            <?php else: ?>
                <span class="site-logo__name"><?= e(setting('site_name', 'AlbaTech Solutions')) ?></span>
            <?php endif; ?>
        </a>

        <nav class="site-nav" id="site-nav" aria-label="Primary Navigation" data-site-nav>
            <div class="site-nav__mobile-intro">
                <strong>Need help?</strong>
                <span>Tell us the task and we’ll help with the next step.</span>
            </div>
            <div class="site-nav__links">
                <?php $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/'; ?>
                <?php foreach ($primaryItems as $item): ?>
                    <?php
                    $itemUrl = (string) $item['url'];
                    $isCurrent = $itemUrl === '/services'
                        ? str_starts_with($currentPath, '/services')
                        : ($itemUrl === '/blog'
                            ? str_starts_with($currentPath, '/blog')
                            : ($itemUrl !== '/#how-it-works' && $currentPath === $itemUrl));
                    ?>
                    <a href="<?= e($itemUrl) ?>" <?= $isCurrent ? 'aria-current="page"' : '' ?>><?= e((string) $item['label']) ?></a>
                <?php endforeach; ?>
            </div>
            <div class="site-nav__actions">
                <a href="/get-help" class="site-nav-cta"><i class="fa-solid fa-hand-holding-heart" aria-hidden="true"></i> Get Assistance</a>
                <?php if (setting('whatsapp_number')): ?>
                    <a href="<?= e(whatsapp_url('Hi AlbaTech Solutions, I need help with a task.')) ?>" target="_blank" rel="noopener noreferrer" class="site-nav-whatsapp js-whatsapp" data-whatsapp-number="<?= e(preg_replace('/\D+/', '', (string) setting('whatsapp_number'))) ?>">
                        <i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WhatsApp
                    </a>
                <?php endif; ?>
            </div>
        </nav>

        <button type="button" id="site-nav-toggle" class="nav-toggle" aria-label="Open navigation menu" aria-expanded="false" aria-controls="site-nav">
            <i class="fa-solid fa-bars" aria-hidden="true"></i>
        </button>
    </div>
</header>
<main>
    <?= $pageContent ?? '' ?>
</main>

<?php if ($whatsapp = setting('whatsapp_number')): ?>
    <a
        href="<?= e(whatsapp_url()) ?>"
        target="_blank"
        rel="noopener noreferrer"
        class="whatsapp-float js-whatsapp" data-whatsapp-number="<?= e(preg_replace('/\D+/', '', (string) setting('whatsapp_number'))) ?>"
        aria-label="Chat with AlbaTech Solutions on WhatsApp">

        <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
        <span class="whatsapp-float__label">Chat on WhatsApp</span>

    </a>
<?php endif; ?>

<footer class="site-footer">

    <div class="site-footer-inner">

        <section>

            <strong><?= e(setting('site_name', 'AlbaTech Solutions')) ?></strong>

            <?php if (setting('site_tagline')): ?>
                <p><?= e(setting('site_tagline')) ?></p>
            <?php endif; ?>

            <address>

                <?php if (setting('contact_email')): ?>
                    <div>
                        <a href="mailto:<?= e(setting('contact_email')) ?>">
                            <?= e(setting('contact_email')) ?>
                        </a>
                    </div>
                <?php endif; ?>

                <?php if (setting('contact_phone')): ?>
                    <div>
                        <a href="tel:<?= e(setting('contact_phone')) ?>">
                            <?= e(setting('contact_phone')) ?>
                        </a>
                    </div>
                <?php endif; ?>

            </address>

        </section>

        <nav class="site-footer-nav" aria-label="Footer Navigation">
            <?php foreach ($footerItems as $item): ?>
                <a href="<?= e($item['url']) ?>">
                    <?= e($item['label']) ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="site-social" aria-label="Social Media">

            <?php if (setting('social_facebook')): ?>
                <a
                    href="<?= e(setting('social_facebook')) ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Follow AlbaTech Solutions on Facebook">

                    <i class="fa-brands fa-facebook-f" aria-hidden="true"></i>

                </a>
            <?php endif; ?>

            <?php if (setting('social_twitter')): ?>
                <a
                    href="<?= e(setting('social_twitter')) ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Follow AlbaTech Solutions on X">

                    <i class="fa-brands fa-x-twitter" aria-hidden="true"></i>

                </a>
            <?php endif; ?>

            <?php if (setting('social_linkedin')): ?>
                <a
                    href="<?= e(setting('social_linkedin')) ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Connect with AlbaTech Solutions on LinkedIn">

                    <i class="fa-brands fa-linkedin-in" aria-hidden="true"></i>

                </a>
            <?php endif; ?>

            <?php if (setting('social_instagram')): ?>
                <a
                    href="<?= e(setting('social_instagram')) ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Follow AlbaTech Solutions on Instagram">

                    <i class="fa-brands fa-instagram" aria-hidden="true"></i>

                </a>
            <?php endif; ?>

        </div>

        <p class="site-copyright">
            &copy; <?= date('Y') ?>
            <?= e(setting('site_name', 'AlbaTech Solutions')) ?>.
            All rights reserved.
        </p>

    </div>

</footer>

<?php

$content = ob_get_clean();

require dirname(__DIR__) . '/layouts/app.php';