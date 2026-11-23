<?php
use AlbaTech\Core\View;
$steps = [
    ['icon' => 'fa-building-circle-check', 'title' => 'Register the business', 'text' => 'Business name or limited company registration with BRS, and the CR12 when you need it.', 'url' => '/services/business-registration'],
    ['icon' => 'fa-file-invoice-dollar', 'title' => 'KRA PIN and tax obligations', 'text' => 'Business KRA PIN, the right tax obligations, and a reminder plan so returns are not missed.', 'url' => '/services/kra-returns-filing'],
    ['icon' => 'fa-receipt', 'title' => 'eTIMS invoicing', 'text' => 'Set up eTIMS so you can issue compliant invoices from day one.', 'url' => '/services/etims-invoice-support'],
    ['icon' => 'fa-globe', 'title' => '.co.ke domain, email and website', 'text' => 'Your domain registered in the business name, a professional email address and a clear business website.', 'url' => '/services/website-design'],
    ['icon' => 'fa-map-location-dot', 'title' => 'Google Business Profile', 'text' => 'Your business on Google Search and Maps, so local customers can find, call and review you.', 'url' => '/services/google-business-profile-setup'],
];
?>
<?php if (!empty($breadcrumbs)): ?>
<nav class="breadcrumbs shell" aria-label="Breadcrumb">
    <ol>
        <?php foreach ($breadcrumbs as $index => $crumb): $isLast = $index === count($breadcrumbs) - 1; ?>
            <li><?php if (!$isLast && !empty($crumb['url'])): ?><a href="<?= View::escape($crumb['url']) ?>"><?= View::escape($crumb['name']) ?></a><?php else: ?><span aria-current="page"><?= View::escape($crumb['name']) ?></span><?php endif; ?></li>
        <?php endforeach; ?>
    </ol>
</nav>
<?php endif; ?>

<section class="border-b border-border bg-surface">
    <div class="shell grid items-center gap-10 py-12 md:py-16 lg:grid-cols-[1.1fr_0.9fr]">
        <div>
            <span class="kicker">SME Launchpad</span>
            <h1>Register your business, get compliant and get online, in one go.</h1>
            <p class="mt-4 max-w-2xl text-lg leading-relaxed">One team handles the registration, KRA and eTIMS setup, your .co.ke domain and website, and your Google Business Profile. You deal with one contact instead of five.</p>
            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <a class="btn btn-whatsapp" href="<?= View::escape($whatsAppUrl) ?>"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Start your launch</a>
                <a class="btn btn-secondary" href="/work">See our work</a>
            </div>
        </div>
        <aside class="card p-6">
            <span class="text-xs font-extrabold uppercase tracking-[0.12em] text-muted">Pricing</span>
            <strong class="mt-2 block text-xl font-extrabold tracking-tight text-ink">Fixed quote after a short call</strong>
            <p class="mt-2 text-sm">You pick the steps you need. We confirm one price in writing before starting. Government fees are paid to the authority and listed separately.</p>
            <ul class="check-list mt-4 text-sm">
                <li>Domain and website owned by you</li>
                <li>Logins handed over at the end</li>
                <li>Your documents kept confidential</li>
            </ul>
        </aside>
    </div>
</section>

<section class="section">
    <div class="shell">
        <div class="mb-8 max-w-2xl"><span class="kicker">What is included</span><h2>Five steps from idea to open for business</h2><p class="mt-3">Already done some of them? Skip those. Each step links to the full service page.</p></div>
        <ol class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($steps as $index => $step): ?>
                <li class="card card-lift flex flex-col p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary-light text-primary"><i class="fa-solid <?= View::escape($step['icon']) ?>" aria-hidden="true"></i></span>
                        <span class="text-sm font-extrabold text-primary"><?= sprintf('%02d', $index + 1) ?></span>
                    </div>
                    <h3 class="text-lg"><?= View::escape($step['title']) ?></h3>
                    <p class="mt-2 flex-1 text-sm"><?= View::escape($step['text']) ?></p>
                    <a class="link-arrow mt-4 text-sm" href="<?= View::escape($step['url']) ?>">Service details <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </li>
            <?php endforeach; ?>
            <li class="card flex flex-col border-dashed bg-surface-muted p-6">
                <span class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-surface text-primary"><i class="fa-solid fa-gears" aria-hidden="true"></i></span>
                <h3 class="text-lg">Next: run it better</h3>
                <p class="mt-2 flex-1 text-sm">Once you are trading, we can build the booking, stock or rental system your business needs.</p>
                <a class="link-arrow mt-4 text-sm" href="/solutions">Explore solutions <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            </li>
        </ol>
    </div>
</section>

<section class="section section-tint">
    <div class="shell">
        <div class="mx-auto mb-8 max-w-2xl text-center"><span class="kicker">How it works</span><h2>Simple from the first message</h2></div>
        <div class="grid gap-5 md:grid-cols-3">
            <article class="card p-6"><span class="text-sm font-extrabold text-primary">01</span><h3 class="mt-3">Tell us about the business</h3><p class="mt-2 text-sm">Name ideas, what you sell and which steps you have already done.</p></article>
            <article class="card p-6"><span class="text-sm font-extrabold text-primary">02</span><h3 class="mt-3">Get one fixed quote</h3><p class="mt-2 text-sm">We list each step, the price, the government fees and the expected timeline.</p></article>
            <article class="card p-6"><span class="text-sm font-extrabold text-primary">03</span><h3 class="mt-3">Launch with everything handed over</h3><p class="mt-2 text-sm">Certificates, PIN, domain, website and Google profile, with all logins in your name.</p></article>
        </div>
    </div>
</section>

<?php if (!empty($faqItems)): ?>
<section class="section">
    <div class="shell">
        <div class="mb-8 max-w-2xl"><span class="kicker">Common questions</span><h2>Before you get started</h2></div>
        <div class="faq-list">
            <?php foreach ($faqItems as $item): ?>
                <details class="faq"><summary><?= View::escape($item['question']) ?></summary><p><?= View::escape($item['answer']) ?></p></details>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="bg-primary py-12 text-white md:py-14">
    <div class="shell flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
        <div class="max-w-2xl">
            <span class="kicker kicker-light">Ready to launch?</span>
            <h2 class="text-white">Tell us about your business idea.</h2>
            <p class="mt-2 text-white/85">We will reply with the steps you need and a fixed quote.</p>
        </div>
        <a class="btn btn-accent w-full shrink-0 md:w-auto" href="<?= View::escape($whatsAppUrl) ?>"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Chat on WhatsApp</a>
    </div>
</section>
