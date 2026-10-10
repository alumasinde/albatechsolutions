<?php
use AlbaTech\Core\Env;
use AlbaTech\Core\View;
use AlbaTech\Repositories\SiteSettingsRepository;

$siteSettings = (new SiteSettingsRepository())->publicValues();
$whatsAppNumber = preg_replace('/\D+/', '', $siteSettings['whatsapp_number'] ?? (Env::get('WHATSAPP_NUMBER', '') ?? ''));
$contactEmail = trim((string) ($siteSettings['contact_email'] ?? ''));
$contactPhone = trim((string) ($siteSettings['business_phone'] ?? ''));
$contactPhoneHref = preg_replace('/[^0-9+]/', '', $contactPhone);
$contactLocation = trim((string) ($siteSettings['business_address'] ?? 'Nairobi, Kenya'));
$contactHours = trim((string) ($siteSettings['contact_hours'] ?? 'Monday–Friday, 8:00 am–5:00 pm'));
$contactResponseTime = trim((string) ($siteSettings['contact_response_time'] ?? 'Within one business day'));
$contactCity = trim((string) ($siteSettings['business_city'] ?? 'Nairobi, Kenya'));
$contactServiceArea = trim((string) ($siteSettings['service_area'] ?? 'Nationwide — fully online'));
$helpEmail = trim((string) ($siteSettings['support_email'] ?? ''));

$serviceSlug = trim((string) ($_GET['service'] ?? ''));
$whatsAppMessage = 'Hi AlbaTech Solutions, I would like to make an enquiry.';
if ($serviceSlug !== '') {
    $whatsAppMessage = 'Hi AlbaTech Solutions, I would like to ask about: ' . str_replace('-', ' ', $serviceSlug) . '.';
}
$whatsAppUrl = $whatsAppNumber !== ''
    ? 'https://wa.me/' . $whatsAppNumber . '?text=' . rawurlencode($whatsAppMessage)
    : null;

$hasEmail = ($contactEmail !== '' && filter_var($contactEmail, FILTER_VALIDATE_EMAIL))
    || ($helpEmail !== '' && filter_var($helpEmail, FILTER_VALIDATE_EMAIL));
$hasPhone = $contactPhoneHref !== '' && strlen(preg_replace('/\D+/', '', $contactPhoneHref)) >= 7;
$hasWhatsApp = $whatsAppNumber !== '';
?>
<section class="contact-hero">
    <div class="container contact-hero__inner">
        <span class="eyebrow">Contact us</span>
        <h1>Tell us what you need.<br><span>We'll help with the next step.</span></h1>
        <p>Have a question about a website, an online business service or help with a digital process? Use the form below or reach us directly. We'll guide you on what to do next.</p>
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

                <form class="enquiry-form" id="enquiryForm" method="post" action="/get-help">
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
                        <select id="enquiry-service" name="service">
                            <option value="">Choose a service or describe your need</option>
                            <option value="website-design">Website Design</option>
                            <option value="landing-page">Landing Page</option>
                            <option value="google-business">Google Business Profile</option>
                            <option value="kra-returns">KRA Returns Filing</option>
                            <option value="business-registration">Business Registration</option>
                            <option value="cv-writing">CV Writing</option>
                            <option value="other">Something else</option>
                        </select>
                        <?php if ($serviceSlug !== ''): ?>
                        <script nonce="<?= View::escape($cspNonce ?? '') ?>">
                        document.addEventListener('DOMContentLoaded', function() {
                            const select = document.getElementById('enquiry-service');
                            const slug = '<?= View::escape($serviceSlug) ?>';
                            if (slug.includes('website') || slug.includes('landing')) select.value = 'website-design';
                            else if (slug.includes('google')) select.value = 'google-business';
                            else if (slug.includes('kra')) select.value = 'kra-returns';
                            else if (slug.includes('registration')) select.value = 'business-registration';
                            else if (slug.includes('cv')) select.value = 'cv-writing';
                            else select.value = 'other';
                        });
                        </script>
                        <?php endif; ?>
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
                        <button type="submit" class="button button--whatsapp" id="whatsapp-submit">
                            <i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Send via WhatsApp
                        </button>
                        <?php if ($hasEmail): ?>
                        <button type="button" class="button button--secondary" id="email-submit">
                            <i class="fa-regular fa-envelope" aria-hidden="true"></i> Send via Email
                        </button>
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
    const whatsappSubmit = document.getElementById('whatsapp-submit');
    const emailSubmit = document.getElementById('email-submit');

    if (!form || !whatsappSubmit) return;

    // WhatsApp submission
    whatsappSubmit.addEventListener('click', function(e) {
        e.preventDefault();

        const name = form.name.value.trim();
        const email = form.email.value.trim();
        const phone = form.phone.value.trim();
        const service = form.service.value;
        const message = form.message.value.trim();

        if (!name || !email || !message) {
            alert('Please fill in your name, email, and message.');
            return;
        }

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Build WhatsApp message
        let whatsappMsg = 'Hi AlbaTech Solutions,\n\n';
        whatsappMsg += 'Name: ' + name + '\n';
        whatsappMsg += 'Email: ' + email + '\n';
        if (phone) whatsappMsg += 'Phone: ' + phone + '\n';
        if (service) whatsappMsg += 'Service interest: ' + service + '\n';
        whatsappMsg += '\nMessage:\n' + message;

        const whatsappNumber = '<?= $whatsAppNumber ?>';
        if (whatsappNumber) {
            const whatsappUrl = 'https://wa.me/' + whatsappNumber + '?text=' + encodeURIComponent(whatsappMsg);
            window.open(whatsappUrl, '_blank');
        } else {
            alert('WhatsApp contact is not configured. Please use email instead.');
        }
    });

    // Email submission
    if (emailSubmit) {
        emailSubmit.addEventListener('click', function(e) {
            e.preventDefault();

            const name = form.name.value.trim();
            const email = form.email.value.trim();
            const phone = form.phone.value.trim();
            const service = form.service.value;
            const message = form.message.value.trim();

            if (!name || !email || !message) {
                alert('Please fill in your name, email, and message.');
                return;
            }

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Build email
            let emailSubject = 'Enquiry from ' + name;
            if (service) emailSubject += ' - ' + service;

            let emailBody = 'Name: ' + name + '\n';
            emailBody += 'Email: ' + email + '\n';
            if (phone) emailBody += 'Phone: ' + phone + '\n';
            if (service) emailBody += 'Service interest: ' + service + '\n';
            emailBody += '\nMessage:\n' + message;

            const contactEmail = '<?= $contactEmail ?: $helpEmail ?>';
            if (contactEmail) {
                const mailtoUrl = 'mailto:' + contactEmail + '?subject=' + encodeURIComponent(emailSubject) + '&body=' + encodeURIComponent(emailBody);
                window.location.href = mailtoUrl;
            } else {
                alert('Email contact is not configured. Please use WhatsApp instead.');
            }
        });
    }
})();
</script>
