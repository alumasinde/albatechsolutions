<?php

use AlbaTech\Core\View; ?>
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
        <span class="kicker">Guides &amp; resources</span>
        <h1 class="max-w-3xl">Practical Guides for Kenyan Businesses and Individuals</h1>
        <p class="mt-4 max-w-3xl text-lg leading-relaxed">Straightforward guides on business registration, KRA filing, eTIMS, passports, websites and online visibility. Written for people who want to understand the actual steps and costs before paying anyone for help.</p>
    </div>
</section>

<section class="section">
    <div class="shell">
        <div class="mb-8"><h2>Browse Our Latest Guides</h2></div>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach (($guideArticles ?? []) as $slug => $article): $guideUrl = '/guides/' . rawurlencode((string) $slug); ?>
                <article class="card card-lift flex flex-col p-5">
                    <div class="flex items-center justify-between text-xs font-semibold text-subtle">
                        <span class="rounded-full bg-primary-light px-2.5 py-1 font-bold text-secondary"><?= View::escape($article['category'] ?? 'Guide') ?></span>
                        <span><?= View::escape(date('M Y', strtotime((string) ($article['updated'] ?? 'now')))) ?></span>
                    </div>
                    <h2 class="mt-4 text-lg leading-snug"><a class="text-ink hover:text-primary" href="<?= $guideUrl ?>"><?= View::escape($article['title'] ?? '') ?></a></h2>
                    <p class="mt-2 flex-1 text-sm"><?= View::escape($article['summary'] ?? '') ?></p>
                    <a class="link-arrow mt-4 text-sm" href="<?= $guideUrl ?>">Read more <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="card mt-10 flex flex-col gap-5 p-6 md:flex-row md:items-center md:justify-between">
            <div>
                <span class="kicker">Need help with a specific task?</span>
                <h2 class="text-2xl">Start with a conversation.</h2>
                <p class="mt-2">Tell us what you are trying to achieve and we can discuss the available options.</p>
            </div>
            <a class="btn btn-primary w-full shrink-0 md:w-auto" href="/get-help">Contact AlbaTech Solutions</a>
        </div>
    </div>
</section>
