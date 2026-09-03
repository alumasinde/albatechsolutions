<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

$config = require dirname(__DIR__) . '/config/database.php';

$pdo = new PDO(
    sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $config['host'], $config['port'], $config['name'], $config['charset']),
    $config['user'],
    $config['pass'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Generous window — well beyond the 15-minute reuse window in
// PaymentService, so this never touches a payment a customer could
// still legitimately resume.
$cutoffMinutes = 60;

$stmt = $pdo->prepare(
    "UPDATE payments
     SET status = 'failed', gateway_response = 'Expired: abandoned, cleaned up by scheduled task.'
     WHERE status = 'pending' AND created_at < (NOW() - INTERVAL :minutes MINUTE)"
);
$stmt->bindValue(':minutes', $cutoffMinutes, PDO::PARAM_INT);
$stmt->execute();

echo "Expired {$stmt->rowCount()} stale pending payment(s) older than {$cutoffMinutes} minutes.\n";
