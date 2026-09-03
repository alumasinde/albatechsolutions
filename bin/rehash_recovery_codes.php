<?php

declare(strict_types=1);

/**
 * One-time migration helper for legacy plaintext 2FA recovery-code JSON.
 * It rewrites only values that are not already password hashes and never
 * prints recovery codes or other user secrets.
 */
$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable($root);
$dotenv->safeLoad();

$db = require $root . '/config/database.php';
$pdo = new PDO(
    sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $db['host'], $db['port'], $db['name'], $db['charset']),
    $db['user'],
    $db['pass'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
);

$rows = $pdo->query("SELECT id, two_factor_recovery_codes FROM users WHERE two_factor_recovery_codes IS NOT NULL AND two_factor_recovery_codes <> ''")->fetchAll();
$update = $pdo->prepare('UPDATE users SET two_factor_recovery_codes = :codes WHERE id = :id');
$changed = 0;

foreach ($rows as $row) {
    $codes = json_decode((string) $row['two_factor_recovery_codes'], true);
    if (!is_array($codes) || $codes === []) continue;

    $changedRow = false;
    foreach ($codes as $index => $code) {
        if (!is_string($code) || $code === '') continue;
        if (password_get_info($code)['algo'] !== 0) continue;
        $codes[$index] = password_hash($code, PASSWORD_DEFAULT);
        $changedRow = true;
    }

    if ($changedRow) {
        $update->execute(['codes' => json_encode(array_values($codes), JSON_THROW_ON_ERROR), 'id' => (int) $row['id']]);
        $changed++;
    }
}

echo "Recovery-code rows migrated: {$changed}" . PHP_EOL;
