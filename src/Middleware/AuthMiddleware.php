<?php
declare(strict_types=1);
// File: src/Middleware/AuthMiddleware.php

namespace App\Middleware;
use App\Core\{Request, Session, Response};

class AuthMiddleware implements MiddlewareInterface {
    public function handle(Request $request): bool {
        if (!Session::has('user')) {
            Response::redirect('/auth');
        }
        return true;
    }
}
