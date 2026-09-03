<?php
$title = $project['meta_title'] ?: ($project['title'] . ' — ' . setting('site_name', 'AlbaTech Solutions'));
$metaDescription = $project['meta_description'] ?: $project['summary'];
$canonicalUrl = rtrim(config('app.url'), '/') . '/projects/' . $project['slug'];
$jsonLd = [[
    '@context' => 'https://schema.org', '@type' => 'CreativeWork', 'name' => $project['title'], 'description' => $project['summary'], 'url' => $canonicalUrl,
    'image' => !empty($project['image_path']) ? url($project['image_path']) : null,
    'creator' => ['@type' => 'Organization', 'name' => setting('site_name', 'AlbaTech Solutions')],
], \App\Core\Seo::breadcrumbs([
    ['name' => 'Home', 'url' => rtrim(config('app.url'), '/') . '/'], ['name' => 'Projects', 'url' => rtrim(config('app.url'), '/') . '/projects'], ['name' => $project['title'], 'url' => $canonicalUrl],
])];
$whatsappMessage = 'Hi AlbaTech Solutions, I saw the project "' . $project['title'] . '" and I would like to discuss something similar.';
$analyticsPageType = 'project';
$analyticsEntityId = (int)$project['id'];
ob_start();
?>
<section class="detail-hero">
    <div class="public-container">
        <a href="/projects" class="phase1-back"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Back to projects</a>
        <div class="detail-hero__row">
            <div>
                <span class="catalogue-eyebrow"><i class="fa-solid fa-briefcase" aria-hidden="true"></i> <?= e($project['industry'] ?: 'Digital project') ?></span>
                <h1><?= e($project['title']) ?></h1>
                <p><?= e($project['summary']) ?></p>
            </div>
            <div class="detail-hero__actions"><a class="btn btn-primary btn-lg" href="/get-help?project=<?= (int)$project['id'] ?>">Get Assistance <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div>
        </div>
    </div>
</section>

<section class="public-section">
    <div class="public-container detail-layout">
        <article class="detail-main">
            <?php if (!empty($project['image_path'])): ?><div class="detail-main__media"><img src="<?= e(url($project['image_path'])) ?>" alt="<?= e($project['title']) ?>" fetchpriority="high" decoding="async"></div><?php endif; ?>
            <div class="detail-copy">
                <?php if (!empty($project['description'])): ?><h2>Overview</h2><?= $project['description'] ?><?php endif; ?>
                <?php foreach ([['Challenge', $project['challenge'] ?? ''], ['Solution', $project['solution'] ?? ''], ['Results', $project['results'] ?? '']] as [$heading, $content]): if ($content): ?><h2><?= e($heading) ?></h2><p><?= nl2br(e($content)) ?></p><?php endif; endforeach; ?>
                <?php if (!empty($project['project_url'])): ?><p><a class="btn btn-secondary" href="<?= e($project['project_url']) ?>" target="_blank" rel="noopener noreferrer">Visit project <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a></p><?php endif; ?>
            </div>
        </article>
        <aside class="detail-sidebar">
            <div class="detail-cta">
                <span class="detail-cta__eyebrow">Like what you see?</span>
                <h2>Need something similar?</h2>
                <p>Tell us what you liked about this project and what you want your version to do.</p>
                <?php if (!empty($project['technologies'])): ?><div class="detail-meta"><div><strong>Built with</strong><span><?= e($project['technologies']) ?></span></div><?php if (!empty($project['location'])): ?><div><strong>Location</strong><span><?= e($project['location']) ?></span></div><?php endif; ?></div><?php endif; ?>
                <a class="btn btn-primary btn-lg" href="/get-help?project=<?= (int)$project['id'] ?>">Get Assistance <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            </div>
        </aside>
    </div>
</section>

<?php if (!empty($relatedProjects)): ?>
<section class="public-section"><div class="public-container"><div class="public-section__head-copy" ><span class="catalogue-eyebrow">More work</span><h2>More projects</h2></div><div class="project-grid">
<?php foreach (array_slice($relatedProjects, 0, 2) as $other): ?><article class="project-card"><a href="/projects/<?= e($other['slug']) ?>" class="project-card__media"><?php if (!empty($other['image_path'])): ?><img src="<?= e(url($other['image_path'])) ?>" alt="<?= e($other['title']) ?>" loading="lazy" decoding="async"><?php else: ?><span class="project-card__placeholder"><i class="fa-solid fa-code" aria-hidden="true"></i></span><?php endif; ?></a><div class="project-card__body"><div class="project-card__meta"><span><?= e($other['industry'] ?: 'Digital solution') ?></span></div><h3><?= e($other['title']) ?></h3><p><?= e($other['summary']) ?></p><div class="project-card__footer"><a class="project-card__link" href="/projects/<?= e($other['slug']) ?>">View project <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div></div></article><?php endforeach; ?>
</div></div></section>
<?php endif; ?>

<?php
$pageContent = ob_get_clean();
require __DIR__ . '/layout.php';
