<?php

use AlbaTech\Core\View;
$homeContent = is_array($homeContent ?? null) ? $homeContent : [];
$ctaItems = is_array($ctaItems ?? null) ? $ctaItems : [];
$featuredProducts = is_array($featuredProducts ?? null) ? $featuredProducts : [];
$solutions = is_array($solutions ?? null) ? $solutions : [];
$statusLabel = static fn (string $status): string => match ($status) { 'live' => 'Live', 'beta' => 'Beta', 'early_access' => 'Early access', default => 'In development' };
$statusClass = static fn (string $status): string => match ($status) { 'live' => 'badge-live', 'beta' => 'badge-beta', 'early_access' => 'badge-early', default => 'badge-dev' };
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

<section class="hero">
    <div class="shell grid items-center gap-10 py-12 md:py-16 lg:grid-cols-[1.05fr_0.95fr] lg:py-20">
        <div>
            <span class="kicker"><?= View::escape($homeContent['home_hero_eyebrow'] ?? 'Software studio in Nairobi') ?></span>
            <h1 class="max-w-3xl"><?= View::escape($homeContent['home_hero_title'] ?? 'We build software and websites for Kenyan businesses.') ?></h1>
            <p class="mt-4 max-w-xl text-lg leading-relaxed"><?= View::escape($homeContent['home_hero_lead'] ?? 'We build our own products and develop booking, rental and operations systems for Kenyan businesses.') ?></p>
            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <?php // The primary CTA opens WhatsApp; the CMS URL is only the fallback when no WhatsApp number is set. ?>
                <a class="btn <?= !empty($hasWhatsApp) ? 'btn-whatsapp' : 'btn-primary' ?>" href="<?= View::escape(!empty($hasWhatsApp) ? (string) $whatsAppUrl : ($homeContent['home_hero_primary_url'] ?? '/get-help')) ?>"><?php if (!empty($hasWhatsApp)): ?><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> <?php endif; ?><?= View::escape($homeContent['home_hero_primary_label'] ?? 'Chat on WhatsApp') ?></a>
                <a class="btn btn-secondary" href="<?= View::escape($homeContent['home_hero_secondary_url'] ?? '#choose-path') ?>"><?= View::escape($homeContent['home_hero_secondary_label'] ?? 'Find the right help') ?></a>
            </div>
            <div class="mt-7 flex flex-col gap-2 border-t border-border pt-5 text-sm font-semibold text-body sm:flex-row sm:flex-wrap sm:gap-x-5">
                <span class="inline-flex items-center gap-2"><i class="fa-solid fa-location-dot text-primary" aria-hidden="true"></i> Built in Nairobi, supporting clients across Kenya</span>
                <span class="inline-flex items-center gap-2"><i class="fa-solid fa-receipt text-primary" aria-hidden="true"></i> Price agreed before we start</span>
                <span class="inline-flex items-center gap-2"><i class="fa-solid fa-user-shield text-primary" aria-hidden="true"></i> Your documents kept confidential</span>
            </div>
        </div>
        <aside aria-label="AlbaTech products">
            <?php if (!empty($featuredProducts)): ?>
                <div class="grid gap-4 sm:grid-cols-2">
                    <?php foreach (array_slice($featuredProducts, 0, 3) as $index => $product): ?>
                        <a class="card card-lift group overflow-hidden<?= $index === 0 ? ' sm:col-span-2' : '' ?>" href="/products/<?= rawurlencode($product['slug']) ?>">
                            <span class="media-16-10 relative block">
                                <img class="transition-transform duration-300 group-hover:scale-[1.02]" src="/<?= View::escape(ltrim($product['cover_image'], '/')) ?>" alt="<?= View::escape($product['title']) ?>" width="1200" height="800" <?= $index === 0 ? 'loading="eager" fetchpriority="high"' : 'loading="lazy"' ?> decoding="async">
                                <span class="badge badge-float <?= View::escape($statusClass((string) $product['lifecycle_status'])) ?>"><?= View::escape($statusLabel((string) $product['lifecycle_status'])) ?></span>
                            </span>
                            <span class="block px-4 py-3 text-sm font-bold text-ink group-hover:text-primary"><?= View::escape($product['title']) ?></span>
                        </a>
                    <?php endforeach; ?>
                    <div class="sm:col-span-2"><a class="link-arrow text-sm" href="/products">All products <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div>
                </div>
            <?php else: ?>
                <img src="<?= View::escape($homeContent['home_hero_image'] ?? '/assets/images/albatech-hero.webp') ?>" alt="<?= View::escape($appName ?? 'AlbaTech Solutions') ?> - software and websites for Kenya" class="w-full rounded-2xl border border-border object-cover" width="1200" height="630" loading="eager" fetchpriority="high" decoding="async">
            <?php endif; ?>
        </aside>
    </div>
