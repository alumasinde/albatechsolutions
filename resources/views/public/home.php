<?php
$title = 'Practical Digital & IT Help in Kenya | AlbaTech Solutions';
$metaDescription = 'Get practical help with KRA, eCitizen, business tasks, CVs, websites, computers and IT in Kenya. Tell us the task. We’ll help with the next step.';
$canonicalUrl = rtrim(config('app.url'), '/') . '/';
$jsonLd = [[
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => $title,
    'url' => $canonicalUrl,
    'description' => $metaDescription,
]];
$quickServiceSlugs = ['kra-returns-filing','ecitizen-services','business-registration','cv-writing','website-design-kenya','computer-repair','it-support'];
$quickServicesBySlug = [];
foreach (($homepageServices ?? []) as $service) $quickServicesBySlug[(string) $service['slug']] = $service;
$quickServices = [];
foreach ($quickServiceSlugs as $slug) if (isset($quickServicesBySlug[$slug])) $quickServices[] = $quickServicesBySlug[$slug];
$digitalItServices = array_values(array_filter(($homepageServices ?? []), static fn(array $service): bool =>
    in_array((string) ($service['slug'] ?? ''), ['website-design-kenya','computer-repair','it-support','google-business-profile-setup','wifi-networking','cctv-installation','domain-hosting-business-email','software-development'], true)
));
ob_start();
?>
<section class="home-hero">
  <div class="public-container home-hero__grid">
    <div class="home-hero__copy">
      <span class="ui-kicker">Practical help for people and businesses in Kenya</span>
      <h1>Tell us the task. We’ll help with the next step.</h1>
      <p class="home-hero__lead">Need help with an online task, business step, CV, website, computer or IT problem? Start by telling us what you are trying to get done.</p>
      <div class="home-actions">
        <a class="btn btn-primary btn-lg" href="/get-help"><i class="fa-solid fa-hand-holding-heart" aria-hidden="true"></i> Get Assistance</a>
        <a class="btn btn-secondary btn-lg" href="/services">Browse Services <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
      </div>
      <ul class="home-hero__signals" aria-label="How AlbaTech helps">
        <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Clear next steps</li>
        <li><i class="fa-solid fa-comments" aria-hidden="true"></i> WhatsApp-friendly</li>
        <li><i class="fa-solid fa-user" aria-hidden="true"></i> Human help</li>
      </ul>
    </div>
    <aside class="home-hero__task-card ui-card" aria-label="Start with your task">
      <span class="ui-kicker">Start with the task</span>
      <strong>What do you need help with?</strong>
      <p>You do not need to know the exact service name before contacting us.</p>
      <a href="/get-help" class="home-hero__task-link">Explain what you need <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
    </aside>
  </div>
</section>

<section class="public-section home-section" id="help">
  <div class="public-container">
    <div class="home-section-head">
      <div>
        <span class="ui-kicker">Quick task entry</span>
        <h2>What are you trying to get done?</h2>
        <p>Choose the closest task. If yours is different, simply tell us what you need.</p>
      </div>
      <a class="home-text-link" href="/services">See all services <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
    </div>
    <div class="home-task-grid">
      <?php foreach ($quickServices as $service): ?>
        <a class="home-task-card ui-card ui-card--interactive" href="/services/<?= e($service['slug']) ?>">
          <span class="home-task-card__icon"><i class="fa-solid <?= e($service['icon'] ?: 'fa-circle-check') ?>" aria-hidden="true"></i></span>
          <h3><?= e($service['name']) ?></h3>
          <p><?= e($service['summary'] ?? '') ?></p>
          <span>See how we can help <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
        </a>
      <?php endforeach; ?>
      <a class="home-task-card home-task-card--help ui-card ui-card--interactive" href="/get-help">
        <span class="home-task-card__icon"><i class="fa-solid fa-question" aria-hidden="true"></i></span>
        <h3>Not sure where to start?</h3>
        <p>Explain the task in simple words and we will help you find the next step.</p>
        <span>Get Assistance <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
      </a>
    </div>
  </div>
</section>

<section class="public-section public-section--muted home-section" id="how-it-works">
  <div class="public-container">
    <div class="home-section-head home-section-head--center">
      <div>
        <span class="ui-kicker">How it works</span>
        <h2>Simple from the first message.</h2>
        <p>Start with the task. We will help you understand what happens next.</p>
      </div>
    </div>
    <div class="home-steps">
      <article class="home-step ui-card"><span>1</span><h3>Tell us what you need</h3><p>Use the Get Assistance page or WhatsApp and explain what you are trying to do.</p></article>
      <article class="home-step ui-card"><span>2</span><h3>We check the next step</h3><p>We review the task and explain what information, documents or service may be needed.</p></article>
      <article class="home-step ui-card"><span>3</span><h3>We guide or assist you</h3><p>We confirm the practical next step and the assistance process before work starts.</p></article>
    </div>
  </div>
</section>

