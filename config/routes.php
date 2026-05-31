<?php

declare(strict_types=1);
// File: config/routes.php

use App\Core\Router;
use App\Controllers\{AuthController, UserController, WelcomeController, MenuController, CategoryController, OrderController, EventController, LangController};

return function (Router $router): void {
    $router->group(['middleware' => ['csrf']], function (Router $r): void {
        $r->get('/lang/{locale}', [LangController::class, 'switch']);
        $r->get('/', [WelcomeController::class, 'index']);

        $r->get('/auth',    [AuthController::class, 'index']);
        $r->get('/login',   [AuthController::class, 'loginPageRedirect']);

        $r->group(['middleware' => ['rate.limit:5,60']], function (Router $r): void {
            $r->post('/auth/login',    [AuthController::class, 'login']);
            $r->post('/auth/register', [AuthController::class, 'register']);
            $r->post('/login',         [AuthController::class, 'login']);
        });

        $r->post('/logout', [AuthController::class, 'logout']);

        $r->get('/order-form',  [OrderController::class, 'publicForm']);
        $r->group(['middleware' => ['rate.limit:5,60']], function (Router $r): void {
            $r->post('/order-form', [OrderController::class, 'publicSubmit']);
        });

        $r->get('/track-order',      [OrderController::class, 'trackForm']);
        $r->get('/track-order/{id}', [OrderController::class, 'trackResult']);
        $r->group(['middleware' => ['rate.limit:10,60']], function (Router $r): void {
            $r->post('/track-order', [OrderController::class, 'track']);
        });

        $r->get('/menu/{code}', [WelcomeController::class, 'publicShow']);
        $r->get('/api/menus',   [WelcomeController::class, 'apiMenus']);

        $r->group(['middleware' => ['auth', 'session.timeout:7200']], function (Router $r): void {
            $r->get('/my-orders', [OrderController::class, 'myOrders']);
        });

        $r->group(['middleware' => ['auth', 'role:admin', 'session.timeout:1800']], function (Router $r): void {
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
            $r->get('/menus/{code}',                    [MenuController::class, 'show']);
            $r->post('/menus',                          [MenuController::class, 'store']);
            $r->post('/menus/generate-description',     [MenuController::class, 'generateDescription']);
            $r->post('/menus/{code}',                   [MenuController::class, 'update']);
            $r->post('/menus/{code}/delete',            [MenuController::class, 'destroy']);
            $r->get('/api/menus/{code}',                [MenuController::class, 'apiShow']);

            // Orders
            $r->get('/orders',              [OrderController::class, 'index']);
            $r->post('/orders',             [OrderController::class, 'store']);
            $r->get('/orders/{id}',         [OrderController::class, 'show']);
            $r->post('/orders/{id}',        [OrderController::class, 'update']);
        });
    });
};