</section>

<?php
$complianceServices = is_array($complianceServices ?? null) ? $complianceServices : [];
$servicePrice = static function (array $service): string {
    if (in_array((string) ($service['pricing_type'] ?? ''), ['fixed', 'starting_at'], true) && is_numeric($service['pricing_amount'] ?? null)) {
        $amount = (float) $service['pricing_amount'];
        return ($service['pricing_type'] === 'starting_at' ? 'From ' : '') . 'KSh ' . number_format($amount, $amount == (int) $amount ? 0 : 2);
    }
    return '';
};
$buildItems = [
    ['label' => 'Register, launch and get found: SME Launchpad', 'url' => '/services/sme-launchpad'],
    ['label' => 'Business websites and landing pages', 'url' => '/services/website-design'],
    ['label' => 'Get found on Google: Business Profile setup', 'url' => '/services/google-business-profile-setup'],
    ['label' => 'Booking, rental, stock and custom internal systems', 'url' => '/solutions'],
];
?>
<section id="choose-path" class="section scroll-mt-20" aria-labelledby="home-paths-title">
    <div class="shell">
        <div class="mx-auto mb-8 max-w-2xl text-center">
            <span class="kicker"><?= View::escape($homeContent['home_services_kicker'] ?? 'Two ways we help') ?></span>
            <h2 id="home-paths-title"><?= View::escape($homeContent['home_services_title'] ?? 'What do you need today?') ?></h2>
        </div>
        <div class="grid gap-6 lg:grid-cols-2">
            <article class="card flex flex-col p-6 lg:p-8">
                <span class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-primary-light text-primary"><i class="fa-solid fa-rocket" aria-hidden="true"></i></span>
                <span class="text-xs font-bold uppercase tracking-wide text-primary">For businesses and SMEs</span>
                <h3 class="mt-2 text-2xl">Build &amp; grow</h3>
                <p class="mt-2 text-sm">Website design, booking and business systems, plus launch support from registration to your first customer online.</p>
                <ul class="mt-5 grid flex-1 content-start gap-3 text-sm">
                    <?php foreach ($buildItems as $item): ?>
                        <li><a class="flex items-start gap-3 font-semibold text-ink hover:text-primary" href="<?= View::escape($item['url']) ?>"><i class="fa-solid fa-check mt-1 text-primary" aria-hidden="true"></i><span><?= View::escape($item['label']) ?></span></a></li>
                    <?php endforeach; ?>
                </ul>
                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <a class="btn btn-primary" href="/services/sme-launchpad">Launch or upgrade your business</a>
                    <a class="btn btn-secondary" href="/work">See our work</a>
                </div>
            </article>
            <article class="card flex flex-col p-6 lg:p-8">
                <span class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-primary-light text-primary"><i class="fa-solid fa-file-circle-check" aria-hidden="true"></i></span>
                <span class="text-xs font-bold uppercase tracking-wide text-primary">For individuals and busy owners</span>
                <h3 class="mt-2 text-2xl">Get it sorted</h3>
                <p class="mt-2 text-sm">KRA returns, eTIMS, eCitizen, SHA, NTSA and other government services handled fast, with the price agreed before we start.</p>
                <?php if (!empty($complianceServices)): ?>
                    <ul class="mt-5 grid flex-1 content-start gap-3 text-sm">
                        <?php foreach ($complianceServices as $service): $price = $servicePrice($service); ?>
                            <li><a class="flex items-start justify-between gap-3 font-semibold text-ink hover:text-primary" href="/services/<?= rawurlencode($service['slug']) ?>"><span class="flex items-start gap-3"><i class="fa-solid fa-check mt-1 text-primary" aria-hidden="true"></i><span><?= View::escape($service['name']) ?></span></span><?php if ($price !== ''): ?><span class="shrink-0 rounded-full bg-primary-light px-2 py-0.5 text-xs text-primary"><?= View::escape($price) ?></span><?php endif; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="mt-5 flex-1 text-sm">KRA returns, eTIMS, business registration, eCitizen, SHA and NTSA.</p>
                <?php endif; ?>
                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <a class="btn btn-primary" href="/services/kra-ecitizen-help">Get fast admin help</a>
                    <a class="btn btn-secondary" href="/guides">Do it yourself: guides</a>
                </div>
            </article>
        </div>
    </div>
