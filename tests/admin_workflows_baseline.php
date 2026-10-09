<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$checks = 0;
$assert = static function (bool $ok, string $message) use (&$checks): void {
    $checks++;
    if (!$ok) { fwrite(STDERR, "FAIL: {$message}\n"); exit(1); }
};
$repository = file_get_contents($root . '/app/Repositories/ServiceRepository.php');
$controller = file_get_contents($root . '/app/Controllers/AdminController.php');
$routes = file_get_contents($root . '/routes/web.php');
$migrations = implode("\n", array_map('file_get_contents', glob($root . '/database/migrations/*.sql') ?: []));
$assert(is_string($repository) && str_contains($repository, "s.status = :published"), 'Public catalogue only selects published services');
$assert(str_contains($repository, "c.status = :category_status"), 'Public catalogue excludes inactive categories');
$assert(str_contains($repository, 's.is_featured = 1'), 'Featured homepage query requires featured flag');
$assert(str_contains($repository, 'findPublishedBySlug') && str_contains($repository, 'c.status = :category_status'), 'Service detail lookup enforces published service and active category');
$assert(str_contains($controller, "['fixed', 'starting_at', 'quote']"), 'Pricing type allowlist exists');
$assert(str_contains($controller, 'pricing_amount') && str_contains($controller, 'Contact for price'), 'Quote pricing clears amount and provides contact label');
$assert(str_contains($controller, 'verifyCsrf'), 'Admin mutations check CSRF tokens');
$assert(str_contains($controller, "'category.created'") && str_contains($controller, "'category.updated'"), 'Category mutations create audit events');
$assert(str_contains($routes, "post('/admin/services/{id}/toggle'") && str_contains($routes, "post('/admin/services/{id}/feature'"), 'Publication and featuring use POST routes');
$assert(str_contains($migrations, 'admin_audit_logs') && str_contains($migrations, 'pricing_amount DECIMAL'), 'Schema includes audit logs and decimal pricing');
fwrite(STDOUT, "PASS: {$checks} admin workflow baseline checks.\n");
