<?php

use AlbaTech\Core\View;
/**
 * @var array<string, mixed> $categories
 */
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

<section class="page-hero">
    <div class="shell grid items-center gap-10 py-12 md:py-16 lg:grid-cols-[1.1fr_0.9fr]">
        <div>
            <span class="kicker">Services</span>
            <h1>Software, websites and business services for Kenya</h1>
            <p class="mt-4 max-w-2xl text-lg leading-relaxed">Start with a business system, a website or practical digital assistance. The full catalogue, with scope and pricing information, is below.</p>
            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <a class="btn btn-primary" href="#service-catalogue">Explore services <i class="fa-solid fa-arrow-down text-sm" aria-hidden="true"></i></a>
                <a class="btn btn-secondary" href="/solutions">Business systems</a>
            </div>
            <div class="mt-6 flex flex-wrap gap-x-6 gap-y-2 text-sm font-semibold text-body">
                <span class="inline-flex items-center gap-2"><i class="fa-solid fa-file-circle-check text-primary" aria-hidden="true"></i> Clear scope</span>
                <span class="inline-flex items-center gap-2"><i class="fa-solid fa-mobile-screen-button text-primary" aria-hidden="true"></i> Mobile-friendly solutions</span>
            </div>
        </div>
        <?php if ($categories !== []): ?>
        <aside class="card p-5 sm:p-6" aria-label="Browse by category">
            <p class="mb-3 text-xs font-extrabold uppercase tracking-[0.12em] text-muted">Browse by category</p>
            <nav class="grid gap-1" aria-label="Service categories">
                <?php foreach ($categories as $category): ?>
                    <a class="group flex items-center gap-3 rounded-lg px-3 py-3 hover:bg-surface-muted" href="#category-<?= View::escape($category['slug']) ?>">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-light text-xs font-bold text-secondary"><?= count($category['services']) ?></span>
                        <span class="flex-1 text-sm font-semibold text-ink"><?= View::escape($category['name']) ?></span>
                        <i class="fa-solid fa-arrow-right text-xs text-subtle transition-transform duration-150 group-hover:translate-x-1 group-hover:text-primary" aria-hidden="true"></i>
                    </a>
                <?php endforeach; ?>
            </nav>
        </aside>
        <?php endif; ?>
    </div>
</section>

<section class="section scroll-mt-20" id="service-catalogue">
    <div class="shell">
        <div class="mb-8 flex flex-col gap-4 md:mb-10 md:flex-row md:items-end md:justify-between">
            <div class="max-w-2xl">
                <span class="kicker">Service catalogue</span>
                <h2>Find the support you need</h2>
                <p class="mt-3">Choose a category to explore services and see what each one includes.</p>
            </div>
            <a class="link-arrow text-sm" href="/get-help">Need guidance? Talk to us <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </div>

        <?php if ($categories === []): ?>
            <div class="card mx-auto max-w-xl p-8 text-center">
                <span class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-primary-light text-primary"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></span>
                <h3>Our service catalogue is being updated.</h3>
                <p class="mt-2">Contact us and tell us what you are looking to achieve.</p>
                <a class="btn btn-primary mt-5" href="/get-help">Get in touch</a>
            </div>
        <?php else: ?>
            <?php foreach ($categories as $categoryIndex => $category): ?>
                <section class="mt-12 border-t border-border pt-10 first:mt-0 first:border-0 first:pt-0" aria-labelledby="category-<?= View::escape($category['slug']) ?>">
                    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center">
                        <div class="flex flex-1 items-start gap-3">
                            <span class="mt-1 text-sm font-extrabold tabular-nums text-primary"><?= str_pad((string) ($categoryIndex + 1), 2, '0', STR_PAD_LEFT) ?></span>
                            <div>
                                <h3 id="category-<?= View::escape($category['slug']) ?>"><?= View::escape($category['name']) ?></h3>
                                <?php if (!empty($category['description'])): ?>
                                    <p class="mt-1 max-w-2xl text-sm"><?= View::escape($category['description']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <span class="w-fit rounded-full bg-surface-muted px-3 py-1 text-xs font-semibold text-muted"><?= count($category['services']) ?> <?= count($category['services']) === 1 ? 'service' : 'services' ?></span>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <?php foreach ($category['services'] as $service): ?>
                            <article class="card card-lift flex flex-col p-5">
                                <div class="mb-4 flex items-center justify-between">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-light text-primary"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
                                    <i class="fa-solid fa-arrow-up-right-from-square text-xs text-subtle" aria-hidden="true"></i>
                                </div>
                                <h4 class="text-base font-bold tracking-normal text-ink"><?= View::escape($service['name']) ?></h4>
                                <p class="mt-2 flex-1 text-sm"><?= View::escape($service['short_description'] ?? '') ?></p>
                                <div class="mt-4 text-sm font-bold text-ink">
                                    <?php if (in_array((string) ($service['pricing_type'] ?? 'quote'), ['fixed', 'starting_at'], true) && ($service['pricing_amount'] ?? null) !== null && $service['pricing_amount'] !== ''): ?>
                                        <span><?= ($service['pricing_type'] ?? '') === 'starting_at' ? 'From ' : '' ?>KSh <?= View::escape(number_format((float) $service['pricing_amount'], ((float) $service['pricing_amount'] == (int) $service['pricing_amount']) ? 0 : 2)) ?></span>
                                    <?php else: ?>
                                        <span class="font-semibold text-muted"><?= View::escape(($service['pricing_label'] ?? '') ?: 'Contact for price') ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($service['turnaround'])): ?><p class="mt-1 text-xs font-semibold text-muted"><i class="fa-regular fa-clock mr-1" aria-hidden="true"></i><?= View::escape($service['turnaround']) ?></p><?php endif; ?>
                                <a href="/services/<?= rawurlencode($service['slug']) ?>" class="link-arrow mt-3 text-sm" aria-label="View service: <?= View::escape($service['name']) ?>">View service <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<section class="border-y border-warning-border bg-warning-bg py-5">
    <div class="shell flex gap-3 text-sm leading-relaxed text-warning">
        <i class="fa-solid fa-circle-info mt-1" aria-hidden="true"></i>
        <p class="m-0 text-warning"><strong>Important to know</strong> AlbaTech Solutions is an independent service provider, not a government agency. Government applications and registrations are subject to the relevant authority's requirements, processing times and decisions.</p>
    </div>
</section>

<section class="bg-primary py-12 text-white md:py-14">
    <div class="shell flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
        <div class="max-w-2xl">
            <span class="kicker kicker-light">Have a different task in mind?</span>
            <h2 class="text-white">Let's talk about what you need.</h2>
            <p class="mt-2 text-white/85">Tell us what you're trying to achieve. We'll help you identify a sensible next step.</p>
        </div>
        <a class="btn btn-accent w-full md:w-auto" href="/get-help">Contact us <i class="fa-solid fa-arrow-right text-sm" aria-hidden="true"></i></a>
    </div>
</section>
