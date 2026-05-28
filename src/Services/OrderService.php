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

    /**
     * Create order. Implements "Member via Phone Number" logic.
     */
    public function createOrder(array $data): int {
        // 1. Handle Customer (Member by Phone)
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

        // 2. Fetch Menu to calculate total price
        $menuItem = $this->menu->find((int)$data['menu_id']);
        if (!$menuItem) {
            throw new \Exception("Menu not found.");
        }

        $quantity = (int)$data['quantity'];
        if ($quantity < (int)$menuItem['minimum_portions']) {
            throw new \Exception("Quantity is less than the minimum limit ({$menuItem['minimum_portions']} portions).");
        }

        $totalPrice = $quantity * (float)$menuItem['price'];

        // 3. Create Order
        return $this->order->create([
            'customer_id' => $customerId,
            'event_id' => $data['event_id'],
            'menu_id' => $menuItem['id'],
            'event_date' => $data['event_date'],
            'quantity' => $quantity,
            'total_price' => $totalPrice,
            'delivery_address' => $data['delivery_address'],
            'notes' => $data['notes'] ?? '',
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function updateStatus(int $id, string $status, string $paymentStatus): bool {
        return $this->order->update($id, [
            'status' => $status,
            'payment_status' => $paymentStatus,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}
