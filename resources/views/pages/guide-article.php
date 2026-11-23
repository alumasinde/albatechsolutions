<?php
use AlbaTech\Core\View;

$article = is_array($guideArticle ?? null) ? $guideArticle : [];

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

<article class="guide-article">
    <header class="border-b border-border bg-surface">
        <div class="shell py-12 md:py-16">
            <span class="kicker"><?= View::escape($article['category'] ?? 'Guide') ?></span>
            <h1 class="max-w-3xl"><?= View::escape($article['title'] ?? 'Guide') ?></h1>
            <p class="mt-4 max-w-3xl text-lg leading-relaxed"><?= View::escape($article['intro'] ?? '') ?></p>
            <?php $bylineName = trim((string) ($founderName ?? '')) ?: 'AlbaTech Solutions'; ?>
            <div class="mt-5 flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-muted">
                <span>Prepared by <?php if ($bylineName !== 'AlbaTech Solutions'): ?><a class="font-semibold text-primary" href="/about#founder"><?= View::escape($bylineName) ?></a><?php else: ?><?= View::escape($bylineName) ?><?php endif; ?></span>
                <?php $publishedDate = (string) ($article['published'] ?? ''); ?>
                <?php $updatedDate = (string) ($article['updated'] ?? ''); ?>
                <?php if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $publishedDate) === 1): ?>
                    <span aria-hidden="true">·</span><span>Published <time datetime="<?= View::escape($publishedDate) ?>"><?= View::escape(date('j F Y', strtotime($publishedDate))) ?></time></span>
                <?php endif; ?>
                <?php if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $updatedDate) === 1): ?>
                    <span aria-hidden="true">·</span><span>Updated <time datetime="<?= View::escape($updatedDate) ?>"><?= View::escape(date('j F Y', strtotime($updatedDate))) ?></time></span>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <div class="shell grid items-start gap-12 py-12 lg:grid-cols-[minmax(0,1fr)_320px] lg:py-16">
        <div>
            <?php if (!empty($article['content_html'])): ?>
                <div class="prose-page guide-rich-content"><?= $article['content_html'] /* Sanitized by GuideAdminController before persistence. */ ?></div>
            <?php else: ?>
                <?php foreach (($article['sections'] ?? []) as $section): ?>
                    <section class="mb-8">
                        <h2><?= View::escape($section['heading'] ?? '') ?></h2>
                        <?php foreach (($section['paragraphs'] ?? []) as $paragraph): ?><p class="mt-3"><?= View::escape($paragraph) ?></p><?php endforeach; ?>
                        <?php if (!empty($section['bullets']) && is_array($section['bullets'])): ?><ul class="mt-3 space-y-1.5 pl-5"><?php foreach ($section['bullets'] as $bullet): ?><li class="list-disc text-muted"><?= View::escape($bullet) ?></li><?php endforeach; ?></ul><?php endif; ?>
                        <?php if (!empty($section['link']) && is_array($section['link'])): ?><p class="mt-4"><a class="link-arrow text-sm" href="<?= View::escape($section['link'][0] ?? '/services') ?>"><?= View::escape($section['link'][1] ?? 'Learn more') ?> <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></p><?php endif; ?>
                    </section>
                <?php endforeach; ?>
            <?php endif; ?>
            <aside class="notice mt-10"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><p>Guides provide general information and may not cover every situation. Confirm current requirements with the relevant official source before making decisions or submitting applications.</p></aside>
            <div class="mt-8"><a class="link-arrow text-sm" href="/guides"><i class="fa-solid fa-arrow-left text-xs" aria-hidden="true"></i> Back to all guides</a></div>
        </div>
        <aside class="grid gap-5 lg:sticky lg:top-24">
            <div class="card p-6">
                <span class="kicker">Need a hand?</span>
                <h2 class="text-xl">Let's work out the next step.</h2>
                <p class="mt-2 text-sm">Tell us what you need and ask about the services currently available.</p>
                <a class="btn btn-whatsapp mt-5 w-full" href="/get-help">Make an enquiry</a>
            </div>
            <nav class="card p-5" aria-label="More resources">
                <h2 class="text-base font-extrabold tracking-normal text-ink">More resources</h2>
                <div class="mt-3 flex flex-col gap-2">
                    <a class="text-sm font-semibold text-primary hover:text-primary-hover" href="/services">Explore services</a>
                    <a class="text-sm font-semibold text-primary hover:text-primary-hover" href="/about">About AlbaTech Solutions</a>
                    <a class="text-sm font-semibold text-primary hover:text-primary-hover" href="/get-help">Contact</a>
                </div>
            </nav>
        </aside>
    </div>
</article>
