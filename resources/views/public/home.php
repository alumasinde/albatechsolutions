<?php
$title = 'Digital & IT Services in Kenya | AlbaTech Solutions';
$metaDescription = 'Get practical help in Kenya with KRA returns, eCitizen, business registration, CV writing, websites, IT support and more. Tell us the task.';
$canonicalUrl = rtrim(config('app.url'), '/') . '/';
$jsonLd = [[
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => $title,
    'url' => $canonicalUrl,
    'description' => $metaDescription,
    'about' => ['@type' => 'Thing', 'name' => 'Practical digital and technology services in Kenya']
]];
ob_start();
?>
<section class="phase4-hero">
  <div class="public-container phase4-hero__grid">
    <div class="phase4-hero__copy">
      <span class="phase4-eyebrow">Practical help for people and businesses in Kenya</span>
      <h1>Tell us the task. We'll help with the next step.</h1>
      <p>Need help with KRA returns, eCitizen, business registration, a CV, a website, computer repair or IT support? Start with what you are trying to do.</p>
      <div class="phase4-hero__actions">
        <a class="btn btn-primary btn-lg" href="/get-help"><i class="fa-solid fa-hand-holding-heart"></i> Get help</a>
        <a class="btn btn-hero-ghost btn-lg" href="/services">Browse services <i class="fa-solid fa-arrow-right"></i></a>
      </div>
      <div class="phase4-hero__signals">
        <span><i class="fa-solid fa-circle-check"></i> Clear next steps</span>
        <span><i class="fa-solid fa-comments"></i> WhatsApp-first</span>
        <span><i class="fa-solid fa-user"></i> Human help</span>
      </div>
    </div>
    <div class="phase4-hero__visual" aria-label="Examples of AlbaTech services">
      <div class="phase4-window">
        <div class="phase4-window__bar"><span></span><span></span><span></span><b>albatech</b></div>
        <div class="phase4-window__content">
          <div class="phase4-window__label">WHAT DO YOU NEED HELP WITH?</div>
          <strong>Start with the task.<br>We will guide the next step.</strong>
          <div class="phase4-window__cards"><span>KRA</span><span>eCitizen</span><span>Business</span><span>IT</span></div>
        </div>
      </div>
      <div class="phase4-float phase4-float--one"><i class="fa-solid fa-comments"></i><span>Let's talk</span></div>
    </div>
  </div>
</section>

<section class="public-section" id="help">
  <div class="public-container">
    <div class="phase4-section-head">
      <div>
        <span class="public-kicker">Start here</span>
        <h2>What are you trying to get done?</h2>
        <p>You do not need to know the exact service name. Choose the task closest to yours.</p>
      </div>
    </div>
    <div class="v3-intent-grid">
      <a class="v3-intent-card" href="/services/kra-returns-filing"><span class="v3-intent-card__icon"><i class="fa-solid fa-file-invoice-dollar"></i></span><h3>File my KRA returns</h3><p>KRA returns, nil returns and practical tax compliance help.</p></a>
      <a class="v3-intent-card" href="/services/ecitizen-services"><span class="v3-intent-card__icon"><i class="fa-solid fa-landmark"></i></span><h3>Get help with eCitizen</h3><p>Independent help with selected online government services.</p></a>
      <a class="v3-intent-card" href="/services/business-registration"><span class="v3-intent-card__icon"><i class="fa-solid fa-store"></i></span><h3>Register or support my business</h3><p>Business registration, CR12 and practical business steps.</p></a>
      <a class="v3-intent-card" href="/services/cv-writing"><span class="v3-intent-card__icon"><i class="fa-solid fa-file-lines"></i></span><h3>Improve my CV</h3><p>Clear, professional CV writing for job applications.</p></a>
      <a class="v3-intent-card" href="/services/website-design-kenya"><span class="v3-intent-card__icon"><i class="fa-solid fa-globe"></i></span><h3>Get my business online</h3><p>Website design, domains, hosting, email and digital tools.</p></a>
      <a class="v3-intent-card" href="/get-help"><span class="v3-intent-card__icon"><i class="fa-solid fa-question"></i></span><h3>I am not sure</h3><p>Explain the task in simple words and we will help you find the next step.</p></a>
    </div>
  </div>
</section>

<section class="public-section public-section--muted">
  <div class="public-container">
    <div class="phase4-section-head">
      <div>
        <span class="public-kicker">What we help with</span>
        <h2>Two simple sides of AlbaTech.</h2>
        <p>Practical digital assistance for everyday tasks, plus technology services for people and businesses that need to get online or keep working.</p>
      </div>
    </div>
    <div class="v3-trust">
      <div><strong>Online and business tasks</strong><span>KRA, eCitizen, business registration, CR12, SHA, NSSF, NTSA and CV writing.</span></div>
      <div><strong>Web and business tools</strong><span>Website design, domains, hosting, business email, Google Business Profile and software.</span></div>
      <div><strong>IT and computer help</strong><span>IT support, laptop and desktop repair, Wi-Fi, networking and CCTV.</span></div>
      <div><strong>Clear communication</strong><span>We explain what is needed and what happens next before you move forward.</span></div>
    </div>
  </div>
