<?php use AlbaTech\Core\View; ?>
<section class="shell flex min-h-[60vh] flex-col justify-center py-16 md:py-24">
    <span class="kicker">Error 500</span>
    <h1 class="mt-3 max-w-3xl">Something went wrong.</h1>
    <p class="mt-4 max-w-xl text-lg leading-relaxed text-muted"><?= View::escape($errorMessage ?? 'Please try again later.') ?></p>
    <div class="mt-8 flex flex-wrap gap-3">
        <a class="btn btn-primary" href="/">Return to homepage</a>
        <a class="btn btn-secondary" href="/get-help">Contact us</a>
    </div>
</section>
