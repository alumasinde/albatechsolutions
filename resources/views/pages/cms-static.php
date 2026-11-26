<?php

use AlbaTech\Core\View;

/**
 * @var array<string, mixed> $cmsPage
 * @var string $founderName
 * @var string $founderTitle
 * @var string $founderBio
 * @var string $founderLinkedin
 */
$founderName = trim((string) ($founderName ?? ''));
$founderTitle = trim((string) ($founderTitle ?? ''));
$founderBio = trim((string) ($founderBio ?? ''));
$founderLinkedin = trim((string) ($founderLinkedin ?? ''));
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
        <span class="kicker">About</span>
        <h1 class="max-w-3xl"><?= View::escape($cmsPage['title'] ?? '') ?></h1>
        <?php if (trim((string) ($cmsPage['intro'] ?? '')) !== ''): ?>
            <p class="mt-4 max-w-2xl text-lg leading-relaxed"><?= View::escape($cmsPage['intro']) ?></p>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="shell">
        <article class="prose-page max-w-[72ch]">
            <?php $body = trim((string) ($cmsPage['body_content'] ?? '')); ?>
            <?php if ($body !== ''): ?>
                <?php foreach (preg_split('/\R\s*\R/', $body) ?: [] as $paragraph): ?>
                    <?php if (trim($paragraph) !== ''): ?><p><?= nl2br(View::escape(trim($paragraph)), false) ?></p><?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </article>
    </div>
</section>

<?php if ($founderName !== ''): ?>
<section class="section-tint" id="founder">
    <div class="shell grid gap-8 py-14 md:grid-cols-[minmax(0,1fr)_auto] md:items-center md:py-16">
        <div class="max-w-2xl">
            <span class="kicker">Who you work with</span>
            <h2 class="mt-2">Meet <?= View::escape($founderName) ?></h2>
            <?php if ($founderTitle !== ''): ?><p class="mt-2 text-sm font-semibold text-primary"><?= View::escape($founderTitle) ?></p><?php endif; ?>
            <?php if ($founderBio !== ''): ?><p class="mt-4 leading-relaxed text-muted"><?= nl2br(View::escape($founderBio), false) ?></p><?php endif; ?>
            <?php if ($founderLinkedin !== '' && filter_var($founderLinkedin, FILTER_VALIDATE_URL)): ?>
                <a class="link-arrow mt-4" href="<?= View::escape($founderLinkedin) ?>" target="_blank" rel="noopener">Connect on LinkedIn <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
            <?php endif; ?>
        </div>
        <a class="btn btn-primary md:justify-self-end" href="/get-help">Talk to <?= View::escape(explode(' ', $founderName)[0]) ?></a>
    </div>
</section>
<?php endif; ?>

<?php if ($founderName === ''): ?>
<section class="section">
    <div class="shell">
        <div class="flex flex-col items-start justify-between gap-5 rounded-2xl border border-border bg-surface p-6 md:flex-row md:items-center md:p-8">
            <div>
                <h2 class="text-2xl">Tell us what you need</h2>
                <p class="mt-2 text-muted">Describe the website, system or task and we will suggest a sensible next step.</p>
            </div>
            <a class="btn btn-primary" href="/get-help">Start an enquiry <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </div>
    </div>
</section>
<?php endif; ?>
