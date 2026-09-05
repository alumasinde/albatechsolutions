<?php
$title = $service['meta_title'] ?: ($service['name'] . ' in Kenya');
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
$requirementsList = $service['requirements_list'] ?? [];
$features = json_decode((string)($service['features'] ?? '[]'), true);
$features = is_array($features) ? array_values(array_filter(array_map('strval', $features))) : [];
$faqItems = is_array($service['faq_items'] ?? null) ? $service['faq_items'] : [];
if ($faqItems) $jsonLd[] = \App\Core\Seo::faqPage($faqItems);
$pricingMode = $service['commerce_pricing_mode'] ?? $service['price_type'] ?? 'quote';
$hasCommerceFee = isset($service['customer_fee']) && $service['customer_fee'] !== null && $pricingMode !== 'quote' && $pricingMode !== 'free';
$hasPrice = $hasCommerceFee || (($service['price_type'] ?? 'quote') !== 'quote' && isset($service['price']));
$displayPrice = $hasCommerceFee ? (float)$service['customer_fee'] : (float)($service['price'] ?? 0);
$turnaround = null;
if (!empty($service['turnaround_min_days']) || !empty($service['turnaround_max_days'])) {
    $min=(int)($service['turnaround_min_days']??0); $max=(int)($service['turnaround_max_days']??0);
    $days=$max?:$min; $turnaround=$min&&$max&&$min!==$max ? "$min–$max working days" : ($days . ' working ' . ($days===1?'day':'days'));
}
$isGovernmentRelated = in_array((string)$service['slug'], ['kra-returns-filing','ecitizen-services','business-registration','cr12-application','sha-registration','nssf-registration','ntsa-services'], true);
ob_start();
?>
<section class="service-detail-hero">
 <div class="public-container">
  <a href="/services" class="service-detail-back"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Back to services</a>
  <div class="service-detail-hero__grid">
   <div>
    <span class="ui-kicker"><?= e($service['category_name'] ?: 'Service') ?></span>
    <h1><?= e($service['name']) ?></h1>
    <p><?= e($service['summary'] ?: 'Tell us what you need and we will help you understand the practical next step.') ?></p>
   </div>
   <div class="service-detail-hero__actions">
    <a class="btn btn-primary btn-lg" href="/get-help?service_id=<?= (int)$service['id'] ?>">Get Assistance</a>
    <?php if (setting('whatsapp_number')): ?><button type="button" class="btn btn-secondary btn-lg js-whatsapp-service" data-service-name="<?= e($service['name']) ?>" data-whatsapp-number="<?= e(preg_replace('/\D+/', '', (string)setting('whatsapp_number'))) ?>"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WhatsApp</button><?php endif; ?>
   </div>
  </div>
 </div>
</section>

<section class="public-section service-detail-section">
 <div class="public-container service-detail-layout">
  <main class="service-detail-main">
   <section class="service-detail-block" id="what-we-help-with">
    <span class="ui-kicker">What we help with</span>
    <h2><?= e($service['name']) ?>, explained simply.</h2>
    <div class="service-detail-copy">
      <?= !empty($service['description']) ? $service['description'] : '<p>Tell us what you are trying to get done. We will review the task and explain the practical next step.</p>' ?>
    </div>
    <?php if ($features): ?><ul class="service-detail-checklist"><?php foreach ($features as $feature): ?><li><i class="fa-solid fa-circle-check" aria-hidden="true"></i><?= e($feature) ?></li><?php endforeach; ?></ul><?php endif; ?>
   </section>

   <section class="service-detail-block" id="who-it-is-for">
    <span class="ui-kicker">Who it is for</span>
    <h2>This service may be a good fit if this is your task.</h2>
    <p><?= e($service['summary'] ?: 'If this is close to what you are trying to get done, start by telling us your situation and we will confirm the next step.') ?></p>
    <a class="service-detail-inline-link" href="/get-help?service_id=<?= (int)$service['id'] ?>">Not sure? Explain your task <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
   </section>

   <?php if ($requirementsList): ?>
   <section class="service-detail-block" id="what-you-may-need">
    <span class="ui-kicker">What you may need</span>
    <h2>Prepare these details where they apply.</h2>
    <p>Requirements can depend on your specific situation. We will confirm what is relevant before work starts.</p>
    <ul class="service-detail-requirements"><?php foreach ($requirementsList as $req): ?><li><?= e((string)$req) ?></li><?php endforeach; ?></ul>
   </section>
   <?php endif; ?>

   <section class="service-detail-block service-detail-process" id="how-it-works">
    <span class="ui-kicker">How it works</span>
    <h2>Start with the task. Then take the next step.</h2>
    <div class="service-detail-steps">
     <article><span>1</span><h3>Tell us what you need</h3><p>Send the task through Get Assistance or WhatsApp.</p></article>
     <article><span>2</span><h3>We review what applies</h3><p>We confirm the information, requirements and assistance needed for your situation.</p></article>
     <article><span>3</span><h3>Agree on the next step</h3><p>We explain the process and any applicable AlbaTech assistance fee before work starts.</p></article>
    </div>
   </section>

   <section class="service-detail-block" id="price">
    <span class="ui-kicker">Price guidance</span>
    <h2>Know what to expect before you continue.</h2>
    <?php if ($hasPrice): ?><div class="service-detail-price ui-card"><strong><?= $pricingMode === 'starting_from' ? 'From ' : '' ?>KES <?= number_format($displayPrice, 0) ?></strong><span>AlbaTech assistance fee<?= $pricingMode === 'starting_from' ? ' starting price' : '' ?>.</span></div><?php else: ?><p>This service is priced based on the work involved. Tell us what you need and we will explain the applicable assistance fee before work starts.</p><?php endif; ?>
    <?php if ($turnaround): ?><p class="service-detail-meta"><strong>Typical turnaround:</strong> <?= e($turnaround) ?></p><?php endif; ?>
    <?php if (!empty($service['government_fee_note'])): ?><p class="service-detail-note"><strong>Official fees:</strong> <?= e($service['government_fee_note']) ?></p><?php endif; ?>
    <?php if (!empty($service['fee_disclaimer'])): ?><p class="service-detail-note"><?= e($service['fee_disclaimer']) ?></p><?php endif; ?>
   </section>

   <?php if ($faqItems): ?>
   <section class="service-detail-block" id="faq">
    <span class="ui-kicker">Common questions</span>
    <h2>Questions about <?= e($service['name']) ?></h2>
    <div class="service-detail-faqs"><?php foreach ($faqItems as $faq): ?><details class="service-detail-faq ui-card"><summary><?= e((string)$faq['question']) ?><i class="fa-solid fa-plus" aria-hidden="true"></i></summary><p><?= e((string)$faq['answer']) ?></p></details><?php endforeach; ?></div>
   </section>
   <?php endif; ?>
  </main>

  <aside class="service-detail-sidebar">
   <div class="service-detail-summary ui-card">
    <span class="ui-kicker">Ready to start?</span>
    <h2>Get help with <?= e($service['name']) ?>.</h2>
    <p>You do not need to know every requirement before contacting us.</p>
    <a class="btn btn-primary" href="/get-help?service_id=<?= (int)$service['id'] ?>">Get Assistance</a>
    <?php if (setting('whatsapp_number')): ?><button type="button" class="btn btn-secondary js-whatsapp-service" data-service-name="<?= e($service['name']) ?>" data-whatsapp-number="<?= e(preg_replace('/\D+/', '', (string)setting('whatsapp_number'))) ?>"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WhatsApp</button><?php endif; ?>
    <a href="/services" class="service-detail-text-link">Browse all services</a>
   </div>
  </aside>
 </div>
