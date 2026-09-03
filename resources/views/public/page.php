<?php
$title = $page['meta_title'] ?: $page['title'];
$metaDescription = $page['meta_description'] ?: $page['excerpt'] ?: setting('seo_default_description', '');
$canonicalUrl = $page['canonical_url'] ?: (rtrim(config('app.url'), '/') . '/' . $page['slug']);
$robots = !empty($page['noindex']) ? 'noindex, follow' : null;
$jsonLd = [
    \App\Core\Seo::landingPage($page),
    \App\Core\Seo::breadcrumbs([
        ['name' => 'Home', 'url' => rtrim(config('app.url'), '/') . '/'],
        ['name' => $page['title'], 'url' => $canonicalUrl],
    ]),
];
ob_start();
?>
<section class="seo-landing-hero">
    <div class="seo-container">
        <?php if (!empty($page['page_type']) && $page['page_type'] !== 'general'): ?>
            <span class="eyebrow"><?= e(str_replace('_', ' ', $page['page_type'])) ?></span>
        <?php endif; ?>
        <h1><?= e($page['title']) ?></h1>
        <?php if (!empty($page['seo_intro'])): ?><p><?= e($page['seo_intro']) ?></p><?php endif; ?>
    </div>
</section>
<section class="seo-landing-body section">
    <div class="seo-container seo-content-layout">
        <article class="article-content seo-rich-content">
            <div><?= $page['content'] ?? '' ?></div>
        </article>
        <aside class="seo-sidebar">
            <div class="seo-sidebar-card"><span class="eyebrow">Explore</span><h2>Related services</h2><ul><?php foreach ($relatedServices as $service): ?><li><a href="/services/<?= e($service['slug']) ?>"><?= e($service['name']) ?></a></li><?php endforeach; ?></ul><a href="/services" class="text-link">View all services <i class="fa-solid fa-arrow-right"></i></a></div>
            <div class="seo-sidebar-card"><span class="eyebrow">Proof</span><h2>Recent work</h2><?php foreach ($relatedProjects as $project): ?><a class="seo-project-link" href="/projects/<?= e($project['slug']) ?>"><strong><?= e($project['title']) ?></strong><span><?= e($project['industry'] ?: 'Digital project') ?></span></a><?php endforeach; ?></div>
        </aside>
    </div>
</section>
<section class="section section-muted seo-related-content"><div class="seo-container"><div class="section-heading"><span class="eyebrow">Learn more</span><h2>Latest insights</h2></div><div class="seo-post-grid"><?php foreach ($recentPosts as $post): ?><a href="/blog/<?= e($post['slug']) ?>" class="seo-post-card"><span><?= e($post['category_name'] ?: 'Insights') ?></span><h3><?= e($post['title']) ?></h3><p><?= e($post['excerpt'] ?: '') ?></p></a><?php endforeach; ?></div><a href="/get-help" class="btn btn-primary"><i class="fa-solid fa-hand-holding-heart"></i> Get Assistance</a></div></section>
<?php
$pageContent = ob_get_clean();
require __DIR__ . '/layout.php';
