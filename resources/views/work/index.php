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
<section class="work-page-hero"><div class="container"><span class="section-kicker">Selected projects</span><h1>Our Work</h1><p>Explore selected projects and practical digital solutions. Each project reflects its own goals, scope and requirements.</p></div></section>
<section class="section"><div class="container"><nav class="work-filters" aria-label="Filter projects"><a class="work-filter <?= empty($selectedCategory)?'is-active':'' ?>" href="/work" <?= empty($selectedCategory)?'aria-current="page"':'' ?>>All work</a><?php foreach($categories as $category): ?><a class="work-filter <?= $selectedCategory===$category['slug']?'is-active':'' ?>" href="/work?category=<?= rawurlencode($category['slug']) ?>" <?= $selectedCategory===$category['slug']?'aria-current="page"':'' ?>><?= View::escape($category['name']) ?></a><?php endforeach; ?></nav>
<?php if (!empty($projects)): ?><div class="work-grid"><?php foreach($projects as $project): ?><article class="work-card"><a class="work-card__image-link" href="/work/<?= rawurlencode($project['slug']) ?>"><img class="work-card__image" src="/<?= View::escape(ltrim($project['cover_image'],'/')) ?>" alt="Project cover: <?= View::escape($project['title']) ?>" width="600" height="400" loading="lazy" decoding="async"><span class="work-card__category"><?= View::escape($project['category_name']) ?></span></a><div class="work-card__body"><h2><a href="/work/<?= rawurlencode($project['slug']) ?>"><?= View::escape($project['title']) ?></a></h2><p><?= View::escape($project['description']) ?></p><a class="text-link" href="/work/<?= rawurlencode($project['slug']) ?>">View project <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div></article><?php endforeach; ?></div><?php else: ?><div class="work-empty"><h2><?= $selectedCategory!==''?'No published projects in this category yet':'Our portfolio is growing' ?></h2><p><?= $selectedCategory!==''?'Choose another category to explore other work.':'Published projects will appear here as they are added.' ?></p><?php if($selectedCategory!==''): ?><a class="button button--secondary" href="/work">View all work</a><?php endif; ?></div><?php endif; ?></div></section>
