<?php
declare(strict_types=1);
// File: src/Core/Database.php

namespace App\Core;
use PDO;

class Database {
    private static ?PDO $instance = null;

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $config = require BASE_PATH . '/config/database.php';
            $dsn = sprintf(
                '%s:host=%s;port=%d;dbname=%s;charset=%s',
                $config['driver'],
                $config['host'],
                $config['port'],
                $config['database'],
                $config['charset']
            );
            self::$instance = new PDO($dsn, $config['username'], $config['password'], $config['options']);
        }
        return self::$instance;
    }

    private function __construct() {
    }

    private function __clone(): void {
    }

    public function __wakeup(): never {
        throw new \Exception('Cannot unserialize singleton');
    }
}
