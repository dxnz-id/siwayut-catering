<?php
declare(strict_types=1);

namespace Database\Seeds;

class OrderSeeder {
    public function __construct(private \PDO $db) {}

    public function run(): void {
        $events = $this->db->query("SELECT id FROM events")->fetchAll(\PDO::FETCH_COLUMN);
        $menus = $this->db->query("SELECT id, price FROM menus")->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($events) || empty($menus)) {
            echo "Error: Events or Menus table is empty. Please run MenuSeeder first.\n";
            return;
        }

        $this->db->query("SET FOREIGN_KEY_CHECKS = 0");
        $this->db->query("TRUNCATE TABLE orders");
        $this->db->query("TRUNCATE TABLE customers");
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1");

        $customers = [
            ['name' => 'Budi Santoso', 'phone' => '081234567890', 'email' => 'budi.santoso@example.com', 'address' => 'Jl. Merdeka No. 10, Jakarta', 'notes' => 'Customer VIP'],
            ['name' => 'Siti Aminah', 'phone' => '089876543210', 'email' => 'siti.aminah@example.com', 'address' => 'Jl. Sudirman No. 25, Bandung', 'notes' => ''],
            ['name' => 'Agus Pratama', 'phone' => '085612349876', 'email' => 'agus.p@example.com', 'address' => 'Perumahan Indah Blok C2, Surabaya', 'notes' => 'Tolong hubungi sebelum pengiriman'],
            ['name' => 'Rina Melati', 'phone' => '087711223344', 'email' => 'rina.melati@example.com', 'address' => 'Jl. Mawar 5, Yogyakarta', 'notes' => ''],
        ];

        $customerIds = [];
        $stmtCustomer = $this->db->prepare("INSERT INTO customers (name, phone, email, address, notes, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)");

        foreach ($customers as $c) {
            $now = date('Y-m-d H:i:s');
            $stmtCustomer->execute([$c['name'], $c['phone'], $c['email'], $c['address'], $c['notes'], $now, $now]);
            $customerIds[] = $this->db->lastInsertId();
        }
        echo "Customers seeded successfully.\n";

        $statuses = ['pending', 'processing', 'delivering', 'completed', 'cancelled'];

        $stmtOrder = $this->db->prepare("INSERT INTO orders (customer_id, event_id, menu_id, event_date, quantity, total_price, delivery_address, notes, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        for ($i = 0; $i < 15; $i++) {
            $customerId = $customerIds[array_rand($customerIds)];
            $eventId = $events[array_rand($events)];
            $menu = $menus[array_rand($menus)];
            $menuId = $menu['id'];
            $menuPrice = $menu['price'];

            $quantity = rand(5, 50) * 10;
            $totalPrice = $quantity * $menuPrice;
            $daysOffset = rand(1, 30);
            $eventDate = date('Y-m-d H:i:s', strtotime("+$daysOffset days"));
            $status = $statuses[array_rand($statuses)];

            $notesOptions = [
                'Tolong dikirim tepat waktu ya',
                'Makanan jangan terlalu pedas',
                'Tolong packing yang rapi',
                '',
                '',
                'Koordinasi dengan satpam depan saat pengiriman'
            ];
            $notes = $notesOptions[array_rand($notesOptions)];

            $stmtAddr = $this->db->prepare("SELECT address FROM customers WHERE id = ?");
            $stmtAddr->execute([$customerId]);
            $deliveryAddress = $stmtAddr->fetchColumn();

            if (rand(1, 10) > 8) {
                $deliveryAddress = "Gedung Serbaguna, " . $deliveryAddress;
            }

            $now = date('Y-m-d H:i:s', strtotime("-" . rand(0, 10) . " days"));

            $stmtOrder->execute([
                $customerId, $eventId, $menuId, $eventDate, $quantity, $totalPrice,
                $deliveryAddress, $notes, $status, $now, $now
            ]);
        }
        echo "15 Orders seeded successfully.\n";
    }
}
