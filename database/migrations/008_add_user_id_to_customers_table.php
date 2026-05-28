<?php
declare(strict_types=1);

namespace Database\Migrations;

use App\Core\BaseMigration;

class AddUserIdToCustomersTable extends BaseMigration {
    protected string $filename = '008_add_user_id_to_customers_table';

    public function up(): array {
        return [
            "ALTER TABLE `customers` ADD COLUMN `user_id` INT UNSIGNED NULL AFTER `id`",
            "ALTER TABLE `customers` ADD INDEX `idx_customers_user_id` (`user_id`)",
            "ALTER TABLE `customers` ADD CONSTRAINT `fk_customers_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL",
        ];
    }

    public function down(): array {
        return [
            "ALTER TABLE `customers` DROP FOREIGN KEY `fk_customers_user`",
            "ALTER TABLE `customers` DROP INDEX `idx_customers_user_id`",
            "ALTER TABLE `customers` DROP COLUMN `user_id`",
        ];
    }
}
