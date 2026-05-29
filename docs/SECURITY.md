# Security

## Defense Matrix

| Attack Vector | Mitigation | Implementation |
|--------------|------------|----------------|
| **SQL Injection** | Prepared statements | `PDO::EMULATE_PREPARES = false`, all queries use `?` placeholders |
| **XSS** | Output escaping | `View::e()` → `htmlspecialchars(ENT_QUOTES, UTF-8)` |
| **CSRF** | Token verification | `Csrf::token()` + `Csrf::verify()` via `CsrfMiddleware` |
| **Session Hijacking** | Session regeneration | `Session::regenerate()` on login |
| **Password Cracking** | HMAC + Bcrypt | `password_hash(Encryptor::hmac($plain), PASSWORD_DEFAULT)` |
| **Bot abuse (public forms)** | Cloudflare Turnstile (optional) | `Turnstile::verify()` on login, register, order, track |

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

## APP_KEY and password handling

Passwords are **never** hashed directly. The app derives an HMAC with `APP_KEY` first, then bcrypts the result:

```php
password_hash(Encryptor::hmac($plainPassword), PASSWORD_DEFAULT);
password_verify(Encryptor::hmac($input), $storedHash);
```

| Requirement | Detail |
|-------------|--------|
| `APP_KEY` in `.env` | Required before login, registration, or seeding users |
| Format | Plain string or `base64:` + 32 bytes (see `Encryptor::key()`) |
| Used in | `AuthService`, `UserService`, `AdminSeeder` |

Bcrypt uses PHP's default cost (currently 10).

## Cloudflare Turnstile (optional)

When `TURNSTILE_ENABLED=true`, public POST endpoints verify `cf-turnstile-response`:

| Endpoint | Controller |
|----------|------------|
| `POST /auth/login`, `POST /login` | `AuthController::login` |
| `POST /auth/register` | `AuthController::register` |
| `POST /order-form` | `OrderController::publicSubmit` |
| `POST /track-order` | `OrderController::track` |

Env keys: `TURNSTILE_SITE_KEY_MANAGED`, `TURNSTILE_SECRET_KEY_MANAGED` (mapped to `TURNSTILE_SITE_KEY` / `TURNSTILE_SECRET_KEY` constants in `config/app.php`).

Widget markup: `Turnstile::widget()` in auth and order views. JS: `public/assets/js/modules/turnstile.js`.

## Session Security

| Measure | Implementation |
|---------|----------------|
| Session regeneration on login | `Session::regenerate(true)` — deletes old session |
| Session destroy on logout | `Session::destroy()` — clears all session data |
| HTTP-only cookies | PHP default `session.cookie_httponly` |

## Auth Flow

```
Auth page (GET /auth) — login + register tabs
     │
     ▼
POST /auth/login or POST /login (email + password + optional Turnstile)
     │
     ├── Turnstile::verify() (if enabled)
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
