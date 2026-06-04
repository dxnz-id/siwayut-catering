<?php
declare(strict_types=1);

namespace Database\Migrations;

use App\Core\BaseMigration;

class DropAvatarFromUsers extends BaseMigration {
    protected string $filename = '022_drop_avatar_from_users';

    public function up(): string {
        return "ALTER TABLE `users` DROP COLUMN `avatar`";
    }

    public function down(): string {
        return "ALTER TABLE `users` ADD COLUMN `avatar` VARCHAR(255) NULL AFTER `email`";
    }
}
