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
];

foreach ($required as $needle) {
    if (!str_contains($css, $needle)) {
        fwrite(STDERR, "[FAIL] Missing v5 foundation selector: {$needle}\n");
        exit(1);
    }
}

echo "[PASS] CSS build and foundation smoke test\n";
