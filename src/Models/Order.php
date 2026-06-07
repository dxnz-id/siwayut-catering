<?php
declare(strict_types=1);

namespace App\Models;

class Order extends BaseModel {
    public function __construct() {
        parent::__construct();
        $this->table = 'orders';
        $this->fillable = ['order_number', 'invoice_number', 'customer_id', 'event_date', 'occasion', 'total_price', 'total_cost', 'tax_rate', 'tax_amount', 'discount_type', 'discount_value', 'discount_amount', 'grand_total', 'down_payment', 'down_payment_due', 'remaining_balance', 'delivery_address', 'notes', 'status', 'payment_status', 'payment_method', 'paid_at'];
        $this->sortableColumns = ['id', 'order_number', 'customer_id', 'occasion', 'event_date', 'total_price', 'total_cost', 'grand_total', 'status', 'payment_status', 'payment_method', 'created_at', 'customer_name', 'items_count'];
    }

    public function find(int $id): ?array {
        $sql = "SELECT o.*, c.`name` AS customer_name, c.`phone` AS customer_phone
FROM `{$this->table}` o
INNER JOIN `customers` c ON c.`id` = o.`customer_id`
WHERE o.`id` = ? LIMIT 1";
        $results = $this->query($sql, [$id]);
        return $results[0] ?? null;
    }

    public function findByOrderNumber(string $orderNumber): ?array {
        $sql = "SELECT o.*, c.`name` AS customer_name, c.`phone` AS customer_phone
FROM `{$this->table}` o
INNER JOIN `customers` c ON c.`id` = o.`customer_id`
WHERE o.`order_number` = ? LIMIT 1";
        $results = $this->query($sql, [$orderNumber]);
        return $results[0] ?? null;
    }

    public function getByCustomerId(int $customerId): array {
        $sql = "SELECT o.*, c.`name` AS customer_name, c.`phone` AS customer_phone
FROM `{$this->table}` o
INNER JOIN `customers` c ON c.`id` = o.`customer_id`
WHERE o.`customer_id` = ?
ORDER BY o.`created_at` DESC";
        return $this->query($sql, [$customerId]);
    }

    public function getItemsByOrderId(int $orderId): array {
        $sql = "SELECT oi.*, m.`name` AS menu_name, m.`image` AS menu_image
FROM `order_items` oi
INNER JOIN `menus` m ON m.`id` = oi.`menu_id`
WHERE oi.`order_id` = ?
ORDER BY oi.`id` ASC";
        return $this->query($sql, [$orderId]);
    }

    public function rawInsertOrderItem(int $orderId, array $item): void {
        $sql = "INSERT INTO `order_items` (`order_id`, `menu_id`, `quantity`, `price_at_time`, `cost_price_at_time`, `subtotal`) VALUES (?, ?, ?, ?, ?, ?)";
        $this->execute($sql, [$orderId, $item['menu_id'], $item['quantity'], $item['price_at_time'], $item['cost_price_at_time'] ?? 0, $item['subtotal']]);
    }

    public function getOrdersByMenuId(int $menuId, int $limit = 10): array {
        $sql = "SELECT o.*, c.`name` AS customer_name
FROM `orders` o
INNER JOIN `customers` c ON c.`id` = o.`customer_id`
INNER JOIN `order_items` oi ON oi.`order_id` = o.`id`
WHERE oi.`menu_id` = ?
ORDER BY o.`created_at` DESC
LIMIT ?";
        return $this->query($sql, [$menuId, $limit]);
    }

    public function countByMenuIds(array $menuIds): array {
        if (empty($menuIds)) return [];
        $placeholders = implode(',', array_fill(0, count($menuIds), '?'));
        $sql = "SELECT oi.menu_id, COUNT(*) as cnt FROM `order_items` oi WHERE oi.menu_id IN ($placeholders) GROUP BY oi.menu_id";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute(array_map('intval', $menuIds));
        $result = [];
        foreach ($stmt->fetchAll() as $row) {
            $result[(int)$row['menu_id']] = (int)$row['cnt'];
        }
        return $result;
    }

