<?php
$robots = 'noindex, follow';
$title = 'Page Not Found — ' . setting('site_name', 'AlbaTech Solutions');
$metaDescription = 'The page you requested could not be found. Explore AlbaTech Solutions services, projects or contact us.';
$canonicalUrl = rtrim(config('app.url'), '/') . '/404';
ob_start();
?>
<section class="error-page error-page-modern" aria-labelledby="not-found-title">
    <span class="section-kicker">404</span>
    <h1 id="not-found-title">We couldn't find that page.</h1>
    <p>The link may be outdated, or the page may have moved. Try one of these instead.</p>
    <div class="error-actions">
        <a href="/" class="btn btn-primary">Back to home</a>
        <a href="/services" class="btn btn-secondary">Explore services</a>
        <a href="/projects" class="btn btn-secondary">See our work</a>
    </div>
</section>
<?php
$pageContent = ob_get_clean();
require __DIR__ . '/layout.php';
