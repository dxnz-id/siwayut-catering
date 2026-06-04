<?php
declare(strict_types=1);

namespace Database\Migrations;

use App\Core\BaseMigration;

class AddPhoneAddressToUsers extends BaseMigration {
    protected string $filename = '023_add_phone_address_to_users';

    public function up(): array {
        return [
            "ALTER TABLE `users` ADD COLUMN `phone` VARCHAR(20) NULL AFTER `email`",
            "ALTER TABLE `users` ADD COLUMN `address` TEXT NULL AFTER `phone`",
        ];
    }

    public function down(): array {
        return [
            "ALTER TABLE `users` DROP COLUMN `address`",
            "ALTER TABLE `users` DROP COLUMN `phone`",
        ];
    }
}
