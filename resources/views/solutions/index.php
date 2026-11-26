<?php
use AlbaTech\Core\View;
$icons = ['booking-reservation-systems' => 'fa-calendar-check', 'property-rental-management-systems' => 'fa-building', 'visitor-gate-pass-management' => 'fa-id-card', 'pos-inventory-systems' => 'fa-cash-register', 'custom-internal-tools' => 'fa-sliders'];
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
            <span class="kicker">Solutions</span>
            <h1>Systems built around how your business works.</h1>
            <p class="mt-4 max-w-2xl text-lg leading-relaxed">Booking, property, visitor, stock and internal tools for Kenyan businesses. We start with the process you use today, then build the smallest system that makes it work better.</p>
            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <a class="btn btn-whatsapp" href="<?= View::escape($whatsAppUrl) ?>">Discuss your system <i class="fa-brands fa-whatsapp" aria-hidden="true"></i></a>
                <a class="btn btn-secondary" href="/work">See client work</a>
            </div>
        </div>
        <aside class="card p-6">
            <h2 class="text-lg">What we build</h2>
            <ul class="check-list mt-4 text-sm">
                <li>Booking and reservation systems</li>
                <li>Property and rental management</li>
                <li>Visitor and gate pass management</li>
                <li>POS and inventory tools</li>
                <li>Custom internal tools</li>
            </ul>
        </aside>
    </div>
</section>

<section class="section">
    <div class="shell">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div class="max-w-2xl"><span class="kicker">Systems</span><h2>Where a custom system helps</h2></div>
            <p class="max-w-xl text-sm">Each engagement is scoped separately. Prices are confirmed in writing before development begins.</p>
        </div>
        <?php if (!empty($solutions)): ?>
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($solutions as $solution): $solutionUrl = '/services/' . rawurlencode($solution['slug']); ?>
                    <article class="card card-lift flex flex-col p-6">
                        <span class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-primary-light text-primary"><i class="fa-solid <?= View::escape($icons[$solution['slug']] ?? 'fa-cubes') ?>" aria-hidden="true"></i></span>
                        <h3 class="text-lg"><a class="text-ink hover:text-primary" href="<?= $solutionUrl ?>"><?= View::escape($solution['name']) ?></a></h3>
                        <p class="mt-2 flex-1 text-sm"><?= View::escape($solution['short_description']) ?></p>
                        <a class="link-arrow mt-4 text-sm" href="<?= $solutionUrl ?>">View details <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    </article>
                <?php endforeach; ?>
                <article class="card flex flex-col border-dashed bg-surface-muted p-6">
                    <span class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-surface text-primary"><i class="fa-solid fa-circle-question" aria-hidden="true"></i></span>
                    <h3 class="text-lg">Something else?</h3>
                    <p class="mt-2 flex-1 text-sm">Tell us about the process that is slowing you down. If a small system would fix it, we will say so.</p>
                    <a class="link-arrow mt-4 text-sm" href="/get-help">Talk to us <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </article>
            </div>
        <?php else: ?>
            <div class="card mx-auto max-w-xl p-8 text-center"><h2>Solutions are being updated</h2><p class="mt-2">Tell us the process you want to improve and we will discuss the options.</p><a class="btn btn-secondary mt-5" href="/get-help">Send an enquiry</a></div>
        <?php endif; ?>
    </div>
</section>

<?php if (!empty($proofProject)): ?>
<section class="section section-tint">
    <div class="shell">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div><span class="kicker">Client work</span><h2>Built for a real business process</h2></div>
            <a class="link-arrow text-sm" href="/work">All case studies <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </div>
        <article class="card grid overflow-hidden lg:grid-cols-2">
            <a class="media-16-10 relative block" href="/work/<?= rawurlencode($proofProject['slug']) ?>"><img src="/<?= View::escape(ltrim($proofProject['cover_image'], '/')) ?>" alt="<?= View::escape($proofProject['title']) ?>" width="1200" height="800" loading="lazy" decoding="async"></a>
            <div class="flex flex-col justify-center p-6 lg:p-9">
                <span class="kicker"><?= View::escape($proofProject['category_name']) ?></span>
                <h3 class="text-xl"><?= View::escape($proofProject['title']) ?></h3>
                <p class="mt-2 text-sm"><?= View::escape($proofProject['description']) ?></p>
                <a class="link-arrow mt-4 text-sm" href="/work/<?= rawurlencode($proofProject['slug']) ?>">Read the case study <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            </div>
        </article>
    </div>
</section>
<?php endif; ?>

<section class="section">
    <div class="shell">
        <div class="mx-auto mb-8 max-w-2xl text-center"><span class="kicker">How we deliver</span><h2>A clear path from process to working system.</h2></div>
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <article class="card p-5"><span class="text-sm font-extrabold text-primary">01</span><h3 class="mt-3 text-base">Map the process</h3><p class="mt-2 text-sm">We look at how the work happens today, who is involved and where time is lost.</p></article>
            <article class="card p-5"><span class="text-sm font-extrabold text-primary">02</span><h3 class="mt-3 text-base">Agree the scope</h3><p class="mt-2 text-sm">We define the first version, the price and the milestones before development starts.</p></article>
            <article class="card p-5"><span class="text-sm font-extrabold text-primary">03</span><h3 class="mt-3 text-base">Build in stages</h3><p class="mt-2 text-sm">You see progress as it happens and can correct the direction before launch.</p></article>
            <article class="card p-5"><span class="text-sm font-extrabold text-primary">04</span><h3 class="mt-3 text-base">Hand over with support</h3><p class="mt-2 text-sm">We agree training, handover and support so your team can run the system.</p></article>
        </div>
    </div>
</section>

<section class="bg-primary py-12 text-white md:py-14">
    <div class="shell flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
        <div class="max-w-2xl">
            <span class="kicker kicker-light">Start with the process</span>
            <h2 class="text-white">What would you like to stop doing manually?</h2>
            <p class="mt-2 text-white/85">Tell us the task or process. We will tell you what a practical first version looks like.</p>
        </div>
        <a class="btn btn-whatsapp w-full shrink-0 md:w-auto" href="<?= View::escape($whatsAppUrl) ?>">Request a quote <i class="fa-brands fa-whatsapp" aria-hidden="true"></i></a>
    </div>
</section>