</section>

<?php if ($isGovernmentRelated): ?>
<section class="public-section service-detail-disclaimer-section"><div class="public-container"><div class="service-detail-disclaimer"><strong>Independent help, not a government office.</strong><p>AlbaTech Solutions provides independent assistance. Official requirements, fees, eligibility and processing times are determined by the relevant authority.</p></div></div></section>
<?php endif; ?>

<?php if (!empty($relatedServices) || !empty($relatedPosts)): ?>
<section class="public-section public-section--muted service-detail-related"><div class="public-container">
 <div class="service-detail-related__head"><span class="ui-kicker">Keep exploring</span><h2>Helpful next steps and related services.</h2></div>
 <?php if (!empty($relatedServices)): ?><div class="service-detail-related-grid"><?php foreach (array_slice($relatedServices,0,3) as $other): ?><a href="/services/<?= e($other['slug']) ?>" class="service-detail-related-card ui-card ui-card--interactive"><span class="service-detail-related-card__icon"><i class="fa-solid <?= e($other['icon'] ?: 'fa-circle-check') ?>" aria-hidden="true"></i></span><h3><?= e($other['name']) ?></h3><p><?= e($other['summary'] ?: '') ?></p><span class="home-card-link">See service <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span></a><?php endforeach; ?></div><?php endif; ?>
 <?php if (!empty($relatedPosts)): ?><div class="service-detail-guides"><h3>Helpful guides</h3><div class="service-detail-related-grid"><?php foreach ($relatedPosts as $post): ?><a href="/blog/<?= e($post['slug']) ?>" class="service-detail-related-card ui-card ui-card--interactive"><span class="ui-kicker"><?= e($post['category_name'] ?? 'Guide') ?></span><h3><?= e($post['title']) ?></h3><p><?= e($post['excerpt'] ?? $post['meta_description'] ?? '') ?></p><span class="home-card-link">Read guide <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span></a><?php endforeach; ?></div></div><?php endif; ?>
</div></section>
<?php endif; ?>

<section class="public-section service-detail-final"><div class="public-container"><div class="service-detail-final__inner ui-card"><div><span class="ui-kicker">Need help?</span><h2>Tell us the task. We’ll help with the next step.</h2><p>You can start with a request or message us on WhatsApp.</p></div><div class="home-actions"><a class="btn btn-primary btn-lg" href="/get-help?service_id=<?= (int)$service['id'] ?>">Get Assistance</a><?php if (setting('whatsapp_number')): ?><button type="button" class="btn btn-secondary btn-lg js-whatsapp-service" data-service-name="<?= e($service['name']) ?>" data-whatsapp-number="<?= e(preg_replace('/\D+/', '', (string)setting('whatsapp_number'))) ?>"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WhatsApp</button><?php endif; ?></div></div></div></section>
<?php
$pageContent = ob_get_clean();
require __DIR__ . '/layout.php';
