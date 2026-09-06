<?php
$title = 'About Us | ' . setting('site_name', 'AlbaTech Solutions');
$metaDescription = 'Learn about AlbaTech Solutions, an independent Kenya-based service helping people and businesses with practical digital, IT and online tasks.';
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
            <h1>Tell us the task. We'll help with the <em>next step.</em></h1>
            <p class="conversion-lead">AlbaTech Solutions provides practical help with digital services, IT tasks and online business needs. We keep the conversation simple and focus on what you are trying to get done.</p>
            <div class="conversion-hero-actions">
                <a class="btn btn-primary btn-lg" href="/get-help">Get Assistance <i class="fa-solid fa-arrow-right"></i></a>
                <a href="/services" class="btn btn-secondary btn-lg">Explore services <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
        <div class="conversion-about-hero__panel" aria-label="AlbaTech approach">
            <div class="phase6-orbit"><span>ASK</span><span>CHECK</span><span>HELP</span></div>
            <div class="conversion-about-hero__core"><i class="fa-solid fa-code"></i><strong>ALBATECH</strong><span>Practical help</span></div>
        </div>
    </div>
</section>

<section class="public-section conversion-trust-strip">
    <div class="public-container conversion-stats">
        <div><strong><?= (int) ($serviceCount ?? 0) ?>+</strong><span>Services available</span></div>
        <div><strong>01</strong><span>Clear next step</span></div>
        <div><strong>KE</strong><span>Kenya-focused help</span></div>
    </div>
</section>

<section class="public-section">
    <div class="public-container conversion-story-grid">
        <div><span class="public-kicker">The approach</span><h2>Simple help for real tasks.</h2></div>
        <div class="conversion-story-copy">
            <p>AlbaTech is built around a simple idea: understand the task first, then help with the practical next step.</p>
            <p>That can mean help with a digital process, an IT problem, your online business presence, a website or a custom software need.</p>
            <p>You do not need technical language or a detailed specification. Tell us what you are trying to do. We will check whether AlbaTech can help and explain what happens next.</p>
        </div>
    </div>
</section>

<section class="public-section public-section--muted">
    <div class="public-container">
        <div class="conversion-section-heading"><span class="public-kicker">Why work with AlbaTech</span><h2>A straightforward way to get help.</h2></div>
        <div class="conversion-trust-grid">
            <article><span class="conversion-trust-icon"><i class="fa-solid fa-comments"></i></span><h3>Direct communication</h3><p>Tell us what you need in simple words and get a clear response about the next step.</p></article>
            <article><span class="conversion-trust-icon"><i class="fa-solid fa-bullseye"></i></span><h3>Problem-first thinking</h3><p>We start with the task, not with unnecessary technical terms or complicated packages.</p></article>
            <article><span class="conversion-trust-icon"><i class="fa-solid fa-mobile-screen-button"></i></span><h3>Built for real users</h3><p>We keep mobile use and simple communication in mind because many requests start on a phone.</p></article>
            <article><span class="conversion-trust-icon"><i class="fa-solid fa-shield-halved"></i></span><h3>Careful engineering</h3><p>For technical work, we use sensible, security-conscious practices and explain what we are doing.</p></article>
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
    <div class="public-container"><div class="phase6-final-cta__inner"><div><span class="public-kicker">Ready when you are</span><h2>Need help with a task?</h2><p>Tell us what you are trying to do. We will help you understand the next step.</p></div><div class="phase6-final-cta__actions"><a class="btn btn-primary btn-lg" href="/get-help"><i class="fa-solid fa-hand-holding-heart"></i> Get Assistance</a><a class="btn btn-ghost-light btn-lg" href="<?= e(whatsapp_url('Hi AlbaTech Solutions, I need help with a task.')) ?>" target="_blank" rel="noopener noreferrer">WhatsApp</a></div></div></div>
</section>
<?php
$pageContent = ob_get_clean();
require __DIR__ . '/layout.php';
