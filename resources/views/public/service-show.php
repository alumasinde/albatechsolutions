<?php
$title = $service['meta_title'] ?: ($service['name'] . ' in Kenya — ' . setting('site_name', 'AlbaTech Solutions'));
$metaDescription = $service['meta_description'] ?: ($service['summary'] ?: setting('seo_default_description', ''));
$canonicalUrl = rtrim(config('app.url'), '/') . '/services/' . $service['slug'];
$jsonLd = [
    \App\Core\Seo::service($service),
    \App\Core\Seo::breadcrumbs([
        ['name' => 'Home', 'url' => rtrim(config('app.url'), '/') . '/'],
        ['name' => 'Services', 'url' => rtrim(config('app.url'), '/') . '/services'],
        ['name' => $service['name'], 'url' => $canonicalUrl],
    ]),
];
$whatsappMessage = 'Hi AlbaTech Solutions, I am interested in: ' . $service['name'] . '.';
$commerceRequirements = json_decode((string)($service['commerce_requirements'] ?? $service['requirements'] ?? '[]'), true);
$intakeQuestions = json_decode((string)($service['intake_questions'] ?? '[]'), true);
$hasCommerceFee = isset($service['customer_fee']) && $service['customer_fee'] !== null && ($service['commerce_pricing_mode'] ?? 'quote') !== 'quote' && ($service['commerce_pricing_mode'] ?? '') !== 'free';
$hasPrice = $hasCommerceFee || (($service['price_type'] ?? 'quote') !== 'quote' && isset($service['price']));
$pricingMode = $service['commerce_pricing_mode'] ?? $service['price_type'] ?? 'quote';
$displayPrice = $hasCommerceFee ? (float)$service['customer_fee'] : (float)($service['price'] ?? 0);
$turnaround = null;
if (!empty($service['turnaround_min_days']) || !empty($service['turnaround_max_days'])) {
    $min = (int)($service['turnaround_min_days'] ?? 0); $max = (int)($service['turnaround_max_days'] ?? 0);
    $turnaround = $min && $max && $min !== $max ? "$min–$max working days" : (($max ?: $min) . ' working ' . (($max ?: $min) === 1 ? 'day' : 'days'));
}
$analyticsPageType = 'service';
$analyticsEntityId = (int)$service['id'];
ob_start();
?>
<section class="detail-hero">
    <div class="public-container">
        <a href="/services" class="phase1-back"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Back to services</a>
        <div class="detail-hero__row">
            <div>
                <span class="catalogue-eyebrow"><i class="fa-solid <?= e($service['icon'] ?: 'fa-circle-check') ?>" aria-hidden="true"></i> <?= e($service['category_name'] ?: 'Service') ?></span>
                <h1><?= e($service['name']) ?></h1>
                <?php if (!empty($service['summary'])): ?><p><?= e($service['summary']) ?></p><?php endif; ?>
            </div>
            <div class="detail-hero__actions">
                <a class="btn btn-primary btn-lg" href="/get-help?service_id=<?= (int)$service['id'] ?>">Get Assistance <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                <?php if (setting('whatsapp_number')): ?><button type="button" class="btn btn-secondary btn-lg js-whatsapp-service" data-service-name="<?= e($service['name']) ?>" data-whatsapp-number="<?= e(preg_replace('/\D+/', '', (string) setting('whatsapp_number'))) ?>"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WhatsApp</button><?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="public-section">
    <div class="public-container">
        <div class="v3-answer">
            <strong>Need help with <?= e($service['name']) ?>?</strong>
            <span><?= e($service['summary'] ?: 'We can explain the requirements, guide you through the process and help with the digital steps involved.') ?></span>
        </div>
    </div>
    <div class="public-container detail-layout">
        <article class="detail-main">
            <div class="detail-copy">
                <?php if (!empty($service['description'])): ?><?= $service['description'] ?><?php else: ?><h2>Let's talk about what you need</h2><p>This service is shaped around your actual requirements. Send me a message and we can discuss the scope, timeline and the best way to approach it.</p><?php endif; ?>
            </div>
        </article>
        <aside class="detail-sidebar">
            <div class="detail-cta">
                <span class="detail-cta__eyebrow">Service details</span>
                <h2>What to expect</h2>
                <?php if ($hasPrice): ?><div class="detail-meta"><div><strong>AlbaTech fee</strong><span><?= $pricingMode === 'starting_from' ? 'From ' : '' ?>KES <?= number_format($displayPrice, 0) ?></span></div></div><?php endif; ?>
                <?php if ($turnaround): ?><div class="detail-meta"><div><strong>Typical turnaround</strong><span><?= e($turnaround) ?></span></div></div><?php endif; ?>
                <?php if (!empty($service['government_fee_note'])): ?><p class="detail-note"><strong>Official fees:</strong> <?= e($service['government_fee_note']) ?></p><?php endif; ?>
                <?php if (!empty($service['fee_disclaimer'])): ?><p class="detail-note"><?= e($service['fee_disclaimer']) ?></p><?php endif; ?>
                <?php if (is_array($commerceRequirements) && $commerceRequirements): ?><h3>What we may need</h3><ul class="detail-list"><?php foreach ($commerceRequirements as $req): ?><li><?= e((string)$req) ?></li><?php endforeach; ?></ul><?php endif; ?>
            </div>
        </aside>
        <aside class="detail-sidebar">
            <div class="detail-cta">
                <span class="detail-cta__eyebrow">Interested?</span>
                <h2>Let's discuss <?= e($service['name']) ?>.</h2>
                <p>No complicated checkout. Tell us what you need and we'll work out the right next step together.</p>
                <?php if ($hasPrice): ?><div class="detail-meta"><div><strong>Pricing</strong><span><?= $pricingMode === 'starting_from' ? 'From ' : '' ?>KES <?= number_format($displayPrice, 0) ?></span></div></div><?php endif; ?>
                <a href="/get-help?service_id=<?= (int)$service['id'] ?>" class="btn btn-primary btn-lg">Get Assistance</a>
                <?php if (setting('whatsapp_number')): ?><button type="button" class="btn btn-secondary btn-lg js-whatsapp-service" data-service-name="<?= e($service['name']) ?>" data-whatsapp-number="<?= e(preg_replace('/\D+/', '', (string) setting('whatsapp_number'))) ?>"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WhatsApp</button><?php endif; ?>
            </div>
        </aside>
    </div>
    <?php if (is_array($intakeQuestions) && $intakeQuestions): ?>
    <div class="public-container service-detail-note-wrap"><div class="v3-answer"><strong>We'll ask a few service-specific questions.</strong><span>When you request help, AlbaTech will ask only the details relevant to <?= e($service['name']) ?>. Never send passwords, PINs or OTPs.</span></div></div>
    <?php endif; ?>
    <div class="public-container">
      <div class="v3-disclaimer"><strong>Independent assistance:</strong> AlbaTech is not a government agency. Official fees, eligibility, requirements and processing times are determined by the relevant authority.</div>
    </div>
