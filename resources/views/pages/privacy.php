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
    ['id' => 'who', 'title' => 'Who we are'],
    ['id' => 'information', 'title' => 'Information we collect'],
    ['id' => 'use', 'title' => 'How information is used'],
    ['id' => 'whatsapp', 'title' => 'WhatsApp and Meta'],
    ['id' => 'sharing', 'title' => 'Sharing and providers'],
    ['id' => 'security', 'title' => 'Security and retention'],
    ['id' => 'rights', 'title' => 'Your rights'],
    ['id' => 'deletion', 'title' => 'Data deletion'],
    ['id' => 'contact', 'title' => 'Updates and contact'],
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

<section class="page-hero">
    <div class="shell py-12 md:py-16">
        <span class="kicker">Your information</span>
        <h1 class="max-w-3xl">Privacy policy</h1>
        <p class="mt-4 max-w-2xl text-lg leading-relaxed">We handle personal information shared through this website, our enquiry form and our WhatsApp business number carefully, and use it only for legitimate service and communication purposes.</p>
        <p class="mt-3 text-sm text-subtle">Last updated: 26 November 2026</p>
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
                <section id="who"><h2>1. Who we are</h2><p>AlbaTech Solutions is an independent technology service provider based in Nairobi, Kenya. For the purposes of Kenya's Data Protection Act, 2019, AlbaTech Solutions is the data controller for personal information collected through this website, the enquiry form and our direct communication channels.</p></section>
                <section id="information"><h2>2. Information we collect</h2><p>When you contact us, you may choose to share details such as your name, phone number, email address, project requirements or information needed to respond to your enquiry. When you message us on WhatsApp, we receive your WhatsApp name, phone number and the content of your messages. With your consent, this website may also collect analytics information about how pages are used. Please share only what is reasonably necessary for the task.</p></section>
                <section id="use"><h2>3. How information is used</h2><p>Information is used to respond to you, understand a requested service, prepare a quote, deliver agreed work, provide support and maintain appropriate business records. We do not ask you to send passwords, PINs, one-time verification codes or M-Pesa credentials through an enquiry or on WhatsApp.</p></section>
                <section id="whatsapp"><h2>4. WhatsApp and Meta</h2><p>If you contact us through our WhatsApp business number, your messages are processed by Meta Platforms, Inc. through the WhatsApp Business Platform in addition to being received by us. Meta processes this information under its own terms and privacy policy. We use WhatsApp conversation information only to respond to your enquiry and provide the requested service, and we do not use it for unrelated marketing.</p></section>
                <section id="sharing"><h2>5. Sharing and service providers</h2><p>We do not sell your personal information. Information may be processed by communication providers (including Meta for WhatsApp messaging), email and hosting providers where needed to operate the website or provide a requested service, or disclosed where required by law. External services have their own privacy practices and terms.</p></section>
                <section id="security"><h2>6. Security and retention</h2><p>Reasonable safeguards are used to protect information from unauthorised access, loss or misuse. No internet transmission or storage method can be guaranteed completely secure. Information is retained only for as long as reasonably needed for the purpose collected, business records or applicable legal obligations.</p></section>
                <section id="rights"><h2>7. Your rights</h2><p>Under the Data Protection Act, 2019 you may have the right to access your personal information, request correction of inaccurate information, request deletion, object to or restrict certain processing, and withdraw consent where processing is based on consent. You may also lodge a complaint with the Office of the Data Protection Commissioner (ODPC) if you believe your information has been mishandled. To exercise any of these rights, <a href="/get-help">contact us</a>.</p></section>
                <section id="deletion"><h2>8. Data deletion</h2><p>You can ask us to delete personal information we hold about you at any time. See our <a href="/data-deletion">data deletion instructions</a> for how to submit a request, how requests are verified and what information may need to be retained by law.</p></section>
                <section id="contact"><h2>9. Updates and contact</h2><p>This policy may be updated as our website and services change. The current version will be published here. For a privacy question or request, <a href="/get-help">contact us</a> or use the details on our <a href="/contact-details">contact details page</a>.</p></section>
            <?php endif; ?>
        </article>
    </div>
</section>
