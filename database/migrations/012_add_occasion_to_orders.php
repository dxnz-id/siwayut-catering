<?php
declare(strict_types=1);

namespace Database\Migrations;

use App\Core\BaseMigration;

class AddOccasionToOrders extends BaseMigration {
    protected string $filename = '012_add_occasion_to_orders';

    public function up(): string {
        return "ALTER TABLE `orders`
ADD COLUMN `occasion` VARCHAR(100) NOT NULL DEFAULT '' AFTER `event_date`";
    }

    public function down(): string {
        return "ALTER TABLE `orders` DROP COLUMN `occasion`";
    }
}
