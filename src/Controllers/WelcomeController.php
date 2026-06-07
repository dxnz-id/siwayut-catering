<?php
declare(strict_types=1);
// File: src/Controllers/WelcomeController.php

namespace App\Controllers;

use App\Core\{Request, Response};
use App\Exceptions\NotFoundException;
use App\Services\{EventService, MenuService, CategoryService};

class WelcomeController extends BaseController {
    public function __construct(
        private EventService $eventService,
        private MenuService $menuService,
        private CategoryService $categoryService
    ) {
        parent::__construct();
    }

    public function index(Request $request): void {
        $events = $this->eventService->getActive();
        $categories = $this->categoryService->all();
        $allMenus = $this->menuService->all();

        // First 9 active menus for the featured grid
        $initial = $this->menuService->paginate(1, 9, ['status' => 'active']);

        $this->render('welcome', [
            'title' => 'Siwayut Catering — ' . __('premium_holiday_catering'),
            'events' => $events,
            'categories' => $categories,
            'menus' => $allMenus,
            'initialMenus' => $initial['data'],
            'totalMenus' => $initial['total'],
            'perPage' => $initial['per_page'],
            'currentPage' => $initial['current_page'],
            'lastPage' => $initial['last_page'],
            'navMode' => 'session',
        ], 'public');
    }

    public function publicShow(Request $request): void {
        $code = $request->param('code');
        $menu = $this->menuService->findByCode($code);
        if (!$menu) throw new NotFoundException(__('menu_not_found'));

        $category = $this->categoryService->find((int)$menu['category_id']);
        $event = $this->eventService->find((int)$menu['event_id']);

        $related = [];
        if ($menu['category_id']) {
            $related = $this->menuService->paginate(1, 4, [
                'status' => 'active',
                'category_id' => $menu['category_id'],
            ]);
            $related = array_filter($related['data'], fn($m) => $m['id'] !== $menu['id']);
        }

        $this->render('menu/public-show', [
            'title' => e($menu['name']) . ' — Siwayut Catering',
            'menu' => $menu,
            'category' => $category,
            'event' => $event,
            'related' => array_slice($related, 0, 3),
            'navMode' => 'back',
        ], 'public');
    }

    public function apiMenus(Request $request): void {
        $page = max(1, (int) ($request->input('page', '1')));

        $conditions = ['status' => 'active'];
        $categoryId = $request->input('category_id');
        if ($categoryId !== null && $categoryId !== '') {
            $conditions['category_id'] = (int) $categoryId;
        }

        $result = $this->menuService->paginate($page, 9, $conditions);

        // Build category & event maps for the response
        $events = $this->eventService->getActive();
        $eventMap = [];
        foreach ($events as $ev) {
            $eventMap[$ev['id']] = $ev['name'];
        }

        $categories = $this->categoryService->all();
        $categoryMap = [];
        foreach ($categories as $cat) {
            $categoryMap[$cat['id']] = $cat['name'];
        }

        // Inject event_name and category_name into each menu
        foreach ($result['data'] as &$menu) {
            $menu['event_name'] = $eventMap[$menu['event_id']] ?? null;
            $menu['category_name'] = $categoryMap[$menu['category_id']] ?? null;
        }
        unset($menu);

        Response::jsonSuccess($result);
    }
}
