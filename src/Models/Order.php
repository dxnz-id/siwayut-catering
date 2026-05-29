<?php
declare(strict_types=1);

namespace App\Models;

class Order extends BaseModel {
    public function __construct() {
        parent::__construct();
        $this->table = 'orders';
        $this->sortableColumns = ['id', 'customer_id', 'event_id', 'event_date', 'total_price', 'status', 'payment_status', 'created_at', 'customer_name', 'items_count'];
    }

    public function find(int $id): ?array {
        $sql = "SELECT o.*, c.`name` AS customer_name, c.`phone` AS customer_phone
FROM `{$this->table}` o
INNER JOIN `customers` c ON c.`id` = o.`customer_id`
WHERE o.`id` = ? LIMIT 1";
        $results = $this->query($sql, [$id]);
        return $results[0] ?? null;
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
        $sql = "INSERT INTO `order_items` (`order_id`, `menu_id`, `quantity`, `price_at_time`, `subtotal`) VALUES (?, ?, ?, ?, ?)";
        $this->execute($sql, [$orderId, $item['menu_id'], $item['quantity'], $item['price_at_time'], $item['subtotal']]);
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
                'CAST(o.`customer_id` AS CHAR) LIKE ?',
                'CAST(o.`event_id` AS CHAR) LIKE ?',
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
            for ($i = 0; $i < 16; $i++) {
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

        $countSql = 'SELECT COUNT(*)' . $from . $where;
        $stmt = $this->db()->prepare($countSql);
        $stmt->execute($bindings);
        $total = (int) $stmt->fetchColumn();

        $lastPage = (int) ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;

        if (!in_array($orderBy, $this->sortableColumns, true)) {
            $orderBy = 'created_at';
        }
        $direction = strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC';

        $sortMap = [
            'customer_name' => 'c.`name`',
            'items_count'   => 'item_cnt',
        ];

        if (isset($sortMap[$orderBy])) {
            $orderCol = $sortMap[$orderBy];
        } else {
            $orderCol = "o.`{$orderBy}`";
        }

        $sql = 'SELECT o.*, c.`name` AS customer_name, c.`phone` AS customer_phone'
            . ', (SELECT COUNT(*) FROM `order_items` oi3 WHERE oi3.`order_id` = o.`id`) AS item_cnt'
            . $from . $where
            . " ORDER BY {$orderCol} {$direction} LIMIT {$perPage} OFFSET {$offset}";

        return [
            'data' => $this->query($sql, $bindings),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => $lastPage,
        ];
    }
}
