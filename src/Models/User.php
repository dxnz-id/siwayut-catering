<?php
declare(strict_types=1);
// File: src/Models/User.php

namespace App\Models;

class User extends BaseModel {
    public function __construct() {
        parent::__construct();
        $this->table = 'users';
        $this->fillable = ['user_code', 'name', 'email', 'password', 'role'];
        $this->sortableColumns = ['id', 'user_code', 'name', 'email', 'role', 'created_at', 'updated_at'];
        $this->searchableColumns = [
            'id',
            'user_code',
            'name',
            'email',
            'role',
            'created_at',
            'updated_at',
        ];
    }

    public function create(array $data): int {
        $data['user_code'] = $data['user_code'] ?? 'TEMP';
        $id = parent::create($data);
        if ($id > 0) {
            $code = 'USR-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
            $this->update($id, ['user_code' => $code]);
        }
        return $id;
    }

    public function findByEmail(string $email): ?array {
        return $this->findWhere(['email' => $email]);
    }
}
