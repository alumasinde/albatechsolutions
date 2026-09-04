<?php
$title = setting('seo_default_title', 'Digital Assistance in Kenya | AlbaTech Solutions');
$metaDescription = setting('seo_default_description', 'AlbaTech Solutions helps Kenyans get things done online — government and business services, documents, CVs, websites and practical digital solutions.');
$canonicalUrl = rtrim(config('app.url'), '/') . '/';
$jsonLd = [
    [
        '@context'=>'https://schema.org',
        '@type'=>'WebPage',
        'name'=>$title,
        'url'=>$canonicalUrl,
        'description'=>$metaDescription,
        'about'=>[
            '@type'=>'Thing',
            'name'=>'Digital assistance services in Kenya'
        ]
    ]
];
ob_start();
?>
<section class="phase4-hero">
  <div class="public-container phase4-hero__grid">
    <div class="phase4-hero__copy">
      <span class="phase4-eyebrow">Digital help for everyday life in Kenya</span>
      <h1>Need to get something done online?</h1>
      <p>Tell AlbaTech what you're trying to do. We help with online applications, government and business services, documents, CVs — and, when your business needs more, websites and software.</p>
      <div class="phase4-hero__actions">
        <?php if (setting('whatsapp_number')): ?>
          <a class="btn btn-primary btn-lg" href="/get-help"><i class="fa-solid fa-hand-holding-heart"></i> Get Assistance</a>
        <?php endif; ?>
        <a class="btn btn-hero-ghost btn-lg" href="/services">Browse Services <i class="fa-solid fa-arrow-right"></i></a>
      </div>
      <div class="phase4-hero__signals">
        <span><i class="fa-solid fa-circle-check"></i> Human assistance</span>
        <span><i class="fa-solid fa-mobile-screen-button"></i> Mobile-first</span>
        <span><i class="fa-solid fa-comments"></i> WhatsApp-first</span>
      </div>
    </div>
    <div class="phase4-hero__visual" aria-label="AlbaTech digital assistance">
      <div class="phase4-window">
        <div class="phase4-window__bar"><span></span><span></span><span></span><b>albatech</b></div>
        <div class="phase4-window__content">
          <div class="phase4-window__label">WHAT DO YOU NEED HELP WITH?</div>
          <strong>Tell us the task.<br>We'll help with the next step.</strong>
          <div class="phase4-window__cards"><span>KRA & tax</span><span>eCitizen</span><span>Business</span><span>CVs</span></div>
        </div>
      </div>
      <div class="phase4-float phase4-float--one"><i class="fa-solid fa-hand-holding-heart"></i><span>Practical help</span></div>
      
    </div>
  </div>
</section>

<section class="public-section" id="help">
  <div class="public-container">
    <div class="phase4-section-head">
      <div><span class="public-kicker">Start here</span><h2>What are you trying to do?</h2><p>You don't need to know the name of the service. Pick the problem you're trying to solve.</p></div>
    </div>
    <div class="v3-intent-grid">
      <a class="v3-intent-card" href="/get-help?category=government">
        <span class="v3-intent-card__icon"><i class="fa-solid fa-landmark"></i></span><h3>Government & online services</h3><p>KRA, eCitizen, SHA, NSSF, NTSA and selected online applications.</p>
      </a>
      <a class="v3-intent-card" href="/get-help?category=business">
        <span class="v3-intent-card__icon"><i class="fa-solid fa-store"></i></span><h3>Start or sort out my business</h3><p>Business registration, Google presence, email, websites and digital tools.</p>
      </a>
      <a class="v3-intent-card" href="/get-help?category=documents">
        <span class="v3-intent-card__icon"><i class="fa-solid fa-file-lines"></i></span><h3>Documents & applications</h3><p>CVs, forms, document preparation and practical online application help.</p>
      </a>
      <a class="v3-intent-card" href="/get-help?category=website">
        <span class="v3-intent-card__icon"><i class="fa-solid fa-globe"></i></span><h3>Get my business online</h3><p>Websites, domains, hosting, business email and Google Business Profile setup.</p>
      </a>
      <a class="v3-intent-card" href="/get-help?category=software">
        <span class="v3-intent-card__icon"><i class="fa-solid fa-gears"></i></span><h3>Build a system</h3><p>Custom software, automation, integrations and business workflows.</p>
      </a>
      <a class="v3-intent-card" href="/contact">
        <span class="v3-intent-card__icon"><i class="fa-solid fa-question"></i></span><h3>I'm not sure</h3><p>Just explain what you're stuck on. We'll help you identify the right next step.</p>
      </a>
    </div>
  </div>
</section>

<section class="public-section public-section--muted">
  <div class="public-container">
    <div class="v3-answer">
      <strong>What is AlbaTech Solutions?</strong>
      <span>AlbaTech Solutions is a Kenya-based digital assistance and technology business. We help people complete practical online tasks and help businesses build the digital tools they need.</span>
    </div>
    <div class="v3-trust">
      <div><strong>Human help</strong><span>Talk to a person, not a confusing dashboard.</span></div>
      <div><strong>Clear process</strong><span>We explain what is needed before work starts.</span></div>
      <div><strong>Kenya-focused</strong><span>Services and guides designed around Kenyan digital services.</span></div>
      <div><strong>Technology when useful</strong><span>Websites and software come after the problem is understood.</span></div>
    </div>
  </div>
</section>

<?php if (!empty($featuredServices)): ?>
<section class="public-section" id="services">
  <div class="public-container">
    <div class="phase4-section-head">
      <div><span class="public-kicker">Popular help</span><h2>Common things we help people do.</h2><p>Start with a specific service or contact us if you are not sure what you need.</p></div>
      <a href="/services" class="btn btn-secondary">View all services <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <div class="phase4-service-grid">
      <?php foreach ($featuredServices as $service): ?>
        <a class="phase4-service-card" href="/services/<?= e($service['slug']) ?>">
          <span class="phase4-service-card__icon"><i class="fa-solid <?= e($service['icon'] ?: 'fa-circle-check') ?>"></i></span>
          <h3><?= e($service['name']) ?></h3>
          <p><?= e($service['summary'] ?? '') ?></p>
          <span class="phase4-arrow">How it works <i class="fa-solid fa-arrow-right"></i></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="public-section">
  <div class="public-container">
    <div class="v3-help-strip">
      <div><span class="public-kicker">Not sure where to start?</span><h2>Just tell us what you're trying to do.</h2><p>No technical language required. We'll help you work out the next step.</p></div>
      <?php if (setting('whatsapp_number')): ?><a class="btn btn-primary btn-lg js-whatsapp" data-whatsapp-number="<?= e(preg_replace('/\D+/', '', (string) setting('whatsapp_number'))) ?>" href="<?= e(whatsapp_url('Hi AlbaTech Solutions, I am not sure what service I need. Here is what I am trying to do: ')) ?>" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-whatsapp"></i> Ask for help</a><?php endif; ?>
    </div>
    <div class="v3-disclaimer"><strong>Important:</strong> AlbaTech provides independent digital assistance. We are not a government agency. Official government fees, requirements and processing times are set by the relevant agency.</div>
  </div>
</section>
<?php
$pageContent = ob_get_clean();
require __DIR__ . '/layout.php';
