<?php
declare(strict_types=1);
// File: bootstrap/app.php

use App\Core\{Container, Session, Logger};

Logger::setPath(BASE_PATH . '/storage/logs');

set_exception_handler(function (Throwable $e) {
    Logger::error($e->getMessage(), [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ]);
    $isHttpException = $e instanceof \App\Exceptions\HttpException;
    $statusCode = $isHttpException ? $e->getStatusCode() : 500;
    http_response_code($statusCode);

    // JSON response untuk AJAX/API requests
    $isJson = (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))
           || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest');

    if ($isJson) {
        header('Content-Type: application/json; charset=utf-8');
        $message = $isHttpException ? $e->getMessage() : 'Internal Server Error';
        $resp = ['success' => false, 'message' => $message];
        if (APP_DEBUG) {
            $resp['debug'] = [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ];
        }
        echo json_encode($resp, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit(1);
    }

    if (APP_DEBUG) {
        echo "<h1>Error</h1>";
        echo "<p>" . \App\Core\View::e($e->getMessage()) . "</p>";
    } else {
        $message = $isHttpException ? $e->getMessage() : 'Terjadi kesalahan pada server kami.';
        if ($statusCode === 404 && file_exists(BASE_PATH . '/src/Views/errors/404.php')) {
            require BASE_PATH . '/src/Views/errors/404.php';
        } elseif (file_exists(BASE_PATH . '/src/Views/errors/500.php')) {
            require BASE_PATH . '/src/Views/errors/500.php';
        } else {
            echo "<h1>{$statusCode} Error</h1><p>" . \App\Core\View::e($message) . "</p>";
        }
    }
    exit(1);
});

Session::start();
$container = new Container();
require BASE_PATH . '/config/bindings.php';
return $container;
