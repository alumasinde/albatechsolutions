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
        <div class="form-grid"><label class="field full">Website / business name<input name="site_name" required value="<?= $get('site_name', 'AlbaTech Solutions') ?>"></label><label class="field full">Website URL<input type="url" name="site_url" value="<?= $get('site_url') ?>" placeholder="https://example.com"></label><label class="field">Contact email<input type="email" name="contact_email" value="<?= $get('contact_email') ?>" placeholder="hello@example.com"></label><label class="field">Business phone<input name="business_phone" value="<?= $get('business_phone') ?>" placeholder="+254..."></label><label class="field full">WhatsApp number<input name="whatsapp_number" value="<?= $get('whatsapp_number') ?>" placeholder="2547XXXXXXXX"><small>Use international format, for example 254712345678. The number is stored as digits only and must include the country code. Leave blank to hide WhatsApp actions.</small></label><label class="field full">Business location<input name="business_address" value="<?= $get('business_address', 'Nairobi, Kenya') ?>"></label><label class="field">City / location label<input name="business_city" value="<?= $get('business_city', 'Nairobi, Kenya') ?>"></label><label class="field">Service area<input name="service_area" value="<?= $get('service_area', 'Nationwide — fully online') ?>"></label><label class="field">Contact hours<input name="contact_hours" value="<?= $get('contact_hours', 'Monday–Friday, 8:00 am–5:00 pm') ?>"></label><label class="field">Expected response time<input name="contact_response_time" value="<?= $get('contact_response_time', 'Within one business day') ?>"></label><label class="field">Support email<input type="email" name="support_email" value="<?= $get('support_email') ?>" placeholder="support@example.com"></label><label class="field full">WhatsApp pre-filled message<textarea name="whatsapp_prefill_message" rows="3"><?= $get('whatsapp_prefill_message', 'Hi AlbaTech Solutions, I would like to make an enquiry. Please guide me on the next steps.') ?></textarea><small>Appears in the visitor's WhatsApp compose window; the visitor can edit it before sending.</small></label></div>
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
        <div class="section-heading"><span><i class="fa-solid fa-file-lines"></i></span>
            <div>
                <h3>Legal page content</h3>
                <p>Update the Terms and Privacy page copy here. Plain text is displayed as paragraphs; keep headings on separate lines.</p>
            </div>
        </div>
        <div class="form-grid"><label class="field full">Terms of Service content<textarea name="terms_content" rows="10"><?= $get('terms_content') ?></textarea></label><label class="field full">Privacy Policy content<textarea name="privacy_content" rows="10"><?= $get('privacy_content') ?></textarea></label></div>
    </section>
    <section class="panel editor-section">
        <div class="section-heading"><span><i class="fa-solid fa-magnifying-glass"></i></span>
            <div>
                <h3>Default SEO</h3>
                <p>Used where a page does not provide its own metadata.</p>
            </div>
        </div>
        <div class="form-grid"><label class="field full">Default author metadata<input name="default_author" value="<?= $get('default_author', 'Albert Masinde') ?>"><small>This is metadata, not public page copy.</small></label><label class="field full">Default meta description<textarea name="default_meta_description" rows="3" maxlength="320"><?= $get('default_meta_description') ?></textarea></label><label class="field full">Default meta keywords<textarea name="default_meta_keywords" rows="2"><?= $get('default_meta_keywords') ?></textarea><small>Search engines may not use meta keywords. Write page content for people first.</small></label></div>
    </section>
    <div class="settings-save"><span><i class="fa-solid fa-circle-info"></i> Changes save directly to the website database.</span><button class="btn btn-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save settings</button></div>
</form>