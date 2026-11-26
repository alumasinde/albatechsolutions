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

<section class="page-hero">
    <div class="shell grid items-center gap-10 py-12 md:py-16 lg:grid-cols-[1.1fr_0.9fr]">
        <div>
            <span class="kicker">About us</span>
            <h1>A software studio <span class="text-primary">in Nairobi.</span></h1>
            <p class="mt-4 max-w-2xl text-lg leading-relaxed">We build our own products and develop booking, rental and operations systems for Kenyan businesses, alongside websites and practical digital assistance.</p>
            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <a class="btn btn-primary" href="/products">See our products</a>
                <a class="btn btn-secondary" href="/work">See client work</a>
            </div>
        </div>
        <aside class="card p-6" aria-label="Our approach">
            <p class="text-xs font-extrabold uppercase tracking-[0.12em] text-muted">Our approach</p>
            <h2 class="mt-2 text-xl">Clear advice. Useful work. A human touch.</h2>
            <ul class="check-list mt-4 text-sm">
                <li>Understand the task before recommending a solution</li>
                <li>Agree on the scope and costs before work begins</li>
                <li>Keep communication clear from start to finish</li>
            </ul>
        </aside>
    </div>
</section>

<section class="section">
    <div class="shell grid gap-8 lg:grid-cols-[240px_minmax(0,1fr)]">
        <div><span class="kicker">Why we exist</span></div>
        <div class="max-w-3xl">
            <h2>Technology should move you forward, not get in your way.</h2>
            <p class="mt-4">For many small businesses, getting online can feel like a big project. For individuals, completing a digital process can mean dealing with unfamiliar steps, forms and requirements. It is easy to lose time trying to work out where to begin.</p>
            <p>AlbaTech Solutions was created to make those next steps more approachable. We focus on practical solutions, straightforward communication and work that starts with what the client actually needs, not unnecessary complexity.</p>
        </div>
    </div>
</section>

<section class="section section-tint">
    <div class="shell">
        <div class="mb-8 max-w-2xl">
            <span class="kicker">What we focus on</span>
            <h2>Useful services for real-world needs</h2>
            <p class="mt-3">Our work centres on three areas where clear guidance and dependable execution can make a difference.</p>
        </div>
        <div class="grid gap-6 md:grid-cols-3">
            <article class="card card-lift p-6">
                <span class="text-sm font-extrabold text-primary">01</span>
                <h3 class="mt-3">Products</h3>
                <p class="mt-2 text-sm">PesaLens for private M-Pesa expense tracking, Budget254 for Kenyan cost calculators and a rental management platform in early access.</p>
                <a class="link-arrow mt-4 text-sm" href="/products">Explore products <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            </article>
            <article class="card card-lift p-6">
                <span class="text-sm font-extrabold text-primary">02</span>
                <h3 class="mt-3">Client systems</h3>
                <p class="mt-2 text-sm">Booking, property, visitor, stock and internal tools built around the process a business already uses.</p>
                <a class="link-arrow mt-4 text-sm" href="/solutions">Explore solutions <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            </article>
            <article class="card card-lift p-6">
                <span class="text-sm font-extrabold text-primary">03</span>
                <h3 class="mt-3">Digital assistance</h3>
                <p class="mt-2 text-sm">Websites and independent help with selected business and government processes when that is what the task needs.</p>
                <a class="link-arrow mt-4 text-sm" href="/services">Explore services <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            </article>
        </div>
    </div>
</section>

<section class="section">
    <div class="shell">
        <div class="mb-8 max-w-3xl">
            <span class="kicker">How we work</span>
            <h2>Clear communication. Practical solutions. No unnecessary complications.</h2>
        </div>
        <div class="grid gap-6 sm:grid-cols-2">
            <article class="flex gap-4"><span class="text-sm font-extrabold text-primary">01</span><div><h3 class="text-base">Listen first</h3><p class="mt-1 text-sm">We start by understanding your goal, your situation and what a useful outcome looks like.</p></div></article>
            <article class="flex gap-4"><span class="text-sm font-extrabold text-primary">02</span><div><h3 class="text-base">Be clear</h3><p class="mt-1 text-sm">We explain the process, expected deliverables, requirements and charges before work starts.</p></div></article>
            <article class="flex gap-4"><span class="text-sm font-extrabold text-primary">03</span><div><h3 class="text-base">Build for people</h3><p class="mt-1 text-sm">We prioritise clarity, mobile usability and practical outcomes over flashy features that do not serve a purpose.</p></div></article>
            <article class="flex gap-4"><span class="text-sm font-extrabold text-primary">04</span><div><h3 class="text-base">Respect your information</h3><p class="mt-1 text-sm">We treat customer information carefully and encourage safe handling of passwords, PINs and other sensitive credentials.</p></div></article>
        </div>
    </div>
</section>

<section class="bg-primary py-12 text-white md:py-14">
    <div class="shell flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
        <div class="max-w-2xl">
            <span class="kicker kicker-light">Have something in mind?</span>
            <h2 class="text-white">Let's work out the next step.</h2>
            <p class="mt-2 text-white/85">Tell us what you are trying to achieve. We will help you understand the options and what to do next.</p>
        </div>
        <a class="btn btn-accent w-full shrink-0 md:w-auto" href="/get-help">Get in touch</a>
    </div>
</section>
