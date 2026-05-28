<?php
declare(strict_types=1);

namespace Database\Migrations;

use App\Core\BaseMigration;

class CreateOrdersTable extends BaseMigration {
    protected string $filename = '006_create_orders_table';

    public function up(): string {
        return "CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `customer_id` INT UNSIGNED NOT NULL,
    `event_id` INT UNSIGNED NOT NULL,
    `menu_id` INT UNSIGNED NOT NULL,
    `event_date` DATETIME NOT NULL,
    `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
    `total_price` DECIMAL(12,2) NOT NULL DEFAULT 0,
    `delivery_address` TEXT NOT NULL,
    `notes` TEXT,
    `status` ENUM('pending', 'processing', 'delivering', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_order_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_order_event` FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_order_menu` FOREIGN KEY (`menu_id`) REFERENCES `menus`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    }

    public function down(): string {
        return "DROP TABLE IF EXISTS `orders`";
    }
}
