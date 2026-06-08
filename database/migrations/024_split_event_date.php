<?php

declare(strict_types=1);

namespace Database\Migrations;

use App\Core\BaseMigration;

class SplitEventDate extends BaseMigration {
    protected string $filename = '024_split_event_date';

    public function up(): array {
        return [
            "ALTER TABLE `orders` ADD COLUMN `event_time` TIME NULL AFTER `event_date`",
            "ALTER TABLE `orders` MODIFY COLUMN `event_date` DATE NOT NULL",
            "UPDATE `orders` SET `event_time` = CAST(`event_date` AS TIME) WHERE `event_time` IS NULL",
        ];
    }

    public function down(): array {
        return [
            "UPDATE `orders` SET `event_date` = CONCAT(`event_date`, ' ', COALESCE(`event_time`, '12:00:00')) WHERE 1=1",
            "ALTER TABLE `orders` MODIFY COLUMN `event_date` DATETIME NOT NULL",
            "ALTER TABLE `orders` DROP COLUMN `event_time`",
        ];
    }
}
