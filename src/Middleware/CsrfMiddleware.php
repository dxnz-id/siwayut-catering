<?php
declare(strict_types=1);
// File: src/Middleware/CsrfMiddleware.php

namespace App\Middleware;
use App\Core\{Request, Csrf};
use App\Exceptions\HttpException;

class CsrfMiddleware implements MiddlewareInterface {
    public function handle(Request $request): bool {
        if ($request->method() === 'GET') {
            return true;
        }
        $token = $request->input('_csrf_token', '');
        if (!Csrf::verify((string) $token)) {
            throw new HttpException(419, 'Page Expired - CSRF token mismatch.');
        }
        return true;
    }
}
