<?php
declare(strict_types=1);

namespace Database\Migrations;

use App\Core\BaseMigration;

class AddAvatarToUsers extends BaseMigration {
    protected string $filename = '021_add_avatar_to_users';

    public function up(): string {
        return "ALTER TABLE `users` ADD COLUMN `avatar` VARCHAR(255) NULL AFTER `email`";
    }

    public function down(): string {
        return "ALTER TABLE `users` DROP COLUMN `avatar`";
    }
}