</section>

<?php if (!empty($featuredProjects)): $needsWorkCarousel = true; ?>
<section class="section section-tint" aria-labelledby="home-work-title">
    <div class="shell">
        <div class="mb-8 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div class="max-w-2xl"><span class="kicker"><?= View::escape($homeContent['home_work_kicker'] ?? 'Client work') ?></span><h2 id="home-work-title"><?= View::escape($homeContent['home_work_title'] ?? 'Systems built for real businesses.') ?></h2></div>
            <p class="max-w-xl text-sm"><?= View::escape($homeContent['home_work_description'] ?? 'Selected projects, the problem each one solved and how we built it.') ?></p>
        </div>
        <div class="work-carousel<?= count($featuredProjects) === 1 ? ' work-carousel--single' : '' ?>" data-work-carousel aria-roledescription="carousel" aria-label="Featured client work">
            <div class="work-carousel__viewport">
                <div class="work-carousel__track" data-carousel-track>
                    <?php foreach ($featuredProjects as $project): $projectUrl = '/work/' . rawurlencode($project['slug']); ?>
                        <article class="work-carousel__slide" data-carousel-slide>
                            <div class="card grid overflow-hidden md:grid-cols-2">
                                <a class="media-16-10 relative block md:h-full" href="<?= $projectUrl ?>">
                                    <img src="/<?= View::escape(ltrim($project['cover_image'], '/')) ?>" alt="<?= View::escape($project['title']) ?>" width="600" height="400" loading="lazy" decoding="async">
                                    <span class="absolute bottom-3 left-3 rounded-full bg-surface/95 px-3 py-1 text-xs font-semibold text-ink"><?= View::escape($project['category_name']) ?></span>
                                </a>
                                <div class="flex flex-col justify-center p-6 lg:p-8">
                                    <span class="text-xs font-bold uppercase tracking-wide text-primary">Case study</span>
                                    <h3 class="mt-2 text-xl"><a class="text-ink hover:text-primary" href="<?= $projectUrl ?>"><?= View::escape($project['title']) ?></a></h3>
                                    <p class="mt-2 text-sm"><?= View::escape($project['description']) ?></p>
                                    <a class="link-arrow mt-4 text-sm" href="<?= $projectUrl ?>">Read the case study <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php if (count($featuredProjects) > 1): ?>
            <div class="work-carousel__controls">
                <div class="work-carousel__dots" data-carousel-dots aria-label="Choose featured project"></div>
                <button class="btn btn-secondary btn-sm" type="button" data-carousel-toggle aria-pressed="false"><i class="fa-solid fa-pause" aria-hidden="true"></i> <span>Pause</span></button>
            </div>
            <?php endif; ?>
        </div>
        <div class="mt-6"><a class="link-arrow text-sm" href="/work">All client work <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($solutions)): $solutionIcons = ['booking-reservation-systems' => 'fa-calendar-check', 'property-rental-management-systems' => 'fa-building', 'visitor-gate-pass-management' => 'fa-id-card', 'pos-inventory-systems' => 'fa-cash-register', 'custom-internal-tools' => 'fa-sliders']; ?>
