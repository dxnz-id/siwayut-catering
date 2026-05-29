<?php

declare(strict_types=1);
// File: config/routes.php

use App\Core\Router;
use App\Controllers\{AuthController, UserController, WelcomeController, MenuController, CategoryController, OrderController, EventController};

return function (Router $router): void {
    $router->get('/', [WelcomeController::class, 'index']);

    $router->get('/auth',    [AuthController::class, 'index']);
    $router->post('/auth/login',    [AuthController::class, 'login']);
    $router->post('/auth/register', [AuthController::class, 'register']);
    $router->get('/login',   [AuthController::class, 'loginPageRedirect']);
    $router->post('/login',  [AuthController::class, 'login']);
    $router->post('/logout', [AuthController::class, 'logout']);

    // Public: Order Form & Tracking
    $router->get('/order-form',              [OrderController::class, 'publicForm']);
    $router->post('/order-form',             [OrderController::class, 'publicSubmit']);
    $router->get('/track-order',             [OrderController::class, 'trackForm']);
    $router->post('/track-order',            [OrderController::class, 'track']);
    $router->get('/track-order/{id}',        [OrderController::class, 'trackResult']);

    // Public API
    $router->get('/api/menus', [WelcomeController::class, 'apiMenus']);

    $router->group(['middleware' => ['auth'], 'prefix' => ''], function (Router $r): void {
        $r->group(['middleware' => ['role:admin']], function (Router $r): void {
            // Users
            $r->get('/users',              [UserController::class, 'index']);
            $r->post('/users',             [UserController::class, 'store']);
            $r->post('/users/{id}',        [UserController::class, 'update']);
            $r->post('/users/{id}/delete', [UserController::class, 'destroy']);
            $r->get('/api/users/{id}',     [UserController::class, 'apiShow']);
            // Events
            $r->get('/events',              [EventController::class, 'index']);
            $r->post('/events',             [EventController::class, 'store']);
            $r->post('/events/{id}',        [EventController::class, 'update']);
            $r->post('/events/{id}/delete', [EventController::class, 'destroy']);
            $r->get('/api/events/{id}',     [EventController::class, 'apiShow']);

            // Categories
            $r->get('/categories',              [CategoryController::class, 'index']);
            $r->post('/categories',             [CategoryController::class, 'store']);
            $r->post('/categories/{id}',        [CategoryController::class, 'update']);
            $r->post('/categories/{id}/delete', [CategoryController::class, 'destroy']);
            $r->get('/api/categories/{id}',     [CategoryController::class, 'apiShow']);

            // Menus
            $r->get('/menus',                           [MenuController::class, 'index']);
            $r->get('/menus/{id}',                      [MenuController::class, 'show']);
            $r->post('/menus',                          [MenuController::class, 'store']);
            $r->post('/menus/generate-description',     [MenuController::class, 'generateDescription']);
            $r->post('/menus/{id}',                     [MenuController::class, 'update']);
            $r->post('/menus/{id}/delete',              [MenuController::class, 'destroy']);
            $r->get('/api/menus/{id}',                  [MenuController::class, 'apiShow']);

            // Orders
            $r->get('/orders',              [OrderController::class, 'index']);
            $r->post('/orders',             [OrderController::class, 'store']);
            $r->get('/orders/{id}',         [OrderController::class, 'show']);
            $r->post('/orders/{id}',        [OrderController::class, 'update']);
        });
    });
};
