<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Menu;

class OrderService {
    public function __construct(
        private Order $order,
        private Customer $customer,
        private Menu $menu
    ) {}

    public function paginate(int $page = 1, int $perPage = 10, string $search = '', array $filters = [], string $orderBy = 'created_at', string $direction = 'DESC'): array {
        return $this->order->paginateForAdmin($page, $perPage, $filters, $search, $orderBy, $direction);
    }

    public function find(int $id): ?array {
        return $this->order->find($id);
    }

    public function findByOrderNumber(string $orderNumber): ?array {
        return $this->order->findByOrderNumber($orderNumber);
    }

    public function getOrdersByCustomerId(int $customerId): array {
        return $this->order->getByCustomerId($customerId);
    }

    public function getItems(int $orderId): array {
        return $this->order->getItemsByOrderId($orderId);
    }

    /**
     * Create order with multiple menu items.
     * $data = ['phone', 'customer_name', 'delivery_address', 'event_id', 'event_date', 'notes']
     * $items = [['menu_id' => int, 'quantity' => int], ...]
     */
    public function createOrder(array $data, array $items): int {
        $phone = preg_replace('/[^0-9]/', '', $data['phone']);
        $customerRow = $this->customer->findByPhone($phone);

        if ($customerRow) {
            $customerId = (int)$customerRow['id'];
            $this->customer->update($customerId, [
                'name' => $data['customer_name'],
                'address' => $data['delivery_address']
            ]);
        } else {
            $customerId = $this->customer->create([
                'name' => $data['customer_name'],
                'phone' => $phone,
                'email' => '',
                'address' => $data['delivery_address'],
                'notes' => 'New member',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        // Calculate total price & validate items
        $totalPrice = 0;
        $orderItems = [];
        foreach ($items as $item) {
            $menuItem = $this->menu->find((int)$item['menu_id']);
            if (!$menuItem) {
                throw new \Exception("Menu #{$item['menu_id']} not found.");
            }

            $quantity = (int)$item['quantity'];
            if ($quantity < (int)$menuItem['minimum_portions']) {
                throw new \Exception("{$menuItem['name']}: quantity is less than minimum ({$menuItem['minimum_portions']} portions).");
            }

            $subtotal = $quantity * (float)$menuItem['price'];
            $totalPrice += $subtotal;

            $orderItems[] = [
                'menu_id' => $menuItem['id'],
                'quantity' => $quantity,
                'price_at_time' => $menuItem['price'],
                'subtotal' => $subtotal,
            ];
        }

        // Create order
        $orderId = $this->order->create([
            'order_number' => 'TEMP',
            'customer_id' => $customerId,
            'event_id' => $data['event_id'],
            'event_date' => $data['event_date'],
            'total_price' => $totalPrice,
            'delivery_address' => $data['delivery_address'],
            'notes' => $data['notes'] ?? '',
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Insert order items
        foreach ($orderItems as $item) {
            $this->order->rawInsertOrderItem($orderId, $item);
        }

        // Generate and update order_number
        $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad((string)$orderId, 4, '0', STR_PAD_LEFT);
        $this->order->update($orderId, ['order_number' => $orderNumber]);

        return $orderId;
    }

    public function countByMenuIds(array $menuIds): array {
        return $this->order->countByMenuIds($menuIds);
    }

    public function updateStatus(int $id, string $status, string $paymentStatus): bool {
        return $this->order->update($id, [
            'status' => $status,
            'payment_status' => $paymentStatus,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}
