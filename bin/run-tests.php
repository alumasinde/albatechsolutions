<?php

declare(strict_types=1);

/**
 * Runs every regression check in tests/ and reports a summary.
 * Usage: php bin/run-tests.php   (or: composer test)
 */

$root = dirname(__DIR__);
$php = PHP_BINARY;
$failures = [];
$passed = 0;

foreach (glob($root . '/tests/*.php') ?: [] as $test) {
    exec(escapeshellarg($php) . ' ' . escapeshellarg($test) . ' 2>&1', $output, $exitCode);
    $summary = trim((string) (end($output) ?: ''));
    if ($exitCode === 0) {
        $passed++;
        echo "PASS  " . basename($test) . ($summary !== '' ? "  — {$summary}" : '') . "\n";
    } else {
        $failures[] = basename($test);
        echo "FAIL  " . basename($test) . "\n";
        foreach ($output as $line) {
            if (str_contains(strtolower($line), 'fail') || str_contains(strtolower($line), 'error')) echo "      {$line}\n";
        }
    }
    $output = [];
}

$total = $passed + count($failures);
echo "\n{$passed}/{$total} test files passed.\n";
if ($failures !== []) {
    echo "Failed: " . implode(', ', $failures) . "\n";
    exit(1);
}
exit(0);
