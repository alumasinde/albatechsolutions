<?php

declare(strict_types=1);

/**
 * Simple migration runner. Usage: php database/migrate.php
 * Applies every .sql file in database/migrations in filename order,
 * tracking applied files in a `migrations` table so re-runs are safe.
 */

require dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

$config = require dirname(__DIR__) . '/config/database.php';

$pdo = new PDO(
    sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $config['host'], $config['port'], $config['name'], $config['charset']),
    $config['user'],
    $config['pass'],
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    ]
);

$pdo->exec(
    'CREATE TABLE IF NOT EXISTS migrations (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(190) NOT NULL UNIQUE,
        run_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
);

$applied = $pdo->query('SELECT migration FROM migrations')->fetchAll(PDO::FETCH_COLUMN);

$files = glob(__DIR__ . '/migrations/*.sql');
sort($files);

// Numeric prefixes are historical ordering hints, not migration identities.
// Warn about duplicates so they can be cleaned up in a future safe release
// without renaming already-applied migration files.
$prefixes = [];
foreach ($files as $file) {
    if (preg_match('/^(\d+)_/', basename($file), $match)) {
        $prefixes[$match[1]][] = basename($file);
    }
}
foreach ($prefixes as $prefix => $names) {
    if (count($names) > 1) {
        fwrite(STDERR, 'Warning: duplicate migration prefix ' . $prefix . ': ' . implode(', ', $names) . "\n");
    }
}

foreach ($files as $file) {
    $name = basename($file);

    if (in_array($name, $applied, true)) {
        echo "Skipping (already applied): {$name}\n";
        continue;
    }

    echo "Applying: {$name}...\n";
    $sql = file_get_contents($file);

    $pdo->exec($sql);
    $pdo->prepare('INSERT INTO migrations (migration) VALUES (:m)')->execute(['m' => $name]);
}

echo "Migrations complete.\n";