</section>

<?php if (!empty($featuredServices)): ?>
<section class="public-section" id="popular-services">
  <div class="public-container">
    <div class="phase4-section-head">
      <div><span class="public-kicker">Popular services</span><h2>Start with a service people already ask us about.</h2><p>Each service page explains what we help with, who it is for and what to do next.</p></div>
      <a href="/services" class="btn btn-secondary">View all services <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <div class="phase4-service-grid">
      <?php foreach ($featuredServices as $service): ?>
        <a class="phase4-service-card" href="/services/<?= e($service['slug']) ?>">
          <span class="phase4-service-card__icon"><i class="fa-solid <?= e($service['icon'] ?: 'fa-circle-check') ?>"></i></span>
          <h3><?= e($service['name']) ?></h3>
          <p><?= e($service['summary'] ?? '') ?></p>
          <span class="phase4-arrow">See service details <i class="fa-solid fa-arrow-right"></i></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="public-section public-section--muted" id="how-it-works">
  <div class="public-container">
    <div class="phase4-section-head">
      <div><span class="public-kicker">How it works</span><h2>Simple from the first message.</h2><p>No long forms or technical language required to start.</p></div>
    </div>
    <div class="v3-trust">
      <div><strong>1. Tell us the task</strong><span>Use WhatsApp or the Get Help page and explain what you are trying to do.</span></div>
      <div><strong>2. We check the next step</strong><span>We tell you what information, documents or service may be needed.</span></div>
      <div><strong>3. Agree before work starts</strong><span>We explain the practical next step and any applicable service process before proceeding.</span></div>
      <div><strong>4. Stay updated</strong><span>Your request is tracked so you can follow up and we can keep the work organised.</span></div>
    </div>
  </div>
</section>

<section class="public-section">
  <div class="public-container">
    <div class="v3-answer">
      <strong>Independent help, not a government office.</strong>
      <span>AlbaTech Solutions provides independent assistance and technology services. We are not a government agency. Official requirements, fees and processing times are set by the relevant institution.</span>
    </div>
  </div>
</section>

<?php if (!empty($recentPosts)): ?>
<section class="public-section public-section--muted" id="guides">
  <div class="public-container">
    <div class="phase4-section-head">
      <div><span class="public-kicker">Helpful guides</span><h2>Understand the task before you start.</h2><p>Simple Kenya-focused guides for common online and business tasks.</p></div>
      <a href="/blog" class="btn btn-secondary">View all guides <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <div class="phase4-service-grid">
      <?php foreach ($recentPosts as $post): ?>
        <a class="phase4-service-card" href="/blog/<?= e($post['slug']) ?>">
          <span class="public-kicker"><?= e($post['category_name'] ?? 'Guide') ?></span>
          <h3><?= e($post['title']) ?></h3>
          <p><?= e($post['excerpt'] ?? $post['meta_description'] ?? 'Read the guide for practical next steps.') ?></p>
          <span class="phase4-arrow">Read guide <i class="fa-solid fa-arrow-right"></i></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if (!empty($faqs)): ?>
<section class="public-section" id="homepage-faqs">
  <div class="public-container">
    <div class="phase4-section-head">
      <div><span class="public-kicker">Common questions</span><h2>Before you get in touch.</h2><p>Quick answers to common questions about getting help from AlbaTech.</p></div>
      <a href="/faqs" class="btn btn-secondary">See all FAQs <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <div class="v3-trust">
      <?php foreach ($faqs as $faq): ?>
        <div><strong><?= e($faq['question']) ?></strong><span><?= e($faq['answer']) ?></span></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="public-section">
  <div class="public-container">
    <div class="v3-help-strip">
      <div><span class="public-kicker">Ready to start?</span><h2>Tell us what you need help with.</h2><p>Send a message on WhatsApp or submit your request. Start with the task. We will help with the next step.</p></div>
      <?php if (setting('whatsapp_number')): ?>
        <a class="btn btn-primary btn-lg js-whatsapp" data-whatsapp-number="<?= e(preg_replace('/\D+/', '', (string) setting('whatsapp_number'))) ?>" href="<?= e(whatsapp_url('Hi AlbaTech Solutions, I need help with the following task: ')) ?>" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-whatsapp"></i> Chat on WhatsApp</a>
      <?php else: ?>
        <a class="btn btn-primary btn-lg" href="/get-help">Get help</a>
      <?php endif; ?>
    </div>
    <div class="v3-disclaimer"><strong>Important:</strong> AlbaTech provides independent digital assistance. We are not a government agency. Official government fees, requirements and processing times are set by the relevant agency.</div>
  </div>
</section>
<?php
$pageContent = ob_get_clean();
require __DIR__ . '/layout.php';
