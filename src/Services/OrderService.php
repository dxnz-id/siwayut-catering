<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Menu;
use PDO;

class OrderService {
    public function __construct(
        private Order $order,
        private Customer $customer,
        private Menu $menu
    ) {}

    public function paginate(int $page = 1, int $perPage = 10, string $search = '', array $filters = [], string $orderBy = 'created_at', string $direction = 'DESC'): array {
        return $this->order->paginateForAdmin($page, $perPage, $filters, $search, $orderBy, $direction);
    }

    public function getAllForExport(string $search = '', array $filters = [], string $orderBy = 'created_at', string $direction = 'DESC'): array {
        return $this->order->getAllForExport($filters, $search, $orderBy, $direction);
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
     * $data = ['phone', 'customer_name', 'delivery_address', 'event_date', 'occasion', 'notes']
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
        $totalCost = 0;
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
            $costSubtotal = $quantity * (float)($menuItem['cost_price'] ?? 0);
            $totalPrice += $subtotal;
            $totalCost += $costSubtotal;

            $orderItems[] = [
                'menu_id' => $menuItem['id'],
                'quantity' => $quantity,
                'price_at_time' => $menuItem['price'],
                'cost_price_at_time' => $menuItem['cost_price'] ?? 0,
                'subtotal' => $subtotal,
            ];
        }

