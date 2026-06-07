# Routing

## Definisi Route

Route didefinisikan di dalam `config/routes.php` menggunakan metode HTTP verb:

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

Format Handler:
- **Array**: `[ControllerClass::class, 'methodName']` — diselesaikan melalui Container
- **Callable**: `function(Request $request) { ... }` — dipanggil secara langsung

Normalisasi path: trailing slash akan dihapus, path kosong secara default akan menjadi `/`.

## Route Groups

Group menerapkan prefix dan/atau middleware bersama ke sekumpulan route:

```php
$router->group([
    'prefix' => '/admin',
    'middleware' => ['auth', 'role:admin'],
], function ($router) {
    $router->get('/users', [UserController::class, 'index']);
    // diselesaikan menjadi: GET /admin/users dengan middleware auth + role:admin
});
```

### Nesting

Group dapat disarangkan (nesting). Prefix akan digabungkan, middleware akan disatukan:

```php
$router->group(['prefix' => '/api', 'middleware' => ['auth']], function ($router) {
    $router->group(['prefix' => '/v1'], function ($router) {
        $router->get('/users', ...);
        // diselesaikan menjadi: GET /api/v1/users dengan middleware auth
    });
});
```

### Tabel Route Lengkap

Tabel ini mencerminkan struktur routing aplikasi secara keseluruhan.

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

Lihat secara langsung: `php vanilla routes`

## Rate Limiting

Router mendukung penerapan rate limit melalui alias middleware `rate.limit`.

```php
$router->get('/api/data', [ApiController::class, 'index'], ['rate.limit:60,1']);
```
- `60`: Jumlah request maksimum yang diizinkan.
- `1`: Jendela waktu dalam menit.
(contoh: `rate.limit:60,1` = 60 request per menit).

## Route Parameters

Parameter menggunakan sintaks `{paramName}`:

```php
$router->get('/users/{id}/edit', [UserController::class, 'edit']);
```

Konversi Regex: `{id}` → `(?P<id>[^/]+)` — dicocokkan melalui `#^/users/(?P<id>[^/]+)/edit$#`.

Akses di dalam controller:

```php
public function edit(Request $request): void {
    $id = (int) $request->param('id');
}
```

## Alur Dispatch

```
Router::dispatch(Request)
         │
         ├── Untuk setiap route yang terdaftar:
         │     ├── Method cocok? (GET, POST, dll.)
         │     └── URI cocok dengan regex?
         │           YA  → ekstrak params → Request::setRouteParams()
         │                 → jalankan pipeline middleware
         │                 → resolve + panggil handler
         │           TIDAK → lanjut ke route berikutnya
         │
         └── Tidak ada kecocokan → lempar NotFoundException (404)
```

**First-match-wins**: Route diperiksa secara berurutan. Route pertama yang cocok akan digunakan.

## Penugasan Middleware

Middleware ditugaskan melalui route groups:

```php
$router->group(['middleware' => ['auth', 'csrf']], function ($router) {
    $router->post('/users', [UserController::class, 'store']);
});
```

Lihat: [MIDDLEWARE.md](MIDDLEWARE.md) untuk detail middleware.

## Resolusi Handler

| Tipe Handler | Resolusi |
|-------------|------------|
| `callable` | `call_user_func($handler, $request)` |
| `[Class, 'method']` | `$container->make(Class)` → `$instance->method($request)` |

## Request API

Objek `Request` diteruskan ke setiap handler:

```php
$request->method(): string               // 'GET', 'POST', dll.
$request->uri(): string                  // '/users/1/edit' (tanpa query string)
$request->input(string $key, $default)   // parameter GET/POST
$request->only(array $keys): array       // subset dari input
$request->all(): array                   // semua GET+POST digabung
$request->file(string $key): ?array      // entri $_FILES
$request->has(string $key): bool         // apakah parameter ada?
$request->param(string $key, $default)   // parameter route
$request->isAjax(): bool                 // XMLHttpRequest?
$request->expectsJson(): bool            // Accept: application/json?
$request->ip(): string                   // IP klien (proxy-aware)
```

## Response API

`App\Core\Response` menyediakan metode statis — semua metode `never`-return akan diakhiri dengan `exit`:

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

Lihat: [CONTAINER.md](CONTAINER.md) · [MIDDLEWARE.md](MIDDLEWARE.md) · [ARCHITECTURE.md](ARCHITECTURE.md)