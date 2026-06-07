# Penanganan Error

## Hierarki Exception

```
\RuntimeException
  └── App\Exceptions\AppException
        ├── App\Exceptions\HttpException
        │     └── App\Exceptions\NotFoundException
        └── App\Exceptions\ValidationException
```

## AppException

Ekstensi kosong dari `\RuntimeException` — kelas dasar untuk semua exception aplikasi.

```php
namespace App\Exceptions;
class AppException extends \RuntimeException {}
```

## HttpException

Error HTTP dengan kode status dan pesan default.

### Constructor

```php
public function __construct(int $code, string $message = '')
```

Jika `$message` kosong, `defaultMessage()` akan memberikan pesan default berdasarkan kode status.

### `getStatusCode(): int`

Mengembalikan kode status HTTP.

### `defaultMessage()` — Tabel Pencocokan

| Kode | Pesan Default |
|------|----------------|
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 405 | Method Not Allowed |
| 419 | Page Expired |
| 422 | Unprocessable Entity |
| 429 | Too Many Requests |
| 500 | Internal Server Error |

### Penggunaan

```php
throw new HttpException(403);                    // "Forbidden"
throw new HttpException(403, 'Custom message');  // "Custom message"
throw new HttpException(419, 'CSRF mismatch');   // 419 Page Expired
```

## NotFoundException

Pintasan untuk error 404.

```php
public function __construct(string $message = 'Resource not found')
```

Kode status diatur secara hardcoded: **404**. Dilempar oleh `Router::dispatch()` ketika tidak ada route yang cocok.

```php
throw new NotFoundException();            // "Resource not found"
throw new NotFoundException('User not found');
```

## ValidationException

Membawa error validasi.

```php
public function __construct(private array $errors, string $message = 'Validation failed')
```

### `getErrors(): array`

Mengembalikan properti `$errors` yang dipromosikan — associative array `['field' => 'message']`.

```php
try {
    // ...
} catch (ValidationException $e) {
    $errors = $e->getErrors();
    // ['email' => 'The email has already been taken.']
}
```

## Global Exception Handler

Terletak di `bootstrap/app.php`:

```
set_exception_handler()
         │
         ├── Logger::error(message, [file, line, trace])
         │
         ├── http_response_code($statusCode)
         │     └── HttpException → getStatusCode()
         │         Other → 500
         │
         ├── APP_DEBUG = true?
         │     │
         │     ├── YES → Output debug HTML:
         │     │         <h1>Exception Caught</h1>
         │     │         Type, Message (escaped), File, Line, Trace
         │     │
         │     └── NO → Halaman error yang ramah pengguna:
         │               ├── 404 → require Views/errors/404.php
         │               ├── *   → require Views/errors/500.php
         │               └── fallback → inline <h1>{code} Error</h1>
         │
         └── exit(1)
```

## Template View Error

### `src/Views/errors/404.php`

Menerima variabel `$message`. Teks default dalam bahasa Indonesia: *"Halaman yang Anda cari tidak ditemukan."*

### `src/Views/errors/500.php`

Menerima variabel `$message`. Teks default dalam bahasa Indonesia: *"Terjadi kesalahan pada server kami."*

## Integrasi Logger

Setiap exception dicatat melalui `Logger::error()` sebelum dirender:

```php
Logger::error($e->getMessage(), [
    'file'  => $e->getFile(),
    'line'  => $e->getLine(),
    'trace' => $e->getTraceAsString(),
]);
```

Log ditulis ke `storage/logs/YYYY-MM-DD.log`.

## Membuat Custom Exception

```php
<?php
declare(strict_types=1);

namespace App\Exceptions;

class PaymentFailedException extends AppException {
    public function __construct(string $reason) {
        parent::__construct("Payment failed: {$reason}");
    }
}
```

Atau gunakan scaffold: `php vanilla make:exception PaymentFailed`

Untuk HTTP exception dengan kode status spesifik:

```php
class ForbiddenException extends HttpException {
    public function __construct(string $message = 'Access denied') {
        parent::__construct(403, $message);
    }
}
```

---

Lihat: [ARCHITECTURE.md](../core/ARCHITECTURE.md) · [VIEWS.md](../frontend/VIEWS.md) · [SECURITY.md](SECURITY.md)