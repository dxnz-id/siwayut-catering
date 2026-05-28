<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Menu;

class MenuService {
    public function __construct(
        private Menu $menu,
        private FileUploadService $fileUpload
    ) {}

    public function all(): array {
        return $this->menu->all();
    }

    public function paginate(int $page = 1, int $perPage = 15, array $conditions = [], string $search = '', array $searchColumns = [], string $orderBy = 'created_at', string $direction = 'DESC'): array {
        if (empty($searchColumns)) {
            $searchColumns = $this->menu->getSearchableColumns();
        }
        return $this->menu->paginate($page, $perPage, $conditions, $search, $searchColumns, $orderBy, $direction);
    }

    public function find(int $id): ?array {
        return $this->menu->find($id);
    }

    public function create(array $data, ?array $gambar = null): int {
        $imagePath = null;
        if ($gambar && $gambar['error'] !== UPLOAD_ERR_NO_FILE) {
            $imagePath = $this->fileUpload->upload($gambar, 'menus');
        }

        return $this->menu->create([
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'price' => $data['price'],
            'category_id' => $data['category_id'],
            'event_id' => $data['event_id'],
            'minimum_portions' => $data['minimum_portions'] ?? 1,
            'image' => $imagePath,
            'status' => $data['status'] ?? 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function update(int $id, array $data, ?array $gambar = null): bool {
        $updateData = [
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'price' => $data['price'],
            'category_id' => $data['category_id'],
            'event_id' => $data['event_id'],
            'minimum_portions' => $data['minimum_portions'] ?? 1,
            'status' => $data['status'] ?? 'active',
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($gambar && $gambar['error'] !== UPLOAD_ERR_NO_FILE) {
            $menu = $this->menu->find($id);
            if ($menu && $menu['image']) {
                $this->fileUpload->delete($menu['image']);
            }
            $updateData['image'] = $this->fileUpload->upload($gambar, 'menus');
        }

        return $this->menu->update($id, $updateData);
    }

    public function delete(int $id): bool {
        $oldMenu = $this->find($id);
        if ($oldMenu && $oldMenu['image']) {
            $this->fileUpload->delete($oldMenu['image']);
        }
        return $this->menu->delete($id);
    }
}
