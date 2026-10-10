<?php

declare(strict_types=1);

use AlbaTech\Core\Database;
use AlbaTech\Core\Env;

$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';
Env::load($root . '/.env');

$seedDir = $root . '/database/seeds';
$options = getopt('', ['file:']);
$requestedFile = $options['file'] ?? null;

if (!is_dir($seedDir)) {
    fwrite(STDERR, "Seed failed: directory not found: {$seedDir}\n");
    exit(1);
}

if ($requestedFile !== null) {
    // Accept a filename only; do not allow paths outside database/seeds.
    if (
        !is_string($requestedFile)
        || basename($requestedFile) !== $requestedFile
        || strtolower(pathinfo($requestedFile, PATHINFO_EXTENSION)) !== 'sql'
    ) {
        fwrite(STDERR, "Seed failed: --file must be a SQL filename, e.g. --file=004_seed_guides.sql\n");
        exit(1);
    }

    $candidate = $seedDir . DIRECTORY_SEPARATOR . $requestedFile;
    if (!is_file($candidate)) {
        fwrite(STDERR, "Seed failed: seed file not found: {$requestedFile}\n");
        exit(1);
    }

    $files = [$candidate];
} else {
    $files = glob($seedDir . DIRECTORY_SEPARATOR . '*.sql') ?: [];
    sort($files, SORT_STRING);
}

if ($files === []) {
    echo "No SQL seed files found in database/seeds.\n";
    exit(0);
}

$pdo = null;
$currentFile = null;

try {
    // Use the same application database connection as bin/migrate.php.
    $pdo = Database::connection();

    foreach ($files as $currentFile) {
        $name = basename($currentFile);
        $sql = file_get_contents($currentFile);

        if ($sql === false) {
            throw new RuntimeException("Could not read seed file: {$name}");
        }

        if (trim($sql) === '') {
            echo "[SKIP] {$name} (empty file)\n";
            continue;
        }

        echo "[RUN ] {$name}\n";

        // Seed files should contain SQL statements, not mysql-client commands
        // such as SOURCE or DELIMITER.
        $pdo->exec($sql);

        echo "[ OK ] {$name}\n";
    }

    echo "Seeding complete. Processed " . count($files) . " file(s).\n";
} catch (Throwable $exception) {
    $failedName = $currentFile !== null ? basename($currentFile) : 'database connection';
    fwrite(STDERR, "Seed failed ({$failedName}): {$exception->getMessage()}\n");
    exit(1);
}
