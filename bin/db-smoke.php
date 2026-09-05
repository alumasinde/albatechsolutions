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

$retiredTables = ['quote_requests','projects','assistant_sessions','assistant_messages','assistant_service_matches','growth_page_views','growth_events','growth_content_notes'];

$tests = [
    'Published services' => static fn () => (new ServiceRepository())->allPublished(),
    'Assistance requests' => static fn () => (new AssistanceRequestRepository())->paginate(1, 1),
    'Retired tables absent' => static function () use ($retiredTables): array {
        $pdo = \App\Core\Database::connection();
        $placeholders = implode(',', array_fill(0, count($retiredTables), '?'));
        $stmt = $pdo->prepare("SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name IN ({$placeholders})");
        $stmt->execute($retiredTables);
        $found = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        if ($found !== []) throw new RuntimeException('Retired tables still present: ' . implode(', ', $found));
        return [];
    },
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
