<?php use AlbaTech\Core\View; ?>
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
        <span class="kicker">Products</span>
        <h1 class="max-w-3xl">Software we build and run ourselves.</h1>
        <p class="mt-4 max-w-2xl text-lg leading-relaxed">We build products for Kenyan businesses and individuals, then apply the same approach to client systems. Every product is labelled with where it stands today.</p>
        <div class="mt-6 flex flex-col gap-3 sm:flex-row">
            <a class="btn btn-primary" href="/solutions">Systems we build for clients</a>
            <a class="btn btn-secondary" href="/work">See client work</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="shell">
        <?php if (!empty($products)): ?>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($products as $product): $productUrl = '/products/' . rawurlencode($product['slug']); ?>
                    <article class="card card-lift group flex flex-col overflow-hidden">
                        <a class="media-16-10 relative block" href="<?= $productUrl ?>">
                            <img class="transition-transform duration-300 group-hover:scale-[1.02]" src="/<?= View::escape(ltrim($product['cover_image'], '/')) ?>" alt="<?= View::escape($product['title']) ?>" width="1200" height="800" loading="lazy" decoding="async">
                            <span class="badge badge-float badge-<?= $product['lifecycle_status'] === 'live' ? 'live' : ($product['lifecycle_status'] === 'beta' ? 'beta' : ($product['lifecycle_status'] === 'early_access' ? 'early' : 'dev')) ?>"><?= View::escape(match ($product['lifecycle_status']) { 'live' => 'Live', 'beta' => 'Beta', 'early_access' => 'Early access', default => 'In development' }) ?></span>
                        </a>
                        <div class="flex flex-1 flex-col p-5">
                            <h2 class="text-lg leading-snug"><a class="text-ink hover:text-primary" href="<?= $productUrl ?>"><?= View::escape($product['title']) ?></a></h2>
                            <?php if (!empty($product['platform'])): ?><p class="mt-1 text-xs font-semibold uppercase tracking-wide text-subtle"><?= View::escape($product['platform']) ?></p><?php endif; ?>
                            <p class="mt-2 flex-1 text-sm"><?= View::escape($product['description']) ?></p>
                            <a class="link-arrow mt-4 text-sm" href="<?= $productUrl ?>">View product <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card mx-auto max-w-xl p-8 text-center">
                <h2>Products are on the way</h2>
                <p class="mt-2">Our products will appear here as they are released. In the meantime, tell us what you are trying to build.</p>
                <a class="btn btn-secondary mt-5" href="/get-help">Send an enquiry</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section section-tint">
    <div class="shell">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div><span class="kicker">Behind the products</span><h2>Need something built for your business?</h2></div>
            <a class="link-arrow text-sm" href="/solutions">Explore solutions <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </div>
        <p class="mt-4 max-w-3xl">Our products solve problems we understand first-hand. The same team can build a booking, rental, visitor or stock system around the way your business already works.</p>
    </div>
</section>
