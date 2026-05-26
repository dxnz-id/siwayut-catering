<?php
declare(strict_types=1);

namespace App\Models;

class Menu extends BaseModel {
    public function __construct() {
        parent::__construct();
        $this->table = 'menus';
        $this->sortableColumns = ['id', 'name', 'price', 'category_id', 'status', 'created_at'];
    }
}
