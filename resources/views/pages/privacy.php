<?php

use AlbaTech\Core\View;
use AlbaTech\Repositories\SiteSettingsRepository;

$legalSettings = (new SiteSettingsRepository())->publicValues();
$legalBody = trim((string) ($legalSettings['privacy_content'] ?? ''));

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
    ['id' => 'information', 'title' => 'Information you share'],
    ['id' => 'use', 'title' => 'How information is used'],
    ['id' => 'sharing', 'title' => 'Sharing and providers'],
    ['id' => 'security', 'title' => 'Security and retention'],
    ['id' => 'choices', 'title' => 'Your choices'],
    ['id' => 'contact', 'title' => 'Contact'],
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
        <span class="kicker">Your information</span>
        <h1 class="max-w-3xl">Privacy policy</h1>
        <p class="mt-4 max-w-2xl text-lg leading-relaxed">We aim to handle enquiry information carefully and use it only for legitimate service and communication purposes.</p>
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
                <section id="information"><h2>1. Information you share</h2><p>When you contact us, you may choose to share details such as your name, phone number, email address, project requirements or information needed to respond to your enquiry. Please share only what is reasonably necessary for the task.</p></section>
                <section id="use"><h2>2. How information is used</h2><p>Information may be used to respond to you, understand a requested service, prepare a quote, deliver agreed work, provide support and maintain appropriate business records. We do not ask you to send passwords, PINs, one-time verification codes or M-Pesa credentials through an enquiry.</p></section>
                <section id="sharing"><h2>3. Sharing and service providers</h2><p>We do not intend to sell your personal information. Information may be processed by communication, hosting or other service providers where needed to operate the website or provide a requested service, or disclosed where required by law. External services have their own privacy practices and terms.</p></section>
                <section id="security"><h2>4. Security and retention</h2><p>Reasonable safeguards should be used to protect information from unauthorised access, loss or misuse. No internet transmission or storage method can be guaranteed completely secure. Information should be retained only for as long as reasonably needed for the purpose collected, business records or applicable legal obligations.</p></section>
                <section id="choices"><h2>5. Your choices</h2><p>You can choose what information to include in an enquiry. You may contact us to ask about information you have shared, request a correction or raise a privacy concern. Applicable rights and obligations depend on the circumstances and Kenyan law.</p></section>
                <section id="contact"><h2>6. Updates and contact</h2><p>This policy may be updated as our website and services change. The current version will be published here. For a privacy question, <a href="/get-help">contact us</a>.</p></section>
            <?php endif; ?>

            <?php if ($legalBody === ''): ?>
                <div class="notice notice-warn mt-8"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i><p>This is a starter privacy notice, not legal advice or a completed compliance assessment. Review the actual data collected by your website, analytics, hosting and communication providers, and have the policy checked for compliance with Kenya's Data Protection Act and applicable regulations before launch.</p></div>
            <?php endif; ?>
        </article>
    </div>
</section>
