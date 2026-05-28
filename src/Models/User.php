<?php
declare(strict_types=1);
// File: src/Models/User.php

namespace App\Models;

class User extends BaseModel {
    public function __construct() {
        parent::__construct();
        $this->table = 'users';
        $this->sortableColumns = ['id', 'name', 'email', 'role', 'created_at', 'updated_at'];
        $this->searchableColumns = [
            'id',
            'name',
            'email',
            'role',
            'created_at',
            'updated_at',
        ];
    }

    public function findByEmail(string $email): ?array {
        return $this->findWhere(['email' => $email]);
    }
}
