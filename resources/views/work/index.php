<?php use AlbaTech\Core\View; ?>
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

<section class="border-b border-border bg-surface">
    <div class="shell py-12 md:py-16">
        <span class="kicker">Client work</span>
        <h1 class="max-w-3xl">Systems built for real businesses.</h1>
        <p class="mt-4 max-w-2xl text-lg leading-relaxed">Case studies from client projects, covering the problem, what we built and the outcome. For our own products, see the <a class="font-semibold text-primary underline underline-offset-2" href="/products">products page</a>.</p>
    </div>
</section>

<section class="section">
    <div class="shell">
        <nav class="mb-8 flex flex-wrap gap-2" aria-label="Filter projects">
            <a class="inline-flex min-h-11 items-center rounded-full border px-4 text-sm font-semibold transition-colors <?= empty($selectedCategory) ? 'border-primary bg-primary-light text-primary' : 'border-border bg-surface text-muted hover:border-border-strong hover:text-ink' ?>" href="/work" <?= empty($selectedCategory) ? 'aria-current="page"' : '' ?>>All work</a>
            <?php foreach ($categories as $category): $isActive = $selectedCategory === $category['slug']; ?>
                <a class="inline-flex min-h-11 items-center rounded-full border px-4 text-sm font-semibold transition-colors <?= $isActive ? 'border-primary bg-primary-light text-primary' : 'border-border bg-surface text-muted hover:border-border-strong hover:text-ink' ?>" href="/work?category=<?= rawurlencode($category['slug']) ?>" <?= $isActive ? 'aria-current="page"' : '' ?>><?= View::escape($category['name']) ?></a>
            <?php endforeach; ?>
        </nav>
        <?php if (!empty($projects)): ?>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($projects as $project): $projectUrl = '/work/' . rawurlencode($project['slug']); ?>
                    <article class="card card-lift group flex flex-col overflow-hidden">
                        <a class="media-16-10 relative block" href="<?= $projectUrl ?>">
                            <img class="transition-transform duration-300 group-hover:scale-[1.02]" src="/<?= View::escape(ltrim($project['cover_image'], '/')) ?>" alt="Project cover: <?= View::escape($project['title']) ?>" width="600" height="400" loading="lazy" decoding="async">
                            <span class="absolute bottom-3 left-3 rounded-full bg-surface/95 px-3 py-1 text-xs font-semibold text-ink"><?= View::escape($project['category_name']) ?></span>
                        </a>
                        <div class="flex flex-1 flex-col p-5">
                            <span class="text-xs font-bold uppercase tracking-wide text-primary">Case study</span>
                            <h2 class="mt-2 text-lg leading-snug"><a class="text-ink hover:text-primary" href="<?= $projectUrl ?>"><?= View::escape($project['title']) ?></a></h2>
                            <p class="mt-2 flex-1 text-sm"><?= View::escape($project['description']) ?></p>
                            <a class="link-arrow mt-4 text-sm" href="<?= $projectUrl ?>">Read the case study <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card mx-auto max-w-xl p-8 text-center">
                <h2><?= $selectedCategory !== '' ? 'No published projects in this category yet' : 'Client case studies are on the way' ?></h2>
                <p class="mt-2"><?= $selectedCategory !== '' ? 'Choose another category to explore other work.' : 'Published case studies will appear here as they are added.' ?></p>
                <?php if ($selectedCategory !== ''): ?><a class="btn btn-secondary mt-5" href="/work">View all work</a><?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="bg-primary py-12 text-white md:py-14">
    <div class="shell flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
        <div class="max-w-2xl">
            <span class="kicker kicker-light">Have a similar idea?</span>
            <h2 class="text-white">Let's talk about what you need.</h2>
            <p class="mt-2 text-white/85">Share your goals and we'll discuss a practical next step.</p>
        </div>
        <a class="btn btn-accent w-full shrink-0 md:w-auto" href="/get-help">Start a conversation <i class="fa-solid fa-arrow-right text-sm" aria-hidden="true"></i></a>
    </div>
</section>
