# Penanganan Kesalahan (Error Handling)

## Hierarki Exception (Pengecualian)

```
\RuntimeException
  └── App\Exceptions\AppException
        ├── App\Exceptions\HttpException
        │     └── App\Exceptions\NotFoundException
        └── App\Exceptions\ValidationException
```

## AppException

Ekstensi kosong dari `\RuntimeException` — merupakan kelas dasar (base class) untuk semua exception aplikasi.

```php
namespace App\Exceptions;
class AppException extends \RuntimeException {}
```

## HttpException

Kesalahan HTTP dengan kode status dan pesan bawaan (default).

### Konstruktor

```php
public function __construct(int $code, string $message = '')
```

Jika `$message` kosong, `defaultMessage()` akan menyediakan pesan default berdasarkan kode status HTTP.

### `getStatusCode(): int`

Mengembalikan kode status HTTP.

### `defaultMessage()` — Tabel Pencocokan

| Kode | Pesan Default | Terjemahan |
|------|---------------|------------|
| 400 | Bad Request | Permintaan Buruk |
| 401 | Unauthorized | Tidak Berwenang |
| 403 | Forbidden | Terlarang |
| 404 | Not Found | Tidak Ditemukan |
| 405 | Method Not Allowed | Metode Tidak Diperbolehkan |
| 419 | Page Expired | Halaman Kedaluwarsa |
| 422 | Unprocessable Entity | Entitas Tidak Dapat Diproses |
| 429 | Too Many Requests | Terlalu Banyak Permintaan |
| 500 | Internal Server Error | Kesalahan Server Internal |

### Penggunaan

```php
throw new HttpException(403);                    // "Forbidden"
throw new HttpException(403, 'Pesan khusus');    // "Pesan khusus"
throw new HttpException(419, 'CSRF tidak cocok'); // 419 Page Expired
```

## NotFoundException

Jalan pintas (shortcut) untuk kesalahan 404.

```php
public function __construct(string $message = 'Resource not found')
```

Kode status yang tertanam secara permanen: **404**. Dilemparkan oleh `Router::dispatch()` ketika tidak ada rute yang cocok.

```php
throw new NotFoundException();            // "Resource not found"
throw new NotFoundException('Pengguna tidak ditemukan');
```

## ValidationException

Membawa kesalahan validasi.

```php
public function __construct(private array $errors, string $message = 'Validation failed')
```

### `getErrors(): array`

Mengembalikan properti `$errors` yang dipromosikan (promoted property) — berupa array asosiatif `['field' => 'message']`.

```php
try {
    // ...
} catch (ValidationException $e) {
    $errors = $e->getErrors();
    // ['email' => 'Email sudah digunakan.']
}
```

## Penangan Exception Global (Global Exception Handler)

Terletak di `bootstrap/app.php`:

```
set_exception_handler()
         │
         ├── Logger::error(message, [file, line, trace])
         │
         ├── http_response_code($statusCode)
         │     └── HttpException → getStatusCode()
         │         Lainnya → 500
         │
         ├── APP_DEBUG = true?
         │     │
         │     ├── YA → Output HTML debug:
         │     │         <h1>Exception Caught</h1>
         │     │         Type, Message (escaped), File, Line, Trace
         │     │
         │     └── TIDAK → Halaman kesalahan ramah pengguna:
         │               ├── 404 → memuat Views/errors/404.php
         │               ├── *   → memuat Views/errors/500.php
         │               └── fallback → inline <h1>{code} Error</h1>
         │
         └── exit(1)
```

## Templat View Kesalahan

### `src/Views/errors/404.php`

Menerima variabel `$message`. Teks bawaan adalah bahasa Indonesia: *"Halaman yang Anda cari tidak ditemukan."*

### `src/Views/errors/500.php`

Menerima variabel `$message`. Teks bawaan adalah bahasa Indonesia: *"Terjadi kesalahan pada server kami."*

## Integrasi Logger

Setiap exception dicatat sebagai log via `Logger::error()` sebelum dirender ke pengguna:

```php
Logger::error($e->getMessage(), [
    'file'  => $e->getFile(),
    'line'  => $e->getLine(),
    'trace' => $e->getTraceAsString(),
]);
```

Log ditulis ke dalam berkas `storage/logs/YYYY-MM-DD.log`.

## Membuat Exception Kustom

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

Atau menggunakan perancah: `php vanilla make:exception PaymentFailed`

Untuk exception HTTP dengan kode status tertentu:

```php
class ForbiddenException extends HttpException {
    public function __construct(string $message = 'Access denied') {
        parent::__construct(403, $message);
    }
}
```

---

Lihat: [ARCHITECTURE.md](ARCHITECTURE.md) · [VIEWS.md](VIEWS.md) · [SECURITY.md](SECURITY.md)
