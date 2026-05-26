# Middleware

## Kontrak Middleware

Semua middleware mengimplementasikan kelas antarmuka `App\Middleware\MiddlewareInterface`:

```php
interface MiddlewareInterface {
    public function handle(Request $request): bool;
}
```

## Semantik Gerbang Boolean (Boolean Gate Semantics)

| Kembalian (Return) | Efek |
|--------------------|------|
| `true` | Pipeline dilanjutkan ke middleware / handler berikutnya |
| `false` | Pipeline dihentikan — handler TIDAK dieksekusi |

> Dalam praktiknya, middleware yang memblokir biasanya memanggil `Response::redirect()` (yang mengembalikan tipe `never`) atau melempar `HttpException`, sehingga pengembalian nilai `false` jarang sekali dicapai.

## Eksekusi Pipeline

Middleware berjalan secara **berurutan** — semuanya harus lolos:

```
Request
  │
  ├── Middleware 1 → true
  │     │
  │     ├── Middleware 2 → true
  │     │     │
  │     │     ├── Middleware 3 → true
  │     │     │     │
  │     │     │     └── Handler dieksekusi ✓
  │     │     │
  │     │     └── Middleware 3 → false/redirect/throw
  │     │           └── Handler TIDAK dieksekusi ✗
```

## Registrasi

Alias middleware didaftarkan di `public/index.php`:

```php
$router->addMiddleware('auth', \App\Middleware\AuthMiddleware::class);
$router->addMiddleware('role', \App\Middleware\RoleMiddleware::class);
$router->addMiddleware('csrf', \App\Middleware\CsrfMiddleware::class);
```

## Resolusi

| Format Alias | Resolusi | Jenis Instance |
|--------------|----------|----------------|
| `'auth'` | `Container::make(AuthMiddleware::class)` | Singleton |
| `'role:admin'` | `new RoleMiddleware('admin')` | Instance Baru dengan argumen |

Alias berparameter dipisah menggunakan tanda `:` — bagian setelah `:` akan diteruskan ke konstruktor middleware.

## Middleware Bawaan

### AuthMiddleware

| Properti | Nilai |
|----------|-------|
| Alias | `auth` |
| Konstruktor | Tanpa argumen |
| Perilaku | Memeriksa `Session::has('user')`. Jika tidak ada → `Response::redirect('/login')` |

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

| Properti | Nilai |
|----------|-------|
| Alias | `csrf` |
| Konstruktor | Tanpa argumen |
| Perilaku | Melewatkan GET. Memverifikasi `_csrf_token` dari input POST dengan token sesi. Melempar `HttpException(419)` jika tidak cocok. |

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

| Properti | Nilai |
|----------|-------|
| Alias | `role` |
| Konstruktor | `(string $requiredRole)` — promoted property |
| Penggunaan | `'role:admin'` |
| Perilaku | Memeriksa apakah `Session::get('user')['role']` cocok dengan peran yang diperlukan. Melempar `HttpException(403)` jika tidak cocok. |

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

## Membuat Middleware Kustom

1. Buat kelas baru:

```php
<?php
declare(strict_types=1);

namespace App\Middleware;
use App\Core\Request;

class ThrottleMiddleware implements MiddlewareInterface {
    public function handle(Request $request): bool {
        // logika pembatasan laju permintaan (rate limiting)
        return true;
    }
}
```

Atau menggunakan perancah: `php vanilla make:middleware Throttle`

2. Daftarkan alias di `public/index.php`:

```php
$router->addMiddleware('throttle', \App\Middleware\ThrottleMiddleware::class);
```

3. Terapkan melalui grup rute:

```php
$router->group(['middleware' => ['throttle']], function ($router) {
    $router->post('/api/submit', ...);
});
```

## API Session

Middleware sering menggunakan `App\Core\Session` (statis facade):

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

Lihat: [ROUTING.md](ROUTING.md) · [CONTAINER.md](CONTAINER.md) · [SECURITY.md](SECURITY.md)
