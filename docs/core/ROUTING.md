# Routing

## Route Definition

Routes are defined in `config/routes.php` using HTTP verb methods:

```php
// config/routes.php
return function (\App\Core\Router $router): void {
    $router->get('/path', [ControllerClass::class, 'method']);
    $router->post('/path', [ControllerClass::class, 'method']);
    $router->put('/path', [ControllerClass::class, 'method']);
    $router->patch('/path', [ControllerClass::class, 'method']);
    $router->delete('/path', [ControllerClass::class, 'method']);
};
```

Handler formats:
- **Array**: `[ControllerClass::class, 'methodName']` — resolved via Container
- **Callable**: `function(Request $request) { ... }` — called directly

Path normalization: trailing slashes are stripped, empty path defaults to `/`.

## Route Groups

Groups apply shared prefix and/or middleware to a set of routes:

```php
$router->group([
    'prefix' => '/admin',
    'middleware' => ['auth', 'role:admin'],
], function ($router) {
    $router->get('/users', [UserController::class, 'index']);
    // resolves to: GET /admin/users with auth + role:admin middleware
});
```

### Nesting

Groups can nest. Prefixes concatenate, middleware merges:

```php
$router->group(['prefix' => '/api', 'middleware' => ['auth']], function ($router) {
    $router->group(['prefix' => '/v1'], function ($router) {
        $router->get('/users', ...);
        // resolves to: GET /api/v1/users with auth middleware
    });
});
```

### Full Route Table

This table reflects the complete application routing structure.

| Method | URI | Handler | Middleware |
|--------|-----|---------|------------|
| GET | `/` | `WelcomeController@index` | session.timeout, rate.limit:60,1 |
| GET | `/menu/{code}` | `WelcomeController@publicShow` | session.timeout, rate.limit:60,1 |
| GET | `/api/menus` | `WelcomeController@apiMenus` | rate.limit:120,1 |
| GET | `/order-form` | `OrderController@publicCreate` | session.timeout, rate.limit:30,1 |
| POST | `/order-form` | `OrderController@publicStore` | csrf, rate.limit:10,1 |
| GET | `/track-order` | `OrderController@trackOrder` | session.timeout, rate.limit:30,1 |
| POST | `/track-order` | `OrderController@processTrackOrder` | csrf, rate.limit:10,1 |
| GET | `/track-order/{id}` | `OrderController@showTrackOrder` | session.timeout |
| GET | `/my-orders` | `OrderController@myOrders` | auth, session.timeout |
| GET | `/login` | `AuthController@index` | guest, rate.limit:20,1 |
| POST | `/login` | `AuthController@login` | guest, csrf, rate.limit:5,1 |
| GET | `/register` | `AuthController@registerPage` | guest, rate.limit:20,1 |
| POST | `/register` | `AuthController@register` | guest, csrf, rate.limit:10,1 |
| POST | `/logout` | `AuthController@logout` | auth, csrf |
| GET | `/lang/{locale}` | `LangController@switch` | rate.limit:30,1 |
| GET | `/dashboard` | `DashboardController@index` | auth, role:admin, session.timeout |
| GET | `/menus` | `MenuController@index` | auth, role:admin, session.timeout |
| POST | `/menus` | `MenuController@store` | auth, role:admin, csrf |
| POST | `/menus/{id}` | `MenuController@update` | auth, role:admin, csrf |
| POST | `/menus/{id}/delete` | `MenuController@destroy` | auth, role:admin, csrf |
| POST | `/menus/generate-description` | `MenuController@generateDescription`| auth, role:admin, csrf, rate.limit:10,1 |
| GET | `/orders` | `OrderController@index` | auth, role:admin, session.timeout |
| GET | `/orders/{id}` | `OrderController@show` | auth, role:admin, session.timeout |
| POST | `/orders/{id}/status` | `OrderController@updateStatus` | auth, role:admin, csrf |
| POST | `/orders/{id}/payment` | `OrderController@updatePayment`| auth, role:admin, csrf |
| POST | `/orders/{id}/notes` | `OrderController@updateAdminNotes`| auth, role:admin, csrf |
| GET | `/orders/{id}/receipt` | `OrderController@printReceipt` | auth, role:admin, session.timeout |
| GET | `/reports/revenue` | `ReportController@revenue` | auth, role:admin, session.timeout |
| GET | `/reports/revenue/export` | `ReportController@exportRevenue` | auth, role:admin, session.timeout |
| GET | `/reports/menu-revenue` | `ReportController@menuRevenue` | auth, role:admin, session.timeout |
| GET | `/reports/menu-revenue/export` | `ReportController@exportMenuRevenue`| auth, role:admin, session.timeout |
| GET | `/users`, `/events`, `/categories` | *(Standard CRUD Endpoints)* | auth, role:admin, csrf |