</section>

<?php if (!empty($relatedServices)): ?>
<section class="public-section">
    <div class="public-container">
        <div class="public-section__head-copy related-services-head"><span class="catalogue-eyebrow">Keep exploring</span><h2>Other services you might need</h2></div>
        <div class="catalogue-grid">
            <?php foreach (array_slice($relatedServices, 0, 3) as $other): ?>
                <a href="/services/<?= e($other['slug']) ?>" class="catalogue-card"><div class="catalogue-card__top"><span class="catalogue-card__icon"><i class="fa-solid <?= e($other['icon'] ?: 'fa-circle-check') ?>" aria-hidden="true"></i></span></div><h3><?= e($other['name']) ?></h3><p><?= e($other['summary'] ?: 'A practical digital service tailored to your needs.') ?></p><div class="catalogue-card__bottom"><span class="catalogue-card__price">Explore service</span><span class="catalogue-card__link">View <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span></div></a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="phase1-final-cta"><div class="public-container"><div class="phase1-final-cta__inner"><div><span class="public-kicker" >Still deciding?</span><h2>Tell us what you are trying to achieve.</h2><p>You don't have to know the technical answer before contacting us.</p></div><a class="btn btn-primary btn-lg" href="/get-help?service_id=<?= (int)$service['id'] ?>">Get Assistance <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div></div></section>
<?php
$pageContent = ob_get_clean();
require __DIR__ . '/layout.php';
