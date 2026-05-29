<?php
declare(strict_types=1);

namespace Database\Seeds;

use App\Services\FileUploadService;

class MenuSeeder {
    public function __construct(
        private \PDO $db,
        private FileUploadService $fileUpload,
    ) {}

    public function run(): void {
        $this->db->exec("SET FOREIGN_KEY_CHECKS = 0");
        $this->db->exec("TRUNCATE TABLE `menus`");
        $this->db->exec("TRUNCATE TABLE `events`");
        $this->db->exec("TRUNCATE TABLE `categories`");
        $this->db->exec("SET FOREIGN_KEY_CHECKS = 1");

        $categories = [
            ['name' => 'Catering Packages', 'slug' => 'paket-katering'],
            ['name' => 'A La Carte Menu', 'slug' => 'menu-ala-carte'],
            ['name' => 'Snacks & Cookies', 'slug' => 'snack-kue-kering'],
            ['name' => 'Special Beverages', 'slug' => 'minuman-spesial'],
        ];

        echo "Seeding categories...\n";
        $stmtCategory = $this->db->prepare("INSERT INTO `categories` (`name`, `slug`, `created_at`, `updated_at`) VALUES (?, ?, NOW(), NOW())");
        $catIds = [];
        foreach ($categories as $kat) {
            $stmtCategory->execute([$kat['name'], $kat['slug']]);
            $catIds[$kat['slug']] = $this->db->lastInsertId();
            echo "  Created Category: {$kat['name']}\n";
        }

        $events = [
            [
                'name' => 'Eid al-Fitr 2026',
                'start_date' => '2026-03-15',
                'end_date' => '2026-03-25',
                'status' => 'active'
            ],
            [
                'name' => 'Christmas & New Year 2026',
                'start_date' => '2026-12-20',
                'end_date' => '2027-01-05',
                'status' => 'active'
            ],
            [
                'name' => 'Chinese New Year 2026',
                'start_date' => '2026-02-10',
                'end_date' => '2026-02-20',
                'status' => 'active'
            ]
        ];

        echo "Seeding events...\n";
        $stmtEvent = $this->db->prepare("INSERT INTO `events` (`name`, `start_date`, `end_date`, `status`, `created_at`, `updated_at`) VALUES (?, ?, ?, ?, NOW(), NOW())");
        $eventIds = [];
        foreach ($events as $ev) {
            $stmtEvent->execute([$ev['name'], $ev['start_date'], $ev['end_date'], $ev['status']]);
            $eventIds[$ev['name']] = $this->db->lastInsertId();
            echo "  Created Event: {$ev['name']}\n";
        }

        $menus = [
            [
                'name' => 'Complete Eid Ketupat Package',
                'description' => 'Soft palm-wrapped ketupat, savory free-range chicken opor, potato-liver sambal goreng, sweet chayote soup, peanut powder, shrimp crackers, and bajak chili sauce.',
                'price' => 75000,
                'category_slug' => 'paket-katering',
                'event_name' => 'Eid al-Fitr 2026',
                'minimum_portions' => 10,
                'image' => 'https://images.unsplash.com/photo-1541518763669-27fef04b14ea?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Special Free-Range Chicken Opor (1 Bird)',
                'description' => 'Whole free-range chicken (cut 4/8/12 pieces) slow-cooked in rich coconut milk and a heritage spice blend.',
                'price' => 185000,
                'category_slug' => 'menu-ala-carte',
                'event_name' => 'Eid al-Fitr 2026',
                'minimum_portions' => 1,
                'image' => 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Liver Gizzard Sambal with Petai',
                'description' => 'Diced potatoes, fresh beef liver and gizzard, and petai beans in aromatic red coconut sauce with a gentle heat.',
                'price' => 95000,
                'category_slug' => 'menu-ala-carte',
                'event_name' => 'Eid al-Fitr 2026',
                'minimum_portions' => 1,
                'image' => 'https://images.unsplash.com/photo-1564834724105-918b73d1b9e0?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Classic Wisman Pineapple Tart (500g)',
                'description' => 'Buttery nastar cookies with real Wisman butter and homemade honey-pineapple jam—sweet and melt-in-the-mouth.',
                'price' => 120000,
                'category_slug' => 'snack-kue-kering',
                'event_name' => 'Eid al-Fitr 2026',
                'minimum_portions' => 2,
                'image' => 'https://images.unsplash.com/photo-1588166524941-3bf61a9c41db?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Roasted Rosemary Chicken Premium',
                'description' => 'Whole roast chicken with fresh rosemary, garlic, and olive oil, served with gravy, roasted potatoes, and mixed vegetables.',
                'price' => 195000,
                'category_slug' => 'menu-ala-carte',
                'event_name' => 'Christmas & New Year 2026',
                'minimum_portions' => 1,
                'image' => 'https://images.unsplash.com/photo-1598514982205-f36b96d1e8d4?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Beef Lasagna Special Bechamel',
                'description' => 'Baked pasta layers with savory beef bolognese, creamy bechamel, and generous melted mozzarella.',
                'price' => 145000,
                'category_slug' => 'paket-katering',
                'event_name' => 'Christmas & New Year 2026',
                'minimum_portions' => 1,
                'image' => 'https://images.unsplash.com/photo-1473093295043-cdd812d0e601?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Edam & Kraft Cheese Cookies (500g)',
                'description' => 'Crispy savory cheese cookies with aged Edam and grated Kraft topping.',
                'price' => 135000,
                'category_slug' => 'snack-kue-kering',
                'event_name' => 'Christmas & New Year 2026',
                'minimum_portions' => 2,
                'image' => 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Complete Cap Go Meh Rice Cake Set',
                'description' => 'Soft lontong rice cakes with chicken opor, chayote lodeh, bamboo shoot sambal, shrimp-paste eggs, soybean powder, and crackers.',
                'price' => 65000,
                'category_slug' => 'paket-katering',
                'event_name' => 'Chinese New Year 2026',
                'minimum_portions' => 5,
                'image' => 'https://images.unsplash.com/photo-1541518763669-27fef04b14ea?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Premium Stuffed Boneless Chicken (CNY)',
                'description' => 'Boneless whole chicken filled with spiced minced beef, roasted golden brown, with egg rollade, sides, and savory sauce.',
                'price' => 380000,
                'category_slug' => 'menu-ala-carte',
                'event_name' => 'Chinese New Year 2026',
                'minimum_portions' => 1,
                'image' => 'https://images.unsplash.com/photo-1598514982205-f36b96d1e8d4?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Fried Nian Gao Fritters (8 Pcs)',
                'description' => 'Sweet traditional nian gao slices dipped in fragrant pandan batter and fried until golden and crisp.',
                'price' => 45000,
                'category_slug' => 'snack-kue-kering',
                'event_name' => 'Chinese New Year 2026',
                'minimum_portions' => 1,
                'image' => 'https://images.unsplash.com/photo-1605807646983-377bc5a76493?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Padang Beef Rendang',
                'description' => 'Premium beef slow-cooked in thick coconut milk and fifteen spices until dark, rich, and tender. Perfect with ketupat.',
                'price' => 175000,
                'category_slug' => 'menu-ala-carte',
                'event_name' => 'Eid al-Fitr 2026',
                'minimum_portions' => 1,
                'image' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Special Fresh Fruit Ice (1L Jar)',
                'description' => 'Seasonal fresh fruit mix—melon, watermelon, pineapple, avocado, grapes, and young coconut with vanilla milk syrup and shaved ice.',
                'price' => 55000,
                'category_slug' => 'minuman-spesial',
                'event_name' => 'Eid al-Fitr 2026',
                'minimum_portions' => 1,
                'image' => 'https://images.unsplash.com/photo-1551024506-0bccd828d307?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Complete Goat Gulai',
                'description' => 'Tender young goat meat and bones in rich yellow gulai broth, with compressed rice, pickles, and crackers.',
                'price' => 165000,
                'category_slug' => 'menu-ala-carte',
                'event_name' => 'Eid al-Fitr 2026',
                'minimum_portions' => 1,
                'image' => 'https://images.unsplash.com/photo-1565557623262-b51c2513a641?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Sweet & Sour Snapper',
                'description' => 'Fresh red snapper fried crisp and topped with thick sweet-sour sauce, pineapple, bell pepper, and onion.',
                'price' => 155000,
                'category_slug' => 'menu-ala-carte',
                'event_name' => 'Christmas & New Year 2026',
                'minimum_portions' => 1,
                'image' => 'https://images.unsplash.com/photo-1559847844-5315695dadae?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Truffle Mushroom Cream Soup',
                'description' => 'Rich mushroom cream soup with button and shiitake mushrooms and truffle oil, served with garlic bread croutons.',
                'price' => 85000,
                'category_slug' => 'paket-katering',
                'event_name' => 'Christmas & New Year 2026',
                'minimum_portions' => 2,
                'image' => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Mango Passion Fruit Panna Cotta',
                'description' => 'Silky Italian panna cotta with fresh mango and passion fruit sauce—a light holiday dessert.',
                'price' => 45000,
                'category_slug' => 'snack-kue-kering',
                'event_name' => 'Christmas & New Year 2026',
                'minimum_portions' => 4,
                'image' => 'https://images.unsplash.com/photo-1488477181946-6428a0291777?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Yee Sang (Fish Salad)',
                'description' => 'Smoked salmon salad with colorful fresh vegetables, sweet plum sauce, and sesame seeds. Served chilled.',
                'price' => 125000,
                'category_slug' => 'menu-ala-carte',
                'event_name' => 'Chinese New Year 2026',
                'minimum_portions' => 2,
                'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Shrimp & Chicken Siomay (10 Pcs)',
                'description' => 'Steamed premium siomay with whole shrimp and fine minced chicken in golden wonton skins, with chili soy dipping sauce.',
                'price' => 65000,
                'category_slug' => 'snack-kue-kering',
                'event_name' => 'Chinese New Year 2026',
                'minimum_portions' => 2,
                'image' => 'https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Butter-Fried Sweet Soy Pork',
                'description' => 'Tender pork slices wok-fried with sweet soy sauce, garlic, and butter, with green chili and onion.',
                'price' => 135000,
                'category_slug' => 'menu-ala-carte',
                'event_name' => 'Chinese New Year 2026',
                'minimum_portions' => 1,
                'image' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
            [
                'name' => 'Iced Chrysanthemum Tea',
                'description' => 'Premium chrysanthemum flower tea served cold with ice and natural honey—a refreshing thirst quencher.',
                'price' => 25000,
                'category_slug' => 'minuman-spesial',
                'event_name' => 'Chinese New Year 2026',
                'minimum_portions' => 1,
                'image' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?auto=format&fit=crop&w=500&q=80',
                'status' => 'active'
            ],
        ];

        echo "Seeding menus...\n";
        $stmtMenu = $this->db->prepare(
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
                    $image = $this->fileUpload->uploadFromUrl($image, 'menus');
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