View live: `php vanilla routes`

## Rate Limiting

The router supports applying rate limits via the `rate.limit` middleware alias.

```php
$router->get('/api/data', [ApiController::class, 'index'], ['rate.limit:60,1']);
```
- `60`: Maximum requests allowed.
- `1`: Time window in minutes.
(e.g., `rate.limit:60,1` = 60 requests per minute).

## Route Parameters

Parameters use `{paramName}` syntax:

```php
$router->get('/users/{id}/edit', [UserController::class, 'edit']);
```

Regex conversion: `{id}` → `(?P<id>[^/]+)` — matched via `#^/users/(?P<id>[^/]+)/edit$#`.

Access in controller:

```php
public function edit(Request $request): void {
    $id = (int) $request->param('id');
}
```

## Dispatch Flow

```
Router::dispatch(Request)
         │
         ├── For each registered route:
         │     ├── Method matches? (GET, POST, etc.)
         │     └── URI matches regex?
         │           YES → extract params → Request::setRouteParams()
         │                 → run middleware pipeline
         │                 → resolve + call handler
         │           NO  → next route
         │
         └── No match found → throw NotFoundException (404)
```

**First-match-wins**: Routes are checked sequentially. The first matching route is used.

## Middleware Assignment

Middleware is assigned via route groups:

```php
$router->group(['middleware' => ['auth', 'csrf']], function ($router) {
    $router->post('/users', [UserController::class, 'store']);
});
```

See: [MIDDLEWARE.md](MIDDLEWARE.md) for middleware details.

## Handler Resolution

| Handler Type | Resolution |
|-------------|------------|
| `callable` | `call_user_func($handler, $request)` |
| `[Class, 'method']` | `$container->make(Class)` → `$instance->method($request)` |

## Request API

The `Request` object is passed to every handler:

```php
$request->method(): string               // 'GET', 'POST', etc.
$request->uri(): string                  // '/users/1/edit' (no query string)
$request->input(string $key, $default)   // GET/POST parameter
$request->only(array $keys): array       // subset of input
$request->all(): array                   // all GET+POST merged
$request->file(string $key): ?array      // $_FILES entry
$request->has(string $key): bool         // parameter exists?
$request->param(string $key, $default)   // route parameter
$request->isAjax(): bool                 // XMLHttpRequest?
$request->expectsJson(): bool            // Accept: application/json?
$request->ip(): string                   // client IP (proxy-aware)
```

## Response API

`App\Core\Response` provides static methods — all `never`-return methods terminate with `exit`:

```php
Response::redirect(string $url, int $code = 302): never
Response::json(mixed $data, int $code = 200): never
Response::jsonSuccess(mixed $data, string $message = 'OK', int $code = 200): never
Response::jsonError(string $message, array $errors = [], int $code = 400): never
Response::text(string $text, int $code = 200): never
Response::download(string $filePath, string $filename): never
Response::setStatusCode(int $code): void
```

---

See: [CONTAINER.md](CONTAINER.md) · [MIDDLEWARE.md](MIDDLEWARE.md) · [ARCHITECTURE.md](ARCHITECTURE.md)
