<?php

use AlbaTech\Core\View;
$homeContent = is_array($homeContent ?? null) ? $homeContent : [];
$ctaItems = is_array($ctaItems ?? null) ? $ctaItems : [];
?>
<section class="home-hero">
  <div class="container home-hero__grid">
    <div class="home-hero__copy">
      <span class="eyebrow"><?= View::escape($homeContent['home_hero_eyebrow'] ?? 'Digital services for people and businesses in Kenya') ?></span>
      <h1><?= View::escape($homeContent['home_hero_title'] ?? 'Technology that helps you move forward.') ?></h1>
      <p class="home-hero__lead"><?= View::escape($homeContent['home_hero_lead'] ?? 'From building a professional website to improving your online business presence, get practical digital assistance with clear communication and no unnecessary complexity.') ?></p>
      <div class="hero-actions">
        <a class="button <?= !empty($hasWhatsApp) ? 'button--whatsapp' : 'button--secondary' ?>" href="<?= View::escape(!empty($hasWhatsApp) ? $whatsAppUrl : ($ctaItems['primary_enquiry']['url'] ?? ($homeContent['home_hero_primary_url'] ?? '/get-help'))) ?>"><?php if (!empty($hasWhatsApp)): ?><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Chat on WhatsApp<?php else: ?><i class="fa-regular fa-envelope" aria-hidden="true"></i> <?= View::escape($ctaItems['primary_enquiry']['label'] ?? ($homeContent['home_hero_primary_label'] ?? 'Send an enquiry')) ?><?php endif; ?></a>
        <a class="button button--secondary" href="<?= View::escape($ctaItems['explore_services']['url'] ?? ($homeContent['home_hero_secondary_url'] ?? '/services')) ?>"><?= View::escape($ctaItems['explore_services']['label'] ?? ($homeContent['home_hero_secondary_label'] ?? 'Explore services')) ?></a>
      </div>
      <div class="home-hero__meta">
        <span><i class="fa-solid fa-receipt" aria-hidden="true"></i> Clear scope and pricing</span>
        <span><i class="fa-solid fa-handshake" aria-hidden="true"></i> Support for real business needs</span>
      </div>
    </div>
    <aside class="home-hero__visual" aria-label="AlbaTech Solutions">
      <img src="<?= View::escape($homeContent['home_hero_image'] ?? '/assets/images/albatech-social-card.png') ?>" alt="<?= View::escape($appName ?? 'AlbaTech Solutions') ?> - Digital services for Kenya" class="home-hero__image" width="400" height="300" loading="eager" fetchpriority="high" decoding="async">
    </aside>
  </div>
</section>

<section class="section home-services-section">
  <div class="container">
    <div class="section-heading section-heading--split">
      <div><span class="section-kicker"><?= View::escape($homeContent['home_services_kicker'] ?? 'Services') ?></span>
        <h2><?= View::escape($homeContent['home_services_title'] ?? 'What can we help you get done?') ?></h2>
      </div>
    </div>
    <?php if (!empty($featuredServices)): ?>
      <div class="home-service-grid">
        <?php foreach ($featuredServices as $service):
          $serviceName = strtolower((string) ($service['name'] ?? ''));
          $iconClass = str_contains($serviceName, 'website') || str_contains($serviceName, 'landing') || str_contains($serviceName, 'web') ? 'fa-laptop-code' : (str_contains($serviceName, 'cv') || str_contains($serviceName, 'career') ? 'fa-file-lines' : (str_contains($serviceName, 'kra') || str_contains($serviceName, 'registration') || str_contains($serviceName, 'government') ? 'fa-file-circle-check' : (str_contains($serviceName, 'business system') || str_contains($serviceName, 'automation') ? 'fa-gears' : 'fa-layer-group')));
        ?>
          <article class="home-service-card">
            <div class="home-service-card__top"><span class="home-service-card__icon"><i class="fa-solid <?= $iconClass ?>" aria-hidden="true"></i></span><span class="home-service-card__category"><?= View::escape($service['category_name']) ?></span></div>
            <h3><?= View::escape($service['name']) ?></h3>
            <p><?= View::escape($service['short_description']) ?></p>
            <div class="service-price">
              <?php if (in_array((string) $service['pricing_type'], ['fixed', 'starting_at'], true) && $service['pricing_amount'] !== null && $service['pricing_amount'] !== ''): ?>
                <span class="service-price__amount"><?= $service['pricing_type'] === 'starting_at' ? 'From ' : '' ?>KSh <?= View::escape(number_format((float) $service['pricing_amount'], ((float) $service['pricing_amount'] == (int) $service['pricing_amount']) ? 0 : 2)) ?></span>
              <?php else: ?><span class="service-price__quote"><?= View::escape($service['pricing_label'] ?: 'Contact for price') ?></span><?php endif; ?>
            </div>
            <a class="service-card-link" href="/services/<?= rawurlencode($service['slug']) ?>">View details </a>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="home-empty-featured"><span class="home-empty-featured__icon"><i class="fa-solid fa-compass" aria-hidden="true"></i></span>
        <div>
          <h3>Let's find the right service for you</h3>
          <p>Browse the full catalogue or tell us what you need help with.</p>
        </div><a class="button button--secondary" href="/services">Browse all services</a>
      </div>
    <?php endif; ?>
    <div class="section-footer-link"><a class="text-link" href="/services">View all services <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div>
  </div>
