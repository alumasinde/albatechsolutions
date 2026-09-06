<?php
$title = 'Privacy Policy | ' . setting('site_name', 'AlbaTech Solutions');
$metaDescription = 'How AlbaTech Solutions handles information shared through our website and assistance requests.';
$canonicalUrl = rtrim(config('app.url'), '/') . '/privacy-policy';
ob_start();
?>
<section class="public-section">
    <div class="public-container public-content-page">
        <span class="public-kicker">Privacy</span>
        <h1>Your information and privacy</h1>
        <p class="conversion-lead">We only ask for information that helps us understand your request and provide the service you ask for.</p>

        <h2>Information you share with us</h2>
        <p>This may include your name, phone number, email address, details about the task you need help with and documents you choose to provide.</p>

        <h2>How we use it</h2>
        <p>We use your information to respond to your request, provide assistance, communicate about the work and keep records where reasonably necessary.</p>

        <h2>Keep your account details safe</h2>
        <p>Do not send us passwords, PINs or one-time passwords (OTPs). Where a digital service requires your personal account access, we will explain the next step without asking you to share sensitive login secrets.</p>

        <h2>Independent assistance</h2>
        <p><?= e(setting('government_services_disclaimer', 'AlbaTech Solutions provides independent assistance and is not a government agency.')) ?></p>

        <h2>Questions about your information</h2>
        <p>If you have a question about information you have shared with us, <a href="/contact">contact AlbaTech Solutions</a> and tell us what you need help with.</p>

        <p class="public-content-page__updated">Last updated: <?= date('F Y') ?></p>
    </div>
</section>
<?php
$pageContent = ob_get_clean();
require __DIR__ . '/layout.php';
