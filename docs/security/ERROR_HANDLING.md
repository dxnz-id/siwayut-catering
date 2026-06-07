# Error Handling

## Exception Hierarchy

```
\RuntimeException
  └── App\Exceptions\AppException
        ├── App\Exceptions\HttpException
        │     └── App\Exceptions\NotFoundException
        └── App\Exceptions\ValidationException
```

## AppException

Empty extension of `\RuntimeException` — base class for all application exceptions.

```php
namespace App\Exceptions;
class AppException extends \RuntimeException {}
```

## HttpException

HTTP error with status code and default messages.

### Constructor

```php
public function __construct(int $code, string $message = '')
```

If `$message` is empty, `defaultMessage()` provides a default based on the status code.

### `getStatusCode(): int`

Returns the HTTP status code.

### `defaultMessage()` — Match Table

| Code | Default Message |
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

### Usage

```php
throw new HttpException(403);                    // "Forbidden"
throw new HttpException(403, 'Custom message');  // "Custom message"
throw new HttpException(419, 'CSRF mismatch');   // 419 Page Expired
```

## NotFoundException

Shortcut for 404 errors.

```php
public function __construct(string $message = 'Resource not found')
```

Hardcoded status code: **404**. Thrown by `Router::dispatch()` when no route matches.

```php
throw new NotFoundException();            // "Resource not found"
throw new NotFoundException('User not found');
```

## ValidationException

Carries validation errors.

```php
public function __construct(private array $errors, string $message = 'Validation failed')
```

### `getErrors(): array`

Returns the promoted `$errors` property — `['field' => 'message']` associative array.

```php
try {
    // ...
} catch (ValidationException $e) {
    $errors = $e->getErrors();
    // ['email' => 'The email has already been taken.']
}
```

## Global Exception Handler

Located in `bootstrap/app.php`:

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
         │     ├── YES → HTML debug output:
         │     │         <h1>Exception Caught</h1>
         │     │         Type, Message (escaped), File, Line, Trace
         │     │
         │     └── NO → Friendly error page:
         │               ├── 404 → require Views/errors/404.php
         │               ├── *   → require Views/errors/500.php
         │               └── fallback → inline <h1>{code} Error</h1>
         │
         └── exit(1)
```

## Error View Templates

### `src/Views/errors/404.php`

Receives `$message` variable. Default text is in Indonesian: *"Halaman yang Anda cari tidak ditemukan."*

### `src/Views/errors/500.php`

Receives `$message` variable. Default text is in Indonesian: *"Terjadi kesalahan pada server kami."*

## Logger Integration

Every exception is logged via `Logger::error()` before rendering:

```php
Logger::error($e->getMessage(), [
    'file'  => $e->getFile(),
    'line'  => $e->getLine(),
    'trace' => $e->getTraceAsString(),
]);
```

Logs are written to `storage/logs/YYYY-MM-DD.log`.

## Creating Custom Exceptions

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

Or scaffold: `php vanilla make:exception PaymentFailed`

For HTTP exceptions with a specific status code:

```php
class ForbiddenException extends HttpException {
    public function __construct(string $message = 'Access denied') {
        parent::__construct(403, $message);
    }
}
```

---

See: [ARCHITECTURE.md](../core/ARCHITECTURE.md) · [VIEWS.md](../frontend/VIEWS.md) · [SECURITY.md](SECURITY.md)
