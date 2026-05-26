<?php
declare(strict_types=1);
// File: config/bindings.php

use App\Core\Container;
use App\Models\{User, Menu, Category, Customer, Order, Event};
use App\Services\{AuthService, UserService, FileUploadService, MenuService, CategoryService, OrderService, EventService};
use App\Controllers\{AuthController, UserController, WelcomeController, MenuController, CategoryController, OrderController, EventController};

// Models
$container->bind(User::class, fn(Container $c): object => new User());
$container->bind(Menu::class, fn(Container $c): object => new Menu());
$container->bind(Category::class, fn(Container $c): object => new Category());
$container->bind(Customer::class, fn(Container $c): object => new Customer());
$container->bind(Order::class, fn(Container $c): object => new Order());
$container->bind(Event::class, fn(Container $c): object => new Event());

// Services
$container->bind(FileUploadService::class, fn(Container $c): object => new FileUploadService(BASE_PATH . '/storage/uploads'));
$container->bind(AuthService::class, fn(Container $c): object => new AuthService($c->make(User::class)));
$container->bind(UserService::class, fn(Container $c): object => new UserService($c->make(User::class)));
$container->bind(CategoryService::class, fn(Container $c): object => new CategoryService($c->make(Category::class)));
$container->bind(MenuService::class, fn(Container $c): object => new MenuService($c->make(Menu::class), $c->make(FileUploadService::class)));
$container->bind(OrderService::class, fn(Container $c): object => new OrderService($c->make(Order::class), $c->make(Customer::class), $c->make(Menu::class)));
$container->bind(EventService::class, fn(Container $c): object => new EventService($c->make(Event::class)));

// Controllers
$container->bind(WelcomeController::class, fn(Container $c): object => new WelcomeController(
    $c->make(EventService::class),
    $c->make(MenuService::class),
    $c->make(CategoryService::class)
));
$container->bind(AuthController::class, fn(Container $c): object => new AuthController($c->make(AuthService::class)));
$container->bind(UserController::class, fn(Container $c): object => new UserController($c->make(UserService::class)));
$container->bind(CategoryController::class, fn(Container $c): object => new CategoryController($c->make(CategoryService::class)));
$container->bind(EventController::class, fn(Container $c): object => new EventController($c->make(EventService::class)));
$container->bind(MenuController::class, fn(Container $c): object => new MenuController($c->make(MenuService::class), $c->make(CategoryService::class), $c->make(EventService::class)));
$container->bind(OrderController::class, fn(Container $c): object => new OrderController($c->make(OrderService::class), $c->make(MenuService::class), $c->make(EventService::class), $c->make(Customer::class)));
