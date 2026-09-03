<?php

declare(strict_types=1);

/**
 * Production preflight check.
 * Usage: php bin/preflight.php
 * Exit code 0 means the application is ready for deployment checks.
 */

$root = dirname(__DIR__);

require $root . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable($root);
$dotenv->safeLoad();

$checks = [];
$fail = static function (string $name, string $message) use (&$checks): void {
    $checks[] = ['FAIL', $name, $message];
};
$pass = static function (string $name, string $message) use (&$checks): void {
    $checks[] = ['PASS', $name, $message];
};

$phpVersion = PHP_VERSION;
version_compare($phpVersion, '8.2.0', '>=')
    ? $pass('PHP', $phpVersion)
    : $fail('PHP', 'PHP 8.2+ is required; found ' . $phpVersion);

$requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'json', 'fileinfo'];
foreach ($requiredExtensions as $extension) {
    extension_loaded($extension)
        ? $pass('Extension ' . $extension, 'loaded')
        : $fail('Extension ' . $extension, 'missing');
}

$env = static fn (string $key, mixed $default = null): mixed => $_ENV[$key] ?? $default;

if ((string) $env('APP_ENV', 'production') === 'production') {
    filter_var($env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN)
        ? $fail('APP_DEBUG', 'must be false in production')
        : $pass('APP_DEBUG', 'disabled');

    $appKey = (string) $env('APP_KEY', '');
    strlen($appKey) >= 32
        ? $pass('APP_KEY', 'configured')
        : $fail('APP_KEY', 'must contain at least 32 random characters');

    str_starts_with((string) $env('APP_URL', ''), 'https://')
        ? $pass('APP_URL', 'HTTPS configured')
        : $fail('APP_URL', 'must use HTTPS in production');
}

foreach (['app', 'config', 'database', 'public_html'] as $path) {
    is_dir($root . '/' . $path)
        ? $pass('Path ' . $path, 'present')
        : $fail('Path ' . $path, 'missing');
}

$writablePaths = ['storage/logs', 'storage/backups', 'app/public/assets/uploads'];
foreach ($writablePaths as $path) {
    is_dir($root . '/' . $path) && is_writable($root . '/' . $path)
        ? $pass('Writable ' . $path, 'ok')
        : $fail('Writable ' . $path, 'missing or not writable');
}

try {
    $db = require $root . '/config/database.php';
    $pdo = new PDO(
        sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $db['host'], $db['port'], $db['name'], $db['charset']),
        $db['user'],
        $db['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
    $pdo->query('SELECT 1');
    $pass('Database', 'connection successful');

    $migrationRows = $pdo->query('SELECT migration FROM migrations ORDER BY migration')->fetchAll(PDO::FETCH_COLUMN);
    $applied = array_fill_keys($migrationRows, true);
    $migrationFiles = glob($root . '/database/migrations/*.sql') ?: [];
    $pending = [];
    foreach ($migrationFiles as $file) {
        $name = basename($file);
        if (!isset($applied[$name])) {
            $pending[] = $name;
        }
    }
    if ($pending === []) {
        $pass('Migrations', 'all applied');
    } else {
        $fail('Migrations', 'pending: ' . implode(', ', $pending));
    }
} catch (Throwable $e) {
    $fail('Database', 'connection or migration check failed: ' . $e->getMessage());
}

$debugArtifacts = array_merge(
    glob($root . '/*.log') ?: [],
    glob($root . '/php-lint*.txt') ?: [],
    glob($root . '/tests/*.log') ?: []
);
if ($debugArtifacts === []) {
    $pass('Debug artifacts', 'none in project root');
} else {
    $fail('Debug artifacts', 'remove before deployment: ' . implode(', ', array_map('basename', $debugArtifacts)));
}

$failed = 0;
foreach ($checks as [$status, $name, $message]) {
    printf('[%s] %-24s %s\n', $status, $name, $message);
    $failed += $status === 'FAIL' ? 1 : 0;
}

printf("\n%d checks failed.\n", $failed);
exit($failed === 0 ? 0 : 1);
