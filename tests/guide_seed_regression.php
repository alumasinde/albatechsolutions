<?php
declare(strict_types=1);

// Source-level regression test for database-driven guides and the editorial seed.
$root = dirname(__DIR__);
$controller = file_get_contents($root . '/app/Controllers/GuideController.php');
$seed = file_get_contents($root . '/database/seeds/004_seed_guides.sql');
$failures = [];

if ($controller === false || $seed === false) {
    fwrite(STDERR, "Could not read guide controller or seed SQL.\n");
    exit(1);
}
if (str_contains($controller, "'how-much-does-a-website-cost-in-kenya' =>")) {
    $failures[] = 'GuideController still contains the hardcoded guide catalogue.';
}
if (!str_contains($controller, "WHERE status = 'published'")) {
    $failures[] = 'GuideController must only return published database guides.';
}
if (!str_contains($seed, 'INSERT IGNORE INTO guides')) {
    $failures[] = 'Guide seed should be idempotent.';
}
$expectedSlugs = [
    'how-much-does-a-website-cost-in-kenya',
    'domain-and-hosting-explained',
    'what-to-prepare-before-building-a-business-website',
    'local-online-visibility-checklist',
];
foreach ($expectedSlugs as $slug) {
    if (!str_contains($seed, "'" . $slug . "'")) {
        $failures[] = "Missing seed article: {$slug}";
    }
}
if (substr_count($seed, '<h2>') < 12 || substr_count($seed, '<p>') < 20) {
    $failures[] = 'Seed articles do not contain enough substantive structured content.';
}
if ($failures !== []) {
    foreach ($failures as $failure) fwrite(STDERR, "[FAIL] {$failure}\n");
    exit(1);
}
echo "[PASS] No hardcoded article catalogue.\n";
echo "[PASS] Public controller selects published database guides only.\n";
echo "[PASS] Four idempotent, substantive guide seed records found.\n";
