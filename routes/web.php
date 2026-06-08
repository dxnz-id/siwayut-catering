<?php
declare(strict_types=1);
// File: routes/web.php

use App\Core\Router;
use App\Controllers\{AuthController, UserController, WelcomeController, MenuController, CategoryController, OrderController, EventController, LangController, DashboardController, ReportController, ProfileController};

return function (Router $r): void {
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

    $r->get('/menu', [OrderController::class, 'publicMenu']);
    $r->get('/cart', [OrderController::class, 'cartShow']);
    $r->post('/cart/add', [OrderController::class, 'cartAdd']);
    $r->post('/cart/update', [OrderController::class, 'cartUpdate']);
    $r->post('/cart/remove', [OrderController::class, 'cartRemove']);
    $r->post('/cart/remove-selected', [OrderController::class, 'cartRemoveSelected']);

    $r->get('/checkout', [OrderController::class, 'checkoutShow']);
    $r->group(['middleware' => ['rate.limit:5,60']], function (Router $r): void {
        $r->post('/checkout', [OrderController::class, 'checkoutSubmit']);
    });

    $r->get('/track-order',      [OrderController::class, 'trackForm']);
    $r->get('/track-order/{id}', [OrderController::class, 'trackResult']);
    $r->group(['middleware' => ['rate.limit:10,60']], function (Router $r): void {
        $r->post('/track-order', [OrderController::class, 'track']);
    });

    $r->get('/menu/{code}', [WelcomeController::class, 'publicShow']);

    $r->group(['middleware' => ['auth', 'session.timeout:7200']], function (Router $r): void {
        $r->get('/my-orders', [OrderController::class, 'myOrders']);
        $r->get('/profile',                [ProfileController::class, 'edit']);
        $r->post('/profile',               [ProfileController::class, 'update']);
        $r->post('/profile/password',      [ProfileController::class, 'changePassword']);
    });

    $r->group(['middleware' => ['auth', 'role:admin', 'session.timeout:1800']], function (Router $r): void {
        $r->get('/users',              [UserController::class, 'index']);
        $r->post('/users',             [UserController::class, 'store']);
        $r->post('/users/{id}',        [UserController::class, 'update']);
        $r->post('/users/{id}/delete', [UserController::class, 'destroy']);

        // Events
        $r->get('/events',              [EventController::class, 'index']);
        $r->post('/events',             [EventController::class, 'store']);
        $r->post('/events/{id}',        [EventController::class, 'update']);
        $r->post('/events/{id}/delete', [EventController::class, 'destroy']);

        // Categories
        $r->get('/categories',              [CategoryController::class, 'index']);
        $r->post('/categories',             [CategoryController::class, 'store']);
        $r->post('/categories/{id}',        [CategoryController::class, 'update']);
        $r->post('/categories/{id}/delete', [CategoryController::class, 'destroy']);

        // Menus
        $r->get('/menus',                           [MenuController::class, 'index']);
        $r->get('/menus/{code}',                    [MenuController::class, 'show']);
        $r->post('/menus',                          [MenuController::class, 'store']);
        $r->post('/menus/generate-description',     [MenuController::class, 'generateDescription']);
        $r->post('/menus/{code}',                   [MenuController::class, 'update']);
        $r->post('/menus/{code}/delete',            [MenuController::class, 'destroy']);

        // Orders
        $r->get('/orders',              [OrderController::class, 'index']);
        $r->get('/orders/export',       [OrderController::class, 'exportCsv']);
        $r->post('/orders',             [OrderController::class, 'store']);
        $r->get('/orders/{order_number}',     [OrderController::class, 'show']);
        $r->post('/orders/{order_number}',    [OrderController::class, 'update']);
        $r->get('/orders/{order_number}/receipt', [OrderController::class, 'receipt']);

        // Dashboard & Reports
        $r->get('/dashboard',                  [DashboardController::class, 'index']);
        $r->get('/reports/revenue',            [ReportController::class, 'revenue']);
        $r->get('/reports/revenue/export',     [ReportController::class, 'exportCsv']);
        $r->get('/reports/menu-revenue',            [ReportController::class, 'menuRevenue']);
        $r->get('/reports/menu-revenue/export',     [ReportController::class, 'exportMenuRevenueCsv']);
    });
};
