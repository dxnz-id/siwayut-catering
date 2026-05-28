<?php
declare(strict_types=1);

namespace App\Models;

class Order extends BaseModel {
    public function __construct() {
        parent::__construct();
        $this->table = 'orders';
        $this->sortableColumns = ['id', 'customer_id', 'menu_id', 'event_date', 'quantity', 'total_price', 'status', 'payment_status', 'created_at'];
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
                'CAST(o.`menu_id` AS CHAR) LIKE ?',
                'o.`event_date` LIKE ?',
                'CAST(o.`quantity` AS CHAR) LIKE ?',
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
                'm.`name` LIKE ?',
                'm.`description` LIKE ?',
                'CAST(m.`price` AS CHAR) LIKE ?',
                'CAST(m.`minimum_portions` AS CHAR) LIKE ?',
                'm.`status` LIKE ?',
                'm.`image` LIKE ?',
            ]) . ')';
            for ($i = 0; $i < 24; $i++) {
                $bindings[] = $searchLike;
            }
        }

        $where = '';
        if (!empty($clauses)) {
            $where = ' WHERE ' . implode(' AND ', $clauses);
        }

        $from = ' FROM `orders` o
            INNER JOIN `customers` c ON c.`id` = o.`customer_id`
            INNER JOIN `menus` m ON m.`id` = o.`menu_id`';

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

        $sql = 'SELECT o.*' . $from . $where
            . " ORDER BY o.`{$orderBy}` {$direction} LIMIT {$perPage} OFFSET {$offset}";

        return [
            'data' => $this->query($sql, $bindings),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => $lastPage,
        ];
    }
}