<?php if (!empty($featuredServices)): ?>
<section class="public-section home-section" id="popular-services">
  <div class="public-container">
    <div class="home-section-head">
      <div><span class="ui-kicker">Featured services</span><h2>Start with a service people ask us about.</h2><p>Each service explains what we help with, who it is for and what happens next.</p></div>
      <a href="/services" class="btn btn-secondary">Browse Services <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
    </div>
    <div class="home-service-grid">
      <?php foreach ($featuredServices as $service): ?>
        <a class="home-service-card ui-card ui-card--interactive" href="/services/<?= e($service['slug']) ?>">
          <span class="home-task-card__icon"><i class="fa-solid <?= e($service['icon'] ?: 'fa-circle-check') ?>" aria-hidden="true"></i></span>
          <h3><?= e($service['name']) ?></h3>
          <p><?= e($service['summary'] ?? '') ?></p>
          <span class="home-card-link">See service details <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if (!empty($digitalItServices)): ?>
<section class="public-section public-section--muted home-section" id="digital-it">
  <div class="public-container">
    <div class="home-section-head">
      <div><span class="ui-kicker">Digital & IT</span><h2>Get your business online or keep technology working.</h2><p>Practical web, computer and IT services for people and businesses.</p></div>
      <a href="/services" class="home-text-link">Explore services <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
    </div>
    <div class="home-service-grid">
      <?php foreach (array_slice($digitalItServices, 0, 6) as $service): ?>
        <a class="home-service-card ui-card ui-card--interactive" href="/services/<?= e($service['slug']) ?>">
          <h3><?= e($service['name']) ?></h3>
          <p><?= e($service['summary'] ?? '') ?></p>
          <span class="home-card-link">Learn more <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="public-section home-section">
  <div class="public-container">
    <div class="home-why">
      <div><span class="ui-kicker">Why AlbaTech</span><h2>Helpful without making the task feel harder.</h2><p>We focus on understanding what you are trying to get done, explaining the practical next step and making it easy to continue the conversation.</p></div>
      <div class="home-why__points">
        <article><i class="fa-solid fa-circle-check" aria-hidden="true"></i><div><h3>Clear help</h3><p>Simple communication about the task and next step.</p></div></article>
        <article><i class="fa-solid fa-arrow-right" aria-hidden="true"></i><div><h3>Practical next steps</h3><p>We help turn a confusing task into an organised next action.</p></div></article>
        <article><i class="fa-brands fa-whatsapp" aria-hidden="true"></i><div><h3>WhatsApp-friendly</h3><p>Start where it is convenient for you and continue the conversation easily.</p></div></article>
      </div>
    </div>
  </div>
</section>

<?php if (!empty($recentPosts)): ?>
<section class="public-section public-section--muted home-section" id="guides">
  <div class="public-container">
    <div class="home-section-head">
      <div><span class="ui-kicker">Helpful guides</span><h2>Understand the task before you start.</h2><p>Simple guides for common online, business and technology tasks.</p></div>
      <a href="/blog" class="btn btn-secondary">View all guides <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
    </div>
    <div class="home-guide-grid">
      <?php foreach ($recentPosts as $post): ?>
        <a class="home-guide-card ui-card ui-card--interactive" href="/blog/<?= e($post['slug']) ?>">
          <span class="ui-kicker"><?= e($post['category_name'] ?? 'Guide') ?></span>
          <h3><?= e($post['title']) ?></h3>
          <p><?= e($post['excerpt'] ?? $post['meta_description'] ?? 'Read the guide for practical next steps.') ?></p>
          <span class="home-card-link">Read guide <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if (!empty($faqs)): ?>
<section class="public-section home-section" id="homepage-faqs">
  <div class="public-container">
    <div class="home-section-head">
      <div><span class="ui-kicker">Common questions</span><h2>Before you get in touch.</h2><p>Quick answers to common questions about getting help from AlbaTech.</p></div>
      <a href="/faqs" class="home-text-link">See all FAQs <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
    </div>
    <div class="home-faq-list">
      <?php foreach (array_slice($faqs, 0, 5) as $faq): ?>
        <details class="home-faq ui-card"><summary><?= e($faq['question']) ?><i class="fa-solid fa-plus" aria-hidden="true"></i></summary><p><?= e($faq['answer']) ?></p></details>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="public-section home-section">
  <div class="public-container">
    <div class="home-disclaimer">
      <strong>Independent help, not a government office.</strong>
      <p>AlbaTech Solutions provides independent assistance and technology services. We are not a government agency. Official requirements, fees and processing times are set by the relevant institution.</p>
    </div>
    <div class="home-final-cta ui-card">
      <div><span class="ui-kicker">Ready to start?</span><h2>Need help with a task?</h2><p>Tell us what you are trying to get done. We will help with the next step.</p></div>
      <div class="home-actions">
        <a class="btn btn-primary btn-lg" href="/get-help">Get Assistance</a>
        <?php if (setting('whatsapp_number')): ?><a class="btn btn-secondary btn-lg js-whatsapp" data-whatsapp-number="<?= e(preg_replace('/\D+/', '', (string) setting('whatsapp_number'))) ?>" href="<?= e(whatsapp_url('Hi AlbaTech Solutions, I need help with the following task: ')) ?>" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WhatsApp</a><?php endif; ?>
      </div>
    </div>
  </div>
</section>
<?php
$pageContent = ob_get_clean();
require __DIR__ . '/layout.php';
