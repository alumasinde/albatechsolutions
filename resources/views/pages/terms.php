<?php

use AlbaTech\Core\View;
use AlbaTech\Repositories\SiteSettingsRepository;

$legalSettings = (new SiteSettingsRepository())->publicValues();
$legalBody = trim((string) ($legalSettings['terms_content'] ?? ''));

$legalSlug = static function (string $title): string {
    $slug = trim((string) preg_replace('/[^a-z0-9]+/', '-', strtolower($title)), '-');

    return $slug !== '' ? $slug : 'section';
};

$legalSections = [];
foreach (preg_split("/\R{2,}/", $legalBody) ?: [] as $paragraph) {
    $paragraph = trim($paragraph);
    if ($paragraph !== '' && str_starts_with($paragraph, '# ')) {
        $title = trim(substr($paragraph, 2));
        $legalSections[] = ['id' => $legalSlug($title), 'title' => $title];
    }
}

$toc = $legalBody !== '' ? $legalSections : [
    ['id' => 'website', 'title' => 'Using this website'],
    ['id' => 'services', 'title' => 'Services and scope'],
    ['id' => 'pricing', 'title' => 'Pricing and payment'],
    ['id' => 'third-parties', 'title' => 'Third-party services'],
    ['id' => 'responsibilities', 'title' => 'Your responsibilities'],
    ['id' => 'changes', 'title' => 'Changes and contact'],
];
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
        <span class="kicker">Legal information</span>
        <h1 class="max-w-3xl">Terms of service</h1>
        <p class="mt-4 max-w-2xl text-lg leading-relaxed">Clear expectations help us work together. These general terms explain how to use this website and engage AlbaTech Solutions.</p>
        <p class="mt-3 text-sm text-subtle">Last updated: 9 October 2026</p>
    </div>
</section>

<section class="section">
    <div class="shell grid gap-10 lg:grid-cols-[240px_minmax(0,1fr)]">
        <?php if ($toc !== []): ?>
            <aside class="lg:sticky lg:top-24 lg:self-start">
                <nav class="rounded-2xl border border-border bg-surface p-5" aria-label="On this page">
                    <h2 class="text-xs font-extrabold uppercase tracking-[0.14em] text-subtle">On this page</h2>
                    <ul class="mt-3 grid gap-1 text-sm">
                        <?php foreach ($toc as $item): ?>
                            <li><a class="block rounded-lg px-2 py-1.5 text-muted transition-colors hover:bg-surface-muted hover:text-primary" href="#<?= View::escape($item['id']) ?>"><?= View::escape($item['title']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            </aside>
        <?php endif; ?>

        <article class="prose-page max-w-[72ch]">
            <?php if ($legalBody !== ''): ?>
                <?php foreach (preg_split("/\R{2,}/", $legalBody) ?: [] as $paragraph): ?>
                    <?php $paragraph = trim($paragraph); if ($paragraph === '') continue; ?>
                    <?php if (str_starts_with($paragraph, '# ')): ?>
                        <?php $heading = trim(substr($paragraph, 2)); ?>
                        <h2 id="<?= View::escape($legalSlug($heading)) ?>"><?= View::escape($heading) ?></h2>
                    <?php else: ?>
                        <p><?= nl2br(View::escape($paragraph), false) ?></p>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <section id="website"><h2>1. Using this website</h2><p>By accessing this website, you agree to use it lawfully and not to interfere with its security, availability or normal operation. Website content is provided for general information and may be updated from time to time.</p></section>
                <section id="services"><h2>2. Services and scope</h2><p>AlbaTech Solutions offers digital services that may include website design, online presence, software and workflow assistance, and help with selected online processes. The exact deliverables, timeline, required information and exclusions should be agreed before paid work begins.</p><p>Submitting an enquiry does not automatically create a service contract or guarantee availability, acceptance or a particular outcome.</p></section>
                <section id="pricing"><h2>3. Pricing and payment</h2><p>Any prices displayed on the website are subject to the scope stated for the relevant service. Where a service is marked as requiring a quote, we will discuss the requirements and confirm the applicable charges before work starts. Do not assume third-party, government, hosting, domain or subscription fees are included unless explicitly agreed.</p></section>
                <section id="third-parties"><h2>4. Third-party and government services</h2><p>Some work may involve external platforms, providers or government portals. Those services are controlled by their respective operators, and their own terms, charges, availability and decisions apply. AlbaTech Solutions is an independent service provider and is not a government agency or an official government representative.</p><p>We cannot guarantee approval, processing time, uninterrupted availability or decisions made by a third party.</p></section>
                <section id="responsibilities"><h2>5. Your responsibilities</h2><p>You are responsible for ensuring that information and documents you provide are accurate and that you are authorised to share them. Keep your passwords, PINs, one-time codes and payment credentials private. Do not ask us to create false documents, misrepresent information or bypass lawful processes.</p></section>
                <section id="changes"><h2>6. Changes and contact</h2><p>We may update website content or these terms as our services change. The version published on this page is the current website version. If a separate written agreement applies to a specific project, that agreement should be reviewed alongside these general terms.</p><p>Questions? <a href="/get-help">Contact AlbaTech Solutions</a>.</p></section>
            <?php endif; ?>

            <?php if ($legalBody === ''): ?>
                <div class="notice notice-warn mt-8"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i><p>These terms are a general website template, not legal advice. Have a qualified Kenyan legal professional review them before relying on them as a binding commercial agreement.</p></div>
            <?php endif; ?>
        </article>
    </div>
</section>
