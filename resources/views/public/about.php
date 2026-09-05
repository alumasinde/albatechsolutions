<?php
$title = 'About — ' . setting('site_name', 'AlbaTech Solutions');
$metaDescription = 'Learn how ' . setting('site_name', 'AlbaTech Solutions') . ' approaches websites, custom software and practical digital solutions for businesses.';
$canonicalUrl = rtrim(config('app.url'), '/') . '/about';
$jsonLd = [
    \App\Core\Seo::breadcrumbs([
        ['name' => 'Home', 'url' => rtrim(config('app.url'), '/') . '/'],
        ['name' => 'About', 'url' => $canonicalUrl],
    ]),
];
ob_start();
?>
<section class="conversion-about-hero">
    <div class="public-container conversion-about-hero__grid">
        <div>
            <a href="/" class="conversion-back" aria-label="Back to home"><i class="fa-solid fa-arrow-left"></i><span>Back home</span></a>
            <span class="public-kicker">About AlbaTech</span>
            <h1>Technology should make business <em>easier.</em></h1>
            <p class="conversion-lead">We build practical digital solutions for businesses and people who need technology to do a real job — not just look impressive.</p>
            <div class="conversion-hero-actions">
                <a class="btn btn-primary btn-lg" href="/get-help">Get Assistance <i class="fa-solid fa-arrow-right"></i></a>
                <a href="/services" class="btn btn-secondary btn-lg">Explore services <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
        <div class="conversion-about-hero__panel" aria-label="AlbaTech approach">
            <div class="phase6-orbit"><span>PLAN</span><span>BUILD</span><span>IMPROVE</span></div>
            <div class="conversion-about-hero__core"><i class="fa-solid fa-code"></i><strong>ALBATECH</strong><span>Digital solutions</span></div>
        </div>
    </div>
</section>

<section class="public-section conversion-trust-strip">
    <div class="public-container conversion-stats">
        <div><strong><?= (int) ($serviceCount ?? 0) ?>+</strong><span>Services available</span></div>
        <div><strong>01</strong><span>Direct point of contact</span></div>
        <div><strong>KE</strong><span>Based in Kenya</span></div>
    </div>
</section>

<section class="public-section">
    <div class="public-container conversion-story-grid">
        <div><span class="public-kicker">The approach</span><h2>Small enough to listen. Technical enough to build.</h2></div>
        <div class="conversion-story-copy">
            <p>AlbaTech is built around a simple idea: understand the problem first, then choose the technology that actually helps.</p>
            <p>That means clear communication, sensible scope, responsive interfaces and systems designed around how people really work.</p>
            <p>You don't need to arrive with a technical specification. Bring the idea, the business problem or the process you want improved. We can work out the solution together.</p>
        </div>
    </div>
</section>

<section class="public-section public-section--muted">
    <div class="public-container">
        <div class="conversion-section-heading"><span class="public-kicker">Why work with AlbaTech</span><h2>A straightforward way to build.</h2></div>
        <div class="conversion-trust-grid">
            <article><span class="conversion-trust-icon"><i class="fa-solid fa-comments"></i></span><h3>Direct communication</h3><p>Talk directly about the work instead of getting lost in layers of communication.</p></article>
            <article><span class="conversion-trust-icon"><i class="fa-solid fa-bullseye"></i></span><h3>Problem-first thinking</h3><p>The goal is a useful outcome. Technology is selected around the problem, not the other way around.</p></article>
            <article><span class="conversion-trust-icon"><i class="fa-solid fa-mobile-screen-button"></i></span><h3>Built for real users</h3><p>Responsive, understandable interfaces that work across the devices your customers actually use.</p></article>
            <article><span class="conversion-trust-icon"><i class="fa-solid fa-shield-halved"></i></span><h3>Careful engineering</h3><p>Reusable components, validation, security-conscious patterns and maintainable code.</p></article>
        </div>
    </div>
</section>

<?php if (!empty($testimonials)): ?>
<section class="public-section public-section--muted">
    <div class="public-container">
        <div class="conversion-section-heading"><span class="public-kicker">Client perspective</span><h2>What good collaboration looks like.</h2></div>
        <div class="phase6-testimonials">
            <?php foreach ($testimonials as $testimonial): ?>
                <figure><div class="phase6-quote-mark">“</div><blockquote><?= e($testimonial['quote'] ?? $testimonial['content'] ?? '') ?></blockquote><figcaption><strong><?= e($testimonial['name'] ?? 'Client') ?></strong><?php if (!empty($testimonial['role'])): ?><span><?= e($testimonial['role']) ?></span><?php endif; ?></figcaption></figure>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="phase6-final-cta">
    <div class="public-container"><div class="phase6-final-cta__inner"><div><span class="public-kicker">Ready when you are</span><h2>Have something you want to build?</h2><p>Start with a conversation. Explain what you need and we'll take it from there.</p></div><div class="phase6-final-cta__actions"><a class="btn btn-primary btn-lg" href="/get-help"><i class="fa-solid fa-hand-holding-heart"></i> Get Assistance</a><a class="btn btn-ghost-light btn-lg" href="/contact">Contact us</a></div></div></div>
</section>
<?php
$pageContent = ob_get_clean();
require __DIR__ . '/layout.php';
