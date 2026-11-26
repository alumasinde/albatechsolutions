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
$contactServiceArea = trim((string) ($siteSettings['service_area'] ?? 'Nationwide, fully online'));
$helpEmail = trim((string) ($siteSettings['support_email'] ?? ''));
$mailFallback = trim((string) Env::get('MAIL_TO', ''));

$serviceSlug = trim((string) ($_GET['service'] ?? ''));
?>
<?php if (isset($breadcrumbs) && is_array($breadcrumbs) && !empty($breadcrumbs)): ?>
<nav class="breadcrumbs shell" aria-label="Breadcrumb">
    <ol>
        <?php foreach ($breadcrumbs as $index => $crumb): $isLast = $index === count($breadcrumbs) - 1; ?>
            <li>
                <?php if (!$isLast && !empty($crumb['url'])): ?><a href="<?= View::escape($crumb['url']) ?>"><?= View::escape($crumb['name']) ?></a><?php else: ?><span aria-current="page"><?= View::escape($crumb['name']) ?></span><?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
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

<section class="page-hero">
    <div class="shell py-12 md:py-16">
        <span class="kicker"><?= View::escape($cmsPage['title'] ?? 'Contact us') ?></span>
        <h1 class="max-w-3xl"><?= View::escape($cmsPage['title'] ?? 'Tell us what you need.') ?></h1>
        <p class="mt-4 max-w-2xl text-lg leading-relaxed"><?= View::escape($cmsPage['intro'] ?? 'Have a question about a website, an online business service or help with a digital process? Use the form below or reach us directly. We will guide you on what to do next.') ?></p>
    </div>
</section>

