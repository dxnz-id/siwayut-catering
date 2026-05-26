<?php
declare(strict_types=1);

namespace App\Models;

class Order extends BaseModel {
    public function __construct() {
        parent::__construct();
        $this->table = 'orders';
        $this->sortableColumns = ['id', 'customer_id', 'menu_id', 'event_date', 'total_price', 'status', 'created_at'];
    }
}
