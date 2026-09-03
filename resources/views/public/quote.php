<?php
$robots = 'noindex, follow';
$title = 'Get a Quote — ' . setting('site_name', 'AlbaTech Solutions');
$metaDescription = 'Tell AlbaTech what you want to build and receive a tailored project quote.';
$canonicalUrl = rtrim(config('app.url'), '/') . '/quote';
$errors = \App\Core\Session::getFlash('_errors') ?? [];
$old = \App\Core\Session::getFlash('_old') ?? [];
$selected = $selectedService ?? ($old['service'] ?? '');
ob_start();
?>
<section class="growth-hero"><div class="growth-container"><span class="eyebrow">Get assistance</span><h1>Tell us what you need to get done.</h1><p>Share the task, business need or digital problem. We’ll help you work out the right next step.</p></div></section>
<section class="growth-section"><div class="growth-container quote-layout">
    <div class="growth-panel quote-form-panel">
        <?php if ($errors): ?><div class="alert alert-error"><strong>Please check the form.</strong><ul><?php foreach ($errors as $fieldErrors) foreach ((array)$fieldErrors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
        <form method="POST" action="/quote" class="growth-form" novalidate>
            <?= csrf_field() ?>
            <div class="hp-field" aria-hidden="true">
                <label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
            </div>
                        <div class="form-grid-2">
                <label>Name *<input name="name" required value="<?= e($old['name'] ?? '') ?>" autocomplete="name"></label>
                <label>Business / organisation<input name="business_name" value="<?= e($old['business_name'] ?? '') ?>" autocomplete="organization"></label>
                <label>Email *<input type="email" name="email" required value="<?= e($old['email'] ?? '') ?>" autocomplete="email"></label>
                <label>Phone / WhatsApp<input name="phone" value="<?= e($old['phone'] ?? '') ?>" autocomplete="tel"></label>
                <label>Service<select name="service">
                    <option value="">Select a service</option>
                    <?php foreach ($services as $service): ?><option value="<?= e($service['slug']) ?>" <?= $selected === $service['slug'] ? 'selected' : '' ?>><?= e($service['name']) ?></option><?php endforeach; ?>
                </select></label>
                <label>Budget<select name="budget"><option value="">Prefer not to say</option><?php foreach (['Under KES 25,000','KES 25,000–50,000','KES 50,000–100,000','KES 100,000–250,000','KES 250,000+'] as $budget): ?><option <?= ($old['budget'] ?? '') === $budget ? 'selected' : '' ?>><?= e($budget) ?></option><?php endforeach; ?></select></label>
            </div>
            <label>What are you looking to build? *<select name="project_type"><option value="">Choose one</option><?php foreach (['Business website','E-commerce website','Custom business system','Mobile app','API / M-Pesa integration','Website improvement','Other'] as $type): ?><option <?= ($old['project_type'] ?? '') === $type ? 'selected' : '' ?>><?= e($type) ?></option><?php endforeach; ?></select></label>
            <label>Tell us about your project *<textarea name="message" rows="7" required placeholder="What do you need, who will use it, and what problem should it solve?"><?= e($old['message'] ?? '') ?></textarea></label>
            <button class="btn btn-primary btn-lg" type="submit">Send Request <i class="fa-solid fa-arrow-right"></i></button>
        </form>
    </div>
    <aside class="growth-panel quote-aside"><span class="eyebrow">What happens next</span><div class="timeline"><div><b>01</b><h3>We review</h3><p>We look at your requirements and identify the right approach.</p></div><div><b>02</b><h3>We clarify</h3><p>We contact you if we need more details before preparing the quote.</p></div><div><b>03</b><h3>You get a plan</h3><p>You receive a practical scope, estimate and next steps.</p></div></div><div class="trust-note"><i class="fa-solid fa-shield-halved"></i><span>Your details are used to respond to this enquiry and are not published.</span></div></aside>
</div></section>
<?php $pageContent = ob_get_clean(); require __DIR__ . '/layout.php';
