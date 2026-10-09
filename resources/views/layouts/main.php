<?php
use AlbaTech\Core\Env;
use AlbaTech\Core\View;

$siteName = $appName ?? Env::get('APP_NAME', 'AlbaTech Solutions');
$pageTitle = $pageTitle ?? $siteName;
$metaDescription = $metaDescription ?? 'Web design and digital assistance for Kenyan businesses.';
$canonicalUrl = $canonicalUrl ?? (Env::get('APP_URL', 'http://albatechsolution.dev:8000') ?? '');
$whatsAppNumber = preg_replace('/\D+/', '', Env::get('WHATSAPP_NUMBER', '') ?? '');
$whatsAppUrl = $whatsAppNumber !== ''
    ? 'https://wa.me/' . $whatsAppNumber . '?text=' . rawurlencode('Hi AlbaTech Solutions, I would like to make an enquiry.')
    : '/get-help';
?>
<!doctype html>
<html lang="en-KE">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= View::escape($metaDescription) ?>">
    <meta name="author" content="<?= View::escape($founderName ?? 'Albert Masinde') ?>">
    <meta name="theme-color" content="#0F4C81">
    <link rel="canonical" href="<?= View::escape($canonicalUrl) ?>">
    <link rel="stylesheet" href="/assets/css/app.css">
    <title><?= View::escape($pageTitle) ?></title>
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="/" aria-label="<?= View::escape($siteName) ?> home">AlbaTech <span>Solutions</span></a>
        <nav class="site-nav" aria-label="Main navigation">
            <a href="/">Home</a>
            <a href="/services">Services</a>
            <a href="/guides">Guides</a>
            <a href="/about">About</a>
            <a href="/get-help">Get help</a>
        </nav>
    </div>
</header>

<main id="main-content">
    <?= $content ?>
</main>

<footer class="site-footer">
    <div class="container footer-inner">
        <div>
            <a class="brand" href="/">AlbaTech <span>Solutions</span></a>
            <p>Practical digital solutions for people and businesses in Kenya.</p>
        </div>
        <p>&copy; <?= date('Y') ?> <?= View::escape($siteName) ?>. Founded by Albert Masinde.</p>
    </div>
</footer>
</body>
</html>
