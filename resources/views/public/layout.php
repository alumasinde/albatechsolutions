<?php

use App\Modules\Cms\Repository\MenuRepository;

$menuRepo = new MenuRepository();

$footerItems = $menuRepo->itemsForSlug('footer');

ob_start();
?>

<header class="site-header">
    <div class="site-header-inner">

        <a href="/" class="site-logo" aria-label="<?= e(setting('site_name', 'AlbaTech Solutions')) ?> Home">
            <?php if ($logoPath = setting('site_logo_path')): ?>
                <img
                    src="<?= e(url($logoPath)) ?>"
                    alt="<?= e(setting('site_name', 'AlbaTech Solutions')) ?> logo"
                    width="180"
                    height="38"
                    decoding="async">
            <?php else: ?>
                <?= e(setting('site_name', 'AlbaTech Solutions')) ?>
            <?php endif; ?>
        </a>

        <nav class="site-nav" id="site-nav" aria-label="Primary Navigation">
            <a href="/" <?= (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/') === '/' ? 'aria-current="page"' : '' ?>>Home</a>
            <a href="/services" <?= str_starts_with((parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/'), '/services') ? 'aria-current="page"' : '' ?>>Services</a>
            <a href="/blog" <?= str_starts_with((parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/'), '/blog') ? 'aria-current="page"' : '' ?>>Guides</a>
            <a href="/faqs" <?= (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/') === '/faqs' ? 'aria-current="page"' : '' ?>>FAQs</a>
            <a href="/about" <?= (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/') === '/about' ? 'aria-current="page"' : '' ?>>About</a>
            <a href="/contact" <?= (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/') === '/contact' ? 'aria-current="page"' : '' ?>>Contact</a>
<a href="/get-help" class="site-nav-cta"><i class="fa-solid fa-hand-holding-heart" aria-hidden="true"></i> Get Assistance</a>
        </nav>

        <button
            type="button"
            id="site-nav-toggle"
            class="nav-toggle"
            aria-label="Toggle navigation menu"
            aria-expanded="false"
            aria-controls="site-nav">

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