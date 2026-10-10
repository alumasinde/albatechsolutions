<?php use AlbaTech\Core\View; ?>
<section class="home-hero">
  <div class="container home-hero__grid">
    <div class="home-hero__copy">
      <span class="eyebrow">Digital services for people and businesses in Kenya</span>
      <h1>Technology that helps you <span>move forward.</span></h1>
      <p class="home-hero__lead">From building a professional website to improving your online business presence, get practical digital assistance with clear communication and no unnecessary complexity.</p>
      <div class="hero-actions">
        <a class="button <?= !empty($hasWhatsApp) ? 'button--whatsapp' : 'button--secondary' ?>" href="<?= View::escape($whatsAppUrl ?? '/get-help') ?>"><?php if (!empty($hasWhatsApp)): ?><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Chat on WhatsApp<?php else: ?><i class="fa-regular fa-envelope" aria-hidden="true"></i> Send an enquiry<?php endif; ?></a>
        <a class="button button--secondary" href="/services">Explore services</a>
      </div>
      <div class="home-hero__meta">
        <span><i class="fa-solid fa-location-dot" aria-hidden="true"></i> Based in Kenya</span>
        <span><i class="fa-solid fa-receipt" aria-hidden="true"></i> Clear scope and pricing</span>
        <span><i class="fa-solid fa-handshake" aria-hidden="true"></i> Support for real business needs</span>
      </div>
    </div>
    <aside class="home-hero__visual" aria-label="AlbaTech Solutions">
      <img src="/assets/images/albatech-social-card.png" alt="AlbaTech Solutions - Digital services for Kenya" class="home-hero__image" width="400" height="300">
    </aside>
  </div>
</section>

<section class="section home-services-section">
  <div class="container">
    <div class="section-heading section-heading--split"><div><span class="section-kicker">Services</span><h2>What can we help you get done?</h2></div><p>Explore the available services, understand what's included and see the price where one is listed.</p></div>
    <?php if (!empty($featuredServices)): ?>
      <div class="home-service-grid">
      <?php foreach ($featuredServices as $service):
        $serviceName = strtolower((string) ($service['name'] ?? ''));
        $iconClass = str_contains($serviceName, 'website') || str_contains($serviceName, 'landing') || str_contains($serviceName, 'web') ? 'fa-laptop-code' : (str_contains($serviceName, 'cv') || str_contains($serviceName, 'career') ? 'fa-file-lines' : (str_contains($serviceName, 'kra') || str_contains($serviceName, 'registration') || str_contains($serviceName, 'government') ? 'fa-file-circle-check' : (str_contains($serviceName, 'business system') || str_contains($serviceName, 'automation') ? 'fa-gears' : 'fa-layer-group')));
      ?>
        <article class="home-service-card">
          <div class="home-service-card__top"><span class="home-service-card__icon"><i class="fa-solid <?= $iconClass ?>" aria-hidden="true"></i></span><span class="home-service-card__category"><?= View::escape($service['category_name']) ?></span></div>
          <h3><?= View::escape($service['name']) ?></h3><p><?= View::escape($service['short_description']) ?></p>
          <div class="service-price">
          <?php if (in_array((string) $service['pricing_type'], ['fixed', 'starting_at'], true) && $service['pricing_amount'] !== null && $service['pricing_amount'] !== ''): ?>
            <span class="service-price__amount"><?= $service['pricing_type'] === 'starting_at' ? 'From ' : '' ?>KSh <?= View::escape(number_format((float) $service['pricing_amount'], ((float) $service['pricing_amount'] == (int) $service['pricing_amount']) ? 0 : 2)) ?></span>
          <?php else: ?><span class="service-price__quote"><?= View::escape($service['pricing_label'] ?: 'Contact for price') ?></span><?php endif; ?>
          </div>
          <a class="service-card-link" href="/services/<?= rawurlencode($service['slug']) ?>">View service details <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </article>
      <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="home-empty-featured"><span class="home-empty-featured__icon"><i class="fa-solid fa-compass" aria-hidden="true"></i></span><div><h3>Let's find the right service for you</h3><p>Browse the full catalogue or tell us what you need help with.</p></div><a class="button button--secondary" href="/services">Browse all services</a></div>
    <?php endif; ?>
    <div class="section-footer-link"><a class="text-link" href="/services">View all services <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div>
  </div>
</section>

