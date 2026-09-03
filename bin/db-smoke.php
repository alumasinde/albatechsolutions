<?php

declare(strict_types=1);

/**
 * AlbaTech database smoke tests.
 *
 * Runs read-only repository queries that are safe for a local/test database.
 * Add new repository read paths here as the application grows.
 *
 * Usage:
 *   php bin/db-smoke.php
 */

$root = dirname(__DIR__);
require $root . '/app/bootstrap.php';

use App\Modules\Growth\Repository\GrowthAnalyticsRepository;

$failures = 0;

$tests = [
    'Growth summary' => static fn () => (new GrowthAnalyticsRepository())->summary(30),
    'Growth top pages' => static fn () => (new GrowthAnalyticsRepository())->topPages(30, 5),
    'Growth sources' => static fn () => (new GrowthAnalyticsRepository())->sources(30, 5),
    'Growth event counts' => static fn () => (new GrowthAnalyticsRepository())->eventCounts(30),
    'Growth service performance' => static fn () => (new GrowthAnalyticsRepository())->servicePerformance(30, 5),
    'Growth assistant insights' => static fn () => (new GrowthAnalyticsRepository())->assistantIntentInsights(30, 5),
    'Growth content gaps' => static fn () => (new GrowthAnalyticsRepository())->contentGaps(30, 5),
    'Growth open notes' => static fn () => (new GrowthAnalyticsRepository())->openNotes(5),
];

echo "AlbaTech DB smoke tests\n";
echo "=======================\n";

foreach ($tests as $label => $test) {
    try {
        $result = $test();
        if (!is_array($result)) {
            throw new RuntimeException('Expected array result.');
        }
        echo "[PASS] {$label}\n";
    } catch (Throwable $e) {
        $failures++;
        echo "[FAIL] {$label}\n";
        echo "       " . $e::class . ": " . $e->getMessage() . "\n";
    }
}

echo "\n";
echo $failures === 0
    ? "DB SMOKE RESULT: PASS\n"
    : "DB SMOKE RESULT: FAIL ({$failures} test(s))\n";

exit($failures === 0 ? 0 : 1);
