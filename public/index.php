<?php
declare(strict_types=1);
// File: public/index.php

use App\Core\{Request, Router};

define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/vendor/autoload.php';

if (file_exists(BASE_PATH . '/.env')) {
    $env = parse_ini_file(BASE_PATH . '/.env');
    if ($env !== false) {
        foreach ($env as $key => $value) {
            $_ENV[$key] = $value;
        }
    }
}

require BASE_PATH . '/config/app.php';
$app = require BASE_PATH . '/bootstrap/app.php';

$request = new Request();
$router  = new Router($app);
$router->addMiddleware('auth', \App\Middleware\AuthMiddleware::class);
$router->addMiddleware('role', \App\Middleware\RoleMiddleware::class);
$router->addMiddleware('csrf', \App\Middleware\CsrfMiddleware::class);
$router->addMiddleware('session.timeout', \App\Middleware\IdleTimeoutMiddleware::class);
$router->addMiddleware('rate.limit', \App\Middleware\RateLimitMiddleware::class);

header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
header('Access-Control-Allow-Origin: ' . APP_URL);
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
if (APP_ENV === 'production') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$registerRoutes = require BASE_PATH . '/config/routes.php';
$registerRoutes($router);
$router->dispatch($request);
