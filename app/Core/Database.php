<?php

declare(strict_types=1);

namespace AlbaTech\Core;

use PDO;
use PDOException;
use RuntimeException;

final class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $host = Env::get('DB_HOST', '127.0.0.1');
        $port = Env::get('DB_PORT', '3306');
        $name = Env::get('DB_DATABASE', 'albatechsolutions');
        $user = Env::get('DB_USERNAME', 'root');
        $password = Env::get('DB_PASSWORD', '');

        if (!preg_match('/^[a-zA-Z0-9_]+$/', (string) $name)) {
            throw new RuntimeException('Invalid database name in configuration.');
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $host,
            $port,
            $name
        );

        try {
            self::$connection = new PDO($dsn, (string) $user, (string) $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false,
            ]);
        } catch (PDOException $exception) {
            throw new RuntimeException(
                'Could not connect to MySQL. Check the local database service and .env settings.',
                previous: $exception
            );
        }

        return self::$connection;
    }
}
