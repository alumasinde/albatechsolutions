<?php
$title = 'Guides — ' . setting('site_name', 'AlbaTech Solutions');
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
    <div class="section-heading"><span class="section-kicker">Helpful information</span><h1>Guides</h1><p>Simple, practical information to help you understand a task and decide on the next step.</p></div>

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

    <div class="grid-3">
        <?php foreach ($posts as $post): ?>
            <a href="/blog/<?= e($post['slug']) ?>" class="post-card">
                <h3><?= e($post['title']) ?></h3>
                <p><?= e($post['excerpt'] ?? '') ?></p>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if (empty($posts)): ?>
        <p class="public-empty-copy">No posts yet.</p>
    <?php endif; ?>
</section>
<?php
$pageContent = ob_get_clean();
require __DIR__ . '/layout.php';
