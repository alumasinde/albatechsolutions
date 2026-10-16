<?php
use AlbaTech\Core\View;
use AlbaTech\Repositories\SiteSettingsRepository;
use AlbaTech\Core\WhatsAppLink;

$contactSettings = (new SiteSettingsRepository())->publicValues();
$contactEmail = trim((string) ($contactSettings['contact_email'] ?? ''));
$contactPhone = trim((string) ($contactSettings['business_phone'] ?? ''));
$contactPhoneHref = preg_replace('/[^0-9+]/', '', $contactPhone);
$contactLocation = trim((string) ($contactSettings['business_address'] ?? 'Nairobi, Kenya'));
$contactLocation = $contactLocation !== '' ? $contactLocation : 'Kenya';
$contactWhatsAppUrl = WhatsAppLink::build($contactSettings);
$contactHours = trim((string) ($contactSettings['contact_hours'] ?? 'Monday–Friday, 8:00 am–5:00 pm'));
$contactResponseTime = trim((string) ($contactSettings['contact_response_time'] ?? 'Within one business day'));
$contactCity = trim((string) ($contactSettings['business_city'] ?? 'Nairobi, Kenya'));
$contactServiceArea = trim((string) ($contactSettings['service_area'] ?? 'Nationwide — fully online'));
$helpEmail = trim((string) ($contactSettings['support_email'] ?? ''));
?>
<?php if (isset($breadcrumbs) && is_array($breadcrumbs) && !empty($breadcrumbs)): ?>
<nav class="breadcrumb-nav" aria-label="Breadcrumb">
  <div class="container">
    <ol class="breadcrumb-list" itemscope itemtype="https://schema.org/BreadcrumbList">
      <?php foreach ($breadcrumbs as $index => $crumb): ?>
        <li class="breadcrumb-item<?= ($index === count($breadcrumbs) - 1) ? ' breadcrumb-item--current' : '' ?>" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
          <?php if (!empty($crumb['url']) && $index < count($breadcrumbs) - 1): ?>
            <a href="<?= View::escape($crumb['url']) ?>" itemprop="item"><span itemprop="name"><?= View::escape($crumb['name']) ?></span></a>
          <?php else: ?>
            <span itemprop="name"><?= View::escape($crumb['name']) ?></span>
          <?php endif; ?>
          <meta itemprop="position" content="<?= $index + 1 ?>">
        </li>
      <?php endforeach; ?>
    </ol>
  </div>
</nav>
<?php endif; ?>
<section class="contact-page-hero">
  <div class="container"><span class="eyebrow">Contact & location</span><h1>How can we help you?</h1><p>Choose the best way to reach AlbaTech Solutions. Tell us what you need and we’ll help you work out the next step.</p></div>
</section>
<section class="section contact-page-section">
  <div class="container">
    <?php
    // Only render channels that are actually configured. Empty placeholder cards used to point at
    // each other ("use email instead" / "use WhatsApp if available"), leaving visitors no way in.
    $hasEmail = ($contactEmail !== '' && filter_var($contactEmail, FILTER_VALIDATE_EMAIL))
        || ($helpEmail !== '' && filter_var($helpEmail, FILTER_VALIDATE_EMAIL));
    $hasPhone = $contactPhoneHref !== '' && strlen(preg_replace('/\D+/', '', $contactPhoneHref)) >= 7;
    $hasWhatsApp = str_starts_with($contactWhatsAppUrl, 'https://wa.me/');
    $contactTabs = [];
    if ($hasWhatsApp) $contactTabs['whatsapp'] = 'WhatsApp';
    if ($hasPhone) $contactTabs['phone'] = 'Phone';
    if ($hasEmail) $contactTabs['email'] = 'Email';
    $contactTabs['location'] = 'Location';
    ?>
    <?php if (!$hasWhatsApp && !$hasPhone && !$hasEmail): ?>
      <div class="contact-details-note" role="status"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><p>Our direct contact details are being added to this page. Please check back shortly, or browse our <a href="/services">services</a> and <a href="/guides">guides</a> in the meantime.</p></div>
    <?php endif; ?>
    <?php if (count($contactTabs) >= 3): ?>
    <div class="contact-filter" role="group" aria-label="Filter contact methods">
      <button class="contact-filter__button is-active" type="button" data-contact-filter="all" aria-pressed="true">All</button>
      <?php foreach ($contactTabs as $tabKey => $tabLabel): ?>
        <button class="contact-filter__button" type="button" data-contact-filter="<?= View::escape($tabKey) ?>" aria-pressed="false"><?= View::escape($tabLabel) ?></button>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <div class="contact-details-grid contact-details-grid--reference">
      <?php if ($hasWhatsApp): ?>
      <article class="contact-detail-card" data-contact-card="whatsapp">
        <span class="contact-card-eyebrow">Fastest response</span><h2>WhatsApp Support</h2>
        <?php if ($contactHours !== ''): ?><div class="contact-detail-facts"><p><strong>Availability:</strong><br><?= View::escape($contactHours) ?></p></div><?php endif; ?>
        <a class="button button--whatsapp contact-card-action" href="<?= View::escape($contactWhatsAppUrl) ?>" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Chat on WhatsApp</a>
      </article>
      <?php endif; ?>
      <?php if ($hasPhone): ?>
      <article class="contact-detail-card" data-contact-card="phone">
        <span class="contact-card-eyebrow">Call us</span><h2>Phone</h2>
        <div class="contact-detail-facts"><p><strong>Number:</strong><br><a href="tel:<?= View::escape($contactPhoneHref) ?>"><?= View::escape($contactPhone) ?></a></p>
        <?php if ($contactHours !== ''): ?><p><strong>Availability:</strong><br><?= View::escape($contactHours) ?></p><?php endif; ?></div>
      </article>
      <?php endif; ?>
      <?php if ($hasEmail): ?>
      <article class="contact-detail-card" data-contact-card="email">
        <span class="contact-card-eyebrow">Email us</span><h2>Email Support</h2>
        <?php if ($contactEmail !== '' && filter_var($contactEmail, FILTER_VALIDATE_EMAIL)): ?><p><strong>General enquiries:</strong><br><a href="mailto:<?= View::escape($contactEmail) ?>"><?= View::escape($contactEmail) ?></a></p><?php endif; ?>
        <?php if ($helpEmail !== '' && filter_var($helpEmail, FILTER_VALIDATE_EMAIL)): ?><p><strong>Help &amp; support:</strong><br><a href="mailto:<?= View::escape($helpEmail) ?>"><?= View::escape($helpEmail) ?></a></p><?php endif; ?>
        <?php if ($contactResponseTime !== ''): ?><p><strong>Response time:</strong><br><?= View::escape($contactResponseTime) ?></p><?php endif; ?>
      </article>
      <?php endif; ?>
      <article class="contact-detail-card" data-contact-card="location">
        <span class="contact-card-eyebrow">Find us</span><h2>Location</h2>
        <p><strong>City:</strong><br><?= View::escape($contactCity !== '' ? $contactCity : $contactLocation) ?></p>
        <?php if ($contactServiceArea !== ''): ?><p><strong>Service area:</strong><br><?= View::escape($contactServiceArea) ?></p><?php endif; ?>
        <?php if ($contactHours !== ''): ?><p><strong>Availability:</strong><br><?= View::escape($contactHours) ?></p><?php endif; ?>
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