<section class="section section--tint home-process-section"><div class="container">
  <div class="section-heading section-heading--center"><span class="section-kicker">How it works</span><h2>A straightforward way to get started.</h2><p>You do not need to know the technical details. Tell us the outcome you want, and we can work through the options together.</p></div>
  <div class="process-grid">
    <article class="process-card"><span class="process-card__number">01</span><span class="process-card__icon"><i class="fa-regular fa-comments" aria-hidden="true"></i></span><h3>Tell us what you need</h3><p>Describe the task, challenge or idea in your own words.</p></article>
    <article class="process-card"><span class="process-card__number">02</span><span class="process-card__icon"><i class="fa-solid fa-clipboard-list" aria-hidden="true"></i></span><h3>Agree on the plan</h3><p>We confirm the scope, price and expected next steps before work begins.</p></article>
    <article class="process-card"><span class="process-card__number">03</span><span class="process-card__icon"><i class="fa-solid fa-laptop-file" aria-hidden="true"></i></span><h3>Move forward</h3><p>We work on the agreed task and keep communication clear along the way.</p></article>
  </div>
</div></section>

<section class="section home-why-section"><div class="container home-why-grid">
  <div class="home-why-copy"><span class="section-kicker">The AlbaTech approach</span><h2>Technology should make life easier, not more complicated.</h2><p>We focus on practical solutions, plain language and realistic expectations. Whether you are building a business presence or need help with a digital task, we start by understanding what you are trying to achieve.</p><a class="text-link" href="/about">Learn about AlbaTech <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div>
  <div class="home-why-points">
    <div class="why-point"><span class="why-point__icon"><i class="fa-solid fa-list-check" aria-hidden="true"></i></span><div><h3>Practical, not complicated</h3><p>Recommendations that fit the task and your situation.</p></div></div>
    <div class="why-point"><span class="why-point__icon"><i class="fa-solid fa-receipt" aria-hidden="true"></i></span><div><h3>Transparent expectations</h3><p>Understand the scope and charges before agreeing to proceed.</p></div></div>
    <div class="why-point"><span class="why-point__icon"><i class="fa-regular fa-message" aria-hidden="true"></i></span><div><h3>Easy to reach</h3><p>Start with a simple enquiry and get help deciding the next step.</p></div></div>
  </div>
</div></section>

<section class="section home-recent-work">
  <div class="container">
    <div class="section-heading">
      <span class="section-kicker">Work areas</span>
      <h2>Types of projects we handle</h2>
      <p>From websites to business processes, we focus on practical outcomes for businesses and individuals in Kenya.</p>
    </div>
    <div class="recent-work-grid">
      <article class="case-study-card">
        <div class="case-study-card__image">
          <div class="case-study-placeholder"><i class="fa-solid fa-laptop-code"></i></div>
        </div>
        <div class="case-study-card__content">
          <span class="case-study-card__category">Website Design</span>
          <h3>Business Websites</h3>
          <p>Professional, mobile-friendly websites that help customers understand your business and contact you easily.</p>
        </div>
      </article>
      <article class="case-study-card">
        <div class="case-study-card__image">
          <div class="case-study-placeholder"><i class="fa-solid fa-file-circle-check"></i></div>
        </div>
        <div class="case-study-card__content">
          <span class="case-study-card__category">Business Registration</span>
          <h3>Company Setup</h3>
          <p>Guidance through business registration, tax compliance setup, and understanding regulatory requirements.</p>
        </div>
      </article>
      <article class="case-study-card">
        <div class="case-study-card__image">
          <div class="case-study-placeholder"><i class="fa-solid fa-globe"></i></div>
        </div>
        <div class="case-study-card__content">
          <span class="case-study-card__category">Online Presence</span>
          <h3>Digital Visibility</h3>
          <p>Google Business Profile setup, online directory listings, and improving how customers find your business.</p>
        </div>
      </article>
    </div>
    <div class="section-footer-link"><a class="text-link" href="/services">Explore all services <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div>
  </div>
</section>

<section class="home-cta"><div class="container home-cta__inner"><div><span class="eyebrow eyebrow--light">Let's talk</span><h2>Have a task in mind?</h2><p>Tell us what you are trying to do. We will help you work out a sensible next step.</p></div><a class="button button--accent" href="<?= View::escape($whatsAppUrl ?? '/get-help') ?>"><?php if (!empty($hasWhatsApp)): ?><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Start a WhatsApp chat<?php else: ?><i class="fa-regular fa-envelope" aria-hidden="true"></i> Send an enquiry<?php endif; ?></a></div></section>

<section class="independent-note independent-note--home"><div class="container independent-note__inner"><span class="independent-note__icon"><i class="fa-solid fa-circle-info" aria-hidden="true"></i></span><p><strong>Our service promise</strong> We explain the scope and charges before starting. For government-related assistance, AlbaTech Solutions is an independent service provider, not a government agency. Official decisions remain with the relevant authority.</p></div></section>
