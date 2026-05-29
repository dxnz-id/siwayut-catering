<?php
declare(strict_types=1);

namespace App\Models;

class Customer extends BaseModel {
    public function __construct() {
        parent::__construct();
        $this->table = 'customers';
        $this->sortableColumns = ['id', 'customer_code', 'name', 'phone', 'created_at'];
        $this->searchableColumns = ['id', 'customer_code', 'name', 'phone', 'email', 'address'];
    }

    public function create(array $data): int {
        $id = parent::create($data);
        if ($id > 0) {
            $code = 'CST-' . date('ym') . '-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
            $this->update($id, ['customer_code' => $code]);
        }
        return $id;
    }

    public function findByPhone(string $phone): ?array {
        return $this->findWhere(['phone' => $phone]);
    }

    public function linkUserByPhone(string $phone, int $userId): bool {
        $sql = "UPDATE `{$this->table}` SET `user_id` = ?, `updated_at` = CURRENT_TIMESTAMP WHERE `phone` = ?";
        return $this->execute($sql, [$userId, $phone]);
    }
}
