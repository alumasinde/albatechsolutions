<?php
use AlbaTech\Core\View;
use AlbaTech\Repositories\SiteSettingsRepository;

$contactSettings = (new SiteSettingsRepository())->publicValues();
$contactEmail = trim((string) ($contactSettings['contact_email'] ?? ''));
$contactPhone = trim((string) ($contactSettings['business_phone'] ?? ''));
$contactPhoneHref = preg_replace('/[^0-9+]/', '', $contactPhone);
$contactLocation = trim((string) ($contactSettings['business_address'] ?? 'Ruaka, Kenya'));
$contactLocation = $contactLocation !== '' ? $contactLocation : 'Kenya';
$contactWhatsApp = preg_replace('/\\D+/', '', (string) ($contactSettings['whatsapp_number'] ?? ''));
?>
<section class="legal-hero">
  <div class="container legal-hero__inner">
    <span class="eyebrow">Contact & location</span>
    <h1>Let's talk about what you need.</h1>
    <p>Reach out with your question, project idea or support request. We will help clarify the next practical step.</p>
  </div>
</section>
<section class="section">
  <div class="container contact-details-grid">
    <article class="contact-detail-card">
      <span class="contact-detail-icon"><i class="fa-solid fa-location-dot" aria-hidden="true"></i></span>
      <h2>Our service area</h2>
      <p class="contact-detail-value"><?= View::escape($contactLocation) ?></p>
      <p>We support clients in our local area and across Kenya. Please contact us before visiting; this listing may describe our service area rather than a public walk-in office.</p>
    </article>
    <article class="contact-detail-card">
      <span class="contact-detail-icon"><i class="fa-regular fa-envelope" aria-hidden="true"></i></span>
      <h2>Email</h2>
      <?php if ($contactEmail !== '' && filter_var($contactEmail, FILTER_VALIDATE_EMAIL)): ?>
        <p class="contact-detail-value"><a href="mailto:<?= View::escape($contactEmail) ?>"><?= View::escape($contactEmail) ?></a></p>
        <p>Useful for project details or information you want to keep in an email thread.</p>
      <?php else: ?>
        <p>Email contact details are being updated. Please use our contact page.</p>
      <?php endif; ?>
    </article>
    <article class="contact-detail-card">
      <span class="contact-detail-icon"><i class="fa-solid fa-phone" aria-hidden="true"></i></span>
      <h2>Phone</h2>
      <?php if ($contactPhone !== '' && $contactPhoneHref !== ''): ?>
        <p class="contact-detail-value"><a href="tel:<?= View::escape($contactPhoneHref) ?>"><?= View::escape($contactPhone) ?></a></p>
        <p>Call to discuss availability and the best way to handle your request.</p>
      <?php else: ?>
        <p>Our phone number is being configured. Use WhatsApp or the contact page where available.</p>
      <?php endif; ?>
    </article>
    <article class="contact-detail-card contact-detail-card--highlight">
      <span class="contact-detail-icon"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i></span>
      <h2>WhatsApp</h2>
      <p>For a quick enquiry, tell us what you want to achieve and we can discuss the available options.</p>
      <?php if ($contactWhatsApp !== ''): ?>
        <a class="button button--whatsapp" href="https://wa.me/<?= View::escape($contactWhatsApp) ?>?text=<?= rawurlencode('Hi AlbaTech Solutions, I would like to make an enquiry.') ?>" target="_blank" rel="noopener noreferrer">Start a WhatsApp chat <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
      <?php else: ?>
        <a class="button" href="/get-help">Open contact page</a>
      <?php endif; ?>
    </article>
  </div>
  <div class="container contact-details-note"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><p>For your safety, never send passwords, PINs, one-time verification codes or M-Pesa credentials in an enquiry. AlbaTech Solutions provides independent digital assistance and is not a government agency.</p></div>
</section>
