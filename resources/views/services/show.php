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

<section class="service-detail-hero">
    <div class="container">
        <div class="service-detail-hero__grid">
            <div>
                <span class="eyebrow"><?= View::escape($service['category_name']) ?></span>
                <h1><?= View::escape($service['name']) ?></h1>
                <p class="service-detail-hero__lead"><?= View::escape($service['short_description'] ?? '') ?></p>
                <div class="hero-actions">
                    <a class="button button--whatsapp" href="/get-help?service=<?= rawurlencode($service['slug']) ?>">Enquire about this service <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    <a class="button button--secondary" href="/services">Explore other services</a>
                </div>
            </div>
            <aside class="service-price-panel">
                <span class="service-price-panel__label">Service pricing</span>
                <?php if (in_array((string) ($service['pricing_type'] ?? 'quote'), ['fixed', 'starting_at'], true) && ($service['pricing_amount'] ?? null) !== null && $service['pricing_amount'] !== ''): ?>
                    <strong class="service-price-panel__amount"><?= ($service['pricing_type'] ?? '') === 'starting_at' ? 'From ' : '' ?>KSh <?= View::escape(number_format((float) $service['pricing_amount'], ((float) $service['pricing_amount'] == (int) $service['pricing_amount']) ? 0 : 2)) ?></strong>
                    <p><?= ($service['pricing_type'] ?? '') === 'starting_at' ? 'Final cost depends on the scope and options you choose.' : 'We will confirm what is included before work begins.' ?></p>
                <?php else: ?>
                    <strong class="service-price-panel__quote"><?= View::escape(($service['pricing_label'] ?? '') ?: 'Contact for price') ?></strong>
                    <p>Tell us what you need and we will confirm the scope and price before you commit.</p>
                <?php endif; ?>
                <a class="text-link" href="/get-help?service=<?= rawurlencode($service['slug']) ?>">Ask a question about pricing <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            </aside>
        </div>
    </div>
</section>

<section class="section">
    <div class="container service-detail-layout">
        <article class="service-content">
            <span class="section-kicker">Service overview</span>
            <h2>What this service is about</h2>
            <div class="prose"><?= nl2br(View::escape($service['description'] ?? 'Contact us to discuss your requirements and confirm the scope of assistance.')) ?></div>

            <?php if (!empty($service['who_for'])): ?>
                <div class="service-content__block"><h3>Who this is for</h3><div class="prose"><?= nl2br(View::escape($service['who_for'])) ?></div></div>
            <?php endif; ?>

            <?php if (!empty($service['what_included'])): ?>
                <div class="service-content__block"><h3>What we can help with</h3><div class="prose"><?= nl2br(View::escape($service['what_included'])) ?></div></div>
            <?php endif; ?>

            <?php if (!empty($service['what_needed'])): ?>
                <div class="service-content__block"><h3>What you may need</h3><div class="prose"><?= nl2br(View::escape($service['what_needed'])) ?></div></div>
            <?php endif; ?>
        </article>

        <aside class="service-detail-sidebar">
            <div class="service-next-card">
                <span class="service-next-card__icon"><i class="fa-solid fa-comments" aria-hidden="true"></i></span>
                <h2>Ready to get started?</h2>
                <p>Send us a short description of what you need. We will confirm any questions, the scope and the charges before starting.</p>
                <a class="button button--whatsapp" href="/get-help?service=<?= rawurlencode($service['slug']) ?>">Discuss this service</a>
                <div class="service-next-card__foot"><i class="fa-solid fa-circle-info" aria-hidden="true"></i> No obligation to enquire</div>
            </div>
            <?php if ((string) $service['category_slug'] === 'government-business-assistance'): ?>
                <p class="notice">AlbaTech Solutions is independent and is not affiliated with a government agency. Final decisions, requirements and processing times are determined by the relevant authority.</p>
            <?php endif; ?>
        </aside>
    </div>
</section>


<?php if (!empty($relatedProjects)): ?><section class="section section--tint"><div class="container"><div class="section-heading section-heading--split"><div><span class="section-kicker">Examples</span><h2>Related work</h2></div><a class="text-link" href="/work">Explore all projects <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div><div class="work-grid work-grid--related"><?php foreach($relatedProjects as $project): ?><article class="work-card"><a class="work-card__image-link" href="/work/<?= rawurlencode($project['slug']) ?>"><img class="work-card__image" src="/<?= View::escape(ltrim($project['cover_image'],'/')) ?>" alt="<?= View::escape($project['title']) ?>" width="600" height="400" loading="lazy" decoding="async"></a><div class="work-card__body"><span class="work-card__text-category"><?= View::escape($project['category_name']) ?></span><h3><a href="/work/<?= rawurlencode($project['slug']) ?>"><?= View::escape($project['title']) ?></a></h3><p><?= View::escape($project['description']) ?></p></div></article><?php endforeach; ?></div></div></section><?php endif; ?>

<section class="service-detail-bottom"><div class="container service-detail-bottom__inner"><div><h2>Need help choosing?</h2><p>Tell us what you are trying to achieve and we will help you identify the right next step.</p></div><a class="button button--secondary" href="/get-help">Contact AlbaTech <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div></section>
