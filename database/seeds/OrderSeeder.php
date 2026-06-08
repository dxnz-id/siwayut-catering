<?php
declare(strict_types=1);

namespace Database\Seeds;

class OrderSeeder {
    public function __construct(private \PDO $db) {}

    public function run(): void {
        $menus = $this->db->query("SELECT id, price, name FROM menus")->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($menus)) {
            echo "Error: Menus table is empty. Please run MenuSeeder first.\n";
            return;
        }

        $this->db->query("SET FOREIGN_KEY_CHECKS = 0");
        $this->db->query("TRUNCATE TABLE order_items");
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
        $stmtCustomer = $this->db->prepare("INSERT INTO customers (name, phone, email, address, notes, created_at, updated_at, customer_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        foreach ($customers as $idx => $c) {
            $now = date('Y-m-d H:i:s');
            $code = 'CST-' . date('ym') . '-' . str_pad((string)($idx + 1), 4, '0', STR_PAD_LEFT);
            $stmtCustomer->execute([$c['name'], $c['phone'], $c['email'], $c['address'], $c['notes'], $now, $now, $code]);
            $customerIds[] = $this->db->lastInsertId();
        }
        echo "Customers seeded successfully.\n";

        $statuses = ['pending', 'processing', 'delivering', 'completed', 'cancelled'];
        $occasionOptions = ['birthday', 'wedding', 'corporate', 'family', 'arisan', 'khitanan', 'Ulang Tahun Perusahaan', 'Wisuda', 'Syukuran Rumah'];

        $stmtOrder = $this->db->prepare("INSERT INTO orders (customer_id, event_date, event_time, total_price, delivery_address, notes, status, occasion, created_at, updated_at, order_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmtItem = $this->db->prepare("INSERT INTO order_items (order_id, menu_id, quantity, price_at_time, subtotal) VALUES (?, ?, ?, ?, ?)");

        for ($i = 0; $i < 15; $i++) {
            $customerId = $customerIds[array_rand($customerIds)];
            $occasion = $occasionOptions[array_rand($occasionOptions)];
            $menu = $menus[array_rand($menus)];
            $menuId = $menu['id'];
            $menuPrice = $menu['price'];

            $quantity = rand(5, 50) * 10;
            $totalPrice = $quantity * $menuPrice;
            $daysOffset = rand(1, 30);
            $eventDateBase = strtotime("+$daysOffset days");
            $hour = rand(1, 5) === 1 ? null : rand(8, 20);
            $minute = $hour !== null ? (rand(0, 3) === 0 ? 0 : rand(0, 59)) : 0;
            $eventDate = date('Y-m-d', $eventDateBase);
            $eventTime = $hour !== null ? sprintf('%02d:%02d:00', $hour, $minute) : null;
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
            $orderNumber = 'ORD-' . date('Ymd', strtotime($now)) . '-' . str_pad((string)($i + 1), 4, '0', STR_PAD_LEFT);

            $stmtOrder->execute([
                $customerId, $eventDate, $eventTime, $totalPrice,
                $deliveryAddress, $notes, $status, $occasion, $now, $now, $orderNumber
            ]);

            $orderId = (int) $this->db->lastInsertId();

            $stmtItem->execute([$orderId, $menuId, $quantity, $menuPrice, $totalPrice]);
        }
        echo "15 Orders seeded successfully.\n";
    }
}
