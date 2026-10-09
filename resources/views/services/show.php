<?php use AlbaTech\Core\View; ?>

<section class="page-intro">
    <div class="container">
        <a class="breadcrumb" href="/services">← All services</a>
        <span class="eyebrow"><?= View::escape($service['category_name']) ?></span>
        <h1><?= View::escape($service['name']) ?></h1>
        <p><?= View::escape($service['short_description'] ?? '') ?></p>
        <?php if (!empty($service['pricing_label'])): ?>
            <p class="price-label"><?= View::escape($service['pricing_label']) ?></p>
        <?php endif; ?>
        <div class="hero-actions">
            <a class="button button--whatsapp" href="/get-help?service=<?= rawurlencode($service['slug']) ?>">Enquire about this service</a>
            <a class="button button--secondary" href="/services">Browse other services</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container service-detail">
        <article class="service-copy">
            <h2>About this service</h2>
            <div class="prose"><?= nl2br(View::escape($service['description'] ?? 'Contact us to discuss your requirements and confirm the scope of assistance.')) ?></div>

            <?php if (!empty($service['who_for'])): ?>
                <h2>Who this is for</h2>
                <div class="prose"><?= nl2br(View::escape($service['who_for'])) ?></div>
            <?php endif; ?>

            <?php if (!empty($service['what_included'])): ?>
                <h2>How we can help</h2>
                <div class="prose"><?= nl2br(View::escape($service['what_included'])) ?></div>
            <?php endif; ?>

            <?php if (!empty($service['what_needed'])): ?>
                <h2>What you may need</h2>
                <div class="prose"><?= nl2br(View::escape($service['what_needed'])) ?></div>
            <?php endif; ?>
        </article>

        <aside class="service-aside">
            <div class="card">
                <h2>Let’s discuss your needs</h2>
                <p>Tell us what you need. We will confirm requirements, scope and any charges before proceeding.</p>
                <a class="button" href="/get-help?service=<?= rawurlencode($service['slug']) ?>">Request assistance</a>
            </div>
            <?php if ((string) $service['category_slug'] === 'government-business-assistance'): ?>
                <p class="notice">AlbaTech Solutions is independent and is not affiliated with a government agency. Final decisions and processing times are determined by the relevant authority.</p>
            <?php endif; ?>
        </aside>
    </div>
</section>
