<?php
declare(strict_types=1);
// File: src/Models/BaseModel.php

namespace App\Models;
use PDO;
use App\Core\Database;

abstract class BaseModel {
    protected ?PDO $db = null;
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $sortableColumns = ['id', 'created_at', 'updated_at'];
    protected array $searchableColumns = [];

    public function __construct() {
        // DB connection deferred — initialized lazily on first query
    }

    protected function db(): PDO {
        if ($this->db === null) {
            $this->db = Database::getInstance();
        }
        return $this->db;
    }

    public function all(array $conditions = [], string $orderBy = 'created_at', string $direction = 'DESC'): array {
        $orderBy = $this->validateSortColumn($orderBy);
        $direction = strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC';
        $sql = "SELECT * FROM `{$this->table}`";
        $bindings = [];

        if (!empty($conditions)) {
            $clauses = [];
            foreach ($conditions as $col => $val) {
                $clauses[] = "`{$col}` = ?";
                $bindings[] = $val;
            }
            $sql .= ' WHERE ' . implode(' AND ', $clauses);
        }

        $sql .= " ORDER BY `{$orderBy}` {$direction}";
        return $this->query($sql, $bindings);
    }

    public function find(int $id): ?array {
        $sql = "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = ? LIMIT 1";
        $results = $this->query($sql, [$id]);
        return $results[0] ?? null;
    }

    public function findWhere(array $conditions): ?array {
        $clauses = [];
        $bindings = [];
        foreach ($conditions as $col => $val) {
            $clauses[] = "`{$col}` = ?";
            $bindings[] = $val;
        }
        $sql = "SELECT * FROM `{$this->table}` WHERE " . implode(' AND ', $clauses) . " LIMIT 1";
        $results = $this->query($sql, $bindings);
        return $results[0] ?? null;
    }

    public function where(array $conditions, string $orderBy = 'created_at', string $direction = 'DESC'): array {
        $orderBy = $this->validateSortColumn($orderBy);
        $direction = strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC';
        $clauses = [];
        $bindings = [];
        foreach ($conditions as $col => $val) {
            $clauses[] = "`{$col}` = ?";
            $bindings[] = $val;
        }
        $sql = "SELECT * FROM `{$this->table}` WHERE " . implode(' AND ', $clauses) . " ORDER BY `{$orderBy}` {$direction}";
        return $this->query($sql, $bindings);
    }

    public function create(array $data): int {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        $sql = sprintf(
            "INSERT INTO `%s` (`%s`) VALUES (%s)",
            $this->table,
            implode('`, `', $columns),
            implode(', ', $placeholders)
        );
        $this->execute($sql, array_values($data));
        return (int) $this->db()->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $setClauses = [];
        $bindings = [];
        foreach ($data as $col => $val) {
            $setClauses[] = "`{$col}` = ?";
            $bindings[] = $val;
        }
        $bindings[] = $id;
        $sql = sprintf(
            "UPDATE `%s` SET %s WHERE `%s` = ?",
            $this->table,
            implode(', ', $setClauses),
            $this->primaryKey
        );
        return $this->execute($sql, $bindings);
    }

    public function delete(int $id): bool {
        $sql = "DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?";
        return $this->execute($sql, [$id]);
    }

    public function count(array $conditions = []): int {
        $sql = "SELECT COUNT(*) FROM `{$this->table}`";
        $bindings = [];
        if (!empty($conditions)) {
            $clauses = [];
            foreach ($conditions as $col => $val) {
                $clauses[] = "`{$col}` = ?";
                $bindings[] = $val;
            }
            $sql .= ' WHERE ' . implode(' AND ', $clauses);
        }
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($bindings);
        return (int) $stmt->fetchColumn();
    }

    public function exists(array $conditions): bool {
        return $this->count($conditions) > 0;
    }

    public function paginate(int $page = 1, int $perPage = 15, array $conditions = [], string $search = '', array $searchColumns = [], string $orderBy = 'created_at', string $direction = 'DESC'): array {
        $page = max(1, $page);
        $bindings = [];
        $where = '';

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
        $sql = "SELECT * FROM `{$this->table}`{$where} ORDER BY `{$orderBy}` {$direction} LIMIT {$perPage} OFFSET {$offset}";

        return [
            'data' => $this->query($sql, $bindings),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => $lastPage,
        ];
    }

    protected function query(string $sql, array $bindings = []): array {
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetchAll();
    }

    protected function execute(string $sql, array $bindings = []): bool {
        $stmt = $this->db()->prepare($sql);
        return $stmt->execute($bindings);
    }

    public function getSearchableColumns(): array {
        return $this->searchableColumns;
    }

    private function validateSortColumn(string $column): string {
        if (in_array($column, $this->sortableColumns, true)) {
            return $column;
        }
        return 'created_at';
    }
}
