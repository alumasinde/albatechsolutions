<?php
$title = 'Projects — ' . setting('site_name', 'AlbaTech Solutions');
$metaDescription = 'Selected websites, software systems and digital projects built by AlbaTech Solutions.';
$canonicalUrl = rtrim(config('app.url'), '/') . '/projects';
$jsonLd = [\App\Core\Seo::breadcrumbs([
    ['name' => 'Home', 'url' => rtrim(config('app.url'), '/') . '/'],
    ['name' => 'Projects', 'url' => $canonicalUrl],
])];
ob_start();
?>
<section class="catalogue-hero">
    <div class="public-container catalogue-hero__inner">
        <a href="/" class="phase1-back"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Back home</a>
        <span class="catalogue-eyebrow"><i class="fa-solid fa-briefcase" aria-hidden="true"></i> Selected work</span>
        <h1>Things I've built, improved and brought to life.</h1>
        <p>Have a look around. If something here feels close to what you need, open the project and tell us about your version.</p>
    </div>
</section>

<section class="public-section">
    <div class="public-container">
        <?php if (!empty($projects)): ?>
            <div class="project-grid">
                <?php foreach ($projects as $project): ?>
                    <article class="project-card">
                        <a href="/projects/<?= e($project['slug']) ?>" class="project-card__media" aria-label="View <?= e($project['title']) ?>">
                            <?php if (!empty($project['image_path'])): ?><img src="<?= e(url($project['image_path'])) ?>" alt="<?= e($project['title']) ?>" loading="lazy" decoding="async"><?php else: ?><span class="project-card__placeholder"><i class="fa-solid fa-code" aria-hidden="true"></i></span><?php endif; ?>
                        </a>
                        <div class="project-card__body">
                            <div class="project-card__meta">
                                <?php if (!empty($project['industry'])): ?><span><?= e($project['industry']) ?></span><?php endif; ?>
                                <?php if (!empty($project['location'])): ?><span>•</span><span><?= e($project['location']) ?></span><?php endif; ?>
                            </div>
                            <h3><?= e($project['title']) ?></h3>
                            <p><?= e($project['summary']) ?></p>
                            <div class="project-card__footer">
                                <a class="project-card__link" href="/projects/<?= e($project['slug']) ?>">View case study <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                                <?php if (!empty($project['technologies'])): ?><span class="project-card__tags"><?= e($project['technologies']) ?></span><?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="catalogue-empty"><i class="fa-solid fa-folder-open" aria-hidden="true"></i><h2>Projects are coming soon</h2><p>In the meantime, tell us what you would like to build.</p><a href="/get-help" class="btn btn-primary"><i class="fa-solid fa-hand-holding-heart"></i> Get Assistance</a></div>
        <?php endif; ?>
    </div>
</section>

<section class="phase1-final-cta"><div class="public-container"><div class="phase1-final-cta__inner"><div><span class="public-kicker" >Your project could be next</span><h2>Have something in mind?</h2><p>Send us the idea, even if it is still rough. We can turn it into a practical plan.</p></div><a class="btn btn-primary btn-lg" href="/get-help"><i class="fa-solid fa-hand-holding-heart"></i> Get Assistance</a></div></div></section>
<?php
$pageContent = ob_get_clean();
require __DIR__ . '/layout.php';
