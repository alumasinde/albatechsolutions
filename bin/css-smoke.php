<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$build = $root . '/bin/build-css.php';
$output = $root . '/public_html/assets/css/v5/production.css';

passthru(PHP_BINARY . ' ' . escapeshellarg($build), $exitCode);
if ($exitCode !== 0) {
    exit($exitCode);
}

$css = file_get_contents($output);
if ($css === false || trim($css) === '') {
    fwrite(STDERR, "[FAIL] Generated stylesheet is empty.\n");
    exit(1);
}

$required = [
    ':root',
    '.public-page .btn-primary',
    '.admin-page .btn-primary',
    '.public-container',
    'legacy.css',
];

foreach ($required as $needle) {
    if (!str_contains($css, $needle)) {
        fwrite(STDERR, "[FAIL] Missing v5 foundation selector: {$needle}\n");
        exit(1);
    }
}

if (str_contains($css, '../v4/') || str_contains($css, 'assets/css/v4/') || str_contains($css, '@import url("../v4/')) {
    fwrite(STDERR, "[FAIL] Generated v5 stylesheet still depends on retired CSS.\n");
    exit(1);
}

echo "[PASS] CSS build and standalone v5 foundation smoke test\n";
