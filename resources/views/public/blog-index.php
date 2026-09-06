<?php
$title = 'Guides | ' . setting('site_name', 'AlbaTech Solutions');
$metaDescription = 'Practical guides and helpful information from ' . setting('site_name', 'AlbaTech Solutions') . '.';
$robots = ($page > 1) ? 'noindex, follow' : null;
$canonicalUrl = rtrim(config('app.url'), '/') . '/blog';
$jsonLd = [
    \App\Core\Seo::breadcrumbs([
        ['name' => 'Home', 'url' => rtrim(config('app.url'), '/') . '/'],
        ['name' => 'Blog', 'url' => $canonicalUrl],
    ]),
];
ob_start();
?>
<section class="guides-page">
    <div class="public-container">
    <div class="section-heading"><span class="section-kicker">Helpful information</span><h1>Guides for common tasks in Kenya</h1><p>Simple, practical information to help you understand what you may need and decide on the next step. Guides are for general information and do not replace official instructions.</p></div>

    <?php if (!empty($categories)): ?>
    <p class="guides-category-nav">
        <a href="/blog" class="guides-category-link<?= !$currentCategory ? ' is-active' : '' ?>">All</a>
        <?php foreach ($categories as $c): ?>
            <a href="/blog?category=<?= e($c['slug']) ?>" class="guides-category-link<?= $currentCategory === $c['slug'] ? ' is-active' : '' ?>">
                <?= e($c['name']) ?>
            </a>
        <?php endforeach; ?>
    </p>
    <?php endif; ?>

    <div class="guides-page__intro"><h2>Start with the information you need</h2><p>Choose a guide below, read the practical steps, then <a href="/get-help">get assistance</a> if you would like help with a task.</p></div>
    <div class="grid-3">
        <?php foreach ($posts as $post): ?>
            <article class="post-card">
                <?php if (!empty($post['category_name'])): ?><span class="public-kicker"><?= e($post['category_name']) ?></span><?php endif; ?>
                <h2><a href="/blog/<?= e($post['slug']) ?>"><?= e($post['title']) ?></a></h2>
                <p><?= e($post['excerpt'] ?? '') ?></p>
                <a class="post-card__link" href="/blog/<?= e($post['slug']) ?>">Read guide <span aria-hidden="true">→</span></a>
            </article>
        <?php endforeach; ?>
    </div>

    <?php if (empty($posts)): ?>
        <p class="public-empty-copy">We are preparing helpful guides. If you need help now, <a href="/get-help">tell us the task</a>.</p>
    <?php endif; ?>

    <section class="guides-page__cta"><span class="public-kicker">Still unsure?</span><h2>Tell us what you are trying to do.</h2><p>We can help you understand whether one of our services fits your task and what happens next.</p><a class="btn btn-primary" href="/get-help">Get Assistance</a></section>
    </div>
</section>
<?php
$pageContent = ob_get_clean();
require __DIR__ . '/layout.php';
