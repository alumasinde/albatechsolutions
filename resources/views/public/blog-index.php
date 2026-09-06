<?php
$title = 'Guides | ' . setting('site_name', 'AlbaTech Solutions');
$metaDescription = 'Practical guides and helpful information from ' . setting('site_name', 'AlbaTech Solutions') . '.';
$robots = ($page > 1) ? 'noindex, follow' : null;
$canonicalUrl = rtrim(config('app.url'), '/') . '/blog';
$jsonLd = [
    \App\Core\Seo::breadcrumbs([
        ['name' => 'Home', 'url' => rtrim(config('app.url'), '/') . '/'],
        ['name' => 'Guides', 'url' => $canonicalUrl],
    ]),
];
ob_start();
?>
<section class="guides-page">
    <div class="public-container">
        <header class="guides-page__hero">
            <div>
                <span class="public-kicker">Helpful information</span>
                <h1>Guides for common tasks in Kenya</h1>
            </div>
            <p>Simple, practical information to help you understand what you may need and decide on the next step. Guides are for general information and do not replace official instructions.</p>
        </header>

        <?php if (!empty($categories)): ?>
        <nav class="guides-category-nav" aria-label="Guide categories">
            <a href="/blog" class="guides-category-link<?= !$currentCategory ? ' is-active' : '' ?>">All guides</a>
            <?php foreach ($categories as $c): ?>
                <a href="/blog?category=<?= e($c['slug']) ?>" class="guides-category-link<?= $currentCategory === $c['slug'] ? ' is-active' : '' ?>">
                    <?= e($c['name']) ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <?php endif; ?>

        <div class="guides-page__intro">
            <div>
                <span class="public-kicker">Choose a guide</span>
                <h2>Start with the information you need.</h2>
            </div>
            <p>Read the practical steps first. If you still need help with the task, you can <a href="/get-help">get assistance</a> or continue on WhatsApp.</p>
        </div>

        <?php if (!empty($posts)): ?>
        <div class="guides-grid">
            <?php foreach ($posts as $post): ?>
                <article class="post-card">
                    <?php if (!empty($post['category_name'])): ?><span class="public-kicker"><?= e($post['category_name']) ?></span><?php endif; ?>
                    <h2><a href="/blog/<?= e($post['slug']) ?>"><?= e($post['title']) ?></a></h2>
                    <p><?= e($post['excerpt'] ?? '') ?></p>
                    <a class="post-card__link" href="/blog/<?= e($post['slug']) ?>">Read guide <span aria-hidden="true">→</span></a>
                </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <div class="guides-empty"><span class="public-kicker">Coming soon</span><h2>We are preparing helpful guides.</h2><p>If you need help now, tell us what you are trying to do and we will help with the next step.</p><a class="btn btn-primary" href="/get-help">Get Assistance</a></div>
        <?php endif; ?>

        <section class="guides-page__cta">
            <div><span class="public-kicker">Still unsure?</span><h2>Tell us what you are trying to do.</h2><p>We can help you understand whether one of our services fits your task and what happens next.</p></div>
            <div class="guides-page__cta-actions">
                <a class="btn btn-primary" href="/get-help">Get Assistance</a>
                <?php if (setting('whatsapp_number')): ?><a class="btn btn-secondary" href="<?= e(whatsapp_url('Hi AlbaTech Solutions, I need help with a task.')) ?>" target="_blank" rel="noopener noreferrer">WhatsApp</a><?php endif; ?>
            </div>
        </section>
    </div>
</section>
<?php
$pageContent = ob_get_clean();
require __DIR__ . '/layout.php';
