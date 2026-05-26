# Security

## Defense Matrix

| Attack Vector | Mitigation | Implementation |
|--------------|------------|----------------|
| **SQL Injection** | Prepared statements | `PDO::EMULATE_PREPARES = false`, all queries use `?` placeholders |
| **XSS** | Output escaping | `View::e()` → `htmlspecialchars(ENT_QUOTES, UTF-8)` |
| **CSRF** | Token verification | `Csrf::token()` + `Csrf::verify()` via `CsrfMiddleware` |
| **Session Hijacking** | Session regeneration | `Session::regenerate()` on login |
| **Password Cracking** | Bcrypt hashing | `password_hash(PASSWORD_DEFAULT)` / `password_verify()` |

## CSRF Protection

### Token Generation

```php
use App\Core\Csrf;

$token = Csrf::token();   // Returns existing token or generates new one
```

Contract: `bin2hex(random_bytes(32))` — 64 hex characters stored in `$_SESSION['_csrf_token']`.

### Token Verification

```php
Csrf::verify(string $token): bool
```

Uses `hash_equals()` for constant-time comparison — prevents timing attacks.

### Hidden Field Helper

```php
Csrf::field(): string
```

Returns: `<input type="hidden" name="_csrf_token" value="...">`.

Use in forms:

```php
<form method="POST" action="/users">
    <?= \App\Core\Csrf::field() ?>
    <!-- form fields -->
</form>
```

### Middleware Enforcement

`CsrfMiddleware` automatically enforces CSRF on all non-GET requests:

```php
// Only applied when middleware alias 'csrf' is in the route group
$router->group(['middleware' => ['csrf']], function ($router) {
    $router->post('/submit', ...);
});
```

When a token is missing or mismatched: `HttpException(419, 'Page Expired — CSRF token mismatch.')`.

### Token Regeneration

```php
Csrf::regenerate(): string  // generate new token, return it
```

Called after login to prevent session fixation attacks.

## SQL Injection Protection

All database queries use PDO prepared statements with parameter binding:

```php
// BaseModel::query() and BaseModel::execute()
$stmt = $this->db()->prepare($sql);
$stmt->execute($bindings);
```

PDO option `EMULATE_PREPARES = false` ensures **real** server-side prepared statements.

**Never** concatenate user input into SQL:

```php
// WRONG — SQL injection
$db->query("SELECT * FROM users WHERE id = " . $_GET['id']);

// CORRECT
$model->find((int) $request->param('id'));
```

## XSS Protection

All user-facing output must be escaped:

```php
<?= \App\Core\View::e($user['name']) ?>
<?= e($user['name']) ?>        // helper shorthand
```

Contract: `htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8')`.

## Password Handling

| Operation | Function | Used In |
|-----------|----------|---------|
| Hash on create/update | `password_hash($password, PASSWORD_DEFAULT)` | `UserService::create()`, `UserService::update()` |
| Verify on login | `password_verify($input, $hash)` | `AuthService::login()` |

Bcrypt cost factor: PHP default (currently 10).

## Session Security

| Measure | Implementation |
|---------|----------------|
| Session regeneration on login | `Session::regenerate(true)` — deletes old session |
| Session destroy on logout | `Session::destroy()` — clears all session data |
| HTTP-only cookies | PHP default `session.cookie_httponly` |

## Auth Flow

```
Login Form (GET /login)
     │
     ▼
POST /login (email + password + _csrf_token)
     │
     ├── CsrfMiddleware::handle() → verify token
     │
     ├── AuthController::login()
     │     ├── Validate input (required email, required password)
     │     ├── AuthService::login(email, password)
     │     │     ├── User::findByEmail(email)
     │     │     ├── password_verify(input, hash)
     │     │     │     ├── PASS → Session::regenerate()
     │     │     │     │         Session::set('user', userData)
     │     │     │     │         return true
     │     │     │     └── FAIL → return false
     │     ├── Success → redirect /users
     │     └── Failure → flash error → redirect /login
     │
     ▼
Authenticated Session
     │
     ├── AuthMiddleware::handle()
     │     └── Session::has('user') → allow/deny
     │
     ├── RoleMiddleware::handle()
     │     └── Session::get('user')['role'] check
     │
     ▼
POST /logout
     ├── Session::destroy()
     └── redirect /login
```

## Stored Session Data

After login, `$_SESSION['user']` contains:

```php
[
    'id'    => int,
    'name'  => string,
    'email' => string,
    'role'  => string,    // 'admin' or 'user'
]
```

> Password hash is **never** stored in the session.

## Production Checklist

| Setting | Value | File |
|---------|-------|------|
| `APP_DEBUG` | `false` | `.env` |
| `APP_ENV` | `production` | `.env` |
| Strong DB password | Set | `.env` |
| HTTPS enforcement | Configure in web server | nginx/Apache config |
| Session cookie secure | `session.cookie_secure = 1` | `php.ini` |
| File permissions | `storage/` writable, `src/` read-only | Server config |

---

See: [MIDDLEWARE.md](MIDDLEWARE.md) · [ERROR_HANDLING.md](ERROR_HANDLING.md) · [CONVENTIONS.md](CONVENTIONS.md)
