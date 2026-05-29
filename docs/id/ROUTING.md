# Rute (Routing)

## Definisi Rute

Rute didefinisikan dalam `config/routes.php` menggunakan metode kata kerja HTTP (HTTP verb methods):

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

Format penangan (Handler formats):
- **Array**: `[ControllerClass::class, 'methodName']` — diselesaikan melalui Kontainer (Container)
- **Callable**: `function(Request $request) { ... }` — dipanggil secara langsung

Normalisasi jalur (Path normalization): garis miring akhir (trailing slashes) akan dihapus, jalur kosong akan dialihkan ke `/` secara default.

## Grup Rute (Route Groups)

Grup menerapkan awalan (prefix) dan/atau middleware yang sama ke sekumpulan rute:

```php
$router->group([
    'prefix' => '/admin',
    'middleware' => ['auth', 'role:admin'],
], function ($router) {
    $router->get('/users', [UserController::class, 'index']);
    // diselesaikan menjadi: GET /admin/users dengan middleware auth + role:admin
});
```

### Bersarang (Nesting)

Grup rute dapat bersarang. Awalan (prefix) akan digabungkan, dan middleware akan digabungkan:

```php
$router->group(['prefix' => '/api', 'middleware' => ['auth']], function ($router) {
    $router->group(['prefix' => '/v1'], function ($router) {
        $router->get('/users', ...);
        // diselesaikan menjadi: GET /api/v1/users dengan middleware auth
    });
});
```

### Tabel rute (`config/routes.php`)

CRUD admin memakai **modal di halaman index** — tidak ada rute GET `/create` atau `/edit`.

| Metode | URI | Handler | Middleware |
|--------|-----|---------|------------|
| GET | `/` | `WelcomeController@index` | — |
| GET | `/auth` | `AuthController@index` | — |
| POST | `/auth/login` | `AuthController@login` | — |
| POST | `/auth/register` | `AuthController@register` | — |
| GET | `/login` | `AuthController@loginPageRedirect` | — |
| POST | `/login` | `AuthController@login` | — |
| POST | `/logout` | `AuthController@logout` | — |
| GET/POST | `/order-form` | `OrderController@publicForm` / `publicSubmit` | — |
| GET/POST | `/track-order`, GET `/track-order/{id}` | Pelacakan pesanan | — |
| GET | `/api/menus` | `WelcomeController@apiMenus` | — |
| GET/POST/DELETE* | `/users`, `/events`, `/categories` | CRUD + `GET /api/{resource}/{id}` | auth, role:admin |
| GET | `/menus`, `/menus/{id}` | Daftar + detail menu | auth, role:admin |
| POST | `/menus/generate-description` | Deskripsi AI | auth, role:admin |
| GET/POST | `/orders`, `/orders/{id}` | Daftar, buat, detail, update | auth, role:admin |

\* Hapus via `POST /{resource}/{id}/delete`.

Lihat lengkap: `php vanilla routes` (39 rute)

## Parameter Rute (Route Parameters)

Parameter rute menggunakan sintaksis `{paramName}`:

```php
$router->get('/users/{id}/edit', [UserController::class, 'edit']);
```

Konversi Regex: `{id}` → `(?P<id>[^/]+)` — dicocokkan melalui `#^/users/(?P<id>[^/]+)/edit$#`.

Mengakses parameter di controller:

```php
public function edit(Request $request): void {
    $id = (int) $request->param('id');
}
```

## Alur Pengiriman (Dispatch Flow)

```
Router::dispatch(Request)
         │
         ├── Untuk setiap rute yang terdaftar:
         │     ├── Metode cocok? (GET, POST, dll.)
         │     └── URI cocok dengan regex?
         │           YA → ekstrak parameter → Request::setRouteParams()
         │                 → jalankan pipeline middleware
         │                 → selesaikan & panggil handler
         │           TIDAK → rute berikutnya
         │
         └── Tidak ada kecocokan ditemukan → lemparkan NotFoundException (404)
```

**Pertama cocok yang menang (First-match-wins)**: Rute diperiksa secara berurutan. Rute pertama yang cocok akan digunakan.

## Penugasan Middleware

Middleware ditetapkan melalui grup rute:

```php
$router->group(['middleware' => ['auth', 'csrf']], function ($router) {
    $router->post('/users', [UserController::class, 'store']);
});
```

Lihat: [MIDDLEWARE.md](MIDDLEWARE.md) untuk detail tentang middleware.

## Resolusi Penangan (Handler Resolution)

| Jenis Penangan | Resolusi |
|----------------|----------|
| `callable` | `call_user_func($handler, $request)` |
| `[Kelas, 'metode']` | `$container->make(Kelas)` → `$instance->metode($request)` |

## API Request

Objek `Request` diteruskan ke setiap penangan (handler):

```php
$request->method(): string               // 'GET', 'POST', dll.
$request->uri(): string                  // '/users/1/edit' (tanpa query string)
$request->input(string $key, $default)   // parameter GET/POST
$request->only(array $keys): array       // bagian dari input
$request->all(): array                   // gabungan semua data GET+POST
$request->file(string $key): ?array      // masukan $_FILES
$request->has(string $key): bool         // parameter tersedia?
$request->param(string $key, $default)   // parameter rute
$request->isAjax(): bool                 // XMLHttpRequest?
$request->expectsJson(): bool            // Accept: application/json?
$request->ip(): string                   // IP klien (mendukung proxy)
```

## API Response

`App\Core\Response` menyediakan metode statis — semua metode pengembalian `never` akan menghentikan eksekusi dengan `exit`:

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