<section class="section">
    <div class="shell grid items-start gap-10 lg:grid-cols-[minmax(0,1.1fr)_minmax(280px,0.9fr)]">
        <div class="card p-6 sm:p-8">
            <span class="kicker">Send an enquiry</span>
            <h2>Quick enquiry form</h2>
            <p class="mt-2 text-sm">Fill in your details and continue in WhatsApp, or send the enquiry by email. A phone number is all we need to reply; email is optional.</p>

            <?php if (($_GET['sent'] ?? '') === '1'): ?><div class="alert alert-success mt-4" role="status">Your enquiry has been sent. We will reply on WhatsApp or by phone.</div><?php elseif (($_GET['sent'] ?? '') === '0'): ?><div class="alert alert-error mt-4" role="alert">We could not send your enquiry right now. Please try again later or use the direct contact details.</div><?php endif; ?>
            <form class="mt-5" id="enquiryForm" method="post" action="/get-help">
                <input type="hidden" name="_csrf" value="<?= View::escape($csrf ?? '') ?>">
                <div class="honeypot" aria-hidden="true"><label>Leave this field empty<input type="text" name="website_url" tabindex="-1" autocomplete="off"></label></div>
                <div class="field">
                    <label class="field-label" for="enquiry-name">Your name <span class="required">*</span></label>
                    <input class="input" type="text" id="enquiry-name" name="name" required placeholder="e.g., John Kamau" autocomplete="name">
                </div>

                <div class="field">
                    <label class="field-label" for="enquiry-phone">WhatsApp or phone number <span class="required">*</span></label>
                    <input class="input" type="tel" id="enquiry-phone" name="phone" required placeholder="e.g., 0712 345 678" autocomplete="tel">
                </div>

                <div class="field">
                    <label class="field-label" for="enquiry-email">Email address (optional)</label>
                    <input class="input" type="email" id="enquiry-email" name="email" placeholder="e.g., john@example.com" autocomplete="email">
                </div>

                <div class="field">
                    <label class="field-label" for="enquiry-service">What do you need help with?</label>
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
                    <select class="select" id="enquiry-service" name="service">
                        <option value="">Choose a service or describe your need</option>
                        <?php foreach ($serviceOptions as $optionKey => $optionLabel): ?><option value="<?= View::escape($optionKey) ?>"<?= $selectedService === (string) $optionKey ? ' selected' : '' ?>><?= View::escape($optionLabel) ?></option><?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label class="field-label" for="enquiry-message">Your message <span class="required">*</span></label>
                    <textarea class="textarea" id="enquiry-message" name="message" required rows="4" placeholder="Tell us what you're trying to achieve. Be as specific as you can."></textarea>
                </div>

                <label class="flex items-start gap-3 text-sm leading-relaxed">
                    <input type="checkbox" name="whatsapp_consent" value="1" class="mt-1 shrink-0">
                    <span><?= View::escape(\AlbaTech\Controllers\EnquiryController::WHATSAPP_CONSENT_TEXT) ?></span>
                </label>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <?php if ($hasWhatsApp): ?>
                        <button type="button" class="btn btn-whatsapp" id="whatsapp-submit"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Continue in WhatsApp</button>
                    <?php endif; ?>
                    <?php if ($hasEmail): ?>
                        <button type="submit" name="send_method" value="email" class="btn btn-secondary" id="email-submit"><i class="fa-regular fa-envelope" aria-hidden="true"></i> Send enquiry by email</button>
                    <?php endif; ?>
                    <?php if (!$hasWhatsApp && !$hasEmail): ?>
                        <p class="alert alert-error" role="alert">Contact delivery is not configured yet. Please try again later.</p>
                    <?php endif; ?>
                </div>

                <p class="mt-5 flex items-start gap-2 text-xs leading-relaxed text-muted"><i class="fa-solid fa-shield-halved mt-0.5 text-primary" aria-hidden="true"></i> For your safety, never send passwords, PINs, one-time verification codes or M-Pesa credentials in an enquiry.</p>
            </form>
        </div>

        <aside class="grid gap-4">
            <div>
                <span class="kicker">Direct contact</span>
                <h2>Other ways to reach us</h2>
            </div>

            <?php if ($hasWhatsApp): ?>
            <div class="card flex gap-4 p-5">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-success-bg text-success"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i></span>
                <div>
                    <h3 class="text-base">WhatsApp</h3>
                    <p class="mt-1 text-sm">Fastest response time</p>
                    <?php if ($contactHours !== ''): ?><p class="mt-1 text-xs text-subtle"><i class="fa-regular fa-clock mr-1" aria-hidden="true"></i><?= View::escape($contactHours) ?></p><?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($hasPhone): ?>
            <div class="card flex gap-4 p-5">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary"><i class="fa-solid fa-phone" aria-hidden="true"></i></span>
                <div>
                    <h3 class="text-base">Phone</h3>
                    <p class="mt-1 text-sm"><a class="font-semibold text-primary" href="tel:<?= View::escape($contactPhoneHref) ?>"><?= View::escape($contactPhone) ?></a></p>
                    <?php if ($contactHours !== ''): ?><p class="mt-1 text-xs text-subtle"><i class="fa-regular fa-clock mr-1" aria-hidden="true"></i><?= View::escape($contactHours) ?></p><?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($hasEmail): ?>
            <div class="card flex gap-4 p-5">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary"><i class="fa-regular fa-envelope" aria-hidden="true"></i></span>
                <div>
                    <h3 class="text-base">Email</h3>
                    <?php if ($contactEmail !== '' && filter_var($contactEmail, FILTER_VALIDATE_EMAIL)): ?><p class="mt-1 break-all text-sm"><a class="font-semibold text-primary" href="mailto:<?= View::escape($contactEmail) ?>"><?= View::escape($contactEmail) ?></a></p><?php endif; ?>
                    <?php if ($helpEmail !== '' && filter_var($helpEmail, FILTER_VALIDATE_EMAIL)): ?><p class="mt-1 break-all text-sm"><a class="font-semibold text-primary" href="mailto:<?= View::escape($helpEmail) ?>"><?= View::escape($helpEmail) ?></a></p><?php endif; ?>
                    <?php if ($contactResponseTime !== ''): ?><p class="mt-1 text-xs text-subtle"><i class="fa-regular fa-clock mr-1" aria-hidden="true"></i><?= View::escape($contactResponseTime) ?></p><?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="card flex gap-4 p-5">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary"><i class="fa-solid fa-location-dot" aria-hidden="true"></i></span>
                <div>
                    <h3 class="text-base">Location</h3>
                    <p class="mt-1 text-sm"><?= View::escape($contactCity !== '' ? $contactCity : $contactLocation) ?></p>
                    <?php if ($contactServiceArea !== ''): ?><p class="mt-1 text-xs text-subtle"><i class="fa-solid fa-globe mr-1" aria-hidden="true"></i><?= View::escape($contactServiceArea) ?></p><?php endif; ?>
                </div>
            </div>

            <div class="notice notice-warn">
                <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                <p><strong>Important</strong> AlbaTech Solutions is an independent service provider, not a government agency. For government-related processes, decisions remain with the relevant authority.</p>
            </div>
        </aside>
    </div>
</section>

<?php if (!empty($faqItems)): ?>
<section class="section section-tint">
    <div class="shell">
        <div class="mb-8 max-w-2xl"><span class="kicker">Common questions</span><h2>Before you send an enquiry</h2></div>
        <div class="faq-list">
            <?php foreach ($faqItems as $item): ?>
                <details class="faq">
                    <summary><?= View::escape($item['question']) ?></summary>
                    <p><?= View::escape($item['answer']) ?></p>
                </details>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

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
        const serviceSelect = form.querySelector('#enquiry-service');
        const service = String(data.get('service') || '').trim();
        const serviceLabel = service !== '' && serviceSelect && serviceSelect.selectedOptions[0]
            ? serviceSelect.selectedOptions[0].textContent.trim()
            : '';
        const message = String(data.get('message') || '').trim();
        const number = '<?= View::escape($whatsAppNumber) ?>';
        if (!/^\d{8,15}$/.test(number) || number.startsWith('0')) {
            window.location.href = '/get-help';
            return;
        }
        let body = 'Hi AlbaTech Solutions,\n\nName: ' + name + '\nPhone: ' + phone;
        if (email) body += '\nEmail: ' + email;
        if (serviceLabel) body += '\nService interest: ' + serviceLabel;
        body += '\n\nMessage:\n' + message;
        const url = 'https://wa.me/' + number + '?text=' + encodeURIComponent(body);
        const opened = window.open(url, '_blank');
        if (opened) opened.opener = null;
        else window.location.href = url;
    });
})();
</script>
