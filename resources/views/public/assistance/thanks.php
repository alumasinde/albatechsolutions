<?php
$title = 'Assistance Request Received | AlbaTech Solutions';
$metaDescription = 'Your AlbaTech digital assistance request has been received.';
$canonicalUrl = rtrim(config('app.url'), '/') . '/get-help/thanks';
$robots = 'noindex, nofollow';
ob_start();
?>
<section class="assist-thanks"><div class="public-container"><div class="assist-thanks__card"><span class="assist-thanks__icon"><i class="fa-solid fa-check"></i></span><span class="public-kicker">Request received</span><h1>We've got it.</h1><p>Your assistance request has been received. Keep this reference in case you need to follow up.</p><?php if ($reference): ?><div class="assist-reference"><span>Reference</span><strong><?= e($reference) ?></strong></div><?php endif; ?><div class="assist-thanks__actions"><a class="btn btn-primary" href="/">Back to AlbaTech <i class="fa-solid fa-arrow-right"></i></a><?php if (setting('whatsapp_number')): ?><a class="btn btn-secondary js-whatsapp" data-whatsapp-number="<?= e(preg_replace('/\D+/', '', (string)setting('whatsapp_number'))) ?>" href="<?= e(whatsapp_url('Hi AlbaTech Solutions, I just submitted an assistance request' . ($reference ? ' ' . $reference : '') . '.')) ?>" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-whatsapp"></i> Continue on WhatsApp</a><?php endif; ?></div></div></div></section>
<?php $pageContent = ob_get_clean(); require dirname(__DIR__) . '/layout.php';
