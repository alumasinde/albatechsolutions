<?php
$title = $post['meta_title'] ?: $post['title'];
$metaDescription = $post['meta_description'] ?: $post['excerpt'] ?: setting('seo_default_description', '');
$canonicalUrl = rtrim(config('app.url'), '/') . '/blog/' . $post['slug'];
$featuredImage = !empty($post['featured_media_path']) ? url('/' . ltrim($post['featured_media_path'], '/')) : null;
$jsonLd = [
    \App\Core\Seo::article($post),
    \App\Core\Seo::breadcrumbs([
        ['name' => 'Home', 'url' => rtrim(config('app.url'), '/') . '/'],
        ['name' => 'Guides', 'url' => rtrim(config('app.url'), '/') . '/blog'],
        ['name' => $post['title'], 'url' => $canonicalUrl],
    ]),
];
ob_start();
?>
<article class="guide-article">
    <a href="/blog" class="guide-article__back">← Back to guides</a>
    <h1><?= e($post['title']) ?></h1>
    <div class="article-meta">
        <?php if ($post['author_name']): ?>By <?= e($post['author_name']) ?> · <?php endif; ?>
        <?= e(date('F j, Y', strtotime($post['published_at'] ?? $post['created_at']))) ?>
        <?php if ($post['category_name']): ?> · <?= e($post['category_name']) ?><?php endif; ?>
    </div>
    <?php if ($featuredImage): ?>
        <figure class="guide-article__featured-image">
            <img src="<?= e($featuredImage) ?>" alt="<?= e($post['featured_media_name'] ?: $post['title']) ?>" fetchpriority="high">
        </figure>
    <?php endif; ?>
    <div class="guide-article__body"><?= $post['content'] ?? '' ?></div>
    <aside class="guide-article__next"><strong>Need help with this task?</strong><p>Tell us what you are trying to do and we will help with the next step.</p><div><a class="btn btn-primary" href="/get-help">Get Assistance</a><?php if (setting('whatsapp_number')): ?> <a class="btn btn-secondary js-whatsapp" href="<?= e(whatsapp_url('Hi AlbaTech Solutions, I need help with this task.')) ?>" target="_blank" rel="noopener noreferrer">WhatsApp</a><?php endif; ?></div></aside>
</article>
<?php
$pageContent = ob_get_clean();
require __DIR__ . '/layout.php';