</section>


<?php if (!empty($featuredProjects)): ?>
  <section class="section home-work-section" aria-labelledby="home-work-title">
    <div class="container">
      <div class="section-heading section-heading--split">
        <div><span class="section-kicker"><?= View::escape($homeContent['home_work_kicker'] ?? 'Selected work') ?></span>
          <h2 id="home-work-title"><?= View::escape($homeContent['home_work_title'] ?? 'A look at what we have built.') ?></h2>
        </div>
        <p><?= View::escape($homeContent['home_work_description'] ?? 'Explore selected projects and practical solutions from AlbaTech Solutions.') ?></p>
      </div>
      <div class="work-carousel" data-work-carousel aria-roledescription="carousel" aria-label="Featured projects">
        <div class="work-carousel__viewport">
          <div class="work-carousel__track" data-carousel-track>
            <?php foreach ($featuredProjects as $project): ?><article class="work-card work-carousel__slide" data-carousel-slide><a class="work-card__image-link" href="/work/<?= rawurlencode($project['slug']) ?>"><img class="work-card__image" src="/<?= View::escape(ltrim($project['cover_image'], '/')) ?>" alt="<?= View::escape($project['title']) ?>" loading="lazy" decoding="async"><span class="work-card__category"><?= View::escape($project['category_name']) ?></span></a>
                <div class="work-card__body">
                  <h3><a href="/work/<?= rawurlencode($project['slug']) ?>"><?= View::escape($project['title']) ?></a></h3>
                  <p><?= View::escape($project['description']) ?></p><a class="text-link" href="/work/<?= rawurlencode($project['slug']) ?>">View project <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </div>
              </article><?php endforeach; ?>
          </div>
        </div>
        <div class="work-carousel__controls">
          <div class="work-carousel__dots" data-carousel-dots aria-label="Choose featured project"></div><button class="button button--secondary button--small" type="button" data-carousel-toggle aria-pressed="false"><i class="fa-solid fa-pause" aria-hidden="true"></i> <span>Pause</span></button>
        </div>
      </div>
      <div class="section-footer-link"><a class="text-link" href="/work">See all work <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div>
    </div>
  </section>
<?php endif; ?>

<section class="section section--tint home-process-section">
  <div class="container">
    <div class="section-heading section-heading--center"><span class="section-kicker"><?= View::escape($homeContent['home_process_kicker'] ?? 'How it works') ?></span>
      <h2><?= View::escape($homeContent['home_process_title'] ?? 'A straightforward way to get started.') ?></h2>
      <p><?= View::escape($homeContent['home_process_description'] ?? 'You do not need to know the technical details. Tell us the outcome you want, and we can work through the options together.') ?></p>
    </div>
    <div class="process-grid">
      <article class="process-card"><span class="process-card__number">01</span><span class="process-card__icon"><i class="fa-regular fa-comments" aria-hidden="true"></i></span>
        <h3><?= View::escape($homeContent['home_step1_title'] ?? 'Tell us what you need') ?></h3>
        <p><?= View::escape($homeContent['home_step1_description'] ?? 'Describe the task, challenge or idea in your own words.') ?></p>
      </article>
      <article class="process-card"><span class="process-card__number">02</span><span class="process-card__icon"><i class="fa-solid fa-clipboard-list" aria-hidden="true"></i></span>
        <h3><?= View::escape($homeContent['home_step2_title'] ?? 'Agree on the plan') ?></h3>
        <p><?= View::escape($homeContent['home_step2_description'] ?? 'We confirm the scope, price and expected next steps before work begins.') ?></p>
      </article>
      <article class="process-card"><span class="process-card__number">03</span><span class="process-card__icon"><i class="fa-solid fa-laptop-file" aria-hidden="true"></i></span>
        <h3><?= View::escape($homeContent['home_step3_title'] ?? 'Move forward with clarity') ?></h3>
        <p><?= View::escape($homeContent['home_step3_description'] ?? 'Know what happens next and what information or action is needed from you.') ?></p>
      </article>
    </div>
  </div>
</section>

