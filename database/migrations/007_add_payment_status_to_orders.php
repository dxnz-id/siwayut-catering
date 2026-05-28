<?php
declare(strict_types=1);

namespace Database\Migrations;

use App\Core\BaseMigration;

class AddPaymentStatusToOrders extends BaseMigration {
    protected string $filename = '007_add_payment_status_to_orders';

    public function up(): string {
        return "ALTER TABLE `orders`
ADD COLUMN `payment_status` ENUM('unpaid', 'paid', 'refunded')
NOT NULL DEFAULT 'unpaid' AFTER `status`";
    }

    public function down(): string {
        return "ALTER TABLE `orders` DROP COLUMN `payment_status`";
    }
}
