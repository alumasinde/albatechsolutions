<?php use AlbaTech\Core\View; ?>
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
<section class="work-detail-hero"><div class="container"><span class="section-kicker"><?= View::escape($project['category_name']) ?></span><h1><?= View::escape($project['title']) ?></h1><?php if(!empty($project['client_name'])): ?><p class="work-detail-client">Client: <?= View::escape($project['client_name']) ?></p><?php endif; ?><p class="work-detail-lead"><?= View::escape($project['description']) ?></p><div class="hero-actions"><a class="button button--whatsapp" href="<?= View::escape($whatsAppUrl) ?>">Request a Similar Project <i class="fa-brands fa-whatsapp" aria-hidden="true"></i></a><?php if(!empty($project['live_url'])): ?><a class="button button--secondary" href="<?= View::escape($project['live_url']) ?>" target="_blank" rel="noopener noreferrer">Visit live project <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a><?php endif; ?></div></div></section>
<section class="section"><div class="container"><figure class="work-detail-image"><img src="/<?= View::escape(ltrim($project['cover_image'],'/')) ?>" alt="<?= View::escape($project['title']) ?> project screenshot" width="1200" height="800" fetchpriority="high" decoding="async"></figure></div></section>
<?php if(!empty($relatedProjects)): ?><section class="section section--tint"><div class="container"><div class="section-heading section-heading--split"><div><span class="section-kicker">More projects</span><h2>Related work</h2></div><a class="text-link" href="/work">View all work <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div><div class="work-grid work-grid--related"><?php foreach($relatedProjects as $related): ?><article class="work-card"><a class="work-card__image-link" href="/work/<?= rawurlencode($related['slug']) ?>"><img class="work-card__image" src="/<?= View::escape(ltrim($related['cover_image'],'/')) ?>" alt="<?= View::escape($related['title']) ?>" width="600" height="400" loading="lazy" decoding="async"></a><div class="work-card__body"><span class="work-card__text-category"><?= View::escape($related['category_name']) ?></span><h3><a href="/work/<?= rawurlencode($related['slug']) ?>"><?= View::escape($related['title']) ?></a></h3></div></article><?php endforeach; ?></div></div></section><?php endif; ?>
<section class="section"><div class="container work-cta"><div><span class="section-kicker">Have a similar idea?</span><h2>Let's talk about what you need.</h2><p>Share your goals and we'll discuss a practical next step.</p></div><a class="button button--whatsapp" href="<?= View::escape($whatsAppUrl) ?>">Request a Similar Project <i class="fa-brands fa-whatsapp" aria-hidden="true"></i></a></div></section>
