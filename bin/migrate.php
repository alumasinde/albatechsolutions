<?php

declare(strict_types=1);

use AlbaTech\Core\Database;
use AlbaTech\Core\Env;
use PDO;
use Throwable;

$root = dirname(__DIR__);

require $root . '/vendor/autoload.php';
Env::load($root . '/.env');

try {
    $pdo = Database::connection();
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS schema_migrations (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            migration VARCHAR(255) NOT NULL,
            checksum CHAR(64) NOT NULL,
            applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uq_schema_migrations_migration (migration)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    $migrationDir = $root . '/database/migrations';
    $files = glob($migrationDir . '/*.sql') ?: [];
    sort($files, SORT_STRING);

    $appliedQuery = $pdo->query('SELECT migration, checksum FROM schema_migrations');
    $applied = [];
    foreach ($appliedQuery->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $applied[$row['migration']] = $row['checksum'];
    }

    if ($files === []) {
        echo "No migration files found.\n";
        exit(0);
    }

    $pendingCount = 0;
    foreach ($files as $file) {
        $name = basename($file);
        $sql = file_get_contents($file);
        if ($sql === false || trim($sql) === '') {
            throw new RuntimeException("Migration is empty or unreadable: {$name}");
        }

        $checksum = hash('sha256', $sql);
        if (isset($applied[$name])) {
            if (!hash_equals($applied[$name], $checksum)) {
                throw new RuntimeException(
                    "Applied migration {$name} has changed. Do not edit applied migrations; create a new migration."
                );
            }
            echo "[SKIP] {$name}\n";
            continue;
        }

        echo "[RUN ] {$name}\n";
        // MySQL DDL commonly auto-commits; a migration failure may leave partial changes.
        // Inspect the schema before retrying a failed migration.
        $pdo->exec($sql);
        $statement = $pdo->prepare(
            'INSERT INTO schema_migrations (migration, checksum) VALUES (:migration, :checksum)'
        );
        $statement->execute(['migration' => $name, 'checksum' => $checksum]);
        $pendingCount++;
        echo "[ OK ] {$name}\n";
    }

    echo $pendingCount === 0
        ? "Database is up to date.\n"
        : "Migrations complete. Applied {$pendingCount} migration(s).\n";
} catch (Throwable $exception) {
    fwrite(STDERR, "Migration failed: " . $exception->getMessage() . PHP_EOL);
    exit(1);
}
