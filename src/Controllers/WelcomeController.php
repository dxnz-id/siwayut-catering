<?php
declare(strict_types=1);
// File: src/Controllers/WelcomeController.php

namespace App\Controllers;

use App\Core\Request;
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
        $menus = $this->menuService->all();

        $this->render('welcome', [
            'title' => 'Siwayut Catering — Premium Holiday Catering Service',
            'events' => $events,
            'categories' => $categories,
            'menus' => $menus
        ], '');
    }
}
