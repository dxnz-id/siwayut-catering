<?php

declare(strict_types=1);

namespace App\Services;

class CartService
{
    public function __construct(
        private MenuService $menuService
    ) {}

    public function getItems(): array
    {
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) return [];

        $items = [];
        foreach ($cart as $menuId => $quantity) {
            $menu = $this->menuService->find((int) $menuId);
            if (!$menu) continue;

            $items[] = [
                'menu_id' => (int) $menu['id'],
                'name' => $menu['name'],
                'price' => (float) $menu['price'],
                'image' => $menu['image'] ?? '',
                'minimum_portions' => (int) $menu['minimum_portions'],
                'quantity' => $quantity,
                'subtotal' => (float) $menu['price'] * $quantity,
            ];
        }

        return $items;
    }

    public function count(): int
    {
        return array_sum($_SESSION['cart'] ?? []);
    }

    public function getTotal(): float
    {
        $total = 0;
        foreach ($this->getItems() as $item) {
            $total += $item['subtotal'];
        }
        return $total;
    }

    public function add(int $menuId, int $qty): void
    {
        $_SESSION['cart'][$menuId] = ($_SESSION['cart'][$menuId] ?? 0) + $qty;
    }

    public function set(int $menuId, int $qty): void
    {
        if ($qty > 0) {
            $_SESSION['cart'][$menuId] = $qty;
        } else {
            $this->remove($menuId);
        }
    }

    public function remove(int $menuId): void
    {
        unset($_SESSION['cart'][$menuId]);
    }

    public function clear(): void
    {
        unset($_SESSION['cart']);
    }
}