        // Create order
        $orderId = $this->order->create([
            'order_number' => 'TEMP',
            'customer_id' => $customerId,
            'event_date' => $data['event_date'],
            'event_time' => !empty($data['event_time']) ? $data['event_time'] : null,
            'occasion' => $data['occasion'] ?? '',
            'total_price' => $totalPrice,
            'total_cost' => $totalCost,
            'grand_total' => $totalPrice,
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

    public function getTotalRevenue(): float {
        $sql = "SELECT COALESCE(SUM(`total_price`), 0) FROM `orders` WHERE `status` != 'cancelled'";
        $stmt = $this->order->db()->query($sql);
        return (float) $stmt->fetchColumn();
    }

    public function getTotalProfit(): float {
        $sql = "SELECT COALESCE(SUM(`total_price` - `total_cost`), 0) FROM `orders` WHERE `status` != 'cancelled'";
        $stmt = $this->order->db()->query($sql);
        return (float) $stmt->fetchColumn();
    }

    public function getKpis(): array {
        $db = $this->order->db();
        $totalRevenue = (float) $db->query("SELECT COALESCE(SUM(`total_price`), 0) FROM `orders` WHERE `status` != 'cancelled'")->fetchColumn();
        $totalOrders = (int) $db->query("SELECT COUNT(*) FROM `orders`")->fetchColumn();
        $unpaidOrders = (int) $db->query("SELECT COUNT(*) FROM `orders` WHERE `payment_status` = 'unpaid'")->fetchColumn();
        $totalCost = (float) $db->query("SELECT COALESCE(SUM(`total_cost`), 0) FROM `orders` WHERE `status` != 'cancelled'")->fetchColumn();
        $totalProfit = $totalRevenue - $totalCost;
        $avgOrder = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $revenueMonth = (float) $db->query("SELECT COALESCE(SUM(`total_price`), 0) FROM `orders` WHERE `status` != 'cancelled' AND MONTH(`created_at`) = MONTH(CURDATE()) AND YEAR(`created_at`) = YEAR(CURDATE())")->fetchColumn();
        $ordersToday = (int) $db->query("SELECT COUNT(*) FROM `orders` WHERE DATE(`created_at`) = CURDATE()")->fetchColumn();

        return [
            'total_revenue' => $totalRevenue,
            'total_profit' => $totalProfit,
            'total_orders' => $totalOrders,
            'unpaid_orders' => $unpaidOrders,
            'avg_order_value' => $avgOrder,
            'revenue_this_month' => $revenueMonth,
            'orders_today' => $ordersToday,
        ];
    }

    public function getRevenueByPeriod(string $startDate, string $endDate, string $sortBy = 'date', string $dir = 'ASC', int $page = 1, int $perPage = 25): array {
        $allowedSort = ['date', 'orders', 'revenue', 'profit'];
        if (!in_array($sortBy, $allowedSort)) $sortBy = 'date';
        $dir = strtoupper($dir) === 'DESC' ? 'DESC' : 'ASC';

        $where = "WHERE `status` != 'cancelled' AND DATE(`created_at`) BETWEEN ? AND ?";

        // Total rows count (for pagination)
        $countSql = "SELECT COUNT(*) FROM (SELECT DATE(`created_at`) FROM `orders` {$where} GROUP BY DATE(`created_at`)) AS sub";
        $stmt = $this->order->db()->prepare($countSql);
        $stmt->execute([$startDate, $endDate]);
        $total = (int) $stmt->fetchColumn();

        // Totals (all matching rows, not just page)
        $totalsSql = "SELECT COUNT(*) AS `orders`, COALESCE(SUM(`total_price`), 0) AS `revenue`, COALESCE(SUM(`total_price` - `total_cost`), 0) AS `profit` FROM `orders` {$where}";
        $stmt = $this->order->db()->prepare($totalsSql);
        $stmt->execute([$startDate, $endDate]);
        $totals = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Paginated rows
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT DATE(`created_at`) AS `date`, COUNT(*) AS `orders`, COALESCE(SUM(`total_price`), 0) AS `revenue`, COALESCE(SUM(`total_price` - `total_cost`), 0) AS `profit`
FROM `orders`
{$where}
GROUP BY DATE(`created_at`)
ORDER BY `{$sortBy}` {$dir}
LIMIT ? OFFSET ?";
        $stmt = $this->order->db()->prepare($sql);
        $stmt->execute([$startDate, $endDate, $perPage, $offset]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $lastPage = max(1, (int) ceil($total / $perPage));

        return [
            'rows' => $rows,
            'totals' => $totals,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => $lastPage,
            ],
        ];
    }

    public function getTopMenus(int $limit = 5, ?string $startDate = null, ?string $endDate = null): array {
        $where = "o.`status` != 'cancelled'";
        $params = [];
        if ($startDate && $endDate) {
            $where .= " AND DATE(o.`created_at`) BETWEEN ? AND ?";
            $params[] = $startDate;
            $params[] = $endDate;
        }
        $params[] = $limit;
        $sql = "SELECT m.`id`, m.`name`, m.`price`, m.`cost_price`,
SUM(oi.`quantity`) AS `total_qty`,
COALESCE(SUM(oi.`quantity` * oi.`price_at_time`), 0) AS `total_revenue`,
COALESCE(SUM(oi.`quantity` * oi.`cost_price_at_time`), 0) AS `total_cost`
FROM `order_items` oi
INNER JOIN `orders` o ON o.`id` = oi.`order_id` AND {$where}
INNER JOIN `menus` m ON m.`id` = oi.`menu_id`
GROUP BY oi.`menu_id`
ORDER BY `total_revenue` DESC
LIMIT ?";
        $stmt = $this->order->db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getRevenueChartData(int $days = 7): array {
        $endDate = date('Y-m-d');
        $startDate = date('Y-m-d', strtotime("-{$days} days"));
        $result = $this->getRevenueByPeriod($startDate, $endDate);
        $dateMap = [];
        foreach ($result['rows'] as $r) {
            $dateMap[$r['date']] = $r;
        }
        $labels = [];
        $revenue = [];
        $profit = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $label = date('d M', strtotime($date));
            $labels[] = $label;
            if (isset($dateMap[$date])) {
                $revenue[] = (float) $dateMap[$date]['revenue'];
                $profit[] = (float) $dateMap[$date]['profit'];
            } else {
                $revenue[] = 0;
                $profit[] = 0;
            }
        }
        return ['labels' => $labels, 'revenue' => $revenue, 'profit' => $profit];
    }

    public function getOrderStatusBreakdown(): array {
        $db = $this->order->db();
        $paid = (int) $db->query("SELECT COUNT(*) FROM `orders` WHERE `payment_status` = 'paid'")->fetchColumn();
        $unpaid = (int) $db->query("SELECT COUNT(*) FROM `orders` WHERE `payment_status` = 'unpaid'")->fetchColumn();
        $partial = (int) $db->query("SELECT COUNT(*) FROM `orders` WHERE `payment_status` = 'partial'")->fetchColumn();
        $cancelled = (int) $db->query("SELECT COUNT(*) FROM `orders` WHERE `status` = 'cancelled'")->fetchColumn();
        return [
            ['label' => 'Paid', 'value' => $paid, 'color' => '#10b981'],
            ['label' => 'Unpaid', 'value' => $unpaid, 'color' => '#ef4444'],
            ['label' => 'Partial', 'value' => $partial, 'color' => '#e58e26'],
            ['label' => 'Cancelled', 'value' => $cancelled, 'color' => '#6b7280'],
        ];
    }

    public function countByMenuIds(array $menuIds): array {
        return $this->order->countByMenuIds($menuIds);
    }

    public function updateOrder(int $id, array $data): void {
        $updateData = [
            'event_date' => $data['event_date'],
            'event_time' => !empty($data['event_time']) ? $data['event_time'] : null,
            'occasion' => $data['occasion'],
            'delivery_address' => $data['delivery_address'],
            'notes' => $data['notes'] ?? '',
            'status' => $data['status'],
            'payment_status' => $data['payment_status'],
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Billing fields
        if (array_key_exists('tax_rate', $data)) {
            $updateData['tax_rate'] = (float) $data['tax_rate'];
        }
        if (array_key_exists('discount_type', $data)) {
            $updateData['discount_type'] = $data['discount_type'];
        }
        if (array_key_exists('discount_value', $data)) {
            $updateData['discount_value'] = (float) $data['discount_value'];
        }
        if (array_key_exists('payment_method', $data)) {
            $updateData['payment_method'] = $data['payment_method'];
        }
        if (array_key_exists('down_payment', $data)) {
            $updateData['down_payment'] = (float) $data['down_payment'];
        }
        if (array_key_exists('down_payment_due', $data)) {
            $updateData['down_payment_due'] = $data['down_payment_due'] ?: null;
        }

        $order = $this->order->find($id);
        $totalPrice = (float) ($data['total_price'] ?? $order['total_price'] ?? 0);

        // Calculate discount
        $discountAmount = 0;
        $discountType = $updateData['discount_type'] ?? $data['discount_type'] ?? $order['discount_type'] ?? 'none';
        $discountValue = (float) ($updateData['discount_value'] ?? $data['discount_value'] ?? $order['discount_value'] ?? 0);
        if ($discountType === 'percentage' && $discountValue > 0) {
            $discountAmount = $totalPrice * ($discountValue / 100);
        } elseif ($discountType === 'fixed' && $discountValue > 0) {
            $discountAmount = min($discountValue, $totalPrice);
        }
        $updateData['discount_amount'] = $discountAmount;

        // Calculate tax
        $afterDiscount = $totalPrice - $discountAmount;
        $taxRate = (float) ($updateData['tax_rate'] ?? $data['tax_rate'] ?? $order['tax_rate'] ?? 0);
        $taxAmount = $afterDiscount * ($taxRate / 100);
        $updateData['tax_amount'] = $taxAmount;
        $updateData['grand_total'] = $afterDiscount + $taxAmount;

        // Calculate remaining balance (grand_total - down_payment)
        $downPayment = (float) ($updateData['down_payment'] ?? $data['down_payment'] ?? $order['down_payment'] ?? 0);
        $updateData['remaining_balance'] = max(0, $updateData['grand_total'] - $downPayment);

        // Generate invoice number when paid
        if ($data['payment_status'] === 'paid' && empty($order['invoice_number'])) {
            $updateData['invoice_number'] = 'INV-' . date('Ymd') . '-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
        }

        // Set paid_at when marked paid
        if ($data['payment_status'] === 'paid') {
            $updateData['paid_at'] = date('Y-m-d H:i:s');
        }

        $this->order->update($id, $updateData);

        // Update items if provided
        if (!empty($data['items']) && is_array($data['items'])) {
            $newTotals = $this->updateOrderItems($id, $data['items']);
            // Recalculate billing with new total_price
            $data['total_price'] = $newTotals['total_price'];
            $this->order->update($id, [
                'total_price' => $newTotals['total_price'],
                'total_cost' => $newTotals['total_cost'],
                'discount_amount' => $newTotals['discount_amount'],
                'tax_amount' => $newTotals['tax_amount'],
                'grand_total' => $newTotals['grand_total'],
                'remaining_balance' => $newTotals['remaining_balance'],
            ]);
        }

        // Update customer name
        $updatedOrder = $this->order->find($id);
        if ($updatedOrder) {
            $this->customer->update((int)$updatedOrder['customer_id'], [
                'name' => $data['customer_name'],
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function updateOrderItems(int $orderId, array $items): array {
        // Delete existing items
        $this->order->db()->prepare("DELETE FROM `order_items` WHERE `order_id` = ?")->execute([$orderId]);

        // Recalculate
        $totalPrice = 0;
        $totalCost = 0;
        foreach ($items as $item) {
            $menuItem = $this->menu->find((int)$item['menu_id']);
            if (!$menuItem) continue;

            $quantity = max((int)$menuItem['minimum_portions'], (int)$item['quantity']);
            $subtotal = $quantity * (float)$menuItem['price'];
            $costSubtotal = $quantity * (float)($menuItem['cost_price'] ?? 0);
            $totalPrice += $subtotal;
            $totalCost += $costSubtotal;

            $this->order->rawInsertOrderItem($orderId, [
                'menu_id' => $menuItem['id'],
                'quantity' => $quantity,
                'price_at_time' => $menuItem['price'],
                'cost_price_at_time' => $menuItem['cost_price'] ?? 0,
                'subtotal' => $subtotal,
            ]);
        }

        // Recalculate discount/tax based on order's current settings
        $order = $this->order->find($orderId);
        $discountType = $order['discount_type'] ?? 'none';
        $discountValue = (float) ($order['discount_value'] ?? 0);
        $discountAmount = 0;
        if ($discountType === 'percentage' && $discountValue > 0) {
            $discountAmount = $totalPrice * ($discountValue / 100);
        } elseif ($discountType === 'fixed' && $discountValue > 0) {
            $discountAmount = min($discountValue, $totalPrice);
        }

        $afterDiscount = $totalPrice - $discountAmount;
        $taxRate = (float) ($order['tax_rate'] ?? 0);
        $taxAmount = $afterDiscount * ($taxRate / 100);
        $grandTotal = $afterDiscount + $taxAmount;
        $downPayment = (float) ($order['down_payment'] ?? 0);
        $remainingBalance = max(0, $grandTotal - $downPayment);

        return [
            'total_price' => $totalPrice,
            'total_cost' => $totalCost,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'grand_total' => $grandTotal,
            'remaining_balance' => $remainingBalance,
        ];
    }
}
