<?php

declare(strict_types=1);

/**
 * AlbaTech database smoke tests.
 *
 * Runs read-only queries against the active assistance and service foundation.
 *
 * Usage:
 *   php bin/db-smoke.php
 */

$root = dirname(__DIR__);
require $root . '/app/bootstrap.php';

use App\Modules\Cms\Repository\ServiceRepository;
use App\Modules\Assistance\Repository\AssistanceRequestRepository;

$tests = [
    'Published services' => static fn () => (new ServiceRepository())->allPublished(),
    'Assistance requests' => static fn () => (new AssistanceRequestRepository())->paginate(1, 1),
];

$failures = 0;
echo "AlbaTech DB smoke tests\n=======================\n";
foreach ($tests as $label => $test) {
    try {
        $result = $test();
        if (!is_array($result)) throw new RuntimeException('Expected array result.');
        echo "[PASS] {$label}\n";
    } catch (Throwable $e) {
        $failures++;
        echo "[FAIL] {$label}\n       " . $e::class . ": " . $e->getMessage() . "\n";
    }
}
exit($failures === 0 ? 0 : 1);
