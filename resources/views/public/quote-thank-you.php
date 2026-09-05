<?php
$robots = 'noindex, follow';
$title = 'Thank You — ' . setting('site_name', 'AlbaTech Solutions');
$metaDescription = 'Your assistance request has been received by AlbaTech Solutions.';
$canonicalUrl = rtrim(config('app.url'), '/') . '/quote/thank-you';
ob_start();
?><section class="growth-hero thank-you-hero"><div class="growth-container"><div class="success-icon"><i class="fa-solid fa-check"></i></div><span class="eyebrow">Request received</span><h1>Thanks — we’ve got your request.</h1><p>Our team will review the information and get back to you with the next steps.</p><div class="hero-actions"><a class="btn btn-primary" href="/">Back to home</a><a class="btn btn-secondary" href="/services">Browse services</a></div></div></section><?php $pageContent=ob_get_clean(); require __DIR__.'/layout.php';
