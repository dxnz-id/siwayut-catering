<?php
declare(strict_types=1);

namespace Database\Migrations;

use App\Core\BaseMigration;

class CreateCustomersTable extends BaseMigration {
    protected string $filename = '005_create_customers_table';

    public function up(): string {
        return "CREATE TABLE IF NOT EXISTS `customers` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20) NOT NULL UNIQUE,
    `email` VARCHAR(255),
    `address` TEXT NOT NULL,
    `notes` TEXT,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    }

    public function down(): string {
        return "DROP TABLE IF EXISTS `customers`";
    }
}
