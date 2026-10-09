<?php

declare(strict_types=1);

use AlbaTech\Core\Database;
use AlbaTech\Core\Env;

$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';
Env::load($root . '/.env');

$pdo = null;
$lockName = 'albatechsolutions_migrations';

try {
    $pdo = Database::connection();
    $lockStatement = $pdo->prepare('SELECT GET_LOCK(:lock_name, 10)');
    $lockStatement->execute(['lock_name' => $lockName]);
    if ((int) $lockStatement->fetchColumn() !== 1) {
        throw new RuntimeException('Could not acquire the migration lock. Another migration process may be running.');
    }

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

    $pendingCount = 0;
    foreach ($files as $file) {
        $name = basename($file);
        if (!preg_match('/^\d{3}_[a-z0-9_]+\.sql$/', $name)) {
            throw new RuntimeException("Invalid migration filename: {$name}. Use names like 000_auth.sql or 010_service_pages.sql.");
        }

        $sql = file_get_contents($file);
        if ($sql === false || trim($sql) === '') {
            throw new RuntimeException("Migration is empty or unreadable: {$name}");
        }

        $checksum = hash('sha256', $sql);
        if (isset($applied[$name])) {
            if (!hash_equals($applied[$name], $checksum)) {
                throw new RuntimeException("Applied migration {$name} has changed. Restore it or create a new migration.");
            }
            echo "[SKIP] {$name}\n";
            continue;
        }

        echo "[RUN ] {$name}\n";
        // MySQL DDL statements auto-commit. If a migration fails, inspect the database before retrying.
        $pdo->exec($sql);

        $record = $pdo->prepare(
            'INSERT INTO schema_migrations (migration, checksum) VALUES (:migration, :checksum)'
        );
        $record->execute(['migration' => $name, 'checksum' => $checksum]);
        $pendingCount++;
        echo "[ OK ] {$name}\n";
    }

    echo $pendingCount === 0
        ? "Database is up to date.\n"
        : "Migrations complete. Applied {$pendingCount} migration(s).\n";
} catch (Throwable $exception) {
    fwrite(STDERR, 'Migration failed: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
} finally {
    if ($pdo instanceof \PDO) {
        try {
            $release = $pdo->prepare('SELECT RELEASE_LOCK(:lock_name)');
            $release->execute(['lock_name' => $lockName]);
        } catch (\Throwable) {
            // Preserve the original migration result.
        }
    }
}
