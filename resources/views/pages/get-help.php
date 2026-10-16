<?php
use AlbaTech\Core\View;
use AlbaTech\Core\Env;
use AlbaTech\Repositories\SiteSettingsRepository;

$siteSettings = (new SiteSettingsRepository())->publicValues();
$whatsAppNumber = preg_replace('/\D+/', '', (string) ($siteSettings['whatsapp_number'] ?? ''));
$contactEmail = trim((string) ($siteSettings['contact_email'] ?? ''));
$contactPhone = trim((string) ($siteSettings['business_phone'] ?? ''));
$contactPhoneHref = preg_replace('/[^0-9+]/', '', $contactPhone);
$contactLocation = trim((string) ($siteSettings['business_address'] ?? 'Nairobi, Kenya'));
$contactHours = trim((string) ($siteSettings['contact_hours'] ?? 'Monday–Friday, 8:00 am–5:00 pm'));
$contactResponseTime = trim((string) ($siteSettings['contact_response_time'] ?? 'Within one business day'));
$contactCity = trim((string) ($siteSettings['business_city'] ?? 'Nairobi, Kenya'));
$contactServiceArea = trim((string) ($siteSettings['service_area'] ?? 'Nationwide — fully online'));
$helpEmail = trim((string) ($siteSettings['support_email'] ?? ''));
$mailFallback = trim((string) Env::get('MAIL_TO', ''));

$serviceSlug = trim((string) ($_GET['service'] ?? ''));
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
<?php
$whatsAppMessage = trim((string) ($siteSettings['whatsapp_prefill_message'] ?? ''));
if ($whatsAppMessage === '') $whatsAppMessage = 'Hi AlbaTech Solutions, I would like to make an enquiry.';
if ($serviceSlug !== '') {
    $whatsAppMessage = 'Hi AlbaTech Solutions, I would like to ask about: ' . str_replace('-', ' ', $serviceSlug) . '.';
}
$whatsAppUrl = $whatsAppNumber !== ''
    ? 'https://wa.me/' . $whatsAppNumber . '?text=' . rawurlencode($whatsAppMessage)
    : null;

$hasEmail = ($contactEmail !== '' && filter_var($contactEmail, FILTER_VALIDATE_EMAIL))
    || ($helpEmail !== '' && filter_var($helpEmail, FILTER_VALIDATE_EMAIL))
    || ($mailFallback !== '' && filter_var($mailFallback, FILTER_VALIDATE_EMAIL));
$hasPhone = $contactPhoneHref !== '' && strlen(preg_replace('/\D+/', '', $contactPhoneHref)) >= 7;
$hasWhatsApp = strlen($whatsAppNumber) >= 8 && strlen($whatsAppNumber) <= 15 && !str_starts_with($whatsAppNumber, '0');
?>
<section class="contact-hero">
    <div class="container contact-hero__inner">
        <span class="eyebrow"><?= View::escape($cmsPage['title'] ?? 'Contact us') ?></span>
        <h1><?= View::escape($cmsPage['title'] ?? 'Tell us what you need.') ?></h1>
        <p><?= View::escape($cmsPage['intro'] ?? 'Have a question about a website, an online business service or help with a digital process? Use the form below or reach us directly. We will guide you on what to do next.') ?></p>
    </div>
</section>

