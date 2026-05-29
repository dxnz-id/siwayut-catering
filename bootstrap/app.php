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

    if (APP_DEBUG) {
        echo "<h1>Exception Caught</h1>";
        echo "<p><strong>Type:</strong> " . get_class($e) . "</p>";
        echo "<p><strong>Message:</strong> " . \App\Core\View::e($e->getMessage()) . "</p>";
        echo "<p><strong>File:</strong> " . $e->getFile() . " on line " . $e->getLine() . "</p>";
        echo "<pre>" . \App\Core\View::e($e->getTraceAsString()) . "</pre>";
    } else {
        $message = $isHttpException ? $e->getMessage() : 'Terjadi kesalahan pada server kami.';
        if ($statusCode === 404 && file_exists(BASE_PATH . '/src/Views/errors/404.php')) {
            require BASE_PATH . '/src/Views/errors/404.php';
        } elseif (file_exists(BASE_PATH . '/src/Views/errors/500.php')) {
            require BASE_PATH . '/src/Views/errors/500.php';
        } else {
            echo "<h1>{$statusCode} Error</h1><p>" . htmlspecialchars($message) . "</p>";
        }
    }
    exit(1);
});

Session::start();
$container = new Container();
require BASE_PATH . '/config/bindings.php';
return $container;