<section class="section" aria-labelledby="home-solutions-title">
    <div class="shell">
        <div class="mb-8 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div class="max-w-2xl"><span class="kicker"><?= View::escape($homeContent['home_areas_kicker'] ?? 'Systems we build') ?></span><h2 id="home-solutions-title"><?= View::escape($homeContent['home_areas_title'] ?? 'Software for how your business runs.') ?></h2></div>
            <p class="max-w-xl text-sm"><?= View::escape($homeContent['home_areas_description'] ?? 'Booking, property, visitor, stock and internal tools, built around the process you already use.') ?></p>
        </div>
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($solutions as $solution): $solutionUrl = '/services/' . rawurlencode($solution['slug']); ?>
                <article class="card card-lift flex flex-col p-5">
                    <span class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-primary-light text-primary"><i class="fa-solid <?= View::escape($solutionIcons[$solution['slug']] ?? 'fa-cubes') ?>" aria-hidden="true"></i></span>
                    <h3 class="text-lg"><a class="text-ink hover:text-primary" href="<?= $solutionUrl ?>"><?= View::escape($solution['name']) ?></a></h3>
                    <p class="mt-2 flex-1 text-sm"><?= View::escape($solution['short_description']) ?></p>
                    <a class="link-arrow mt-4 text-sm" href="<?= $solutionUrl ?>">View details <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </article>
            <?php endforeach; ?>
            <article class="card flex flex-col border-dashed bg-surface-muted p-5">
                <span class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-surface text-primary"><i class="fa-solid fa-circle-question" aria-hidden="true"></i></span>
                <h3 class="text-lg">Something else?</h3>
                <p class="mt-2 flex-1 text-sm">Tell us about the process that is slowing you down. If a small system would fix it, we will say so.</p>
                <a class="link-arrow mt-4 text-sm" href="/get-help">Talk to us <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            </article>
        </div>
        <div class="mt-6"><a class="link-arrow text-sm" href="/solutions">Explore solutions <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div>
    </div>
</section>
<?php endif; ?>

<section class="section section-tint">
    <div class="shell">
        <div class="mx-auto mb-8 max-w-2xl text-center">
            <span class="kicker"><?= View::escape($homeContent['home_process_kicker'] ?? 'How it works') ?></span>
            <h2><?= View::escape($homeContent['home_process_title'] ?? 'A straightforward way to get started.') ?></h2>
            <p class="mt-3"><?= View::escape($homeContent['home_process_description'] ?? 'You do not need to know the technical details. Tell us the outcome you want, and we can work through the options together.') ?></p>
        </div>
        <div class="grid gap-8 md:grid-cols-3">
            <article class="border-t-2 border-primary pt-5"><span class="font-display text-sm font-bold tracking-wide text-primary">01</span><h3 class="mt-3"><?= View::escape($homeContent['home_step1_title'] ?? 'Tell us what you need') ?></h3><p class="mt-2 text-sm"><?= View::escape($homeContent['home_step1_description'] ?? 'Describe the task, challenge or idea in your own words.') ?></p></article>
            <article class="border-t-2 border-primary pt-5"><span class="font-display text-sm font-bold tracking-wide text-primary">02</span><h3 class="mt-3"><?= View::escape($homeContent['home_step2_title'] ?? 'Agree on the plan') ?></h3><p class="mt-2 text-sm"><?= View::escape($homeContent['home_step2_description'] ?? 'We confirm the scope, price and expected next steps before work begins.') ?></p></article>
            <article class="border-t-2 border-primary pt-5"><span class="font-display text-sm font-bold tracking-wide text-primary">03</span><h3 class="mt-3"><?= View::escape($homeContent['home_step3_title'] ?? 'Move forward with clarity') ?></h3><p class="mt-2 text-sm"><?= View::escape($homeContent['home_step3_description'] ?? 'Know what happens next and what information or action is needed from you.') ?></p></article>
        </div>
    </div>
