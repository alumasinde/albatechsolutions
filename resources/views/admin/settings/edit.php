<?php
$fields = [
    'Branding' => [
        'site_name' => 'Site Name',
        'site_tagline' => 'Tagline',
    ],
    'Theme' => [
        'theme_color_primary' => 'Primary Color',
        'theme_color_secondary' => 'Secondary Color',
        'theme_color_accent' => 'Accent Color',
        'theme_color_background' => 'Background Color',
        'theme_font_family' => 'Font Family',
        'theme_radius' => 'Corner Radius',
    ],
    'Contact' => [
        'contact_email' => 'Contact Email',
        'contact_phone' => 'Contact Phone',
        'contact_address' => 'Address',
        'whatsapp_number' => 'WhatsApp Number (e.g. 254712345678, no + or spaces)',
    ],
    'Social' => [
        'social_facebook' => 'Facebook URL',
        'social_twitter' => 'Twitter/X URL',
        'social_linkedin' => 'LinkedIn URL',
        'social_instagram' => 'Instagram URL',
    ],
    'SEO Defaults' => [
        'seo_default_title' => 'Default Meta Title',
        'seo_default_description' => 'Default Meta Description',
        'seo_default_keywords' => 'Default Keywords',
    ],
    'Homepage' => [
        'homepage_eyebrow' => 'Hero Eyebrow',
        'homepage_hero_title' => 'Hero Headline',
        'homepage_hero_subtitle' => 'Hero Supporting Text',
        'homepage_primary_cta_label' => 'Primary CTA Label',
        'homepage_primary_cta_url' => 'Primary CTA URL',
        'homepage_secondary_cta_label' => 'Secondary CTA Label',
        'homepage_secondary_cta_url' => 'Secondary CTA URL',
        'homepage_services_heading' => 'Services Section Heading',
        'homepage_industries_heading' => 'Industries Section Heading',
        'homepage_process_heading' => 'Process Section Heading',
    ],
    'Search Visibility' => [
        'analytics_google_site_verification' => 'Google Search Console Verification (optional)',
    ],
];

ob_start();
?>
<h1><i class="fa-solid fa-palette"></i> Site Settings</h1>

<form method="POST" enctype="multipart/form-data" class="card">
    <?= csrf_field() ?>

    <div class="form-section">
        <h2>Logo</h2>
        <?php if (!empty($settings['site_logo_path'])): ?>
            <img src="<?= e(url($settings['site_logo_path'])) ?>" alt="Current logo" style="height:48px;margin-bottom:12px;">
        <?php endif; ?>
        <input type="file" name="logo" accept="image/*">
    </div>

    <?php foreach ($fields as $section => $keys): ?>
        <div class="form-section">
            <h2><?= e($section) ?></h2>
            <div class="form-grid">
                <?php foreach ($keys as $key => $label): ?>
                    <div>
                        <label for="<?= e($key) ?>"><?= e($label) ?></label>
                        <input type="text" id="<?= e($key) ?>" name="<?= e($key) ?>" value="<?= e($settings[$key] ?? '') ?>">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>


    <button type="submit" class="btn btn-primary">Save Settings</button>
</form>
<?php
$adminContent = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
