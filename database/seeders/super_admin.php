<?php

declare(strict_types=1);

/**
 * Creates the initial Super Admin user.
 * Usage: php database/seeders/super_admin.php
 */

require dirname(__DIR__, 2) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->safeLoad();

$config = require dirname(__DIR__, 2) . '/config/database.php';

$pdo = new PDO(
    sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $config['host'], $config['port'], $config['name'], $config['charset']),
    $config['user'],
    $config['pass'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

fwrite(STDOUT, "Super Admin name: ");
$name = trim(fgets(STDIN));

fwrite(STDOUT, "Super Admin email: ");
$email = trim(fgets(STDIN));

fwrite(STDOUT, "Super Admin password: ");
$password = trim(fgets(STDIN));

$hash = password_hash($password, PASSWORD_BCRYPT);

$pdo->prepare(
    'INSERT INTO users (name, email, password, is_active) VALUES (:name, :email, :password, 1)
     ON DUPLICATE KEY UPDATE name = VALUES(name)'
)->execute(['name' => $name, 'email' => $email, 'password' => $hash]);

$userId = (int) $pdo->lastInsertId();

if ($userId === 0) {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $userId = (int) $stmt->fetchColumn();
}

$roleStmt = $pdo->prepare('SELECT id FROM roles WHERE slug = :slug');
$roleStmt->execute(['slug' => 'super-admin']);
$roleId = (int) $roleStmt->fetchColumn();

$pdo->prepare(
    'INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)'
)->execute(['user_id' => $userId, 'role_id' => $roleId]);

echo "Super Admin created: {$email}\n";
