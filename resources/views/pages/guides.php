<?php

use AlbaTech\Core\View; ?>
<?php if (isset($breadcrumbs) && is_array($breadcrumbs) && !empty($breadcrumbs)): ?>
<nav class="breadcrumb-nav" aria-label="Breadcrumb">
  <div class="container">
    <ol class="breadcrumb-list" itemscope itemtype="https://schema.org/BreadcrumbList">
      <?php foreach ($breadcrumbs as $index => $crumb): ?>
        <li class="breadcrumb-item<?= ($index === count($breadcrumbs) - 1) ? ' breadcrumb-item--current' : '' ?>" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
          <?php if (!empty($crumb['url']) && $index < count($breadcrumbs) - 1): ?>
            <a href="<?= View::escape($crumb['url']) ?>" itemprop="item"><span itemprop="name"><?= View::escape($crumb['name']) ?></span></a>
          <?php else: ?>
            <span itemprop="name"><?= View::escape($crumb['name']) ?></span>
          <?php endif; ?>
          <meta itemprop="position" content="<?= $index + 1 ?>">
        </li>
      <?php endforeach; ?>
    </ol>
  </div>
</nav>
<?php endif; ?>
<section class="page-intro guide-intro">
    <div class="container">
        <span class="eyebrow">Guides & resources</span>
        <h1>Practical Digital Guides and Tech Tips for Kenya</h1>
        <p>Learn how to build a business website, choose web hosting, improve your online presence and use digital services. Get simple, practical advice for Kenyan businesses, entrepreneurs and job seekers.</p>
    </div>
</section>
<section class="section guide-library">
    <div class="container">
        <div class="guide-library__heading">
            <div>
                <h2>Browse Our Latest Guides</h2>
            </div>
        </div>
        <div class="guide-grid">
            <?php foreach (($guideArticles ?? []) as $slug => $article): ?>
                <article class="guide-card">
                    <div class="guide-card__meta"><span><?= View::escape($article['category'] ?? 'Guide') ?></span><span><?= View::escape(date('M Y', strtotime((string) ($article['updated'] ?? 'now')))) ?></span></div>
                    <h2><a href="/guides/<?= rawurlencode((string) $slug) ?>"><?= View::escape($article['title'] ?? '') ?></a></h2>
                    <p><?= View::escape($article['summary'] ?? '') ?></p>
                    <a class="guide-card__link" href="/guides/<?= rawurlencode((string) $slug) ?>">Read More</a>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="guide-help-cta">
            <div><span class="section-kicker">Need help with a specific task?</span>
                <h2>Start with a conversation.</h2>
                <p>Tell us what you are trying to achieve and we can discuss the available options.</p>
            </div>
            <a class="button" href="/get-help">Contact AlbaTech Solutions</a>
        </div>
    </div>
</section>