<?php
use AlbaTech\Core\View;
$features = is_array($product['features'] ?? null) ? $product['features'] : [];
$gallery = is_array($product['gallery'] ?? null) ? $product['gallery'] : [];
$stack = is_array($product['stack'] ?? null) ? $product['stack'] : [];
$statusLabel = match ($product['lifecycle_status']) { 'live' => 'Live', 'beta' => 'Beta', 'early_access' => 'Early access', default => 'In development' };
$statusClass = match ($product['lifecycle_status']) { 'live' => 'badge-live', 'beta' => 'badge-beta', 'early_access' => 'badge-early', default => 'badge-dev' };
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
        <span class="kicker">AlbaTech product</span>
        <h1 class="max-w-3xl"><?= View::escape($product['title']) ?></h1>
        <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-muted">
            <span class="badge <?= $statusClass ?>"><?= View::escape($statusLabel) ?></span>
            <?php if (!empty($product['platform'])): ?><span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-display text-primary" aria-hidden="true"></i> <?= View::escape($product['platform']) ?></span><?php endif; ?>
            <?php if (!empty($product['category_name'])): ?><span><?= View::escape($product['category_name']) ?></span><?php endif; ?>
        </div>
        <p class="mt-4 max-w-2xl text-lg leading-relaxed"><?= View::escape($product['description']) ?></p>
        <div class="mt-6 flex flex-col gap-3 sm:flex-row">
            <?php if (!empty($product['download_url'])): ?>
                <a class="btn btn-primary" href="<?= View::escape($product['download_url']) ?>" rel="noopener">Download <?= View::escape($product['title']) ?></a>
            <?php elseif ($product['lifecycle_status'] === 'live' && !empty($product['live_url'])): ?>
                <a class="btn btn-primary" href="<?= View::escape($product['live_url']) ?>" target="_blank" rel="noopener noreferrer">Visit <?= View::escape($product['title']) ?> <i class="fa-solid fa-arrow-up-right-from-square text-sm" aria-hidden="true"></i></a>
            <?php elseif ($product['lifecycle_status'] === 'beta'): ?>
                <a class="btn btn-whatsapp" href="<?= View::escape($whatsAppUrl) ?>">Request the beta <i class="fa-brands fa-whatsapp" aria-hidden="true"></i></a>
            <?php else: ?>
                <a class="btn btn-whatsapp" href="<?= View::escape($whatsAppUrl) ?>">Join the early access list <i class="fa-brands fa-whatsapp" aria-hidden="true"></i></a>
            <?php endif; ?>
            <a class="btn btn-secondary" href="/products">All products</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="shell">
        <figure class="overflow-hidden rounded-2xl border border-border bg-surface-muted"><img src="/<?= View::escape(ltrim($product['cover_image'], '/')) ?>" alt="<?= View::escape($product['title']) ?> product screenshot" width="1200" height="800" fetchpriority="high" decoding="async"></figure>

        <?php if (!empty($product['privacy_note'])): ?>
            <aside class="card mt-6 flex gap-4 p-5" aria-label="Privacy note">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i></span>
                <div><h2 class="text-lg">Privacy note</h2><div class="prose-page mt-2 text-sm"><?= nl2br(View::escape($product['privacy_note']), false) ?></div></div>
            </aside>
        <?php endif; ?>

        <div class="mt-10 grid gap-8 md:grid-cols-2">
            <?php if (!empty($product['challenge'])): ?>
                <article><span class="kicker">The problem</span><h2>Why we built it</h2><div class="prose-page mt-3"><?= nl2br(View::escape($product['challenge']), false) ?></div></article>
            <?php endif; ?>
            <?php if (!empty($product['solution'])): ?>
                <article><span class="kicker">What it does</span><h2>How it works</h2><div class="prose-page mt-3"><?= nl2br(View::escape($product['solution']), false) ?></div></article>
            <?php endif; ?>
        </div>

        <?php if (!empty($features)): ?>
            <div class="mt-10">
                <h2>What is included</h2>
                <ul class="check-list mt-4"><?php foreach ($features as $feature): ?><li><?= View::escape($feature) ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($gallery)): ?>
            <div class="mt-10 grid gap-4 sm:grid-cols-2">
                <?php foreach ($gallery as $image): ?>
                    <figure class="overflow-hidden rounded-xl border border-border bg-surface-muted"><img src="/<?= View::escape(ltrim($image, '/')) ?>" alt="<?= View::escape($product['title']) ?> screenshot" width="1200" height="800" loading="lazy" decoding="async"></figure>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($product['result'])): ?>
            <div class="card mt-10 p-6"><span class="kicker">Where it is now</span><div class="prose-page"><?= nl2br(View::escape($product['result']), false) ?></div></div>
        <?php endif; ?>

        <?php if (!empty($stack)): ?>
            <div class="mt-8 flex flex-wrap items-center gap-2">
                <span class="mr-1 text-xs font-extrabold uppercase tracking-[0.12em] text-muted">Built with</span>
                <?php foreach ($stack as $item): ?><span class="rounded-full bg-surface-muted px-3 py-1 text-xs font-semibold text-muted"><?= View::escape($item) ?></span><?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php if (!empty($relatedProducts)): ?>
<section class="section section-tint">
    <div class="shell">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div><span class="kicker">More products</span><h2>Other things we have built</h2></div>
            <a class="link-arrow text-sm" href="/products">View all products <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </div>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($relatedProducts as $related): $relatedUrl = '/products/' . rawurlencode($related['slug']); ?>
                <article class="card card-lift group overflow-hidden">
                    <a class="media-16-10 relative block" href="<?= $relatedUrl ?>">
                        <img class="transition-transform duration-300 group-hover:scale-[1.02]" src="/<?= View::escape(ltrim($related['cover_image'], '/')) ?>" alt="<?= View::escape($related['title']) ?>" width="600" height="400" loading="lazy" decoding="async">
                        <span class="badge badge-float <?= match ($related['lifecycle_status']) { 'live' => 'badge-live', 'beta' => 'badge-beta', 'early_access' => 'badge-early', default => 'badge-dev' } ?>"><?= View::escape(match ($related['lifecycle_status']) { 'live' => 'Live', 'beta' => 'Beta', 'early_access' => 'Early access', default => 'In development' }) ?></span>
                    </a>
                    <div class="p-5">
                        <h3 class="text-lg"><a class="text-ink hover:text-primary" href="<?= $relatedUrl ?>"><?= View::escape($related['title']) ?></a></h3>
                        <p class="mt-2 text-sm"><?= View::escape($related['description']) ?></p>
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
            <span class="kicker kicker-light">Need something similar?</span>
            <h2 class="text-white">Tell us what you are trying to build.</h2>
            <p class="mt-2 text-white/85">We can discuss a focused system, a website or a pilot around your process.</p>
        </div>
        <a class="btn btn-whatsapp w-full shrink-0 md:w-auto" href="<?= View::escape($whatsAppUrl) ?>">Start a conversation <i class="fa-brands fa-whatsapp" aria-hidden="true"></i></a>
    </div>
</section>