<section class="section contact-section">
    <div class="container">
        <div class="contact-grid">
            <!-- Enquiry Form -->
            <div class="enquiry-form-wrapper">
                <span class="section-kicker">Send an enquiry</span>
                <h2>Quick enquiry form</h2>
                <p class="contact-intro">Fill in your details and we'll help you work out the next step. Your information is safe with us.</p>

                <?php if (($_GET['sent'] ?? '') === '1'): ?><div class="form-success" role="status">Your enquiry has been sent successfully. We will respond using the contact details you provided.</div><?php elseif (($_GET['sent'] ?? '') === '0'): ?><div class="form-error" role="alert">We could not send your enquiry right now. Please try again later or use the direct contact details.</div><?php endif; ?>
                <form class="enquiry-form" id="enquiryForm" method="post" action="/get-help">
                    <input type="hidden" name="_csrf" value="<?= View::escape($csrf ?? '') ?>">
                    <div class="form-honeypot" aria-hidden="true"><label>Leave this field empty<input type="text" name="website_url" tabindex="-1" autocomplete="off"></label></div>
                    <div class="form-group">
                        <label for="enquiry-name">Your name <span class="required">*</span></label>
                        <input
                            type="text"
                            id="enquiry-name"
                            name="name"
                            required
                            placeholder="e.g., John Kamau"
                            autocomplete="name"
                        >
                    </div>

                    <div class="form-group">
                        <label for="enquiry-email">Email address <span class="required">*</span></label>
                        <input
                            type="email"
                            id="enquiry-email"
                            name="email"
                            required
                            placeholder="e.g., john@example.com"
                            autocomplete="email"
                        >
                    </div>

                    <div class="form-group">
                        <label for="enquiry-phone">Phone number (optional)</label>
                        <input
                            type="tel"
                            id="enquiry-phone"
                            name="phone"
                            placeholder="e.g., 0712 345 678"
                            autocomplete="tel"
                        >
                    </div>

                    <div class="form-group">
                        <label for="enquiry-service">What do you need help with?</label>
                        <?php
                        $serviceOptions = is_array($enquiryServices ?? null) ? $enquiryServices : ['other' => 'Something else'];
                        $selectedService = '';
                        if ($serviceSlug !== '') {
                            if (isset($serviceOptions[$serviceSlug])) {
                                $selectedService = $serviceSlug;
                            } else {
                                foreach (array_keys($serviceOptions) as $optionKey) {
                                    if ($optionKey !== 'other' && (str_contains($serviceSlug, (string) $optionKey) || str_contains((string) $optionKey, $serviceSlug))) {
                                        $selectedService = (string) $optionKey;
                                        break;
                                    }
                                }
                                if ($selectedService === '') $selectedService = 'other';
                            }
                        }
                        ?>
                        <select id="enquiry-service" name="service">
                            <option value="">Choose a service or describe your need</option>
                            <?php foreach ($serviceOptions as $optionKey => $optionLabel): ?><option value="<?= View::escape($optionKey) ?>"<?= $selectedService === (string) $optionKey ? ' selected' : '' ?>><?= View::escape($optionLabel) ?></option><?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="enquiry-message">Your message <span class="required">*</span></label>
                        <textarea
                            id="enquiry-message"
                            name="message"
                            required
                            rows="4"
                            placeholder="Tell us what you're trying to achieve. Be as specific as you can."
                        ></textarea>
                    </div>

                    <div class="form-actions">
                        <?php if ($hasWhatsApp): ?>
                        <button type="button" class="button button--whatsapp" id="whatsapp-submit">
                            <i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Continue in WhatsApp
                        </button>
                        <?php endif; ?>
                        <?php if ($hasEmail): ?>
                        <button type="submit" name="send_method" value="email" class="button button--secondary" id="email-submit">
                            <i class="fa-regular fa-envelope" aria-hidden="true"></i> Send enquiry by email
                        </button>
                        <?php endif; ?>
                        <?php if (!$hasWhatsApp && !$hasEmail): ?>
                        <p class="form-error" role="alert">Contact delivery is not configured yet. Please try again later.</p>
                        <?php endif; ?>
                    </div>

                    <p class="form-notice">
                        <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
                        For your safety, never send passwords, PINs, one-time verification codes or M-Pesa credentials in an enquiry.
                    </p>
                </form>
            </div>

            <!-- Contact Details -->
            <aside class="contact-details-sidebar">
                <span class="section-kicker">Direct contact</span>
                <h2>Other ways to reach us</h2>

                <?php if ($hasWhatsApp): ?>
                <div class="contact-detail-item">
                    <span class="contact-detail-icon contact-detail-icon--whatsapp"><i class="fa-brands fa-whatsapp"></i></span>
                    <div>
                        <h3>WhatsApp</h3>
                        <p>Fastest response time</p>
                        <?php if ($contactHours !== ''): ?>
                        <p class="contact-detail-meta"><i class="fa-regular fa-clock" aria-hidden="true"></i> <?= View::escape($contactHours) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($hasPhone): ?>
                <div class="contact-detail-item">
                    <span class="contact-detail-icon"><i class="fa-solid fa-phone"></i></span>
                    <div>
                        <h3>Phone</h3>
                        <p><a href="tel:<?= View::escape($contactPhoneHref) ?>"><?= View::escape($contactPhone) ?></a></p>
                        <?php if ($contactHours !== ''): ?>
                        <p class="contact-detail-meta"><i class="fa-regular fa-clock" aria-hidden="true"></i> <?= View::escape($contactHours) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($hasEmail): ?>
                <div class="contact-detail-item">
                    <span class="contact-detail-icon"><i class="fa-regular fa-envelope"></i></span>
                    <div>
                        <h3>Email</h3>
                        <?php if ($contactEmail !== '' && filter_var($contactEmail, FILTER_VALIDATE_EMAIL)): ?>
                        <p><a href="mailto:<?= View::escape($contactEmail) ?>"><?= View::escape($contactEmail) ?></a></p>
                        <?php endif; ?>
                        <?php if ($helpEmail !== '' && filter_var($helpEmail, FILTER_VALIDATE_EMAIL)): ?>
                        <p><a href="mailto:<?= View::escape($helpEmail) ?>"><?= View::escape($helpEmail) ?></a></p>
                        <?php endif; ?>
                        <?php if ($contactResponseTime !== ''): ?>
                        <p class="contact-detail-meta"><i class="fa-regular fa-clock" aria-hidden="true"></i> <?= View::escape($contactResponseTime) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="contact-detail-item">
                    <span class="contact-detail-icon"><i class="fa-solid fa-location-dot"></i></span>
                    <div>
                        <h3>Location</h3>
                        <p><?= View::escape($contactCity !== '' ? $contactCity : $contactLocation) ?></p>
                        <?php if ($contactServiceArea !== ''): ?>
                        <p class="contact-detail-meta"><i class="fa-solid fa-globe" aria-hidden="true"></i> <?= View::escape($contactServiceArea) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="contact-safety-box">
                    <strong><i class="fa-solid fa-circle-info" aria-hidden="true"></i> Important</strong>
                    <p>AlbaTech Solutions is an independent service provider, not a government agency. For government-related processes, decisions remain with the relevant authority.</p>
                </div>
            </aside>
        </div>
    </div>
