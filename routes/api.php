<?php
declare(strict_types=1);
// File: routes/api.php

use App\Core\Router;
use App\Controllers\{UserController, WelcomeController, EventController, CategoryController, MenuController};

return function (Router $r): void {
    $r->get('/api/menus',          [WelcomeController::class, 'apiMenus']);
    $r->get('/api/users/{id}',     [UserController::class, 'apiShow']);
    $r->get('/api/events/{id}',    [EventController::class, 'apiShow']);
    $r->get('/api/categories/{id}',[CategoryController::class, 'apiShow']);
    $r->get('/api/menus/{code}',   [MenuController::class, 'apiShow']);
};
