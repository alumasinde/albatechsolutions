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
<section class="section">
    <h2>Frequently Asked Questions</h2>
    <?php foreach ($faqs as $faq): ?>
        <details class="faq-item">
            <summary><?= e($faq['question']) ?></summary>
            <p><?= e($faq['answer']) ?></p>
        </details>
    <?php endforeach; ?>
    <?php if (empty($faqs)): ?>
        <p class="public-empty-copy">No FAQs published yet.</p>
    <?php endif; ?>
</section>
<?php
$pageContent = ob_get_clean();
require __DIR__ . '/layout.php';