</section>

<script nonce="<?= View::escape($cspNonce ?? '') ?>">
(function() {
    const form = document.getElementById('enquiryForm');
    const whatsappButton = document.getElementById('whatsapp-submit');
    if (!form || !whatsappButton) return;

    whatsappButton.addEventListener('click', function() {
        if (!form.checkValidity()) { form.reportValidity(); return; }
        const data = new FormData(form);
        const name = String(data.get('name') || '').trim();
        const email = String(data.get('email') || '').trim();
        const phone = String(data.get('phone') || '').trim();
        const service = String(data.get('service') || '').trim();
        const message = String(data.get('message') || '').trim();
        const number = '<?= View::escape($whatsAppNumber) ?>';
        if (!/^\d{8,15}$/.test(number) || number.startsWith('0')) {
            window.location.href = '/get-help';
            return;
        }
        let body = 'Hi AlbaTech Solutions,\n\nName: ' + name + '\nEmail: ' + email;
        if (phone) body += '\nPhone: ' + phone;
        if (service) body += '\nService interest: ' + service;
        body += '\n\nMessage:\n' + message;
        const url = 'https://wa.me/' + number + '?text=' + encodeURIComponent(body);
        const opened = window.open(url, '_blank');
        if (opened) opened.opener = null;
        else window.location.href = url;
    });
})();
</script>
