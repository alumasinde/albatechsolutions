<?php use AlbaTech\Core\View; ?>

<section class="home-hero">
    <div class="container home-hero__grid">
        <div class="home-hero__copy">
            <span class="eyebrow"><span class="eyebrow__dot" aria-hidden="true"></span> Practical digital solutions in Kenya</span>
            <h1>Make technology work <span>for you.</span></h1>
            <p class="home-hero__lead">From getting your business online to sorting out everyday digital tasks, we help you find a clear, practical way forward.</p>
            <div class="hero-actions">
                <a class="button button--whatsapp" href="<?= View::escape($whatsAppUrl ?? '/get-help') ?>">Talk to us on WhatsApp <span aria-hidden="true">↗</span></a>
                <a class="button button--secondary" href="/services">Explore services <span aria-hidden="true">→</span></a>
            </div>
            <div class="home-hero__proof">
                <span><span aria-hidden="true">✓</span> Clear communication</span>
                <span><span aria-hidden="true">✓</span> Scope agreed upfront</span>
                <span><span aria-hidden="true">✓</span> Solutions for Kenyan needs</span>
            </div>
        </div>
        <div class="home-hero__visual" aria-label="Digital solutions for business">
            <div class="hero-orbit hero-orbit--one"></div>
            <div class="hero-orbit hero-orbit--two"></div>
            <div class="hero-dashboard">
                <div class="hero-dashboard__top"><span class="hero-dashboard__brand"><span>A</span> AlbaTech</span><span class="hero-dashboard__dots">•••</span></div>
                <div class="hero-dashboard__welcome">Your next step starts here</div>
                <div class="hero-dashboard__feature"><span class="hero-dashboard__icon">↗</span><div><strong>Build your online presence</strong><small>Websites & digital services</small></div><span class="hero-dashboard__arrow">→</span></div>
                <div class="hero-dashboard__feature"><span class="hero-dashboard__icon hero-dashboard__icon--gold">⌘</span><div><strong>Solve a technical challenge</strong><small>Practical IT support</small></div><span class="hero-dashboard__arrow">→</span></div>
                <div class="hero-dashboard__footer"><span class="hero-dashboard__status"></span> Let's make it simpler</div>
            </div>
            <div class="hero-float hero-float--top"><span>✦</span> Built around your needs</div>
            <div class="hero-float hero-float--bottom"><span>✓</span> Clear next steps</div>
        </div>
    </div>
</section>

<section class="section home-services-section">
    <div class="container">
        <div class="section-heading section-heading--split">
            <div><span class="section-kicker">Explore our services</span><h2>What can we help you get done?</h2></div>
            <p>Choose a service to see what is included, what you may need and how to get started.</p>
        </div>

        <?php if (!empty($featuredServices)): ?>
            <div class="home-service-grid">
                <?php foreach ($featuredServices as $service): ?>
                    <article class="home-service-card">
                        <div class="home-service-card__top">
                            <span class="home-service-card__icon" aria-hidden="true"><?= View::escape(strtoupper(substr((string) $service['name'], 0, 1))) ?></span>
                            <span class="home-service-card__category"><?= View::escape($service['category_name']) ?></span>
                        </div>
                        <h3><?= View::escape($service['name']) ?></h3>
                        <p><?= View::escape($service['short_description']) ?></p>
                        <div class="service-price">
                            <?php if (in_array((string) $service['pricing_type'], ['fixed', 'starting_at'], true) && $service['pricing_amount'] !== null && $service['pricing_amount'] !== ''): ?>
                                <span class="service-price__amount"><?= $service['pricing_type'] === 'starting_at' ? 'From ' : '' ?>KSh <?= View::escape(number_format((float) $service['pricing_amount'], ((float) $service['pricing_amount'] == (int) $service['pricing_amount']) ? 0 : 2)) ?></span>
                            <?php else: ?>
                                <span class="service-price__quote"><?= View::escape($service['pricing_label'] ?: 'Contact for price') ?></span>
                            <?php endif; ?>
                        </div>
                        <a class="service-card-link" href="/services/<?= rawurlencode($service['slug']) ?>">View service details <span aria-hidden="true">→</span></a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="home-empty-featured">
                <div class="home-empty-featured__icon" aria-hidden="true">✦</div>
                <div><h3>Let's find the right service for you</h3><p>Browse the full catalogue or tell us what you need help with.</p></div>
                <a class="button button--secondary" href="/services">Browse all services</a>
            </div>
        <?php endif; ?>

        <div class="section-footer-link"><a class="text-link" href="/services">View all services <span aria-hidden="true">→</span></a></div>
    </div>
</section>

<section class="section section--tint home-process-section">
    <div class="container">
        <div class="section-heading section-heading--center">
            <span class="section-kicker">Simple from start to finish</span>
            <h2>Good work starts with a clear conversation.</h2>
            <p>You do not need to know the technical details. Tell us the outcome you want, and we can work through the options together.</p>
        </div>
        <div class="process-grid">
            <article class="process-card"><span class="process-card__number">01</span><span class="process-card__icon" aria-hidden="true">☏</span><h3>Tell us what you need</h3><p>Describe the task, challenge or idea in your own words.</p></article>
            <article class="process-card"><span class="process-card__number">02</span><span class="process-card__icon" aria-hidden="true">◎</span><h3>Agree on the plan</h3><p>We confirm the scope, price and expected next steps before work begins.</p></article>
            <article class="process-card"><span class="process-card__number">03</span><span class="process-card__icon" aria-hidden="true">✓</span><h3>Move forward</h3><p>We work on the agreed task and keep communication clear along the way.</p></article>
        </div>
    </div>
</section>

<section class="section home-why-section">
    <div class="container home-why-grid">
        <div class="home-why-copy"><span class="section-kicker">The AlbaTech approach</span><h2>Technology should make life easier, not more complicated.</h2><p>We focus on practical solutions, plain language and realistic expectations. Whether you are building a business presence or need help with a digital task, we start by understanding what you are trying to achieve.</p><a class="text-link" href="/about">Learn about AlbaTech <span aria-hidden="true">→</span></a></div>
        <div class="home-why-points">
            <div class="why-point"><span class="why-point__check">✓</span><div><h3>Practical, not complicated</h3><p>Recommendations that fit the task and your situation.</p></div></div>
            <div class="why-point"><span class="why-point__check">✓</span><div><h3>Transparent expectations</h3><p>Understand the scope and charges before agreeing to proceed.</p></div></div>
            <div class="why-point"><span class="why-point__check">✓</span><div><h3>Easy to reach</h3><p>Start with a simple enquiry and get help deciding the next step.</p></div></div>
        </div>
    </div>
</section>

<section class="home-cta">
    <div class="container home-cta__inner">
        <div><span class="eyebrow eyebrow--light">Ready when you are</span><h2>Have a task in mind?</h2><p>Tell us what you are trying to do. We will help you work out a sensible next step.</p></div>
        <a class="button button--accent" href="<?= View::escape($whatsAppUrl ?? '/get-help') ?>">Start a conversation <span aria-hidden="true">↗</span></a>
    </div>
</section>

<section class="independent-note independent-note--home"><div class="container independent-note__inner"><span class="independent-note__icon" aria-hidden="true">i</span><p><strong>Our service promise</strong> We explain the scope and charges before starting. For government-related assistance, AlbaTech Solutions is an independent service provider, not a government agency. Official decisions remain with the relevant authority.</p></div></section>
