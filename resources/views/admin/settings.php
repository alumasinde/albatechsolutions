<?php

use AlbaTech\Core\View;

$get = static fn(string $key, string $fallback = '') => View::escape($settings[$key]['setting_value'] ?? $fallback); 

/**
 * @var array<string, array{setting_value: string}> $settings
 * @var string $csrf
 */
?>
<div class="welcome-row">
    <div>
        <h2>Website configuration</h2>
        <p>Update public contact details and default search metadata without changing PHP files.</p>
    </div>
</div>
<form method="post" action="/admin/settings" class="settings-layout"><input type="hidden" name="_csrf" value="<?= View::escape($csrf) ?>">
    <section class="panel editor-section">
        <div class="section-heading"><span><i class="fa-solid fa-building"></i></span>
            <div>
                <h3>Business details</h3>
                <p>Basic information used around your website.</p>
            </div>
        </div>
        <div class="form-grid"><label class="field full">Website / business name<input name="site_name" required value="<?= $get('site_name', 'AlbaTech Solutions') ?>"></label><label class="field full">Website URL<input type="url" name="site_url" value="<?= $get('site_url') ?>" placeholder="https://example.com"></label><label class="field">Contact email<input type="email" name="contact_email" value="<?= $get('contact_email') ?>" placeholder="hello@example.com"></label><label class="field">Business phone<input name="business_phone" value="<?= $get('business_phone') ?>" placeholder="+254..."></label><label class="field full">WhatsApp number<input name="whatsapp_number" value="<?= $get('whatsapp_number') ?>" placeholder="2547XXXXXXXX"><small>Use international format, for example 254712345678. The number is stored as digits only and must include the country code. Leave blank to hide WhatsApp actions.</small></label><label class="field full">Business location<input name="business_address" value="<?= $get('business_address', 'Nairobi, Kenya') ?>"></label><label class="field">City / location label<input name="business_city" value="<?= $get('business_city', 'Nairobi, Kenya') ?>"></label><label class="field">Service area<input name="service_area" value="<?= $get('service_area', 'Nationwide — fully online') ?>"></label><label class="field">Business latitude<input name="business_latitude" inputmode="decimal" value="<?= $get('business_latitude') ?>" placeholder="-1.286389"><small>Optional. Adds your location to structured data for local search.</small></label><label class="field">Business longitude<input name="business_longitude" inputmode="decimal" value="<?= $get('business_longitude') ?>" placeholder="36.817223"><small>Enter both coordinates or leave both blank.</small></label><label class="field">Contact hours<input name="contact_hours" value="<?= $get('contact_hours', 'Monday–Friday, 8:00 am–5:00 pm') ?>"></label><label class="field">Expected response time<input name="contact_response_time" value="<?= $get('contact_response_time', 'Within one business day') ?>"></label><label class="field">Support email<input type="email" name="support_email" value="<?= $get('support_email') ?>" placeholder="support@example.com"></label><label class="field full">WhatsApp pre-filled message<textarea name="whatsapp_prefill_message" rows="3"><?= $get('whatsapp_prefill_message', 'Hi AlbaTech Solutions, I would like to make an enquiry. Please guide me on the next steps.') ?></textarea><small>Appears in the visitor's WhatsApp compose window; the visitor can edit it before sending.</small></label></div>
    </section>
    <section class="panel editor-section">
        <div class="section-heading"><span><i class="fa-solid fa-envelope-circle-check"></i></span>
            <div>
                <h3>Email delivery</h3>
                <p>SMTP credentials are configured in the server's <code>.env</code> file, never in the database or browser. Set MAIL_HOST, MAIL_PORT, MAIL_ENCRYPTION, MAIL_USERNAME, MAIL_PASSWORD, MAIL_FROM_ADDRESS and MAIL_FROM_NAME. The enquiry recipient is the Contact email or Support email configured above.</p>
            </div>
        </div>
    </section>
    <section class="panel editor-section">
        <div class="section-heading"><span><i class="fa-solid fa-magnifying-glass"></i></span>
            <div>
                <h3>Default SEO</h3>
                <p>Used where a page does not provide its own metadata.</p>
            </div>
        </div>
        <div class="form-grid"><label class="field full">Default SEO title<input name="default_meta_title" maxlength="255" value="<?= $get('default_meta_title', 'AlbaTech Solutions | Practical Digital Services in Kenya') ?>"><small>Fallback title for pages that do not define their own title.</small></label><label class="field full">Default author metadata<input name="default_author" value="<?= $get('default_author', 'Albert Masinde') ?>"><small>This is metadata, not public page copy.</small></label><label class="field full">Default social-sharing image URL<input type="text" name="default_social_image" maxlength="500" value="<?= $get('default_social_image', '/assets/images/albatech-social-card.jpg') ?>"><small>Use a public HTTPS image URL or a site-relative path. Recommended dimensions: 1200 × 630.</small></label><label class="field full">Default meta description<textarea name="default_meta_description" rows="3" maxlength="320"><?= $get('default_meta_description') ?></textarea></label></div>
    </section>
    <section class="panel editor-section">
        <div class="section-heading"><span><i class="fa-solid fa-user-tie"></i></span>
            <div>
                <h3>Founder &amp; authorship</h3>
                <p>Shown on the About page and used as the author in guide bylines and structured data.</p>
            </div>
        </div>
        <div class="form-grid"><label class="field">Founder name<input name="founder_name" maxlength="190" value="<?= $get('founder_name', 'Albert Masinde') ?>"></label><label class="field">Role / title<input name="founder_title" maxlength="190" value="<?= $get('founder_title', 'Founder, AlbaTech Solutions') ?>"></label><label class="field full">Short bio<textarea name="founder_bio" rows="4" maxlength="2000"><?= $get('founder_bio') ?></textarea><small>Keep this factual. It appears publicly on the About page.</small></label><label class="field full">LinkedIn profile<input type="url" name="founder_linkedin" maxlength="500" value="<?= $get('founder_linkedin') ?>" placeholder="https://www.linkedin.com/in/yourprofile"><small>Optional. Must start with https://.</small></label></div>
    </section>
    <section class="panel editor-section">
        <div class="section-heading"><span><i class="fa-solid fa-share-nodes"></i></span>
            <div>
                <h3>Social profiles</h3>
                <p>Full https:// profile URLs are added to your structured data (sameAs). Leave a field blank if you do not have that profile.</p>
            </div>
        </div>
        <div class="form-grid"><label class="field">Facebook<input type="url" name="social_facebook" maxlength="500" value="<?= $get('social_facebook') ?>" placeholder="https://facebook.com/yourbusiness"></label><label class="field">X / Twitter<input type="url" name="social_twitter" maxlength="500" value="<?= $get('social_twitter') ?>" placeholder="https://x.com/yourbusiness"></label><label class="field">LinkedIn<input type="url" name="social_linkedin" maxlength="500" value="<?= $get('social_linkedin') ?>" placeholder="https://linkedin.com/company/yourbusiness"></label><label class="field">Instagram<input type="url" name="social_instagram" maxlength="500" value="<?= $get('social_instagram') ?>" placeholder="https://instagram.com/yourbusiness"></label></div>
    </section>
    <section class="panel editor-section">
        <div class="section-heading"><span><i class="fa-solid fa-chart-line"></i></span>
            <div>
                <h3>Analytics &amp; footer</h3>
                <p>Optional traffic tracking plus the short paragraph shown under the footer logo.</p>
            </div>
        </div>
        <div class="form-grid"><label class="field">Analytics<select name="analytics_enabled"><option value="0"<?= ($get('analytics_enabled', '0') === '1') ? '' : ' selected'; ?>>Disabled</option><option value="1"<?= ($get('analytics_enabled', '0') === '1') ? ' selected' : ''; ?>>Enabled</option></select></label><label class="field">Provider<select name="analytics_provider"><option value=""<?= $get('analytics_provider') === '' ? ' selected' : ''; ?>>Choose a provider</option><option value="plausible"<?= $get('analytics_provider') === 'plausible' ? ' selected' : ''; ?>>Plausible</option><option value="ga4"<?= $get('analytics_provider') === 'ga4' ? ' selected' : ''; ?>>Google Analytics 4</option></select></label><label class="field">Domain (Plausible) or Measurement ID (GA4)<input name="analytics_domain" maxlength="255" value="<?= $get('analytics_domain') ?>" placeholder="albatechsolutions.co.ke or G-XXXXXXXXXX"></label><label class="field">Script URL<input type="url" name="analytics_script_url" maxlength="500" value="<?= $get('analytics_script_url') ?>" placeholder="https://plausible.io/js/script.js"><small>Must start with https://. CSP only allows plausible.io and googletagmanager.com.</small></label><label class="field full">Footer description<textarea name="footer_description" rows="3" maxlength="500"><?= $get('footer_description', 'Practical website, online presence and digital assistance for people and businesses in Kenya. Clear scope, straightforward communication and realistic next steps.') ?></textarea></label></div>
    </section>
    <div class="settings-save"><span><i class="fa-solid fa-circle-info"></i> Changes save directly to the website database.</span><button class="btn btn-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save settings</button></div>
</form>