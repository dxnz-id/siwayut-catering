<?php
declare(strict_types=1);

namespace App\Middleware;
use App\Core\{Request, Session};
use App\Exceptions\HttpException;

class IdleTimeoutMiddleware implements MiddlewareInterface {
    private int $ttl;

    public function __construct(string $ttl = '1800') {
        $this->ttl = (int)$ttl;
    }

    public function handle(Request $request): bool {
        if (Session::isExpired($this->ttl)) {
            Session::destroy();
            throw new HttpException(401, 'Session expired due to inactivity.');
        }
        Session::touchActivity();
        return true;
    }
}
