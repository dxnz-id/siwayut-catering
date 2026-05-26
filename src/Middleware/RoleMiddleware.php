<?php
declare(strict_types=1);
// File: src/Middleware/RoleMiddleware.php

namespace App\Middleware;
use App\Core\{Request, Session};
use App\Exceptions\HttpException;

class RoleMiddleware implements MiddlewareInterface {
    public function __construct(private string $requiredRole) {
    }

    public function handle(Request $request): bool {
        $user = Session::get('user');
        if (!$user || ($user['role'] ?? '') !== $this->requiredRole) {
            throw new HttpException(403, 'Forbidden — Insufficient permissions.');
        }
        return true;
    }
}
