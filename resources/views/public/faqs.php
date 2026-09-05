<?php
$title = 'FAQs — ' . setting('site_name', 'AlbaTech Solutions');
$metaDescription = 'Frequently asked questions about ' . setting('site_name', 'AlbaTech Solutions') . '.';
$canonicalUrl = rtrim(config('app.url'), '/') . '/faqs';
$jsonLd = array_filter([
    !empty($faqs) ? \App\Core\Seo::faqPage($faqs) : null,
    \App\Core\Seo::breadcrumbs([
        ['name' => 'Home', 'url' => rtrim(config('app.url'), '/') . '/'],
        ['name' => 'FAQs', 'url' => $canonicalUrl],
    ]),
]);
ob_start();
?>
<section class="faq-page">
    <div class="faq-page__head"><span class="section-kicker">Questions</span><h1>Frequently Asked Questions</h1><p>Clear answers about getting help from AlbaTech Solutions. If your question is not here, you can ask us directly.</p></div>
    <?php foreach ($faqs as $faq): ?>
        <details class="v5-faq-item">
            <summary><?= e($faq['question']) ?></summary>
            <p><?= e($faq['answer']) ?></p>
        </details>
    <?php endforeach; ?>
    <?php if (empty($faqs)): ?>
        <p class="public-empty-copy">No FAQs published yet. <a href="/get-help">Tell us what you need help with.</a></p>
    <?php endif; ?>
</section>
<?php
$pageContent = ob_get_clean();
require __DIR__ . '/layout.php';
