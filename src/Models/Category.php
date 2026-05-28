<?php
declare(strict_types=1);

namespace App\Models;

class Category extends BaseModel {
    public function __construct() {
        parent::__construct();
        $this->table = 'categories';
        $this->sortableColumns = ['id', 'name', 'slug', 'created_at'];
        $this->searchableColumns = [
            'id',
            'name',
            'slug',
            'created_at',
            'updated_at',
        ];
    }

    public function findBySlug(string $slug): ?array {
        return $this->findWhere(['slug' => $slug]);
    }
}
