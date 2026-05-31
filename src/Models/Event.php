<?php
declare(strict_types=1);

namespace App\Models;

class Event extends BaseModel {
    public function __construct() {
        parent::__construct();
        $this->table = 'events';
        $this->fillable = ['name', 'start_date', 'end_date', 'status'];
        $this->sortableColumns = ['id', 'name', 'start_date', 'end_date', 'status', 'created_at', 'menu_count'];
        $this->searchableColumns = [
            'id',
            'name',
            'start_date',
            'end_date',
            'status',
            'created_at',
            'updated_at',
        ];
    }

    public function getActive(): array {
        return $this->query("SELECT * FROM `{$this->table}` WHERE `status` = 'active' ORDER BY `start_date` ASC");
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
        $sql = "SELECT *, (SELECT COUNT(*) FROM `menus` WHERE `event_id` = `{$this->table}`.`id`) AS `menu_count` FROM `{$this->table}`{$where} ORDER BY `{$orderBy}` {$direction} LIMIT {$perPage} OFFSET {$offset}";

        return [
            'data' => $this->query($sql, $bindings),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => $lastPage,
        ];
    }
}
