<?php use AlbaTech\Core\View; $article = is_array($guideArticle ?? null) ? $guideArticle : []; ?>
<article class="guide-article">
    <header class="guide-article__hero">
        <div class="container guide-article__hero-inner">
            <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="/">Home</a><span aria-hidden="true">/</span><a href="/guides">Guides</a><span aria-hidden="true">/</span><span><?= View::escape($article['category'] ?? 'Article') ?></span></nav>
            <span class="eyebrow"><?= View::escape($article['category'] ?? 'Guide') ?></span>
            <h1><?= View::escape($article['title'] ?? 'Guide') ?></h1>
            <p class="guide-article__intro"><?= View::escape($article['intro'] ?? '') ?></p>
            <div class="guide-article__byline">
                <span>Prepared by <?= View::escape($article['author'] ?? 'AlbaTech Solutions') ?></span>
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
    <div class="container guide-article__layout">
        <div class="guide-article__body">
            <?php foreach (($article['sections'] ?? []) as $section): ?>
                <section class="guide-article__section">
                    <h2><?= View::escape($section['heading'] ?? '') ?></h2>
                    <?php foreach (($section['paragraphs'] ?? []) as $paragraph): ?><p><?= View::escape($paragraph) ?></p><?php endforeach; ?>
                    <?php if (!empty($section['bullets']) && is_array($section['bullets'])): ?><ul><?php foreach ($section['bullets'] as $bullet): ?><li><?= View::escape($bullet) ?></li><?php endforeach; ?></ul><?php endif; ?>
                    <?php if (!empty($section['link']) && is_array($section['link'])): ?><p><a class="text-link" href="<?= View::escape($section['link'][0] ?? '/services') ?>"><?= View::escape($section['link'][1] ?? 'Learn more') ?> <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></p><?php endif; ?>
                </section>
            <?php endforeach; ?>
            <aside class="guide-article__disclaimer"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><p>Guides provide general information and may not cover every situation. Confirm current requirements with the relevant official source before making decisions or submitting applications.</p></aside>
            <div class="guide-article__back"><a class="text-link" href="/guides"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Back to all guides</a></div>
        </div>
        <aside class="guide-article__sidebar">
            <div class="guide-article__sidebar-card"><span class="section-kicker">Need a hand?</span><h2>Let's work out the next step.</h2><p>Tell us what you need and ask about the services currently available.</p><a class="button button--whatsapp" href="/get-help">Make an enquiry</a></div>
            <div class="guide-article__sidebar-links"><h2>More resources</h2><a href="/services">Explore services</a><a href="/about">About AlbaTech Solutions</a><a href="/get-help">Contact</a></div>
        </aside>
    </div>
</article>
