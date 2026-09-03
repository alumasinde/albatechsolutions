<?php
$title = 'Contact — ' . setting('site_name', 'AlbaTech Solutions');
$metaDescription = 'Talk to ' . setting('site_name', 'AlbaTech Solutions') . ' about digital assistance, business services, websites or software.';
$canonicalUrl = rtrim(config('app.url'), '/') . '/contact';
$jsonLd = [\App\Core\Seo::breadcrumbs([
    ['name' => 'Home', 'url' => rtrim(config('app.url'), '/') . '/'],
    ['name' => 'Contact', 'url' => $canonicalUrl],
])];
ob_start();
?>
<section class="phase6-contact-hero">
    <div class="public-container phase6-contact-hero__grid">
        <div>
            <a href="javascript:history.back()" class="phase2-back-button"><i class="fa-solid fa-arrow-left"></i><span>Go back</span></a>
            <span class="public-kicker">Let's talk</span>
            <h1>Tell us what you need to get done.</h1>
            <p class="phase6-lead">Need help with an online service, business task, website or digital system? Send a message and we'll figure out the next step.</p>
            <div class="phase6-contact-options">
                <?php if (setting('whatsapp_number')): ?><a class="phase6-contact-option phase6-contact-option--whatsapp js-whatsapp" data-whatsapp-number="<?= e(preg_replace('/\D+/', '', (string) setting('whatsapp_number'))) ?>" href="<?= e(whatsapp_url('Hi AlbaTech Solutions, I would like to discuss a project.')) ?>" target="_blank" rel="noopener noreferrer"><span><i class="fa-brands fa-whatsapp"></i></span><div><strong>Prefer WhatsApp?</strong><small>Usually the quickest way to start.</small></div><i class="fa-solid fa-arrow-up-right-from-square"></i></a><?php endif; ?>
                <?php if (setting('contact_email')): ?><a class="phase6-contact-option" href="mailto:<?= e(setting('contact_email')) ?>"><span><i class="fa-solid fa-envelope"></i></span><div><strong>Email</strong><small><?= e(setting('contact_email')) ?></small></div><i class="fa-solid fa-arrow-right"></i></a><?php endif; ?>
                <?php if (setting('contact_phone')): ?><a class="phase6-contact-option" href="tel:<?= e(setting('contact_phone')) ?>"><span><i class="fa-solid fa-phone"></i></span><div><strong>Call</strong><small><?= e(setting('contact_phone')) ?></small></div><i class="fa-solid fa-arrow-right"></i></a><?php endif; ?>
            </div>
        </div>
        <div class="phase6-contact-note"><span class="phase6-note-icon"><i class="fa-solid fa-lightbulb"></i></span><h2>You don't need a perfect brief.</h2><p>Just tell us what you're trying to achieve, what isn't working, or what you'd like to improve. The technical details can come later.</p><ul><li>What are you trying to build?</li><li>Who will use it?</li><li>What would a successful result look like?</li></ul></div>
    </div>
</section>

<section class="public-section">
    <div class="public-container phase6-contact-form-grid">
        <div><span class="public-kicker">Alternative contact</span><h2>Send a message</h2><p>If you prefer email-style communication, use the form. Your message is saved securely so it can be followed up.</p><div class="phase6-contact-trust"><span><i class="fa-solid fa-lock"></i> Your message is protected</span><span><i class="fa-solid fa-bolt"></i> No account required</span></div></div>
        <div>
            <?php $success = \App\Core\Session::getFlash('_success'); ?>
            <?php if ($success): ?><div class="alert alert-success" role="status"><?= e($success) ?></div><?php endif; ?>
            <?php $errors = flash_errors(); ?>
            <?php if ($errors): ?><div class="alert alert-error" role="alert"><?php foreach ($errors as $fieldErrors): foreach ($fieldErrors as $msg): ?><p><?= e($msg) ?></p><?php endforeach; endforeach; ?></div><?php endif; ?>
            <form method="POST" action="/contact" class="phase6-contact-form" novalidate>
                <?= csrf_field() ?>
                <div class="phase6-form-row"><div><label for="name">Your name</label><input type="text" id="name" name="name" value="<?= e(old('name')) ?>" autocomplete="name" required></div><div><label for="email">Email</label><input type="email" id="email" name="email" value="<?= e(old('email')) ?>" autocomplete="email" required></div></div>
                <div class="phase6-form-row"><div><label for="phone">Phone <span>optional</span></label><input type="tel" id="phone" name="phone" value="<?= e(old('phone')) ?>" autocomplete="tel"></div><div><label for="subject">What is this about? <span>optional</span></label><input type="text" id="subject" name="subject" value="<?= e(old('subject')) ?>"></div></div>
                <div><label for="message">Tell us a little about it</label><textarea id="message" name="message" rows="7" maxlength="2000" required><?= e(old('message')) ?></textarea><small class="phase6-form-help">A few sentences are enough.</small></div>
                <button type="submit" class="btn btn-primary btn-lg btn-block"><i class="fa-solid fa-paper-plane"></i> Send message</button>
            </form>
        </div>
    </div>
</section>
<?php
$pageContent = ob_get_clean();
require __DIR__ . '/layout.php';