</section>

<section class="section">
    <div class="shell grid items-start gap-10 lg:grid-cols-2">
        <div>
            <span class="kicker"><?= View::escape($homeContent['home_why_kicker'] ?? 'Why AlbaTech') ?></span>
            <h2><?= View::escape($homeContent['home_why_title'] ?? 'Practical help, without the unnecessary complexity.') ?></h2>
            <p class="mt-3"><?= View::escape($homeContent['home_why_description'] ?? 'Get clear guidance, sensible options and support focused on the outcome you need.') ?></p>
            <a class="link-arrow mt-2 text-sm" href="/about">Learn about AlbaTech <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </div>
        <div class="grid gap-4">
            <div class="flex gap-4">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary"><i class="fa-solid fa-list-check" aria-hidden="true"></i></span>
                <div><h3 class="text-base"><?= View::escape($homeContent['home_why1_title'] ?? 'Clear communication') ?></h3><p class="mt-1 text-sm"><?= View::escape($homeContent['home_why1_description'] ?? 'Understand the options and what each step involves.') ?></p></div>
            </div>
            <div class="flex gap-4">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary"><i class="fa-solid fa-receipt" aria-hidden="true"></i></span>
                <div><h3 class="text-base"><?= View::escape($homeContent['home_why2_title'] ?? 'Know the scope') ?></h3><p class="mt-1 text-sm"><?= View::escape($homeContent['home_why2_description'] ?? 'Understand the scope and charges before agreeing to proceed.') ?></p></div>
            </div>
            <div class="flex gap-4">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary"><i class="fa-regular fa-message" aria-hidden="true"></i></span>
                <div><h3 class="text-base"><?= View::escape($homeContent['home_why3_title'] ?? 'Easy to reach') ?></h3><p class="mt-1 text-sm"><?= View::escape($homeContent['home_why3_description'] ?? 'Start with a simple enquiry and get help deciding the next step.') ?></p></div>
            </div>
        </div>
    </div>
</section>

<section class="border-y border-warning-border bg-warning-bg py-5">
    <div class="shell flex gap-3 text-sm leading-relaxed text-warning">
        <i class="fa-solid fa-circle-info mt-1" aria-hidden="true"></i>
        <p class="m-0 text-warning"><strong>Our service promise</strong> <?= View::escape($homeContent['home_promise'] ?? 'We explain the scope and charges before starting. For government-related assistance, AlbaTech Solutions is an independent service provider, not a government agency. Official decisions remain with the relevant authority.') ?></p>
    </div>
</section>

<section class="bg-deep py-12 text-white md:py-16">
    <div class="shell flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
        <div class="max-w-2xl">
            <span class="kicker kicker-light"><?= View::escape($homeContent['home_cta_kicker'] ?? 'Let us talk') ?></span>
            <h2 class="text-white"><?= View::escape($homeContent['home_cta_title'] ?? 'Not sure where to start?') ?></h2>
            <p class="mt-2 text-white/85"><?= View::escape($homeContent['home_cta_description'] ?? 'Tell us what you are trying to achieve. We will help you understand the next step.') ?></p>
        </div>
        <a class="btn btn-accent w-full shrink-0 md:w-auto" href="<?= View::escape($whatsAppUrl ?? '/get-help') ?>"><?php if (!empty($hasWhatsApp)): ?><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Start a WhatsApp chat<?php else: ?><i class="fa-regular fa-envelope" aria-hidden="true"></i> Send an enquiry<?php endif; ?></a>
    </div>
</section>
