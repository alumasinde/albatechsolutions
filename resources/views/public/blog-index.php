<?php
$title = 'Blog — ' . setting('site_name', 'AlbaTech Solutions');
$metaDescription = 'News, guides, and updates from ' . setting('site_name', 'AlbaTech Solutions') . '.';
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
<section class="section">
    <div class="section-heading"><span class="section-kicker">Insights</span><h1>Blog</h1><p>Practical guides, technology insights and digital growth ideas for Kenyan businesses and organisations.</p></div>

    <?php if (!empty($categories)): ?>
    <p class="blog-category-nav">
        <a href="/blog" class="blog-category-link<?= !$currentCategory ? ' is-active' : '' ?>">All</a>
        <?php foreach ($categories as $c): ?>
            <a href="/blog?category=<?= e($c['slug']) ?>" class="blog-category-link<?= $currentCategory === $c['slug'] ? ' is-active' : '' ?>">
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
