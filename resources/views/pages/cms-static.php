<?php

use AlbaTech\Core\View;

/**
 * @var array<string, mixed> $cmsPage
 */
?>
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
<section class="section">
    <div class="container">
        <div class="section-heading"><span class="section-kicker"><?= View::escape($cmsPage['title'] ?? '') ?></span>
            <h1><?= View::escape($cmsPage['title'] ?? '') ?></h1><?php if (trim((string)($cmsPage['intro'] ?? '')) !== ''): ?><p><?= View::escape($cmsPage['intro']) ?></p><?php endif; ?>
        </div>
        <article class="cms-static-content" style="max-width:850px;margin-inline:auto;line-height:1.8"><?php $body = trim((string)($cmsPage['body_content'] ?? ''));
                                                                                                        foreach (preg_split('/\R\s*\R/', $body) ?: [] as $paragraph): if (trim($paragraph) !== ''): ?><p><?= nl2br(View::escape(trim($paragraph)), false) ?></p><?php endif;
                                                                                                                                                                                                                                                                                                                        endforeach; ?></article>
        <div class="section-footer-link"><a class="button button--primary" href="/get-help">Tell us what you need</a></div>
    </div>
</section>