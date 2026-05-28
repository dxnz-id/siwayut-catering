<?php
declare(strict_types=1);
// File: src/Controllers/WelcomeController.php

namespace App\Controllers;

use App\Core\{Request, Response};
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
            'title' => 'Siwayut Catering — Premium Holiday Catering Service',
            'events' => $events,
            'categories' => $categories,
            'menus' => $allMenus,
            'initialMenus' => $initial['data'],
            'totalMenus' => $initial['total'],
            'perPage' => $initial['per_page'],
            'currentPage' => $initial['current_page'],
            'lastPage' => $initial['last_page'],
        ], '');
    }

    public function apiMenus(Request $request): void {
        $page = max(1, (int) ($request->input('page', '1')));

        $result = $this->menuService->paginate($page, 9, ['status' => 'active']);

        // Build event map for the response
        $events = $this->eventService->getActive();
        $eventMap = [];
        foreach ($events as $ev) {
            $eventMap[$ev['id']] = $ev['name'];
        }

        // Inject event_name into each menu
        foreach ($result['data'] as &$menu) {
            $menu['event_name'] = $eventMap[$menu['event_id']] ?? null;
        }
        unset($menu);

        Response::jsonSuccess($result);
    }
}
