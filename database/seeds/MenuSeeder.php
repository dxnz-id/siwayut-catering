<?php
declare(strict_types=1);
// File: database/seeds/MenuSeeder.php

namespace Database\Seeds;

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 2));
}

require BASE_PATH . '/vendor/autoload.php';

if (file_exists(BASE_PATH . '/.env')) {
    $env = parse_ini_file(BASE_PATH . '/.env');
    if ($env !== false) {
        foreach ($env as $key => $value) {
            $_ENV[$key] = $value;
        }
    }
}

if (!defined('APP_NAME')) {
    require BASE_PATH . '/config/app.php';
}

use App\Core\Database;

class MenuSeeder {
    public static function run(): void {
        $db = Database::getInstance();

        // Clear existing records to prevent duplicates and integrity check errors
        echo "Clearing existing menus, events, and categories...\n";
        $db->exec("SET FOREIGN_KEY_CHECKS = 0");
        $db->exec("TRUNCATE TABLE `menus`");
        $db->exec("TRUNCATE TABLE `events`");
        $db->exec("TRUNCATE TABLE `categories`");
        $db->exec("SET FOREIGN_KEY_CHECKS = 1");

        // 1. Seed Categories
        $categories = [
            ['name' => 'Paket Katering', 'slug' => 'paket-katering'],
            ['name' => 'Menu Ala Carte', 'slug' => 'menu-ala-carte'],
            ['name' => 'Snack & Kue Kering', 'slug' => 'snack-kue-kering'],
            ['name' => 'Minuman Spesial', 'slug' => 'minuman-spesial'],
        ];

        echo "Seeding categories...\n";
        $stmtCategory = $db->prepare("INSERT INTO `categories` (`name`, `slug`, `created_at`, `updated_at`) VALUES (?, ?, NOW(), NOW())");
        $catIds = [];
        foreach ($categories as $kat) {
            $stmtCategory->execute([$kat['name'], $kat['slug']]);
            $catIds[$kat['slug']] = $db->lastInsertId();
            echo "  Created Category: {$kat['name']}\n";
        }

        // 2. Seed Events (Hari Raya)
        $events = [
            [
                'name' => 'Idul Fitri 2026',
                'start_date' => '2026-03-15',
                'end_date' => '2026-03-25',
                'status' => 'active'
            ],
            [
                'name' => 'Natal & Tahun Baru 2026',
                'start_date' => '2026-12-20',
                'end_date' => '2027-01-05',
                'status' => 'active'
            ],
            [
                'name' => 'Tahun Baru Imlek 2026',
                'start_date' => '2026-02-10',
                'end_date' => '2026-02-20',
                'status' => 'active'
            ]
        ];

        echo "Seeding events...\n";
        $stmtEvent = $db->prepare("INSERT INTO `events` (`name`, `start_date`, `end_date`, `status`, `created_at`, `updated_at`) VALUES (?, ?, ?, ?, NOW(), NOW())");
        $eventIds = [];
        foreach ($events as $ev) {
            $stmtEvent->execute([$ev['name'], $ev['start_date'], $ev['end_date'], $ev['status']]);
            $eventIds[$ev['name']] = $db->lastInsertId();
            echo "  Created Event: {$ev['name']}\n";
        }

        // 3. Seed Menus
        $menus = [
            // Idul Fitri
            [
                'name' => 'Paket Ketupat Lebaran Komplit',
                'description' => 'Ketupat janur empuk, Opor Ayam Kampung gurih, Sambal Goreng Kentang Ati Ampela, Sayur Labu Siam manis, Bubuk Koya, Kerupuk Udang, dan Sambal Bajak.',
                'price' => 75000,
                'category_slug' => 'paket-katering',
                'event_name' => 'Idul Fitri 2026',
                'minimum_portions' => 10,
                'image' => 'https://images.unsplash.com/photo-1541518763669-27fef04b14ea?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Opor Ayam Kampung Spesial (1 Ekor)',
                'description' => 'Opor Ayam Kampung utuh (potong 4/8/12) dimasak perlahan dengan santan kental premium dan rempah-rempah warisan keluarga.',
                'price' => 185000,
                'category_slug' => 'menu-ala-carte',
                'event_name' => 'Idul Fitri 2026',
                'minimum_portions' => 1,
                'image' => 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Sambal Goreng Ati Ampela Petai',
                'description' => 'Tumis kentang dadu, ati ampela sapi segar, dan petai pilihan disiram bumbu merah santan harum dan sedikit pedas.',
                'price' => 95000,
                'category_slug' => 'menu-ala-carte',
                'event_name' => 'Idul Fitri 2026',
                'minimum_portions' => 1,
                'image' => 'https://images.unsplash.com/photo-1564834724105-918b73d1b9e0?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Nastar Klasik Wisman (500gr)',
                'description' => 'Kue kering nastar lembut dengan mentega Wisman asli berpadu selai nanas madu alami buatan rumah yang manis dan legit.',
                'price' => 120000,
                'category_slug' => 'snack-kue-kering',
                'event_name' => 'Idul Fitri 2026',
                'minimum_portions' => 2,
                'image' => 'https://images.unsplash.com/photo-1588166524941-3bf61a9c41db?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],

            // Natal
            [
                'name' => 'Roasted Rosemary Chicken Premium',
                'description' => 'Ayam panggang utuh berbumbu rempah segar rosemary, bawang putih, olive oil, disajikan dengan saus gravy, kentang panggang, dan mix vegetables.',
                'price' => 195000,
                'category_slug' => 'menu-ala-carte',
                'event_name' => 'Natal & Tahun Baru 2026',
                'minimum_portions' => 1,
                'image' => 'https://images.unsplash.com/photo-1598514982205-f36b96d1e8d4?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Beef Lasagna Special Bechamel',
                'description' => 'Lapisan pasta panggang dengan saus daging bolognese sapi gurih, diselimuti saus bechamel lembut dan keju mozzarella leleh melimpah.',
                'price' => 145000,
                'category_slug' => 'paket-katering',
                'event_name' => 'Natal & Tahun Baru 2026',
                'minimum_portions' => 1,
                'image' => 'https://images.unsplash.com/photo-1473093295043-cdd812d0e601?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Kastengel Keju Edam & Kraft (500gr)',
                'description' => 'Kue kering keju gurih renyah dengan paduan keju Edam tua dan topping Kraft parut kering.',
                'price' => 135000,
                'category_slug' => 'snack-kue-kering',
                'event_name' => 'Natal & Tahun Baru 2026',
                'minimum_portions' => 2,
                'image' => 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],

            // Imlek
            [
                'name' => 'Lontong Cap Go Meh Komplit',
                'description' => 'Lontong empuk, Opor Ayam, Lodeh Labu Siam, Sambal Goreng Rebung, Telur Petis, Bubuk Kedelai Bubuk, dan Kerupuk.',
                'price' => 65000,
                'category_slug' => 'paket-katering',
                'event_name' => 'Tahun Baru Imlek 2026',
                'minimum_portions' => 5,
                'image' => 'https://images.unsplash.com/photo-1541518763669-27fef04b14ea?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Ayam Kodok Imlek (Premium)',
                'description' => 'Ayam utuh tanpa tulang diisi adonan daging sapi cincang bumbu rempah harum, dipanggang kecokelatan, dilengkapi rolade telur, sayuran pendamping, dan saus gurih.',
                'price' => 380000,
                'category_slug' => 'menu-ala-carte',
                'event_name' => 'Tahun Baru Imlek 2026',
                'minimum_portions' => 1,
                'image' => 'https://images.unsplash.com/photo-1598514982205-f36b96d1e8d4?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Kue Keranjang Goreng Tepung (8 Pcs)',
                'description' => 'Irisan kue keranjang tradisional manis yang dicelup adonan tepung renyah wangi pandan lalu digoreng garing keemasan.',
                'price' => 45000,
                'category_slug' => 'snack-kue-kering',
                'event_name' => 'Tahun Baru Imlek 2026',
                'minimum_portions' => 1,
                'image' => 'https://images.unsplash.com/photo-1605807646983-377bc5a76493?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ]
        ];

        echo "Seeding menus...\n";
        $stmtMenu = $db->prepare(
            "INSERT INTO `menus` (`name`, `description`, `price`, `category_id`, `event_id`, `minimum_portions`, `image`, `status`, `created_at`, `updated_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())"
        );

        foreach ($menus as $m) {
            $catId = $catIds[$m['category_slug']] ?? null;
            $eventId = $eventIds[$m['event_name']] ?? null;

            if ($catId === null || $eventId === null) {
                echo "  Error: Category or Event not found for {$m['name']}. Skipping.\n";
                continue;
            }

            $stmtMenu->execute([
                $m['name'],
                $m['description'],
                $m['price'],
                $catId,
                $eventId,
                $m['minimum_portions'],
                $m['image'],
                $m['status']
            ]);
            echo "  Created Menu: {$m['name']} (Event: {$m['event_name']})\n";
        }
    }
}

MenuSeeder::run();
