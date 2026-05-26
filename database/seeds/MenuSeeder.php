<?php
declare(strict_types=1);

namespace Database\Seeds;

use App\Core\Database;

class MenuSeeder {
    public static function run(): void {
        $db = Database::getInstance();

        $categories = [
            ['name' => 'Catering Package', 'slug' => 'catering-package'],
            ['name' => 'Ala Carte', 'slug' => 'ala-carte'],
            ['name' => 'Snack & Cake', 'slug' => 'snack-cake'],
            ['name' => 'Beverage', 'slug' => 'beverage'],
        ];

        echo "Seeding categories...\n";
        $stmtCategory = $db->prepare("INSERT INTO `categories` (`name`, `slug`, `created_at`, `updated_at`) VALUES (?, ?, NOW(), NOW())");
        foreach ($categories as $kat) {
            $stmtCategory->execute([$kat['name'], $kat['slug']]);
            echo "  Created Category: {$kat['name']}\n";
        }

        $menus = [
            ['Wedding Package A', 'White rice, roasted chicken, black pepper beef, clear soup, crackers, fruit cuts, mineral water', 45000, 1, 50, 'active'],
            ['Wedding Package B', 'White rice, butter fried chicken, beef rendang, sausage meatball soup, crackers, fruit ice, mineral water', 55000, 1, 100, 'active'],
            ['Grilled Chicken Rice Box', 'White rice, sweet soy grilled chicken, tofu tempeh, chili paste, fresh vegetables', 25000, 1, 20, 'active'],
            ['Meeting Snack Box', 'Chicken lemper, mayo rissole, steamed sponge cake, cup mineral water', 15000, 3, 30, 'active'],
            ['Chicken Satay Madura', '10 skewers of chicken satay with peanut sauce and rice cake', 20000, 2, 10, 'active'],
        ];

        echo "Seeding menus...\n";
        $stmtMenu = $db->prepare(
            "INSERT INTO `menus` (`name`, `description`, `price`, `category_id`, `minimum_portions`, `status`, `created_at`, `updated_at`) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())"
        );

        foreach ($menus as $menu) {
            $stmtMenu->execute($menu);
            echo "  Created Menu: {$menu[0]}\n";
        }
    }
}
