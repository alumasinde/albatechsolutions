<?php

declare(strict_types=1);

/**
 * Secure MySQL backup helper.
 * Usage: php bin/backup.php [optional-output-directory]
 */

$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable($root);
$dotenv->safeLoad();

$db = require $root . '/config/database.php';
$outputDir = $argv[1] ?? ($root . '/storage/backups');
$outputDir = rtrim($outputDir, DIRECTORY_SEPARATOR);

if (!is_dir($outputDir) && !mkdir($outputDir, 0700, true) && !is_dir($outputDir)) {
    fwrite(STDERR, "Unable to create backup directory.\n");
    exit(1);
}

if (!is_writable($outputDir)) {
    fwrite(STDERR, "Backup directory is not writable.\n");
    exit(1);
}

$binary = trim((string) shell_exec('command -v mysqldump 2>/dev/null'));
if ($binary === '') {
    fwrite(STDERR, "mysqldump was not found in PATH. Install MySQL client tools first.\n");
    exit(1);
}

$configFile = tempnam(sys_get_temp_dir(), 'alba-mysql-');
if ($configFile === false) {
    fwrite(STDERR, "Unable to create temporary MySQL config.\n");
    exit(1);
}
chmod($configFile, 0600);

$contents = "[client]\n";
$contents .= 'host=' . str_replace(["\r", "\n"], '', (string) $db['host']) . "\n";
$contents .= 'port=' . (int) $db['port'] . "\n";
$contents .= 'user=' . str_replace(["\r", "\n"], '', (string) $db['user']) . "\n";
$contents .= 'password=' . str_replace(["\r", "\n"], '', (string) $db['pass']) . "\n";
file_put_contents($configFile, $contents, LOCK_EX);

$filename = $outputDir . '/albatech_' . date('Ymd_His') . '.sql.gz';
$command = escapeshellarg($binary)
    . ' --defaults-extra-file=' . escapeshellarg($configFile)
    . ' --single-transaction --quick --routines --triggers --events '
    . escapeshellarg((string) $db['name'])
    . ' | gzip -c > ' . escapeshellarg($filename);

passthru($command, $exitCode);
@unlink($configFile);

if ($exitCode !== 0 || !is_file($filename) || filesize($filename) === 0) {
    @unlink($filename);
    fwrite(STDERR, "Backup failed.\n");
    exit(1);
}

chmod($filename, 0600);
printf("Backup created: %s (%d bytes)\n", $filename, filesize($filename));
