<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Category;

class CategoryService {
    public function __construct(private Category $category) {}

    public function all(string $orderBy = 'created_at', string $direction = 'DESC'): array {
        return $this->category->all([], $orderBy, $direction);
    }

    public function paginate(
        int $page = 1,
        int $perPage = 15,
        string $search = '',
        string $orderBy = 'created_at',
        string $direction = 'DESC'
    ): array {
        return $this->category->paginate($page, $perPage, [], $search, $this->category->getSearchableColumns(), $orderBy, $direction);
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
