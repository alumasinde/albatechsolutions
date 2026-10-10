<?php use AlbaTech\Core\View; ?>
<section class="container error-page">
    <span class="eyebrow">Error 500</span>
    <h1>Something went wrong.</h1>
    <p><?= View::escape($errorMessage ?? 'Please try again later.') ?></p>
    <a class="button" href="/">Return to homepage</a>
</section>
