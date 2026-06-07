# Security

## Defense Matrix

| Attack Vector | Mitigation | Implementation |
|--------------|------------|----------------|
| **SQL Injection** | Prepared statements | `PDO::EMULATE_PREPARES = false`, all queries use `?` placeholders |
| **XSS** | Output escaping | `View::e()` → `htmlspecialchars(ENT_QUOTES, UTF-8)` |
| **CSRF** | Token verification | `Csrf::token()` + `Csrf::verify()` via `CsrfMiddleware` |
| **Session Hijacking** | Session regeneration | `Session::regenerate()` on login |
| **Password Cracking** | Bcrypt hashing | `password_hash(PASSWORD_DEFAULT)` / `password_verify()` |
| **Brute Force** | Progressive Delay / Rate Limit | `AuthService::login()` delay + `rate.limit` middleware |
| **Spam / Bot Submissions** | CAPTCHA | Cloudflare Turnstile integration on public forms |
| **SSRF (Server-Side Request Forgery)** | IP Validation | Private IP subnet checks in `FileUploadService::uploadFromUrl` |

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

## Anti-Brute Force & Rate Limiting

### 1. Progressive Delay (Login)
In `AuthService::login()`, failed login attempts increment a counter in the session. Each subsequent failure adds a `sleep()` delay (e.g., 0.5s, 1s, 2s, 4s) to computationally exhaust automated dictionary attacks.

### 2. Route Rate Limiting
Global endpoint abuse is mitigated via the `RateLimitMiddleware` (`rate.limit:requests,minutes`). It tracks requests per IP address in the session (or cache) and throws a 429 Too Many Requests if exceeded.

## Cloudflare Turnstile CAPTCHA

Public forms (like the Order Form) integrate Turnstile to prevent bot spam.
- Controlled via `TURNSTILE_ENABLED=true` in `.env`.
- Frontend loads the widget (`modules/turnstile.js`).
- Backend validates the token via Cloudflare's `/siteverify` endpoint in the Controller before processing data.

## File Upload Security (SSRF Protection)

When downloading menu images from a URL (e.g., from an AI generator), `FileUploadService` validates the resolved IP address of the target URL to ensure it is not a private IP address (`10.0.0.0/8`, `192.168.0.0/16`, `127.0.0.0/8`). This prevents Server-Side Request Forgery attacks targeting internal services.

## Session Security

| Measure | Implementation |
|---------|----------------|
| Session regeneration on login | `Session::regenerate(true)` — deletes old session |
| Session destroy on logout | `Session::destroy()` — clears all session data |
| HTTP-only cookies | PHP default `session.cookie_httponly` |
| **Idle Timeout** | `SessionTimeoutMiddleware` limits idle time (Admin: 30m, User: 2h) |

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

See: [MIDDLEWARE.md](../core/MIDDLEWARE.md) · [ERROR_HANDLING.md](ERROR_HANDLING.md) · [CONVENTIONS.md](../guides/CONVENTIONS.md)
