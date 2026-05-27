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
use App\Services\FileUploadService;

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

        // Additional menus to reach 20 items
        $menus = array_merge($menus, [
            // Idul Fitri
            [
                'name' => 'Rendang Daging Sapi Padang',
                'description' => 'Daging sapi pilihan dimasak perlahan dengan santan kental dan 15 rempah pilihan hingga hitam pekat, gurih, dan empuk. Cocok disantap dengan ketupat.',
                'price' => 175000,
                'category_slug' => 'menu-ala-carte',
                'event_name' => 'Idul Fitri 2026',
                'minimum_portions' => 1,
                'image' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Es Buah Segar Special (Jar 1L)',
                'description' => 'Campuran buah segar musiman: melon, semangka, nanas, alpukat, anggur, dan kelapa muda dengan siraman sirup susu vanila dan es serut.',
                'price' => 55000,
                'category_slug' => 'minuman-spesial',
                'event_name' => 'Idul Fitri 2026',
                'minimum_portions' => 1,
                'image' => 'https://images.unsplash.com/photo-1551024506-0bccd828d307?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Gulai Kambing Komplit',
                'description' => 'Potongan daging dan tulang kambing muda empuk dalam kuah gulai kuning pekat, dilengkapi nasi impit, acar, dan kerupuk.',
                'price' => 165000,
                'category_slug' => 'menu-ala-carte',
                'event_name' => 'Idul Fitri 2026',
                'minimum_portions' => 1,
                'image' => 'https://images.unsplash.com/photo-1565557623262-b51c2513a641?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],

            // Natal
            [
                'name' => 'Ikan Kakap Asam Manis',
                'description' => 'Ikan kakap merah segar digoreng garing krispi, disiram saus asam manis kental dengan potongan nanas, paprika, dan bawang bombay.',
                'price' => 155000,
                'category_slug' => 'menu-ala-carte',
                'event_name' => 'Natal & Tahun Baru 2026',
                'minimum_portions' => 1,
                'image' => 'https://images.unsplash.com/photo-1559847844-5315695dadae?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Sup Krim Jamur Truffle',
                'description' => 'Sup krim jamur kaya rasa dengan campuran jamur kancing, shitake, dan minyak truffle, disajikan dengan garlic bread croutons.',
                'price' => 85000,
                'category_slug' => 'paket-katering',
                'event_name' => 'Natal & Tahun Baru 2026',
                'minimum_portions' => 2,
                'image' => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Panna Cotta Mangga Markisa',
                'description' => 'Panna cotta Italia yang lembut dengan lapisan saus mangga markisa segar, cocok sebagai dessert Natal yang ringan.',
                'price' => 45000,
                'category_slug' => 'snack-kue-kering',
                'event_name' => 'Natal & Tahun Baru 2026',
                'minimum_portions' => 4,
                'image' => 'https://images.unsplash.com/photo-1488477181946-6428a0291777?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],

            // Imlek
            [
                'name' => 'Yee Sang (Salad Ikan)',
                'description' => 'Salad ikan salmon asap dengan irisan sayuran segar berwarna-warni, saus plum asam manis, dan taburan biji wijen. Disajikan dingin.',
                'price' => 125000,
                'category_slug' => 'menu-ala-carte',
                'event_name' => 'Tahun Baru Imlek 2026',
                'minimum_portions' => 2,
                'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Siomay Udang & Ayam (10 Pcs)',
                'description' => 'Siomay kukus premium isi udang utuh dan daging ayam cincang halus, dibungkus kulit pangsit kuning, disajikan dengan saus sambal kedelai.',
                'price' => 65000,
                'category_slug' => 'snack-kue-kering',
                'event_name' => 'Tahun Baru Imlek 2026',
                'minimum_portions' => 2,
                'image' => 'https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Babi Kecap Goreng Mentega',
                'description' => 'Irisan daging babi empuk dimasak dengan kecap manis, bawang putih, dan mentega, dengan tambahan cabai hijau dan bawang bombay.',
                'price' => 135000,
                'category_slug' => 'menu-ala-carte',
                'event_name' => 'Tahun Baru Imlek 2026',
                'minimum_portions' => 1,
                'image' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Teh Bunga Krisan Es',
                'description' => 'Teh bunga krisan alami pilihan disajikan dingin dengan tambahan es batu dan madu manis alami, menyegarkan dahaga.',
                'price' => 25000,
                'category_slug' => 'minuman-spesial',
                'event_name' => 'Tahun Baru Imlek 2026',
                'minimum_portions' => 1,
                'image' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
        ]);

        echo "Seeding menus...\n";
        $fileUpload = new FileUploadService(BASE_PATH . '/storage/uploads');
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

            $image = $m['image'];
            if (str_starts_with($image, 'http')) {
                try {
                    $image = $fileUpload->uploadFromUrl($image, 'menus');
                    echo "  Downloaded image for: {$m['name']}\n";
                } catch (\RuntimeException $e) {
                    echo "  Warning: Failed to download image for {$m['name']}: {$e->getMessage()}\n";
                    $image = '';
                }
            }

            $stmtMenu->execute([
                $m['name'],
                $m['description'],
                $m['price'],
                $catId,
                $eventId,
                $m['minimum_portions'],
                $image,
                $m['status']
            ]);
            echo "  Created Menu: {$m['name']} (Event: {$m['event_name']})\n";
        }
    }
}

MenuSeeder::run();
