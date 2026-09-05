<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();
$config = require dirname(__DIR__) . '/config/database.php';
$database = (string) $config['name'];
if (!preg_match('/^[A-Za-z0-9_]+$/', $database)) { throw new RuntimeException('Invalid DB_NAME.'); }
$reset = in_array('--reset', $argv, true);
$server = new PDO(sprintf('mysql:host=%s;port=%s;charset=%s', $config['host'], $config['port'], $config['charset']), $config['user'], $config['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
if ($reset) { fwrite(STDOUT, 'Dropping configured database...' . PHP_EOL); $server->exec('DROP DATABASE IF EXISTS ' . $database); }
$server->exec('CREATE DATABASE IF NOT EXISTS ' . $database . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
$pdo = new PDO(sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $config['host'], $config['port'], $database, $config['charset']), $config['user'], $config['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$tables = (int) $pdo->query('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE()')->fetchColumn();
if ($tables > 0 && !$reset) { throw new RuntimeException('Configured database is not empty. Use --reset only if you intentionally want to discard it.'); }
$files = glob(dirname(__DIR__) . '/database/migrations/*.sql') ?: []; sort($files, SORT_NATURAL);
if ($files === []) { throw new RuntimeException('No migration files found.'); }
foreach ($files as $file) { fwrite(STDOUT, 'Running ' . basename($file) . '...' . PHP_EOL); $sql = trim((string) file_get_contents($file)); if ($sql !== '') { $pdo->exec($sql); } }
fwrite(STDOUT, 'Running baseline seed...' . PHP_EOL); require dirname(__DIR__) . '/database/seed.php';
fwrite(STDOUT, 'Fresh AlbaTech database installation complete.' . PHP_EOL);