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
    <div class="shell grid items-center gap-10 py-12 md:py-16 lg:grid-cols-[1.15fr_0.85fr]">
        <div>
            <span class="kicker"><?= View::escape($service['category_name']) ?></span>
            <h1><?= View::escape($service['name']) ?></h1>
            <p class="mt-4 max-w-2xl text-lg leading-relaxed"><?= View::escape($service['short_description'] ?? '') ?></p>
            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <a class="btn btn-whatsapp" href="/get-help?service=<?= rawurlencode($service['slug']) ?>">Enquire about this service <i class="fa-solid fa-arrow-right text-sm" aria-hidden="true"></i></a>
                <a class="btn btn-secondary" href="/services">Explore other services</a>
            </div>
        </div>
        <aside class="card p-6">
            <span class="text-xs font-extrabold uppercase tracking-[0.12em] text-muted">Service pricing</span>
            <?php if (in_array((string) ($service['pricing_type'] ?? 'quote'), ['fixed', 'starting_at'], true) && ($service['pricing_amount'] ?? null) !== null && $service['pricing_amount'] !== ''): ?>
                <strong class="mt-2 block text-3xl font-extrabold tracking-tight text-ink"><?= ($service['pricing_type'] ?? '') === 'starting_at' ? 'From ' : '' ?>KSh <?= View::escape(number_format((float) $service['pricing_amount'], ((float) $service['pricing_amount'] == (int) $service['pricing_amount']) ? 0 : 2)) ?></strong>
                <p class="mt-2 text-sm"><?= ($service['pricing_type'] ?? '') === 'starting_at' ? 'Final cost depends on the scope and options you choose.' : 'We will confirm what is included before work begins.' ?></p>
            <?php else: ?>
                <strong class="mt-2 block text-xl font-extrabold tracking-tight text-ink"><?= View::escape(($service['pricing_label'] ?? '') ?: 'Contact for price') ?></strong>
                <p class="mt-2 text-sm">Tell us what you need and we will confirm the scope and price before you commit.</p>
            <?php endif; ?>
            <?php if (!empty($service['turnaround'])): ?>
                <p class="mt-4 flex items-center gap-2 border-t border-border pt-4 text-sm font-semibold text-ink"><i class="fa-regular fa-clock text-primary" aria-hidden="true"></i> Typical turnaround: <?= View::escape($service['turnaround']) ?></p>
            <?php endif; ?>
            <a class="link-arrow mt-4 text-sm" href="/get-help?service=<?= rawurlencode($service['slug']) ?>">Ask a question about pricing <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </aside>
    </div>
</section>

<section class="section">
    <div class="shell grid items-start gap-12 lg:grid-cols-[minmax(0,1fr)_360px]">
        <article>
            <span class="kicker">Service overview</span>
            <h2>What this service is about</h2>
            <div class="prose-page mt-4"><?= nl2br(View::escape($service['description'] ?? 'Contact us to discuss your requirements and confirm the scope of assistance.')) ?></div>

            <?php if (!empty($service['who_for'])): ?>
                <div class="mt-8"><h3>Who this is for</h3><div class="prose-page mt-3"><?= nl2br(View::escape($service['who_for'])) ?></div></div>
            <?php endif; ?>

            <?php if (!empty($service['what_included'])): ?>
                <div class="mt-8"><h3>What we can help with</h3><div class="prose-page mt-3"><?= nl2br(View::escape($service['what_included'])) ?></div></div>
            <?php endif; ?>

            <?php if (!empty($service['what_needed'])): ?>
                <div class="mt-8"><h3>What you may need</h3><div class="prose-page mt-3"><?= nl2br(View::escape($service['what_needed'])) ?></div></div>
            <?php endif; ?>
        </article>

        <aside class="grid gap-4 lg:sticky lg:top-24">
            <div class="card p-6">
                <span class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-primary-light text-primary"><i class="fa-solid fa-comments" aria-hidden="true"></i></span>
                <h2 class="text-xl">Ready to get started?</h2>
                <p class="mt-2 text-sm">Send us a short description of what you need. We will confirm any questions, the scope and the charges before starting.</p>
                <a class="btn btn-whatsapp mt-5 w-full" href="/get-help?service=<?= rawurlencode($service['slug']) ?>">Discuss this service</a>
                <div class="mt-4 flex items-center gap-2 text-xs text-muted"><i class="fa-solid fa-circle-info" aria-hidden="true"></i> No obligation to enquire</div>
            </div>
            <?php if ((string) $service['category_slug'] === 'government-business-assistance'): ?>
                <p class="notice notice-warn"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><span>AlbaTech Solutions is independent and is not affiliated with a government agency. Final decisions, requirements and processing times are determined by the relevant authority.</span></p>
            <?php endif; ?>
        </aside>
    </div>
</section>

<?php if (!empty($faqItems)): ?>
<section class="section section-tint">
    <div class="shell">
        <div class="mb-8 max-w-2xl"><span class="kicker">Common questions</span><h2>Before you get started</h2></div>
        <div class="faq-list">
            <?php foreach ($faqItems as $item): ?>
                <details class="faq">
                    <summary><?= View::escape($item['question']) ?></summary>
                    <p><?= View::escape($item['answer']) ?></p>
                </details>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($relatedProjects)): ?>
<section class="section">
    <div class="shell">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div><span class="kicker">Examples</span><h2>Related work</h2></div>
            <a class="link-arrow text-sm" href="/work">Explore all projects <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </div>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($relatedProjects as $project): $relatedUrl = (($project['kind'] ?? 'project') === 'product' ? '/products/' : '/work/') . rawurlencode($project['slug']); ?>
                <article class="card card-lift group overflow-hidden">
                    <a class="media-16-10 block" href="<?= View::escape($relatedUrl) ?>"><img class="transition-transform duration-300 group-hover:scale-[1.02]" src="/<?= View::escape(ltrim($project['cover_image'], '/')) ?>" alt="<?= View::escape($project['title']) ?>" width="600" height="400" loading="lazy" decoding="async"></a>
                    <div class="p-5">
                        <span class="text-xs font-bold uppercase tracking-wide text-primary"><?= View::escape(($project['kind'] ?? 'project') === 'product' ? 'Product' : $project['category_name']) ?></span>
                        <h3 class="mt-2 text-lg"><a class="text-ink hover:text-primary" href="<?= View::escape($relatedUrl) ?>"><?= View::escape($project['title']) ?></a></h3>
                        <p class="mt-2 text-sm"><?= View::escape($project['description']) ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="border-t border-border bg-surface py-10">
    <div class="shell flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
        <div><h2>Need help choosing?</h2><p class="mt-1">Tell us what you are trying to achieve and we will help you identify the right next step.</p></div>
        <a class="btn btn-secondary w-full shrink-0 md:w-auto" href="/get-help">Contact AlbaTech <i class="fa-solid fa-arrow-right text-sm" aria-hidden="true"></i></a>
    </div>
</section>
