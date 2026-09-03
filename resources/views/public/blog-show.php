<?php
$title = $post['meta_title'] ?: $post['title'];
$metaDescription = $post['meta_description'] ?: $post['excerpt'] ?: setting('seo_default_description', '');
$canonicalUrl = rtrim(config('app.url'), '/') . '/blog/' . $post['slug'];
$jsonLd = [
    \App\Core\Seo::article($post),
    \App\Core\Seo::breadcrumbs([
        ['name' => 'Home', 'url' => rtrim(config('app.url'), '/') . '/'],
        ['name' => 'Blog', 'url' => rtrim(config('app.url'), '/') . '/blog'],
        ['name' => $post['title'], 'url' => $canonicalUrl],
    ]),
];
$analyticsPageType = 'blog';
$analyticsEntityId = (int)$post['id'];
ob_start();
?>
<article class="article-content">
    <h1><?= e($post['title']) ?></h1>
    <div class="article-meta">
        <?php if ($post['author_name']): ?>By <?= e($post['author_name']) ?> · <?php endif; ?>
        <?= e(date('F j, Y', strtotime($post['published_at'] ?? $post['created_at']))) ?>
        <?php if ($post['category_name']): ?> · <?= e($post['category_name']) ?><?php endif; ?>
    </div>
    <div><?= $post['content'] ?? '' ?></div>
</article>
<?php
$pageContent = ob_get_clean();
require __DIR__ . '/layout.php';
