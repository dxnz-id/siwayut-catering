<?php
declare(strict_types=1);

namespace App\Models;

class Menu extends BaseModel {
    public function __construct() {
        parent::__construct();
        $this->table = 'menus';
        $this->fillable = ['menu_code', 'name', 'description', 'price', 'category_id', 'event_id', 'minimum_portions', 'image', 'status'];
        $this->sortableColumns = ['id', 'menu_code', 'name', 'price', 'category_id', 'status', 'created_at', 'order_count'];
        $this->searchableColumns = [
            'id',
            'menu_code',
            'name',
            'description',
            'price',
            'category_id',
            'event_id',
            'minimum_portions',
            'image',
            'status',
            'created_at',
            'updated_at',
        ];
    }

    public function findByMenuCode(string $code): ?array {
        return $this->findWhere(['menu_code' => $code]);
    }

    public function create(array $data): int {
        $data['menu_code'] = $data['menu_code'] ?? 'TEMP';
        $id = parent::create($data);
        if ($id > 0) {
            $code = 'MNU-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
            $this->update($id, ['menu_code' => $code]);
        }
        return $id;
    }

    public function countByCategoryIds(array $ids): array {
        if (empty($ids)) return [];
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT category_id, COUNT(*) as cnt FROM `menus` WHERE category_id IN ($placeholders) GROUP BY category_id";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute(array_map('intval', $ids));
        $result = [];
        foreach ($stmt->fetchAll() as $row) {
            $result[(int)$row['category_id']] = (int)$row['cnt'];
        }
        return $result;
    }

    public function paginate(int $page = 1, int $perPage = 15, array $conditions = [], string $search = '', array $searchColumns = [], string $orderBy = 'created_at', string $direction = 'DESC'): array {
        $page = max(1, $page);
        $bindings = [];
        $clauses = [];

        foreach ($conditions as $col => $val) {
            if ($val !== '' && $val !== null) {
                $clauses[] = "`{$col}` = ?";
                $bindings[] = $val;
            }
        }

        if ($search !== '' && !empty($searchColumns)) {
            $likeClauses = [];
            foreach ($searchColumns as $col) {
                $likeClauses[] = "`{$col}` LIKE ?";
                $bindings[] = "%{$search}%";
            }
            $clauses[] = '(' . implode(' OR ', $likeClauses) . ')';
        }

        $where = '';
        if (!empty($clauses)) {
            $where = ' WHERE ' . implode(' AND ', $clauses);
        }

        $countSql = "SELECT COUNT(*) FROM `{$this->table}`{$where}";
        $stmt = $this->db()->prepare($countSql);
        $stmt->execute($bindings);
        $total = (int) $stmt->fetchColumn();

        $lastPage = (int) ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;

        $orderBy = $this->validateSortColumn($orderBy);
        $direction = strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC';
        $sql = "SELECT *, (SELECT COUNT(*) FROM `order_items` WHERE `menu_id` = `{$this->table}`.`id`) AS `order_count` FROM `{$this->table}`{$where} ORDER BY `{$orderBy}` {$direction} LIMIT {$perPage} OFFSET {$offset}";

        return [
            'data' => $this->query($sql, $bindings),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => $lastPage,
        ];
    }
}
