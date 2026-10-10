<?php use AlbaTech\Core\View; ?>
<section class="page-intro guide-intro">
    <div class="container">
        <span class="eyebrow">Guides & resources</span>
        <h1>Clear answers to common digital questions</h1>
        <p>Practical, plain-language guides for Kenyan business owners, job seekers and anyone preparing to use digital services.</p>
    </div>
</section>
<section class="section guide-library">
    <div class="container">
        <div class="guide-library__heading">
            <div><span class="section-kicker">Learn at your own pace</span><h2>Browse the guides</h2></div>
            <p>Start with the question you have today. Each guide focuses on useful preparation and realistic next steps.</p>
        </div>
        <div class="guide-grid">
            <?php foreach (($guideArticles ?? []) as $slug => $article): ?>
                <article class="guide-card">
                    <div class="guide-card__meta"><span><?= View::escape($article['category'] ?? 'Guide') ?></span><span><?= View::escape(date('M Y', strtotime((string) ($article['updated'] ?? 'now')))) ?></span></div>
                    <h2><a href="/guides/<?= rawurlencode((string) $slug) ?>"><?= View::escape($article['title'] ?? '') ?></a></h2>
                    <p><?= View::escape($article['summary'] ?? '') ?></p>
                    <a class="guide-card__link" href="/guides/<?= rawurlencode((string) $slug) ?>">Read guide <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="guide-help-cta">
            <div><span class="section-kicker">Need help with a specific task?</span><h2>Start with a conversation.</h2><p>Tell us what you are trying to achieve and we can discuss the available options.</p></div>
            <a class="button" href="/get-help">Contact AlbaTech Solutions</a>
        </div>
    </div>
</section>
