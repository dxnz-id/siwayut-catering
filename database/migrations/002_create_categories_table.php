<?php
declare(strict_types=1);

namespace Database\Migrations;

use App\Core\BaseMigration;

class CreateCategoriesTable extends BaseMigration {
    protected string $filename = '002_create_categories_table';

    public function up(): string {
        return "CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    }

    public function down(): string {
        return "DROP TABLE IF EXISTS `categories`";
    }
}
