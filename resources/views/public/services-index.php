<?php
$title = 'Services in Kenya | ' . setting('site_name', 'AlbaTech Solutions');
$metaDescription = 'Browse practical assistance, digital and IT services in Kenya. Find the closest task, explore the service, or tell AlbaTech what you need help with.';
$canonicalUrl = rtrim(config('app.url'), '/') . '/services';
$jsonLd = [\App\Core\Seo::breadcrumbs([
    ['name' => 'Home', 'url' => rtrim(config('app.url'), '/') . '/'],
    ['name' => 'Services', 'url' => $canonicalUrl],
])];
$categoryCount = count($categories ?? []);
ob_start();
?>
<section class="services-hero">
  <div class="public-container services-hero__inner">
    <a href="/" class="services-back"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Back home</a>
    <span class="ui-kicker">Services</span>
    <h1>Find the help that matches your task.</h1>
    <p>Choose the closest service below. You do not need to know the exact service name before contacting us.</p>
    <div class="services-hero__actions">
      <a class="btn btn-primary" href="/get-help">Get Assistance</a>
      <?php if (setting('whatsapp_number')): ?><a class="btn btn-secondary js-whatsapp" data-whatsapp-number="<?= e(preg_replace('/\D+/', '', (string) setting('whatsapp_number'))) ?>" href="<?= e(whatsapp_url('Hi AlbaTech Solutions, I need help but I am not sure which service fits my task.')) ?>" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WhatsApp</a><?php endif; ?>
    </div>
  </div>
</section>

<section class="public-section services-section">
  <div class="public-container">
    <?php if ($categoryCount): ?>
      <div class="services-intro">
        <div><span class="ui-kicker">Start with what you need</span><h2>Browse services without guessing.</h2><p>Use a category, search for a task, or open a service to see what we help with and what happens next.</p></div>
      </div>
      <div class="services-toolbar ui-card" data-services-toolbar>
        <div class="services-filter" role="group" aria-label="Filter services">
          <button type="button" aria-pressed="true" data-service-filter="all">All services</button>
          <?php foreach ($categories as $category): ?><button type="button" aria-pressed="false" data-service-filter="<?= e($category['slug']) ?>"><?= e($category['name']) ?></button><?php endforeach; ?>
        </div>
        <label class="services-search"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i><span class="sr-only">Search services</span><input type="search" placeholder="Search for a task or service..." data-service-search autocomplete="off"></label>
      </div>

      <?php foreach ($categories as $category): ?>
        <?php if (empty($category['services'])) continue; ?>
        <section id="<?= e($category['slug']) ?>" class="services-group" data-service-group data-category="<?= e($category['slug']) ?>">
          <div class="services-group__heading">
            <div><span class="ui-kicker"><?= e($category['name']) ?></span><h2><?= e($category['name']) ?></h2><?php if (!empty($category['description'])): ?><p><?= e($category['description']) ?></p><?php endif; ?></div>
            <span class="services-group__count"><?= count($category['services']) ?> <?= count($category['services']) === 1 ? 'service' : 'services' ?></span>
          </div>
          <div class="services-grid">
            <?php foreach ($category['services'] as $service): ?>
              <a href="/services/<?= e($service['slug']) ?>" class="services-card ui-card ui-card--interactive" data-service-card data-service-name="<?= e(strtolower($service['name'] . ' ' . ($service['summary'] ?? '') . ' ' . ($category['name'] ?? ''))) ?>">
                <div class="services-card__top"><span class="services-card__icon"><i class="fa-solid <?= e($service['icon'] ?: 'fa-circle-check') ?>" aria-hidden="true"></i></span><?php if (!empty($service['is_featured'])): ?><span class="services-card__badge">Popular</span><?php endif; ?></div>
                <h3><?= e($service['name']) ?></h3>
                <p><?= e($service['summary'] ?: 'Tell us what you need and we will help you understand the next step.') ?></p>
                <div class="services-card__bottom"><span class="services-card__price"><?php if (($service['price_type'] ?? 'quote') !== 'quote' && isset($service['price'])): ?><?= ($service['price_type'] ?? '') === 'starting_from' ? 'From ' : '' ?>KES <?= number_format((float) $service['price'], 0) ?><?php else: ?>Ask about this service<?php endif; ?></span><span class="home-card-link">See details <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span></div>
              </a>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endforeach; ?>

      <div class="services-empty ui-card" data-service-no-results hidden>
        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i><h2>We could not find that service.</h2><p>Try another search, or explain what you are trying to get done and we will help you find the right next step.</p>
        <a class="btn btn-primary" href="/get-help">Get Assistance</a>
      </div>
    <?php else: ?>
      <div class="services-empty ui-card"><i class="fa-solid fa-layer-group" aria-hidden="true"></i><h2>Services are being updated.</h2><p>Tell us what you need and we will help you find the right service.</p><a class="btn btn-primary" href="/get-help">Get Assistance</a></div>
    <?php endif; ?>
  </div>
</section>

<section class="public-section services-final">
  <div class="public-container">
    <div class="services-final__inner ui-card">
      <div><span class="ui-kicker">Not sure where to start?</span><h2>Start with the task, not the service name.</h2><p>Tell us what you are trying to achieve. We can help you understand the practical next step.</p></div>
      <div class="home-actions"><a class="btn btn-primary btn-lg" href="/get-help">Get Assistance</a><a class="btn btn-secondary btn-lg" href="/contact">Contact us</a></div>
    </div>
  </div>
</section>
<script>
(() => {
 const toolbar=document.querySelector('[data-services-toolbar]'); if(!toolbar)return;
 const buttons=[...toolbar.querySelectorAll('[data-service-filter]')], input=toolbar.querySelector('[data-service-search]'), groups=[...document.querySelectorAll('[data-service-group]')], empty=document.querySelector('[data-service-no-results]'); let active='all';
 const apply=()=>{const term=(input?.value||'').trim().toLowerCase();let visible=0;groups.forEach(group=>{const categoryMatch=active==='all'||group.dataset.category===active;let groupVisible=0;group.querySelectorAll('[data-service-card]').forEach(card=>{const match=categoryMatch&&(!term||card.dataset.serviceName.includes(term));card.hidden=!match;if(match)groupVisible++;});group.hidden=groupVisible===0;visible+=groupVisible;});if(empty)empty.hidden=visible!==0;};
 buttons.forEach(button=>button.addEventListener('click',()=>{active=button.dataset.serviceFilter;buttons.forEach(item=>item.setAttribute('aria-pressed',item===button?'true':'false'));apply();}));input?.addEventListener('input',apply);
})();
</script>
<?php
$pageContent = ob_get_clean();
require __DIR__ . '/layout.php';
