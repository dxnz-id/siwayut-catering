<?php
declare(strict_types=1);

namespace Database\Migrations;

use App\Core\BaseMigration;

class DropEventIdFromOrders extends BaseMigration {
    protected string $filename = '013_drop_event_id_from_orders';

    public function up(): string|array {
        return [
            "ALTER TABLE `orders` DROP FOREIGN KEY `fk_order_event`",
            "ALTER TABLE `orders` DROP COLUMN `event_id`",
        ];
    }

    public function down(): string|array {
        return [
            "ALTER TABLE `orders` ADD COLUMN `event_id` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `customer_id`",
            "ALTER TABLE `orders` ADD CONSTRAINT `fk_order_event` FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE",
        ];
    }
}
