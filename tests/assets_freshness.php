<?php
declare(strict_types=1);
$root = dirname(__DIR__);

exec(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($root . '/bin/build-css.php') . ' --check 2>&1', $output, $exitCode);
if ($exitCode !== 0) {
    fwrite(STDERR, "FAIL: CSS bundle check failed: " . implode("\n", $output) . "\n");
    fwrite(STDERR, "Fix with: npm run build:tailwind\n");
    exit(1);
}

$bundle = $root . '/public/assets/css/bundle.css';
if (!is_file($bundle) || filesize($bundle) === 0) {
    fwrite(STDERR, "FAIL: bundle.css is missing or empty.\n");
    exit(1);
}

echo "PASS: CSS bundle freshness checks.\n";
