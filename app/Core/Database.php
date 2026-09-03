<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

/**
 * Database - PDO singleton connection manager.
 *
 * Single shared PDO instance across the request lifecycle.
 * All SQL must be executed via prepared statements (repositories only).
 */
final class Database
{
    private static ?PDO $instance = null;

    private function __construct()
    {
        // Non-instantiable
    }

    public static function connection(): PDO
    {
        if (self::$instance === null) {
            $config = Config::get('database');

            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $config['host'],
                $config['port'],
                $config['name'],
                $config['charset']
            );

            try {
                self::$instance = new PDO($dsn, $config['user'], $config['pass'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::ATTR_PERSISTENT         => false,
                ]);
            } catch (PDOException $e) {
                Logger::error('Database connection failed: ' . $e->getMessage());

                throw new PDOException(
                    Config::get('app.debug') ? $e->getMessage() : 'Database connection error.'
                );
            }
        }

        return self::$instance;
    }

    public static function beginTransaction(): bool
    {
        return self::connection()->beginTransaction();
    }

    public static function commit(): bool
    {
        return self::connection()->commit();
    }

    public static function rollBack(): bool
    {
        return self::connection()->inTransaction()
            ? self::connection()->rollBack()
            : false;
    }

    private function __clone()
    {
    }

    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize a singleton.');
    }
}
