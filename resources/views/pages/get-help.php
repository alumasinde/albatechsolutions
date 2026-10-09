<?php
use AlbaTech\Core\Env;
use AlbaTech\Core\View;
use AlbaTech\Repositories\SiteSettingsRepository;

$siteSettings = (new SiteSettingsRepository())->publicValues();
$whatsAppNumber = preg_replace('/\D+/', '', $siteSettings['whatsapp_number'] ?? (Env::get('WHATSAPP_NUMBER', '') ?? ''));
$serviceSlug = trim((string) ($_GET['service'] ?? ''));
$whatsAppMessage = 'Hi AlbaTech Solutions, I would like to make an enquiry.';
if ($serviceSlug !== '') {
    $whatsAppMessage = 'Hi AlbaTech Solutions, I would like to ask about: ' . str_replace('-', ' ', $serviceSlug) . '.';
}
$whatsAppUrl = $whatsAppNumber !== ''
    ? 'https://wa.me/' . $whatsAppNumber . '?text=' . rawurlencode($whatsAppMessage)
    : null;
?>
<section class="contact-hero">
    <div class="container contact-hero__inner">
        <span class="eyebrow">Contact us</span>
        <h1>Tell us what you need.<br><span>We'll help with the next step.</span></h1>
        <p>Have a question about a website, an online business service or help with a digital process? Share a little about what you need and we'll guide you on what to do next.</p>
    </div>
</section>

<section class="section contact-section">
    <div class="container contact-grid">
        <div class="contact-options">
            <span class="section-kicker">Get in touch</span>
            <h2>Choose the easiest way to reach us.</h2>
            <p class="contact-intro">You do not need to know exactly which service you need. Explain your goal in your own words and we can clarify the options together.</p>

            <article class="contact-method">
                <span class="contact-method__icon contact-method__icon--whatsapp" aria-hidden="true">W</span>
                <div>
                    <h3>WhatsApp</h3>
                    <p>Best for a quick question or to explain what you need.</p>
                    <?php if ($whatsAppUrl !== null): ?>
                        <a class="contact-method__link" href="<?= View::escape($whatsAppUrl) ?>" target="_blank" rel="noopener noreferrer">Start a WhatsApp chat <span aria-hidden="true">↗</span></a>
                    <?php else: ?>
                        <p class="contact-method__hint">WhatsApp contact is being configured. Please use the enquiry guidance on this page for now.</p>
                    <?php endif; ?>
                </div>
            </article>

            <article class="contact-method">
                <span class="contact-method__icon" aria-hidden="true">@</span>
                <div>
                    <h3>What can we help with?</h3>
                    <p>Website design, business online presence, selected government and business processes, and career documents.</p>
                    <a class="contact-method__link" href="/services">Browse all services <span aria-hidden="true">→</span></a>
                </div>
            </article>

            <div class="contact-safety">
                <strong>Keep your information safe</strong>
                <p>Do not send passwords, iTax login details, PINs, M-Pesa credentials or one-time verification codes in an enquiry.</p>
            </div>
        </div>

        <aside class="contact-card">
            <span class="contact-card__eyebrow">Before we begin</span>
            <h2>A few simple steps</h2>
            <ol class="contact-steps">
                <li><span>1</span><div><strong>Tell us your goal</strong><p>What are you trying to do or improve?</p></div></li>
                <li><span>2</span><div><strong>We clarify the details</strong><p>We'll explain what information or materials may be needed.</p></div></li>
                <li><span>3</span><div><strong>Agree before work starts</strong><p>We'll confirm the scope, timing and charges before proceeding.</p></div></li>
            </ol>
            <a class="button contact-card__button" href="/services">View services</a>
            <p class="contact-card__note">For government-related processes, AlbaTech Solutions provides independent assistance and is not a government agency. Decisions remain with the relevant authority.</p>
        </aside>
    </div>
</section>
