<?php
use AlbaTech\Core\View;
$services = is_array($services ?? null) ? $services : [];
$icons = [
    'kra-returns-filing' => 'fa-file-invoice-dollar', 'etims-invoice-support' => 'fa-receipt', 'business-registration' => 'fa-building-circle-check',
    'annual-returns-brs-compliance' => 'fa-calendar-check', 'ecitizen-application-assistance' => 'fa-passport', 'sha-registration-help-in-kenya' => 'fa-heart-pulse',
    'ntsa-smart-dl-assistance' => 'fa-id-card',
];
$guides = [
    ['title' => 'How to file a KRA nil return', 'url' => '/guides/how-to-file-a-kra-nil-return-in-kenya'],
    ['title' => 'eTIMS for Kenyan businesses', 'url' => '/guides/etims-kenya-business-guide'],
    ['title' => 'How to register a business in Kenya', 'url' => '/guides/how-to-register-a-business-in-kenya'],
    ['title' => 'BRS annual returns explained', 'url' => '/guides/brs-annual-returns-kenya'],
    ['title' => 'How to apply for a passport in Kenya', 'url' => '/guides/how-to-apply-for-a-passport-in-kenya'],
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
            <span class="kicker">Compliance help</span>
            <h1>KRA, eTIMS, eCitizen and business registration help in Kenya.</h1>
            <p class="mt-4 max-w-2xl text-lg leading-relaxed">Stuck on a portal, facing a deadline or simply short on time? Tell us the task. We confirm the price, handle it and keep you updated on WhatsApp.</p>
            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <a class="btn btn-whatsapp" href="<?= View::escape($whatsAppUrl) ?>"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Chat on WhatsApp</a>
                <a class="btn btn-secondary" href="/get-help">Send a request</a>
            </div>
        </div>
        <aside class="card p-6">
            <h2 class="text-lg"><i class="fa-solid fa-user-shield mr-2 text-primary" aria-hidden="true"></i>Your data stays private</h2>
            <p class="mt-3 text-sm">Your credentials and personal documents are handled in strict confidence, used only for the task you requested, and deleted when the work is complete.</p>
            <ul class="check-list mt-4 text-sm">
                <li>Price confirmed before we start</li>
                <li>Government fees paid directly to the authority</li>
                <li>Updates by WhatsApp, phone or email</li>
            </ul>
        </aside>
    </div>
</section>

<section class="section">
    <div class="shell">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div class="max-w-2xl"><span class="kicker">What we handle</span><h2>Pick the task you need done</h2></div>
            <p class="max-w-xl text-sm">Each page explains what is included and what we need from you.</p>
        </div>
        <?php if (!empty($services)): ?>
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($services as $service):
                    $url = '/services/' . rawurlencode($service['slug']);
                    $hasPrice = in_array((string) $service['pricing_type'], ['fixed', 'starting_at'], true) && is_numeric($service['pricing_amount']);
                ?>
                    <article class="card card-lift flex flex-col p-6">
                        <span class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-primary-light text-primary"><i class="fa-solid <?= View::escape($icons[$service['slug']] ?? 'fa-file-circle-check') ?>" aria-hidden="true"></i></span>
                        <h3 class="text-lg"><a class="text-ink hover:text-primary" href="<?= $url ?>"><?= View::escape($service['name']) ?></a></h3>
                        <p class="mt-2 flex-1 text-sm"><?= View::escape($service['short_description']) ?></p>
                        <p class="mt-4 text-sm font-bold text-ink">
                            <?php if ($hasPrice): $amount = (float) $service['pricing_amount']; ?>
                                <?= $service['pricing_type'] === 'starting_at' ? 'From ' : '' ?>KSh <?= View::escape(number_format($amount, $amount == (int) $amount ? 0 : 2)) ?>
                            <?php else: ?><span class="font-semibold text-muted"><?= View::escape($service['pricing_label'] ?: 'Price confirmed on request') ?></span><?php endif; ?>
                        </p>
                        <?php if (!empty($service['turnaround'])): ?><p class="mt-1 text-xs font-semibold text-muted"><i class="fa-regular fa-clock mr-1" aria-hidden="true"></i><?= View::escape($service['turnaround']) ?></p><?php endif; ?>
                        <a class="link-arrow mt-3 text-sm" href="<?= $url ?>">View details <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card mx-auto max-w-xl p-8 text-center"><h2>Tell us what you need</h2><p class="mt-2">Send the task in your own words and we will confirm the price and next steps.</p><a class="btn btn-secondary mt-5" href="/get-help">Send a request</a></div>
        <?php endif; ?>
    </div>
</section>

<section class="section section-tint">
    <div class="shell grid items-start gap-10 lg:grid-cols-2">
        <div>
            <span class="kicker">Prefer to do it yourself?</span>
            <h2>Free step-by-step guides</h2>
            <p class="mt-3">Each guide walks through the official process. If you get stuck halfway, send us a message and we can pick it up from there.</p>
            <a class="link-arrow mt-2 text-sm" href="/guides">All guides <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </div>
        <ul class="grid gap-3">
            <?php foreach ($guides as $guide): ?>
                <li><a class="card card-lift flex items-center justify-between gap-4 p-4 font-semibold text-ink hover:text-primary" href="<?= View::escape($guide['url']) ?>"><span><i class="fa-regular fa-file-lines mr-2 text-primary" aria-hidden="true"></i><?= View::escape($guide['title']) ?></span><i class="fa-solid fa-arrow-right text-sm" aria-hidden="true"></i></a></li>
            <?php endforeach; ?>
        </ul>
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
            <span class="kicker kicker-light">Short on time?</span>
            <h2 class="text-white">Let us handle it for you today.</h2>
            <p class="mt-2 text-white/85">Send the task on WhatsApp and we will reply with the price and what we need from you.</p>
        </div>
        <a class="btn btn-accent w-full shrink-0 md:w-auto" href="<?= View::escape($whatsAppUrl) ?>"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Chat on WhatsApp</a>
    </div>
</section>

<section class="border-y border-warning-border bg-warning-bg py-5">
    <div class="shell flex gap-3 text-sm leading-relaxed text-warning">
        <i class="fa-solid fa-circle-info mt-1" aria-hidden="true"></i>
        <p class="m-0 text-warning">AlbaTech Solutions is an independent service provider, not a government agency. Official decisions, requirements and processing times remain with the relevant authority.</p>
    </div>
</section>