    public function getAllForExport(array $filters, string $search, string $orderBy = 'created_at', string $direction = 'DESC'): array {
        $q = $this->buildAdminQuery($filters, $search, $orderBy, $direction);

        $sql = 'SELECT o.*, c.`name` AS customer_name, c.`phone` AS customer_phone'
            . ', (SELECT COUNT(*) FROM `order_items` oi3 WHERE oi3.`order_id` = o.`id`) AS item_cnt'
            . $q['from'] . $q['where']
            . " ORDER BY {$q['orderCol']} {$q['direction']}";

        return $this->query($sql, $q['bindings']);
    }

    public function paginateForAdmin(
        int $page = 1,
        int $perPage = 10,
        array $filters = [],
        string $search = '',
        string $orderBy = 'created_at',
        string $direction = 'DESC'
    ): array {
        $page = max(1, $page);
        $perPage = max(1, $perPage);

        $q = $this->buildAdminQuery($filters, $search, $orderBy, $direction);

        $countSql = 'SELECT COUNT(*)' . $q['from'] . $q['where'];
        $stmt = $this->db()->prepare($countSql);
        $stmt->execute($q['bindings']);
        $total = (int) $stmt->fetchColumn();

        $lastPage = (int) ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;

        $sql = 'SELECT o.*, c.`name` AS customer_name, c.`phone` AS customer_phone'
            . ', (SELECT COUNT(*) FROM `order_items` oi3 WHERE oi3.`order_id` = o.`id`) AS item_cnt'
            . $q['from'] . $q['where']
            . " ORDER BY {$q['orderCol']} {$q['direction']} LIMIT {$perPage} OFFSET {$offset}";

        return [
            'data' => $this->query($sql, $q['bindings']),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => $lastPage,
        ];
    }

    /**
     * Shared query builder for admin order listing.
     * Returns ['from', 'where', 'bindings', 'orderCol', 'direction'].
     */
    private function buildAdminQuery(array $filters, string $search, string $orderBy, string $direction): array {
        $bindings = [];
        $clauses = [];

        if (!empty($filters['status'])) {
            $clauses[] = 'o.`status` = ?';
            $bindings[] = $filters['status'];
        }

        if (!empty($filters['payment_status'])) {
            $clauses[] = 'o.`payment_status` = ?';
            $bindings[] = $filters['payment_status'];
        }

        if ($search !== '') {
            $searchLike = '%' . $search . '%';
            $clauses[] = '(' . implode(' OR ', [
                'CAST(o.`id` AS CHAR) LIKE ?',
                'o.`order_number` LIKE ?',
                'CAST(o.`customer_id` AS CHAR) LIKE ?',
                'o.`event_date` LIKE ?',
                'CAST(o.`total_price` AS CHAR) LIKE ?',
                'o.`status` LIKE ?',
                'o.`payment_status` LIKE ?',
                'o.`delivery_address` LIKE ?',
                'o.`notes` LIKE ?',
                'o.`created_at` LIKE ?',
                'o.`updated_at` LIKE ?',
                'c.`name` LIKE ?',
                'c.`phone` LIKE ?',
                'c.`email` LIKE ?',
                'c.`address` LIKE ?',
                'c.`notes` LIKE ?',
                'EXISTS (SELECT 1 FROM `order_items` oi2 INNER JOIN `menus` m2 ON m2.`id` = oi2.`menu_id` WHERE oi2.`order_id` = o.`id` AND m2.`name` LIKE ?)',
            ]) . ')';
            for ($i = 0; $i < 17; $i++) {
                $bindings[] = $searchLike;
            }
            $bindings[] = $searchLike;
        }

        $where = '';
        if (!empty($clauses)) {
            $where = ' WHERE ' . implode(' AND ', $clauses);
        }

        $from = ' FROM `orders` o
            INNER JOIN `customers` c ON c.`id` = o.`customer_id`';

        if (!in_array($orderBy, $this->sortableColumns, true)) {
            $orderBy = 'created_at';
        }
        $direction = strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC';

        $sortMap = [
            'customer_name' => 'c.`name`',
            'items_count'   => 'item_cnt',
        ];

        $orderCol = $sortMap[$orderBy] ?? "o.`{$orderBy}`";

        return [
            'from' => $from,
            'where' => $where,
            'bindings' => $bindings,
            'orderCol' => $orderCol,
            'direction' => $direction,
        ];
    }
}
