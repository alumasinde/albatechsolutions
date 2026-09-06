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
        <div class="conversion-about-hero__content">
            <a href="/" class="conversion-back" aria-label="Back to home"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i><span>Back home</span></a>
            <span class="public-kicker">About AlbaTech</span>
            <h1>Tell us the task.<br>We'll help with the <em>next step.</em></h1>
            <p class="conversion-lead">AlbaTech Solutions provides practical help with digital services, IT tasks and online business needs. We keep the conversation simple and focus on what you are trying to get done.</p>
            <div class="conversion-hero-actions">
                <a class="btn btn-primary btn-lg" href="/get-help">Get Assistance <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                <a href="/services" class="btn btn-secondary btn-lg">Browse Services</a>
            </div>
        </div>

        <aside class="about-journey-card" aria-label="How AlbaTech helps">
            <span class="public-kicker">How we help</span>
            <h2>Simple from the first message.</h2>
            <ol class="about-journey">
                <li><span>1</span><div><strong>Tell us the task</strong><p>Explain what you are trying to do in simple words.</p></div></li>
                <li><span>2</span><div><strong>We check the next step</strong><p>We look at the task and explain whether and how we can help.</p></div></li>
                <li><span>3</span><div><strong>You decide how to proceed</strong><p>Get assistance, ask a question or continue on WhatsApp.</p></div></li>
            </ol>
        </aside>
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
        <div class="conversion-section-heading"><span class="public-kicker">Why use AlbaTech</span><h2>A straightforward way to get help.</h2></div>
        <div class="conversion-trust-grid">
            <article><span class="conversion-trust-icon"><i class="fa-solid fa-comments" aria-hidden="true"></i></span><h3>Clear communication</h3><p>Tell us what you need in simple words and get a clear response about the next step.</p></article>
            <article><span class="conversion-trust-icon"><i class="fa-solid fa-bullseye" aria-hidden="true"></i></span><h3>Task-first thinking</h3><p>We start with the task, not with unnecessary technical terms or complicated packages.</p></article>
            <article><span class="conversion-trust-icon"><i class="fa-solid fa-mobile-screen-button" aria-hidden="true"></i></span><h3>Easy to reach</h3><p>Many requests start on a phone, so the journey is designed to be simple and WhatsApp-friendly.</p></article>
        </div>
    </div>
</section>

<?php if (!empty($testimonials)): ?>
<section class="public-section">
    <div class="public-container">
        <div class="conversion-section-heading"><span class="public-kicker">Client perspective</span><h2>What people say about working with us.</h2></div>
        <div class="conversion-testimonials">
            <?php foreach ($testimonials as $testimonial): ?>
                <figure><blockquote>“<?= e($testimonial['quote'] ?? $testimonial['content'] ?? '') ?>”</blockquote><figcaption><strong><?= e($testimonial['name'] ?? 'Client') ?></strong><?php if (!empty($testimonial['role'])): ?><span><?= e($testimonial['role']) ?></span><?php endif; ?></figcaption></figure>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="conversion-final-cta">
    <div class="public-container">
        <div class="conversion-final-cta__inner">
            <div><span class="public-kicker">Ready when you are</span><h2>Need help with a task?</h2><p>Tell us what you are trying to do. We will help you understand the next step.</p></div>
            <div class="conversion-final-cta__actions">
                <a class="btn btn-primary btn-lg" href="/get-help"><i class="fa-solid fa-hand-holding-heart" aria-hidden="true"></i> Get Assistance</a>
                <?php if (setting('whatsapp_number')): ?><a class="btn btn-secondary btn-lg" href="<?= e(whatsapp_url('Hi AlbaTech Solutions, I need help with a task.')) ?>" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WhatsApp</a><?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php
$pageContent = ob_get_clean();
require __DIR__ . '/layout.php';
