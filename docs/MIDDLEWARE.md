# Middleware

## Middleware Contract

All middleware implement `App\Middleware\MiddlewareInterface`:

```php
interface MiddlewareInterface {
    public function handle(Request $request): bool;
}
```

## Boolean Gate Semantics

| Return | Effect |
|--------|--------|
| `true` | Pipeline continues to next middleware / handler |
| `false` | Pipeline halts — handler is NOT executed |

> In practice, middleware that blocks usually calls `Response::redirect()` (which is `never`-return) or throws an `HttpException`, so the `false` return is rarely reached.

## Pipeline Execution

Middleware runs **sequentially** — all must pass:

```
Request
  │
  ├── Middleware 1 → true
  │     │
  │     ├── Middleware 2 → true
  │     │     │
  │     │     ├── Middleware 3 → true
  │     │     │     │
  │     │     │     └── Handler executes ✓
  │     │     │
  │     │     └── Middleware 3 → false/redirect/throw
  │     │           └── Handler does NOT execute ✗
```

## Registration

Middleware aliases are registered in `public/index.php`:

```php
$router->addMiddleware('auth', \App\Middleware\AuthMiddleware::class);
$router->addMiddleware('role', \App\Middleware\RoleMiddleware::class);
$router->addMiddleware('csrf', \App\Middleware\CsrfMiddleware::class);
```

## Resolution

| Alias Format | Resolution | Instance Type |
|-------------|------------|---------------|
| `'auth'` | `Container::make(AuthMiddleware::class)` | Singleton |
| `'role:admin'` | `new RoleMiddleware('admin')` | Fresh instance with argument |

Parameterized aliases split on `:` — the part after `:` is passed to the constructor.

## Built-in Middleware

### AuthMiddleware

| Property | Value |
|----------|-------|
| Alias | `auth` |
| Constructor | No arguments |
| Behavior | Checks `Session::has('user')`. If absent → `Response::redirect('/login')` |

```php
class AuthMiddleware implements MiddlewareInterface {
    public function handle(Request $request): bool {
        if (!Session::has('user')) {
            Response::redirect('/login'); // never returns
        }
        return true;
    }
}
```

### CsrfMiddleware

| Property | Value |
|----------|-------|
| Alias | `csrf` |
| Constructor | No arguments |
| Behavior | Skips GET. Verifies `_csrf_token` from POST input against session. Throws `HttpException(419)` on mismatch. |

```php
class CsrfMiddleware implements MiddlewareInterface {
    public function handle(Request $request): bool {
        if ($request->method() === 'GET') return true;
        $token = $request->input('_csrf_token', '');
        if (!Csrf::verify((string) $token)) {
            throw new HttpException(419, 'Page Expired — CSRF token mismatch.');
        }
        return true;
    }
}
```

### RoleMiddleware

| Property | Value |
|----------|-------|
| Alias | `role` |
| Constructor | `(string $requiredRole)` — promoted property |
| Usage | `'role:admin'` |
| Behavior | Checks `Session::get('user')['role']` matches required role. Throws `HttpException(403)` if not. |

```php
class RoleMiddleware implements MiddlewareInterface {
    public function __construct(private string $requiredRole) {}

    public function handle(Request $request): bool {
        $user = Session::get('user');
        if (!$user || ($user['role'] ?? '') !== $this->requiredRole) {
            throw new HttpException(403, 'Forbidden');
        }
        return true;
    }
}
```

## Creating Custom Middleware

1. Create the class:

```php
<?php
declare(strict_types=1);

namespace App\Middleware;
use App\Core\Request;

class ThrottleMiddleware implements MiddlewareInterface {
    public function handle(Request $request): bool {
        // rate limiting logic
        return true;
    }
}
```

Or scaffold: `php vanilla make:middleware Throttle`

2. Register the alias in `public/index.php`:

```php
$router->addMiddleware('throttle', \App\Middleware\ThrottleMiddleware::class);
```

3. Apply via route group:

```php
$router->group(['middleware' => ['throttle']], function ($router) {
    $router->post('/api/submit', ...);
});
```

## Session API

Middleware frequently uses `App\Core\Session` (static facade):

```php
Session::start(): void
Session::set(string $key, mixed $value): void
Session::get(string $key, mixed $default = null): mixed
Session::has(string $key): bool
Session::forget(string $key): void
Session::destroy(): void
Session::regenerate(bool $deleteOld = true): void
Session::flash(string $key, string $message): void
Session::getFlash(string $key): ?string
Session::old(): array
Session::setOld(array $data): void
```

---

See: [ROUTING.md](ROUTING.md) · [CONTAINER.md](CONTAINER.md) · [SECURITY.md](SECURITY.md)
