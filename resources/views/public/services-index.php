<?php
$title = 'Digital Assistance & Services in Kenya — ' . setting('site_name', 'AlbaTech Solutions');
$metaDescription = 'Find practical digital assistance in Kenya: KRA, eCitizen, SHA, NSSF, NTSA, business registration, CVs, websites, Google Business Profile and custom software.';
$canonicalUrl = rtrim(config('app.url'), '/') . '/services';
$jsonLd = [\App\Core\Seo::breadcrumbs([
    ['name' => 'Home', 'url' => rtrim(config('app.url'), '/') . '/'],
    ['name' => 'Services', 'url' => $canonicalUrl],
])];
$categoryCount = count($categories ?? []);
ob_start();
?>
<section class="catalogue-hero">
    <div class="public-container catalogue-hero__inner">
        <a href="/" class="phase1-back"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Back home</a>
        <span class="catalogue-eyebrow"><i class="fa-solid fa-hand-holding-heart" aria-hidden="true"></i> Services</span>
        <h1>Digital help for things you need to get done.</h1>
        <p>Need help with an online service, document, business task or website? Browse by service, or get assistance if you are not sure which service fits your situation.</p>
    </div>
</section>

<section class="public-section">
    <div class="public-container">
        <?php if ($categoryCount): ?>
            <div class="catalogue-toolbar" data-catalogue-toolbar>
                <div class="catalogue-filter" role="group" aria-label="Filter services">
                    <button type="button" aria-pressed="true" data-service-filter="all">All services</button>
                    <?php foreach ($categories as $category): ?>
                        <button type="button" aria-pressed="false" data-service-filter="<?= e($category['slug']) ?>"><?= e($category['name']) ?></button>
                    <?php endforeach; ?>
                </div>
                <label class="catalogue-search">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    <span class="sr-only">Search services</span>
                    <input type="search" placeholder="Search services..." data-service-search autocomplete="off">
                </label>
            </div>

            <?php foreach ($categories as $category): ?>
                <section id="<?= e($category['slug']) ?>" class="catalogue-group" data-service-group data-category="<?= e($category['slug']) ?>">
                    <div class="catalogue-group__heading">
                        <div>
                            <span class="catalogue-eyebrow"><?= e($category['name']) ?></span>
                            <?php if (!empty($category['description'])): ?><p><?= e($category['description']) ?></p><?php endif; ?>
                        </div>
                    </div>
                    <div class="catalogue-grid">
                        <?php foreach ($category['services'] as $service): ?>
                            <a href="/services/<?= e($service['slug']) ?>" class="catalogue-card" data-service-card data-service-name="<?= e(strtolower($service['name'] . ' ' . ($service['summary'] ?? ''))) ?>">
                                <div class="catalogue-card__top">
                                    <span class="catalogue-card__icon"><i class="fa-solid <?= e($service['icon'] ?: 'fa-circle-check') ?>" aria-hidden="true"></i></span>
                                    <?php if (!empty($service['is_featured'])): ?><span class="catalogue-card__badge">Featured</span><?php endif; ?>
                                </div>
                                <h3><?= e($service['name']) ?></h3>
                                <p><?= e($service['summary'] ?: 'A practical digital service tailored to your needs.') ?></p>
                                <div class="catalogue-card__bottom">
                                    <span class="catalogue-card__price">
                                        <?php if (($service['price_type'] ?? 'quote') !== 'quote' && isset($service['price'])): ?>
                                            <?= ($service['price_type'] ?? '') === 'starting_from' ? 'From ' : '' ?>KES <?= number_format((float) $service['price'], 0) ?>
                                        <?php else: ?>Get Assistance<?php endif; ?>
                                    </span>
                                    <span class="catalogue-card__link">Explore <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>
            <div class="catalogue-empty" data-service-no-results hidden>
                <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                <h2>No matching services</h2>
                <p>Try a different search, or tell us what you are trying to achieve and we can help you find the right service.</p>
                <?php if (setting('whatsapp_number')): ?><a class="btn btn-primary js-whatsapp" data-whatsapp-number="<?= e(preg_replace('/\D+/', '', (string) setting('whatsapp_number'))) ?>" href="<?= e(whatsapp_url('Hi AlbaTech Solutions, I am not sure which service I need. Can we discuss what I am trying to achieve?')) ?>" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-whatsapp"></i> Get Assistance</a><?php endif; ?>
            </div>
        <?php else: ?>
            <div class="catalogue-empty"><i class="fa-solid fa-layer-group" aria-hidden="true"></i><h2>Services are being updated</h2><p>Tell us what you need and we can help you find the right service.</p><a class="btn btn-primary" href="/get-help"><i class="fa-solid fa-hand-holding-heart"></i> Get Assistance</a></div>
        <?php endif; ?>
    </div>
</section>

<section class="phase1-final-cta">
    <div class="public-container"><div class="phase1-final-cta__inner"><div><span class="public-kicker" >Not sure what you need?</span><h2>Start with the problem, not the service.</h2><p>Explain what you are trying to achieve. We can figure out the right technical solution together.</p></div><?php if (setting('whatsapp_number')): ?><a class="btn btn-primary btn-lg" data-whatsapp-number="<?= e(preg_replace('/\D+/', '', (string) setting('whatsapp_number'))) ?>" href="<?= e(whatsapp_url('Hi AlbaTech Solutions, I have a problem or idea I would like to discuss.')) ?>" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-whatsapp"></i> Get Assistance</a><?php else: ?><a class="btn btn-primary btn-lg" href="/contact">Contact AlbaTech</a><?php endif; ?></div></div>
</section>
<script>
(() => {
    const toolbar = document.querySelector('[data-catalogue-toolbar]');
    if (!toolbar) return;
    const buttons = [...toolbar.querySelectorAll('[data-service-filter]')];
    const input = toolbar.querySelector('[data-service-search]');
    const groups = [...document.querySelectorAll('[data-service-group]')];
    const empty = document.querySelector('[data-service-no-results]');
    let active = 'all';
    const apply = () => {
        const term = (input?.value || '').trim().toLowerCase();
        let visible = 0;
        groups.forEach(group => {
            const categoryMatch = active === 'all' || group.dataset.category === active;
            let groupVisible = 0;
            group.querySelectorAll('[data-service-card]').forEach(card => {
                const match = categoryMatch && (!term || card.dataset.serviceName.includes(term));
                card.hidden = !match;
                if (match) groupVisible++;
            });
            group.hidden = groupVisible === 0;
            visible += groupVisible;
        });
        if (empty) empty.hidden = visible !== 0;
    };
    buttons.forEach(button => button.addEventListener('click', () => {
        active = button.dataset.serviceFilter;
        buttons.forEach(item => item.setAttribute('aria-pressed', item === button ? 'true' : 'false'));
        apply();
    }));
    input?.addEventListener('input', apply);
})();
</script>
<?php
$pageContent = ob_get_clean();
require __DIR__ . '/layout.php';
