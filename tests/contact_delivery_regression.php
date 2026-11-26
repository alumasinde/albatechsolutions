<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$checks = [
    'POST enquiry route exists' => str_contains(file_get_contents($root . '/routes/web.php'), "post('/get-help', [new EnquiryController(), 'submit'])"),
    'SMTP transport uses verified TLS peer checks' => str_contains(file_get_contents($root . '/app/Core/SmtpMailer.php'), "'verify_peer' => true") && str_contains(file_get_contents($root . '/app/Core/SmtpMailer.php'), "'verify_peer_name' => true"),
    'SMTP production disallows plaintext' => str_contains(file_get_contents($root . '/app/Core/SmtpMailer.php'), "Env::get('APP_ENV', 'local') === 'production'"),
    'Enquiry form checks CSRF' => str_contains(file_get_contents($root . '/app/Controllers/EnquiryController.php'), 'AdminSecurity::verifyCsrf'),
    'Enquiry form has rate limiting' => str_contains(file_get_contents($root . '/app/Controllers/EnquiryController.php'), "RateLimiter::key('public-enquiry'"),
    'Enquiry form validates recipient and sender' => str_contains(file_get_contents($root . '/app/Controllers/EnquiryController.php'), 'FILTER_VALIDATE_EMAIL'),
    'WhatsApp destination comes from admin setting' => str_contains(file_get_contents($root . '/resources/views/pages/get-help.php'), "\$siteSettings['whatsapp_number']") && !str_contains(file_get_contents($root . '/resources/views/pages/get-help.php'), 'WHATSAPP_NUMBER'),
    'Mail secrets documented in env example' => str_contains(file_get_contents($root . '/.env.example'), 'MAIL_HOST=') && str_contains(file_get_contents($root . '/.env.example'), 'MAIL_PASSWORD='),
    'Contact form includes honeypot' => str_contains(file_get_contents($root . '/resources/views/pages/get-help.php'), 'name="website_url"'),
    'Mail failure is not shown as raw SMTP detail' => str_contains(file_get_contents($root . '/app/Controllers/EnquiryController.php'), "error_log('[AlbaTech enquiry email] "),
];
$layout = file_get_contents($root . '/resources/views/layouts/main.php');
$checks['Floating WhatsApp icon uses centralized URL builder'] = str_contains($layout, 'class="whatsapp-float" href="<?= View::escape($whatsAppUrl) ?>"') && str_contains($layout, 'WhatsAppLink::build($siteSettings, ') && str_contains(file_get_contents($root . '/app/Core/WhatsAppLink.php'), 'https://wa.me/') && !str_contains($layout, 'data-whatsapp-panel') && !str_contains($layout, 'data-whatsapp-open');

$failed = 0;
foreach ($checks as $label => $passed) {
    echo ($passed ? 'PASS: ' : 'FAIL: ') . $label . PHP_EOL;
    if (!$passed) $failed++;
}
exit($failed === 0 ? 0 : 1);
