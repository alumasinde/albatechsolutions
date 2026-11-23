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

<section class="border-b border-border bg-surface">
    <div class="shell py-12 md:py-16">
        <span class="kicker">Contact &amp; location</span>
        <h1 class="max-w-3xl">How can we help you?</h1>
        <p class="mt-4 max-w-2xl text-lg leading-relaxed">Choose the best way to reach AlbaTech Solutions. Tell us what you need and we'll help you work out the next step.</p>
    </div>
</section>

<section class="section">
    <div class="shell">
        <?php
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
            <div class="alert alert-error mb-6" role="status"><i class="fa-solid fa-circle-info mr-2" aria-hidden="true"></i>Our direct contact details are being added to this page. Please check back shortly, or browse our <a class="underline" href="/services">services</a> and <a class="underline" href="/guides">guides</a> in the meantime.</div>
        <?php endif; ?>
        <?php if (count($contactTabs) >= 3): ?>
            <div class="mb-8 flex flex-wrap gap-2" role="group" aria-label="Filter contact methods">
                <button class="contact-filter__button is-active" type="button" data-contact-filter="all" aria-pressed="true">All</button>
                <?php foreach ($contactTabs as $tabKey => $tabLabel): ?>
                    <button class="contact-filter__button" type="button" data-contact-filter="<?= View::escape($tabKey) ?>" aria-pressed="false"><?= View::escape($tabLabel) ?></button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <?php if ($hasWhatsApp): ?>
            <article class="card p-5" data-contact-card="whatsapp">
                <span class="text-xs font-extrabold uppercase tracking-[0.12em] text-primary">Fastest response</span>
                <h2 class="mt-2 text-lg">WhatsApp support</h2>
                <?php if ($contactHours !== ''): ?><p class="mt-3 text-sm"><strong class="text-ink">Availability:</strong><br><?= View::escape($contactHours) ?></p><?php endif; ?>
                <a class="btn btn-whatsapp mt-4 w-full" href="<?= View::escape($contactWhatsAppUrl) ?>" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Chat on WhatsApp</a>
            </article>
            <?php endif; ?>
            <?php if ($hasPhone): ?>
            <article class="card p-5" data-contact-card="phone">
                <span class="text-xs font-extrabold uppercase tracking-[0.12em] text-primary">Call us</span>
                <h2 class="mt-2 text-lg">Phone</h2>
                <div class="mt-3 grid gap-3 text-sm">
                    <p class="m-0"><strong class="text-ink">Number:</strong><br><a class="font-semibold text-primary" href="tel:<?= View::escape($contactPhoneHref) ?>"><?= View::escape($contactPhone) ?></a></p>
                    <?php if ($contactHours !== ''): ?><p class="m-0"><strong class="text-ink">Availability:</strong><br><?= View::escape($contactHours) ?></p><?php endif; ?>
                </div>
            </article>
            <?php endif; ?>
            <?php if ($hasEmail): ?>
            <article class="card p-5" data-contact-card="email">
                <span class="text-xs font-extrabold uppercase tracking-[0.12em] text-primary">Email us</span>
                <h2 class="mt-2 text-lg">Email support</h2>
                <div class="mt-3 grid gap-3 text-sm">
                    <?php if ($contactEmail !== '' && filter_var($contactEmail, FILTER_VALIDATE_EMAIL)): ?><p class="m-0"><strong class="text-ink">General enquiries:</strong><br><a class="break-all font-semibold text-primary" href="mailto:<?= View::escape($contactEmail) ?>"><?= View::escape($contactEmail) ?></a></p><?php endif; ?>
                    <?php if ($helpEmail !== '' && filter_var($helpEmail, FILTER_VALIDATE_EMAIL)): ?><p class="m-0"><strong class="text-ink">Help &amp; support:</strong><br><a class="break-all font-semibold text-primary" href="mailto:<?= View::escape($helpEmail) ?>"><?= View::escape($helpEmail) ?></a></p><?php endif; ?>
                    <?php if ($contactResponseTime !== ''): ?><p class="m-0"><strong class="text-ink">Response time:</strong><br><?= View::escape($contactResponseTime) ?></p><?php endif; ?>
                </div>
            </article>
            <?php endif; ?>
            <article class="card p-5" data-contact-card="location">
                <span class="text-xs font-extrabold uppercase tracking-[0.12em] text-primary">Find us</span>
                <h2 class="mt-2 text-lg">Location</h2>
                <div class="mt-3 grid gap-3 text-sm">
                    <p class="m-0"><strong class="text-ink">City:</strong><br><?= View::escape($contactCity !== '' ? $contactCity : $contactLocation) ?></p>
                    <?php if ($contactServiceArea !== ''): ?><p class="m-0"><strong class="text-ink">Service area:</strong><br><?= View::escape($contactServiceArea) ?></p><?php endif; ?>
                    <?php if ($contactHours !== ''): ?><p class="m-0"><strong class="text-ink">Availability:</strong><br><?= View::escape($contactHours) ?></p><?php endif; ?>
                    <p class="m-0 text-xs text-subtle">Online and remote support is available. Please arrange an appointment before visiting any physical location.</p>
                </div>
            </article>
        </div>
        <div class="notice mt-8"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><p>For your safety, never send passwords, PINs, one-time verification codes or M-Pesa credentials in an enquiry. AlbaTech Solutions is an independent service provider, not a government agency.</p></div>
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
