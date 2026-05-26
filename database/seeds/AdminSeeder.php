<?php
declare(strict_types=1);
// File: database/seeds/AdminSeeder.php

namespace Database\Seeds;

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 2));
}

require BASE_PATH . '/vendor/autoload.php';

if (file_exists(BASE_PATH . '/.env')) {
    $env = parse_ini_file(BASE_PATH . '/.env');
    if ($env !== false) {
        foreach ($env as $key => $value) {
            $_ENV[$key] = $value;
        }
    }
}

if (!defined('APP_NAME')) {
    require BASE_PATH . '/config/app.php';
}

use App\Core\Database;

class AdminSeeder {
    public static function run(): void {
        $db = Database::getInstance();

        // Check if admin already exists
        $stmt = $db->prepare("SELECT COUNT(*) FROM `users` WHERE `email` = ?");
        $stmt->execute(['admin@admin.com']);
        if ((int) $stmt->fetchColumn() > 0) {
            echo "Admin user already exists. Skipping.\n";
            return;
        }

        $stmt = $db->prepare(
            "INSERT INTO `users` (`name`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            'Administrator',
            'admin@admin.com',
            password_hash('password', PASSWORD_DEFAULT),
            'admin',
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s'),
        ]);

        echo "Admin user created successfully.\n";
        echo "Email: admin@admin.com\n";
        echo "Password: password\n";
    }
}

AdminSeeder::run();
