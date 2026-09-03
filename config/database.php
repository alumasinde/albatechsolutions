<?php

return [
    'host'    => $_ENV['DB_HOST'] ?? '127.0.0.1',
    'port'    => $_ENV['DB_PORT'] ?? '3306',
    'name'    => $_ENV['DB_NAME'] ?? 'albatech_platform',
    'user'    => $_ENV['DB_USER'] ?? 'root',
    'pass'    => $_ENV['DB_PASS'] ?? '',
    'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
];
