<?php

declare(strict_types=1);

use AlbaTech\Core\Database;
use AlbaTech\Core\Env;

require dirname(__DIR__) . '/vendor/autoload.php';
Env::load(dirname(__DIR__) . '/.env');

if (PHP_SAPI !== 'cli') { fwrite(STDERR, "Run this script from the command line.\n"); exit(1); }
$email = strtolower(trim((string) ($argv[1] ?? '')));
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Usage: php bin/create-admin.php admin@example.com\n");
    exit(1);
}
$firstName = trim((string) readline('First name: '));
$lastName = trim((string) readline('Last name: '));
$password = (string) readline('Password (minimum 8 characters): ');
if ($firstName === '' || $lastName === '' || strlen($password) < 8) {
    fwrite(STDERR, "First and last name are required, and the password must be at least 8 characters.\n");
    exit(1);
}
$pdo = Database::connection();
$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare("INSERT INTO admin_users (first_name,last_name,email,password_hash,status) VALUES (:first_name,:last_name,:email,:password_hash,'active') ON DUPLICATE KEY UPDATE first_name=VALUES(first_name),last_name=VALUES(last_name),password_hash=VALUES(password_hash),status='active'");
    $stmt->execute(['first_name'=>$firstName,'last_name'=>$lastName,'email'=>$email,'password_hash'=>password_hash($password, PASSWORD_DEFAULT)]);
    $stmt = $pdo->prepare('SELECT id FROM admin_users WHERE email=:email'); $stmt->execute(['email'=>$email]); $adminId = (int)$stmt->fetchColumn();
    $stmt = $pdo->prepare('INSERT IGNORE INTO admin_user_roles (admin_user_id,role_id) SELECT :admin_id,id FROM roles WHERE slug = \'administrator\''); $stmt->execute(['admin_id'=>$adminId]);
    $pdo->commit();
    fwrite(STDOUT, "Administrator account ready for {$email}. Sign in at /admin/login.\n");
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    fwrite(STDERR, "Could not create administrator: {$e->getMessage()}\n"); exit(1);
}
