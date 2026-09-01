<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

final class Database
{
    private static ?PDO $connection = null;

    private function __construct()
    {
    }

    public static function getConnection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $configPath = dirname(__DIR__, 2) . '/config/database.php';
        if (!is_file($configPath)) {
            throw new RuntimeException('Database configuration file not found.');
        }

        /** @var array{host:string,port:int,database:string,username:string,password:string,charset:string} $config */
        $config = require $configPath;

        $dsn = sprintf(
            'pgsql:host=%s;port=%d;dbname=%s;options=--client_encoding=%s',
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset']
        );

        try {
            self::$connection = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $exception) {
            throw new RuntimeException('Unable to connect to PostgreSQL.', 0, $exception);
        }

        return self::$connection;
    }
}
