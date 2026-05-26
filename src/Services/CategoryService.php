<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Category;

class CategoryService {
    public function __construct(private Category $category) {}

    public function all(): array {
        return $this->category->all();
    }

    public function find(int $id): ?array {
        return $this->category->find($id);
    }

    public function create(array $data): int {
        return $this->category->create([
            'name' => $data['name'],
            'slug' => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['name']))),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function update(int $id, array $data): bool {
        return $this->category->update($id, [
            'name' => $data['name'],
            'slug' => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['name']))),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function delete(int $id): bool {
        return $this->category->delete($id);
    }
}
