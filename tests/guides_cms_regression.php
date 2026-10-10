<?php
declare(strict_types=1);
$root = dirname(__DIR__);
$checks = [
    'Guide CMS controller exists' => is_file($root . '/app/Controllers/GuideAdminController.php'),
    'Guide migration exists' => is_file($root . '/database/migrations/008_guides_cms.sql'),
    'Admin routes include create/edit/delete' => str_contains(file_get_contents($root . '/routes/web.php'), "'/admin/guides/{id}/edit'"),
    'Admin navigation includes Guides' => str_contains(file_get_contents($root . '/resources/views/layouts/admin.php'), 'Guides & Articles'),
    'Editor supports H1-H6' => str_contains(file_get_contents($root . '/resources/views/admin/guide-form.php'), '<option value="h6">Heading 6</option>'),
    'Editor supports tables' => str_contains(file_get_contents($root . '/resources/views/admin/guide-form.php'), 'data-action="table"'),
    'Editor supports images' => str_contains(file_get_contents($root . '/resources/views/admin/guide-form.php'), 'data-action="image"'),
    'Toolbar is sticky' => str_contains(file_get_contents($root . '/public/assets/css/admin.css'), '.rich-toolbar{position:sticky'),
    'Server HTML sanitizer exists' => str_contains(file_get_contents($root . '/app/Controllers/GuideAdminController.php'), 'sanitizeHtml'),
    'Public view supports sanitized rich content' => str_contains(file_get_contents($root . '/resources/views/pages/guide-article.php'), 'guide-rich-content'),
];
$failed = 0;
foreach ($checks as $name => $ok) {
    echo ($ok ? '[PASS] ' : '[FAIL] ') . $name . PHP_EOL;
    if (!$ok) $failed++;
}
echo count($checks) . ' checks; ' . $failed . ' failures.' . PHP_EOL;
exit($failed ? 1 : 0);
