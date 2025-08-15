<?php

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

abstract class Connection
{
    private static ?PDO $instance = null;

    private function __construct() {}

    private function __clone() {}

    public function __wakeup(): void
    {
        throw new RuntimeException('Cannot unserialize singleton');
    }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::createConnection();
        }

        return self::$instance;
    }

    private static function createConnection(): void
    {
        try {
            $dbConfig = require __DIR__ . '/../config/database.php';
            self::$instance = new PDO(
                $dbConfig['driver']
                    . ":dbname=" . $dbConfig['database']
                    . ";host=" . $dbConfig['host'] . ":" . $dbConfig['port'],
                $dbConfig['username'],
                $dbConfig['password'],
                $dbConfig['options']
            );
        } catch (PDOException $e) {
            self::handleConnectionError($e);
        }
    }

    private static function handleConnectionError(PDOException $e): void
    {
        $message = "Erro de conexão com o banco de dados.";

        //error_log("Database Connection Error: " . $e->getMessage());

        $appConfig = require __DIR__ . '/../config/app.php';

        // DO not show details errots in production
        if ($appConfig['env'] === 'production') {
            throw new RuntimeException($message);
        }

        throw new RuntimeException($message . ': ' . $e->getMessage());
    }
}
