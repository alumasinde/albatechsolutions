<?php
use AlbaTech\Core\View;
$features = is_array($project['features'] ?? null) ? $project['features'] : [];
$gallery = is_array($project['gallery'] ?? null) ? $project['gallery'] : [];
$stack = is_array($project['stack'] ?? null) ? $project['stack'] : [];
$statusLabel = match ($project['lifecycle_status']) { 'live' => 'Live', 'beta' => 'Beta', 'early_access' => 'Early access', default => 'In development' };
$statusClass = match ($project['lifecycle_status']) { 'live' => 'badge-live', 'beta' => 'badge-beta', 'early_access' => 'badge-early', default => 'badge-dev' };
?>
<?php if (isset($breadcrumbs) && is_array($breadcrumbs) && !empty($breadcrumbs)): ?>
<nav class="breadcrumbs shell" aria-label="Breadcrumb">
    <ol>
        <?php foreach ($breadcrumbs as $index => $crumb): $isLast = $index === count($breadcrumbs) - 1; ?>
            <li>
                <?php if (!$isLast && !empty($crumb['url'])): ?><a href="<?= View::escape($crumb['url']) ?>"><?= View::escape($crumb['name']) ?></a><?php else: ?><span aria-current="page"><?= View::escape($crumb['name']) ?></span><?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>
<?php endif; ?>

<section class="border-b border-border bg-surface">
    <div class="shell py-12 md:py-16">
        <span class="kicker"><?= View::escape($project['category_name']) ?> case study</span>
        <h1 class="max-w-3xl"><?= View::escape($project['title']) ?></h1>
        <?php if (!empty($project['client_name'])): ?><p class="mt-2 text-sm font-semibold text-muted">Client: <?= View::escape($project['client_name']) ?></p><?php endif; ?>
        <p class="mt-4 max-w-2xl text-lg leading-relaxed"><?= View::escape($project['description']) ?></p>
        <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-muted">
            <?php if (!empty($project['platform'])): ?><span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-display text-primary" aria-hidden="true"></i> <?= View::escape($project['platform']) ?></span><?php endif; ?>
            <span class="badge <?= $statusClass ?>"><?= View::escape($statusLabel) ?></span>
        </div>
        <div class="mt-6 flex flex-col gap-3 sm:flex-row">
            <a class="btn btn-whatsapp" href="<?= View::escape($whatsAppUrl) ?>">Request a similar project <i class="fa-brands fa-whatsapp" aria-hidden="true"></i></a>
            <?php if (!empty($project['live_url'])): ?><a class="btn btn-secondary" href="<?= View::escape($project['live_url']) ?>" target="_blank" rel="noopener noreferrer">Visit live project <i class="fa-solid fa-arrow-up-right-from-square text-sm" aria-hidden="true"></i></a><?php endif; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="shell">
        <figure class="overflow-hidden rounded-2xl border border-border bg-surface-muted"><img src="/<?= View::escape(ltrim($project['cover_image'], '/')) ?>" alt="<?= View::escape($project['title']) ?> project screenshot" width="1200" height="800" fetchpriority="high" decoding="async"></figure>
    </div>
</section>

<section class="section-tight">
    <div class="shell grid gap-8 md:grid-cols-2">
        <?php if (!empty($project['challenge'])): ?><article><span class="kicker">The challenge</span><h2>What needed to change</h2><div class="prose-page mt-3"><?= nl2br(View::escape($project['challenge']), false) ?></div></article><?php endif; ?>
        <?php if (!empty($project['solution'])): ?><article><span class="kicker">What we built</span><h2>The system we delivered</h2><div class="prose-page mt-3"><?= nl2br(View::escape($project['solution']), false) ?></div></article><?php endif; ?>
    </div>
</section>

<?php if (!empty($features) || !empty($project['result'])): ?>
<section class="section section-tint">
    <div class="shell grid items-start gap-10 lg:grid-cols-2">
        <?php if (!empty($features)): ?>
            <div><span class="kicker">Features</span><h2>What the system does</h2><ul class="check-list mt-4"><?php foreach ($features as $feature): ?><li><?= View::escape($feature) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>
        <?php if (!empty($project['result'])): ?>
            <aside class="card p-6"><span class="kicker">Outcome</span><h2>Where it is now</h2><div class="prose-page mt-3"><?= nl2br(View::escape($project['result']), false) ?></div></aside>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($gallery)): ?>
<section class="section">
    <div class="shell grid gap-4 sm:grid-cols-2">
        <?php foreach ($gallery as $image): ?>
            <figure class="overflow-hidden rounded-xl border border-border bg-surface-muted"><img src="/<?= View::escape(ltrim($image, '/')) ?>" alt="<?= View::escape($project['title']) ?> screen" width="1200" height="800" loading="lazy" decoding="async"></figure>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($stack)): ?>
<section class="section-tight">
    <div class="shell flex flex-wrap items-center gap-2">
        <span class="mr-1 text-xs font-extrabold uppercase tracking-[0.12em] text-muted">Built with</span>
        <?php foreach ($stack as $item): ?><span class="rounded-full bg-surface-muted px-3 py-1 text-xs font-semibold text-muted"><?= View::escape($item) ?></span><?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($relatedProjects)): ?>
<section class="section section-tint">
    <div class="shell">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div><span class="kicker">More projects</span><h2>Related work</h2></div>
            <a class="link-arrow text-sm" href="/work">View all work <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </div>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($relatedProjects as $related): $relatedUrl = '/work/' . rawurlencode($related['slug']); ?>
                <article class="card card-lift group overflow-hidden">
                    <a class="media-16-10 relative block" href="<?= $relatedUrl ?>">
                        <img class="transition-transform duration-300 group-hover:scale-[1.02]" src="/<?= View::escape(ltrim($related['cover_image'], '/')) ?>" alt="<?= View::escape($related['title']) ?>" width="600" height="400" loading="lazy" decoding="async">
                        <span class="absolute bottom-3 left-3 rounded-full bg-surface/95 px-3 py-1 text-xs font-semibold text-ink"><?= View::escape($related['category_name']) ?></span>
                    </a>
                    <div class="p-5">
                        <h3 class="text-lg"><a class="text-ink hover:text-primary" href="<?= $relatedUrl ?>"><?= View::escape($related['title']) ?></a></h3>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="bg-primary py-12 text-white md:py-14">
    <div class="shell flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
        <div class="max-w-2xl">
            <span class="kicker kicker-light">Have a similar idea?</span>
            <h2 class="text-white">Let's talk about what you need.</h2>
            <p class="mt-2 text-white/85">Share your goals and we'll discuss a practical next step.</p>
        </div>
        <a class="btn btn-whatsapp w-full shrink-0 md:w-auto" href="<?= View::escape($whatsAppUrl) ?>">Request a similar project <i class="fa-brands fa-whatsapp" aria-hidden="true"></i></a>
    </div>
</section>
