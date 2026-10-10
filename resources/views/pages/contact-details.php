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
$contactMessage = trim((string) ($contactSettings['whatsapp_prefill_message'] ?? ''));
if ($contactMessage === '') $contactMessage = 'Hi AlbaTech Solutions, I would like to make an enquiry. Please guide me on the next steps.';
$contactHours = trim((string) ($contactSettings['contact_hours'] ?? 'Monday–Friday, 8:00 am–5:00 pm'));
$contactResponseTime = trim((string) ($contactSettings['contact_response_time'] ?? 'Within one business day'));
$contactCity = trim((string) ($contactSettings['business_city'] ?? 'Nairobi, Kenya'));
$contactServiceArea = trim((string) ($contactSettings['service_area'] ?? 'Nationwide — fully online'));
$helpEmail = trim((string) ($contactSettings['support_email'] ?? ''));
$contactTabs = ['All', 'WhatsApp', 'Email', 'Location'];
?>
<section class="contact-page-hero">
  <div class="container"><span class="eyebrow">Contact & location</span><h1>How can we help you?</h1><p>Choose the best way to reach AlbaTech Solutions. Tell us what you need and we’ll help you work out the next step.</p></div>
</section>
<section class="section contact-page-section">
  <div class="container">
    <div class="contact-filter" role="group" aria-label="Filter contact methods">
      <button class="contact-filter__button is-active" type="button" data-contact-filter="all" aria-pressed="true">All</button>
      <button class="contact-filter__button" type="button" data-contact-filter="whatsapp" aria-pressed="false">WhatsApp</button>
      <button class="contact-filter__button" type="button" data-contact-filter="email" aria-pressed="false">Email</button>
      <button class="contact-filter__button" type="button" data-contact-filter="location" aria-pressed="false">Location</button>
    </div>
    <div class="contact-details-grid contact-details-grid--reference">
      <article class="contact-detail-card" data-contact-card="whatsapp">
        <span class="contact-card-eyebrow">Fastest response</span><h2>WhatsApp Support</h2>
        <div class="contact-detail-facts"><p><strong>Availability:</strong><br><?= View::escape($contactHours) ?></p>
        <?php if ($contactPhone !== ''): ?><p><strong>Number:</strong><br><?= View::escape($contactPhone) ?></p><?php endif; ?></div>
        <?php if ($contactWhatsApp !== ''): ?><a class="button button--whatsapp contact-card-action" href="https://wa.me/<?= View::escape($contactWhatsApp) ?>?text=<?= rawurlencode($contactMessage) ?>" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Chat on WhatsApp</a><?php else: ?><p>WhatsApp contact is being configured. Please email us instead.</p><?php endif; ?>
      </article>
      <article class="contact-detail-card" data-contact-card="email">
        <span class="contact-card-eyebrow">Email us</span><h2>Email Support</h2>
        <?php if ($contactEmail !== '' && filter_var($contactEmail, FILTER_VALIDATE_EMAIL)): ?><p><strong>General enquiries:</strong><br><a href="mailto:<?= View::escape($contactEmail) ?>"><?= View::escape($contactEmail) ?></a></p><?php endif; ?>
        <?php if ($helpEmail !== '' && filter_var($helpEmail, FILTER_VALIDATE_EMAIL)): ?><p><strong>Help & support:</strong><br><a href="mailto:<?= View::escape($helpEmail) ?>"><?= View::escape($helpEmail) ?></a></p><?php endif; ?>
        <p><strong>Response time:</strong><br><?= View::escape($contactResponseTime) ?></p>
        <?php if ($contactEmail === '' && $helpEmail === ''): ?><p>Email details are being updated. Use WhatsApp if available.</p><?php endif; ?>
      </article>
      <article class="contact-detail-card" data-contact-card="location">
        <span class="contact-card-eyebrow">Find us</span><h2>Location</h2>
        <p><strong>City:</strong><br><?= View::escape($contactCity !== '' ? $contactCity : $contactLocation) ?></p>
        <p><strong>Service area:</strong><br><?= View::escape($contactServiceArea) ?></p>
        <p><strong>Availability:</strong><br><?= View::escape($contactHours) ?></p>
        <p class="contact-location-note">Online and remote support is available. Please arrange an appointment before visiting any physical location.</p>
      </article>
    </div>
    <div class="contact-details-note"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><p>For your safety, never send passwords, PINs, one-time verification codes or M-Pesa credentials in an enquiry. AlbaTech Solutions is an independent service provider, not a government agency.</p></div>
  </div>
</section>
<script nonce="<?= View::escape($cspNonce ?? '') ?>">
document.addEventListener('DOMContentLoaded', function () {
  const buttons = document.querySelectorAll('[data-contact-filter]');
  const cards = document.querySelectorAll('[data-contact-card]');
  buttons.forEach(function (button) {
    button.addEventListener('click', function () {
      const filter = button.dataset.contactFilter;
      buttons.forEach(function (item) { const active = item === button; item.classList.toggle('is-active', active); item.setAttribute('aria-pressed', active ? 'true' : 'false'); });
      cards.forEach(function (card) { card.hidden = filter !== 'all' && card.dataset.contactCard !== filter; });
    });
  });
});
</script>
