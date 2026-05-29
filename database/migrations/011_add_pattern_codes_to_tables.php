<?php
declare(strict_types=1);

namespace Database\Migrations;

use App\Core\BaseMigration;

class AddPatternCodesToTables extends BaseMigration {
    protected string $filename = '011_add_pattern_codes_to_tables';

    public function up(): array {
        return [
            // Add columns allowing NULL initially
            "ALTER TABLE `orders` ADD COLUMN `order_number` VARCHAR(50) NULL AFTER `id`",
            "ALTER TABLE `customers` ADD COLUMN `customer_code` VARCHAR(50) NULL AFTER `id`",
            "ALTER TABLE `menus` ADD COLUMN `menu_code` VARCHAR(50) NULL AFTER `id`",
            "ALTER TABLE `users` ADD COLUMN `user_code` VARCHAR(50) NULL AFTER `id`",

            // Update existing rows with pattern codes based on their existing IDs
            "UPDATE `orders` SET `order_number` = CONCAT('ORD-', DATE_FORMAT(`created_at`, '%Y%m%d'), '-', LPAD(`id`, 4, '0'))",
            "UPDATE `customers` SET `customer_code` = CONCAT('CST-', DATE_FORMAT(`created_at`, '%y%m'), '-', LPAD(`id`, 4, '0'))",
            "UPDATE `menus` SET `menu_code` = CONCAT('MNU-', LPAD(`id`, 4, '0'))",
            "UPDATE `users` SET `user_code` = CONCAT('USR-', LPAD(`id`, 4, '0'))",

            // Alter columns to NOT NULL
            "ALTER TABLE `orders` MODIFY COLUMN `order_number` VARCHAR(50) NOT NULL",
            "ALTER TABLE `customers` MODIFY COLUMN `customer_code` VARCHAR(50) NOT NULL",
            "ALTER TABLE `menus` MODIFY COLUMN `menu_code` VARCHAR(50) NOT NULL",
            "ALTER TABLE `users` MODIFY COLUMN `user_code` VARCHAR(50) NOT NULL",

            // Add UNIQUE constraints
            "ALTER TABLE `orders` ADD UNIQUE INDEX `idx_order_number` (`order_number`)",
            "ALTER TABLE `customers` ADD UNIQUE INDEX `idx_customer_code` (`customer_code`)",
            "ALTER TABLE `menus` ADD UNIQUE INDEX `idx_menu_code` (`menu_code`)",
            "ALTER TABLE `users` ADD UNIQUE INDEX `idx_user_code` (`user_code`)"
        ];
    }

    public function down(): array {
        return [
            "ALTER TABLE `orders` DROP INDEX `idx_order_number`",
            "ALTER TABLE `orders` DROP COLUMN `order_number`",

            "ALTER TABLE `customers` DROP INDEX `idx_customer_code`",
            "ALTER TABLE `customers` DROP COLUMN `customer_code`",

            "ALTER TABLE `menus` DROP INDEX `idx_menu_code`",
            "ALTER TABLE `menus` DROP COLUMN `menu_code`",

            "ALTER TABLE `users` DROP INDEX `idx_user_code`",
            "ALTER TABLE `users` DROP COLUMN `user_code`"
        ];
    }
}
