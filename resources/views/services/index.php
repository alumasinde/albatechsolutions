<?php

use AlbaTech\Core\View;
/**
 * @var array<string, mixed> $categories
 */
?>
<?php if (isset($breadcrumbs) && is_array($breadcrumbs) && !empty($breadcrumbs)): ?>
<nav class="breadcrumb-nav" aria-label="Breadcrumb">
  <div class="container">
    <ol class="breadcrumb-list" itemscope itemtype="https://schema.org/BreadcrumbList">
      <?php foreach ($breadcrumbs as $index => $crumb): ?>
        <li class="breadcrumb-item<?= ($index === count($breadcrumbs) - 1) ? ' breadcrumb-item--current' : '' ?>" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
          <?php if (!empty($crumb['url']) && $index < count($breadcrumbs) - 1): ?>
            <a href="<?= View::escape($crumb['url']) ?>" itemprop="item"><span itemprop="name"><?= View::escape($crumb['name']) ?></span></a>
          <?php else: ?>
            <span itemprop="name"><?= View::escape($crumb['name']) ?></span>
          <?php endif; ?>
          <meta itemprop="position" content="<?= $index + 1 ?>">
        </li>
      <?php endforeach; ?>
    </ol>
  </div>
</nav>
<?php endif; ?>
<section class="services-hero">
    <div class="container services-hero__inner">
        <div class="services-hero__copy">
            <span class="eyebrow">What we can help with</span>
            <h1>Website Design, Business Registration & Digital Services in Kenya</h1>
            <p>From getting your business online to navigating selected digital processes, find practical support for the task in front of you.</p>
            <div class="hero-actions">
                <a class="button" href="#service-catalogue">Explore services <i class="fa-solid fa-arrow-down" aria-hidden="true"></i></a>
                <a class="button button--secondary" href="/get-help">Not sure where to start?</a>
            </div>
            <div class="services-trust-line">
                <span><i class="fa-solid fa-file-circle-check" aria-hidden="true"></i> Clear scope</span>
                <span><i class="fa-solid fa-mobile-screen-button" aria-hidden="true"></i> Mobile-friendly solutions</span>
            </div>
        </div>
        <div class="services-hero__visual" aria-hidden="true">
            <div class="service-visual-card service-visual-card--main">
                <span class="service-visual-card__icon"><i class="fa-solid fa-laptop-code" aria-hidden="true"></i></span>
                <span class="service-visual-card__label">Your next step</span>
                <strong>From idea<br>to done.</strong>
                <div class="service-visual-card__line"><span></span></div>
            </div>
            <div class="service-visual-chip service-visual-chip--top">Web & digital presence</div>
            <div class="service-visual-chip service-visual-chip--bottom">Business assistance</div>
            <div class="service-visual-dot service-visual-dot--one"></div>
            <div class="service-visual-dot service-visual-dot--two"></div>
        </div>
    </div>
</section>

<section class="section services-catalogue" id="service-catalogue">
    <div class="container">
        <div class="catalogue-heading">
            <div>
                <span class="section-kicker">Service catalogue</span>
                <h2>Find the support you need</h2>
                <p>Choose a category to explore services and see what each one includes.</p>
            </div>
            <a class="catalogue-help-link" href="/get-help">Need guidance? Talk to us →</a>
        </div>

        <?php if ($categories === []): ?>
            <div class="empty-state">
                <span class="empty-state__icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></span>
                <h3>Our service catalogue is being updated.</h3>
                <p>Contact us and tell us what you are looking to achieve.</p>
                <a class="button" href="/get-help">Get in touch</a>
            </div>
        <?php else: ?>
            <?php foreach ($categories as $categoryIndex => $category): ?>
                <section class="service-category service-category--designed" aria-labelledby="category-<?= View::escape($category['slug']) ?>">
                    <div class="service-category__heading">
                        <div class="service-category__index"><?= str_pad((string) ($categoryIndex + 1), 2, '0', STR_PAD_LEFT) ?></div>
                        <div class="service-category__intro">
                            <h3 id="category-<?= View::escape($category['slug']) ?>"><?= View::escape($category['name']) ?></h3>
                            <?php if (!empty($category['description'])): ?>
                                <p><?= View::escape($category['description']) ?></p>
                            <?php endif; ?>
                        </div>
                        <span class="service-category__count"><?= count($category['services']) ?> <?= count($category['services']) === 1 ? 'service' : 'services' ?></span>
                    </div>

                    <?php if ($category['services'] !== []): ?>
                        <div class="service-card-grid">
                            <?php foreach ($category['services'] as $service): ?>
                                <article class="service-tile">
                                    <div class="service-tile__top">
                                        <span class="service-tile__symbol"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
                                        <span class="service-tile__arrow"><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></span>
                                    </div>
                                    <h4><?= View::escape($service['name']) ?></h4>
                                    <p><?= View::escape($service['short_description'] ?? '') ?></p>
                                    <div class="service-price">
                                        <?php if (in_array((string) ($service['pricing_type'] ?? 'quote'), ['fixed', 'starting_at'], true) && ($service['pricing_amount'] ?? null) !== null && $service['pricing_amount'] !== ''): ?>
                                            <span class="service-price__amount"><?= ($service['pricing_type'] ?? '') === 'starting_at' ? 'From ' : '' ?>KSh <?= View::escape(number_format((float) $service['pricing_amount'], ((float) $service['pricing_amount'] == (int) $service['pricing_amount']) ? 0 : 2)) ?></span>
                                        <?php else: ?>
                                            <span class="service-price__quote"><?= View::escape(($service['pricing_label'] ?? '') ?: 'Contact for price') ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <a href="/services/<?= rawurlencode($service['slug']) ?>" class="service-tile__link" aria-label="View service: <?= View::escape($service['name']) ?>">View service <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="empty-category">Services in this category will be announced soon.</p>
                    <?php endif; ?>
                </section>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<section class="independent-note">
    <div class="container independent-note__inner">
        <span class="independent-note__icon"><i class="fa-solid fa-circle-info" aria-hidden="true"></i></span>
        <p><strong>Important to know</strong> AlbaTech Solutions is an independent service provider, not a government agency. Government applications and registrations are subject to the relevant authority's requirements, processing times and decisions.</p>
    </div>
</section>

<section class="services-bottom-cta">
    <div class="container services-bottom-cta__inner">
        <div>
            <span class="eyebrow eyebrow--light">Have a different task in mind?</span>
            <h2>Let's talk about what you need.</h2>
            <p>Tell us what you're trying to achieve. We'll help you identify a sensible next step.</p>
        </div>
        <a class="button button--accent" href="/get-help">Contact us <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
    </div>
</section>