<section class="section home-why-section">
  <div class="container home-why-grid">
    <div class="home-why-copy"><span class="section-kicker"><?= View::escape($homeContent['home_why_kicker'] ?? 'The AlbaTech approach') ?></span>
      <h2><?= View::escape($homeContent['home_why_title'] ?? 'Technology should make life easier, not more complicated.') ?></h2>
      <p><?= View::escape($homeContent['home_why_description'] ?? 'We focus on practical solutions, plain language and realistic expectations. Whether you are building a business presence or need help with a digital task, we start by understanding what you are trying to achieve.') ?></p><a class="text-link" href="/about">Learn about AlbaTech <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
    </div>
    <div class="home-why-points">
      <div class="why-point"><span class="why-point__icon"><i class="fa-solid fa-list-check" aria-hidden="true"></i></span>
        <div>
          <h3><?= View::escape($homeContent['home_why1_title'] ?? 'Practical, not complicated') ?></h3>
          <p><?= View::escape($homeContent['home_why1_description'] ?? 'Recommendations that fit the task and your situation.') ?></p>
        </div>
      </div>
      <div class="why-point"><span class="why-point__icon"><i class="fa-solid fa-receipt" aria-hidden="true"></i></span>
        <div>
          <h3><?= View::escape($homeContent['home_why2_title'] ?? 'Transparent expectations') ?></h3>
          <p><?= View::escape($homeContent['home_why2_description'] ?? 'Understand the scope and charges before agreeing to proceed.') ?></p>
        </div>
      </div>
      <div class="why-point"><span class="why-point__icon"><i class="fa-regular fa-message" aria-hidden="true"></i></span>
        <div>
          <h3><?= View::escape($homeContent['home_why3_title'] ?? 'Easy to reach') ?></h3>
          <p><?= View::escape($homeContent['home_why3_description'] ?? 'Start with a simple enquiry and get help deciding the next step.') ?></p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section home-recent-work">
  <div class="container">
    <div class="section-heading">
      <span class="section-kicker"><?= View::escape($homeContent['home_areas_kicker'] ?? 'Work areas') ?></span>
      <h2><?= View::escape($homeContent['home_areas_title'] ?? 'Types of projects we handle') ?></h2>
      <p><?= View::escape($homeContent['home_areas_description'] ?? 'From websites to business processes, we focus on practical outcomes for businesses and individuals in Kenya.') ?></p>
    </div>
    <div class="recent-work-grid">
      <article class="case-study-card">
        <div class="case-study-card__image">
          <div class="case-study-placeholder"><i class="fa-solid fa-laptop-code"></i></div>
        </div>
        <div class="case-study-card__content">
          <span class="case-study-card__category"><?= View::escape($homeContent['home_area1_category'] ?? 'Website Design') ?></span>
          <h3><?= View::escape($homeContent['home_area1_title'] ?? 'Business Websites') ?></h3>
          <p><?= View::escape($homeContent['home_area1_description'] ?? 'Professional, mobile-friendly websites that help customers understand your business and contact you easily.') ?></p>
        </div>
      </article>
      <article class="case-study-card">
        <div class="case-study-card__image">
          <div class="case-study-placeholder"><i class="fa-solid fa-file-circle-check"></i></div>
        </div>
        <div class="case-study-card__content">
          <span class="case-study-card__category"><?= View::escape($homeContent['home_area2_category'] ?? 'Business Registration') ?></span>
          <h3><?= View::escape($homeContent['home_area2_title'] ?? 'Company Setup') ?></h3>
          <p><?= View::escape($homeContent['home_area2_description'] ?? 'Guidance through business registration, tax compliance setup, and understanding regulatory requirements.') ?></p>
        </div>
      </article>
      <article class="case-study-card">
        <div class="case-study-card__image">
          <div class="case-study-placeholder"><i class="fa-solid fa-globe"></i></div>
        </div>
        <div class="case-study-card__content">
          <span class="case-study-card__category"><?= View::escape($homeContent['home_area3_category'] ?? 'Online Presence') ?></span>
          <h3><?= View::escape($homeContent['home_area3_title'] ?? 'Digital Visibility') ?></h3>
          <p><?= View::escape($homeContent['home_area3_description'] ?? 'Google Business Profile setup, online directory listings, and improving how customers find your business.') ?></p>
        </div>
      </article>
    </div>
    <div class="section-footer-link"><a class="text-link" href="/services">Explore all services <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div>
  </div>
</section>

<section class="home-cta">
  <div class="container home-cta__inner">
    <div><span class="eyebrow eyebrow--light"><?= View::escape($homeContent['home_cta_kicker'] ?? 'Talk to us') ?></span>
      <h2><?= View::escape($homeContent['home_cta_title'] ?? 'Have a task in mind?') ?></h2>
      <p><?= View::escape($homeContent['home_cta_description'] ?? 'Tell us what you are trying to do. We will help you work out a sensible next step.') ?></p>
    </div><a class="button button--accent" href="<?= View::escape($whatsAppUrl ?? '/get-help') ?>"><?php if (!empty($hasWhatsApp)): ?><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Start a WhatsApp chat<?php else: ?><i class="fa-regular fa-envelope" aria-hidden="true"></i> Send an enquiry<?php endif; ?></a>
  </div>
</section>

<section class="independent-note independent-note--home">
  <div class="container independent-note__inner"><span class="independent-note__icon"><i class="fa-solid fa-circle-info" aria-hidden="true"></i></span>
    <p><strong>Our service promise</strong> <?= View::escape($homeContent['home_promise'] ?? 'We explain the scope and charges before starting. For government-related assistance, AlbaTech Solutions is an independent service provider, not a government agency. Official decisions remain with the relevant authority.') ?></p>
  </div>
</section>