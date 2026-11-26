<?php

use AlbaTech\Core\View;
use AlbaTech\Repositories\SiteSettingsRepository;

$legalSettings = (new SiteSettingsRepository())->publicValues();
$legalBody = trim((string) ($legalSettings['data_deletion_content'] ?? ''));

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
    ['id' => 'what', 'title' => 'What you can ask us to delete'],
    ['id' => 'request', 'title' => 'How to request deletion'],
    ['id' => 'verification', 'title' => 'How we verify requests'],
    ['id' => 'process', 'title' => 'What happens next'],
    ['id' => 'retained', 'title' => 'Information we may keep'],
    ['id' => 'meta', 'title' => 'WhatsApp and Meta data'],
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

<section class="page-hero">
    <div class="shell py-12 md:py-16">
        <span class="kicker">Your information</span>
        <h1 class="max-w-3xl">Data deletion instructions</h1>
        <p class="mt-4 max-w-2xl text-lg leading-relaxed">You can ask AlbaTech Solutions to delete personal information you have shared with us through this website, the enquiry form, email or WhatsApp. This page explains how.</p>
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
                <section id="what"><h2>1. What you can ask us to delete</h2><p>You can request deletion of personal information we hold about you, such as your name, phone number, email address, enquiry details and records of conversations you have had with us on this website or through our WhatsApp business number.</p><p>You do not need to give a reason, and there is no charge for making a deletion request.</p></section>
                <section id="request"><h2>2. How to request deletion</h2><p>Send your request through either of these channels:</p><p><strong>Enquiry form:</strong> submit a request at <a href="/get-help">albatechsolutions.co.ke/get-help</a> and state that you would like your personal information deleted.<br><strong>Email:</strong> write to us using the contact email shown on our <a href="/contact-details">contact details page</a> with the subject line &ldquo;Data deletion request&rdquo;.<br><strong>WhatsApp:</strong> message our WhatsApp business number and ask for your information to be deleted.</p><p>Please include the name and the phone number or email address you used when contacting us, so we can find the right records.</p></section>
                <section id="verification"><h2>3. How we verify requests</h2><p>Before deleting anything, we confirm that the request genuinely comes from you. We normally do this by replying through the same channel you used to contact us, such as the phone number or email address on record. We will never ask you for passwords, PINs, one-time codes or payment credentials to verify a deletion request.</p></section>
                <section id="process"><h2>4. What happens next</h2><p>Once your request is verified, we delete or anonymise your personal information from our enquiry records and conversation history within 30 days. We will confirm to you when the deletion is complete. If we cannot complete a request (for example, where a legal obligation requires us to keep a record), we will explain why.</p></section>
                <section id="retained"><h2>5. Information we may keep</h2><p>Some information may be retained where the law requires or permits it, such as invoices, payment records and other business records needed for tax or accounting purposes under Kenyan law. Information kept for these reasons is limited to what is necessary and is not used for any other purpose.</p></section>
                <section id="meta"><h2>6. WhatsApp and Meta data</h2><p>When you message us on WhatsApp, your messages are also processed by Meta Platforms under their own terms and privacy policy. Deleting information from our records does not delete data Meta holds under your own WhatsApp or Facebook account. You can manage information held by Meta through your account settings in the relevant Meta app.</p><p>Read our <a href="/privacy">privacy policy</a> for more detail on how WhatsApp conversations are handled.</p></section>
                <section id="contact"><h2>7. Contact</h2><p>Questions about deletion or about how your information is handled? <a href="/get-help">Contact AlbaTech Solutions</a> and we will help.</p></section>
            <?php endif; ?>
        </article>
    </div>
</section>
