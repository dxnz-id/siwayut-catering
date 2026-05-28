<?php
declare(strict_types=1);

namespace Database\Seeds;

class DatabaseSeeder {
    public function __construct(
        private AdminSeeder $adminSeeder,
        private MenuSeeder  $menuSeeder,
        private OrderSeeder $orderSeeder,
    ) {}

    public function run(): void {
        $this->adminSeeder->run();
        $this->menuSeeder->run();
        $this->orderSeeder->run();
    }
}
