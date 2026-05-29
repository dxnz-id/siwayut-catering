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

### Route table (`config/routes.php`)

Admin CRUD uses **modals on index pages** — there are no `/create` or `/edit` GET routes.

| Method | URI | Handler | Middleware |
|--------|-----|---------|------------|
| GET | `/` | `WelcomeController@index` | — |
| GET | `/auth` | `AuthController@index` | — |
| POST | `/auth/login` | `AuthController@login` | — |
| POST | `/auth/register` | `AuthController@register` | — |
| GET | `/login` | `AuthController@loginPageRedirect` | — |
| POST | `/login` | `AuthController@login` | — |
| POST | `/logout` | `AuthController@logout` | — |
| GET | `/order-form` | `OrderController@publicForm` | — |
| POST | `/order-form` | `OrderController@publicSubmit` | — |
| GET | `/track-order` | `OrderController@trackForm` | — |
| POST | `/track-order` | `OrderController@track` | — |
| GET | `/track-order/{id}` | `OrderController@trackResult` | — |
| GET | `/api/menus` | `WelcomeController@apiMenus` | — |
| GET | `/users` | `UserController@index` | auth, role:admin |
| POST | `/users` | `UserController@store` | auth, role:admin |
| POST | `/users/{id}` | `UserController@update` | auth, role:admin |
| POST | `/users/{id}/delete` | `UserController@destroy` | auth, role:admin |
| GET | `/api/users/{id}` | `UserController@apiShow` | auth, role:admin |
| GET | `/events` | `EventController@index` | auth, role:admin |
| POST | `/events` | `EventController@store` | auth, role:admin |
| POST | `/events/{id}` | `EventController@update` | auth, role:admin |
| POST | `/events/{id}/delete` | `EventController@destroy` | auth, role:admin |
| GET | `/api/events/{id}` | `EventController@apiShow` | auth, role:admin |
| GET | `/categories` | `CategoryController@index` | auth, role:admin |
| POST | `/categories` | `CategoryController@store` | auth, role:admin |
| POST | `/categories/{id}` | `CategoryController@update` | auth, role:admin |
| POST | `/categories/{id}/delete` | `CategoryController@destroy` | auth, role:admin |
| GET | `/api/categories/{id}` | `CategoryController@apiShow` | auth, role:admin |
| GET | `/menus` | `MenuController@index` | auth, role:admin |
| GET | `/menus/{id}` | `MenuController@show` | auth, role:admin |
| POST | `/menus` | `MenuController@store` | auth, role:admin |
| POST | `/menus/generate-description` | `MenuController@generateDescription` | auth, role:admin |
| POST | `/menus/{id}` | `MenuController@update` | auth, role:admin |
| POST | `/menus/{id}/delete` | `MenuController@destroy` | auth, role:admin |
| GET | `/api/menus/{id}` | `MenuController@apiShow` | auth, role:admin |
| GET | `/orders` | `OrderController@index` | auth, role:admin |
| POST | `/orders` | `OrderController@store` | auth, role:admin |
| GET | `/orders/{id}` | `OrderController@show` | auth, role:admin |
| POST | `/orders/{id}` | `OrderController@update` | auth, role:admin |

View live: `php vanilla routes` (39 routes)

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
