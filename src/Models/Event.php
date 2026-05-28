<?php
declare(strict_types=1);

namespace App\Models;

class Event extends BaseModel {
    public function __construct() {
        parent::__construct();
        $this->table = 'events';
        $this->sortableColumns = ['id', 'name', 'start_date', 'end_date', 'status', 'created_at'];
        $this->searchableColumns = [
            'id',
            'name',
            'start_date',
            'end_date',
            'status',
            'created_at',
            'updated_at',
        ];
    }

    public function getActive(): array {
        return $this->query("SELECT * FROM `{$this->table}` WHERE `status` = 'active' ORDER BY `start_date` ASC");
    }
}
