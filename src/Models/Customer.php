<?php
declare(strict_types=1);

namespace App\Models;

class Customer extends BaseModel {
    public function __construct() {
        parent::__construct();
        $this->table = 'customers';
        $this->sortableColumns = ['id', 'name', 'phone', 'created_at'];
    }

    public function findByPhone(string $phone): ?array {
        return $this->findWhere(['phone' => $phone]);
    }
}
