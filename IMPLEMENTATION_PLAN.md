# IMPLEMENTATION_PLAN.md — Siwayut Catering

> Build plan reconstructed from repository structure on 2026-05-26.
> Describes the exact dependency ordering and implementation phases required to complete the scaffolded codebase.

---

## Dependency Graph

```
                    ┌──────────────────────┐
                    │   .env / config/     │
                    │   app.php            │
                    │   (constants)        │
                    └──────────┬───────────┘
                               │
              ┌────────────────┼────────────────┐
              │                │                │
              ▼                ▼                ▼
     ┌────────────┐   ┌──────────────┐   ┌──────────────┐
     │  Logger    │   │  Session     │   │  Database    │
     │  (static)  │   │  (static)    │   │  (singleton) │
     └──────┬─────┘   └──────┬───────┘   └──────┬───────┘
            │                │                   │
            │                │                   │
            │          ┌─────┴──────┐            │
            │          │   Csrf     │            │
            │          │  (static)  │            │
            │          └────────────┘            │
            │                                    │
            │                               ┌────┴───────┐
            │                               │ Validator  │
            │                               └────────────┘
            │                                    │
            │                ┌───────────────────┘
            │                │
            │                ▼
            │         ┌─────────────┐
            │         │  BaseModel  │
            │         └──────┬──────┘
            │                │
            │                ▼
            │         ┌─────────────┐
            │         │  User       │
            │         └──────┬──────┘
            │                │
            │     ┌──────────┼───────────────┐
            │     │          │               │
            │     ▼          ▼               ▼
            │ ┌──────────┐ ┌──────────┐ ┌────────────────┐
            │ │AuthService│ │UserService│ │FileUploadService│
            │ └─────┬────┘ └────┬─────┘ └────────────────┘
            │       │           │
            │       ▼           ▼
            │ ┌──────────────────────────┐
            │ │      BaseController      │
            │ │     (View, Response)      │
            │ └──────────┬───────────────┘
            │            │
            │  ┌─────────┼──────────┐
            │  ▼         ▼          ▼
            │ ┌────────┐┌─────────┐┌───────────────┐
            │ │Welcome ││Auth     ││User           │
            │ │Ctrl    ││Ctrl     ││Ctrl           │
            │ └────────┘└─────────┘└───────────────┘
            │
            │         ┌─────────────────┐
            │         │MiddlewareInterface│
            │         └────────┬────────┘
            │    ┌─────────────┼────────────┐
            │    ▼             ▼            ▼
            │ ┌──────────┐ ┌──────────┐ ┌──────────┐
            │ │AuthMW    │ │CsrfMW    │ │RoleMW    │
            │ └──────────┘ └──────────┘ └──────────┘
            │
            │                                  ┌──────────┐
            └──────────────────────────────────►│Container │
                                               └────┬─────┘
                                                    │
                                                    ▼
                                               ┌──────────┐
                                               │  Router  │
                                               └──────────┘
                                                    │
                                                    ▼
                                               ┌──────────┐
                                               │  Request  │
                                               │ (dispatch)│
                                               └──────────┘


  Exceptions (independent):
    RuntimeException
      └── AppException
            ├── HttpException
            │     └── NotFoundException
            └── ValidationException

  Helpers (independent, auto-loaded):
    functions.php
```

---

## Ordered Build Phases

### Phase 0 — Prerequisites (Foundation)
No code dependencies. Can be completed immediately.

| # | File | Task |
|---|------|------|
| 0.1 | `database/migrations/001_create_users_table.sql` | Write CREATE TABLE DDL |

### Phase 1 — Core Infrastructure (No Inter-Dependencies)
These components depend only on PHP superglobals and constants. Implement in any order.

| # | File | Task |
|---|------|------|
| 1.1 | `src/Core/Session.php` | Implement all static methods |
| 1.2 | `src/Core/Logger.php` | Implement `setPath()`, `write()`, and level methods |
| 1.3 | `src/Core/Database.php` | Implement singleton `getInstance()` from `config/database.php` |
| 1.4 | `src/Core/Request.php` | Implement all TODO methods |
| 1.5 | `src/Core/Response.php` | Implement `json()`, `jsonSuccess()`, `jsonError()`, `setStatusCode()`, `text()`, `download()` |
| 1.6 | `src/Helpers/functions.php` | Implement all 10 helper functions |
| 1.7 | `src/Exceptions/ValidationException.php` | Implement constructor (call `parent::__construct`) |

### Phase 2 — Security Layer
Depends on Session (Phase 1.1).

| # | File | Task |
|---|------|------|
| 2.1 | `src/Core/Csrf.php` | Implement `token()`, `verify()`, `field()`, `regenerate()` |
| 2.2 | `src/Core/Validator.php` | Implement `validate()`, `errors()`, `error()`, `fails()`, `applyRule()` |

### Phase 3 — Data Layer
Depends on Database (Phase 1.3).

| # | File | Task |
|---|------|------|
| 3.1 | `src/Models/BaseModel.php` | Implement constructor + all CRUD/query methods |
| 3.2 | `src/Models/User.php` | Implement constructor (set `$table`) + `findByEmail()` |

### Phase 4 — Middleware Layer
Depends on Session (Phase 1.1), Csrf (Phase 2.1), Request (Phase 1.4).

| # | File | Task |
|---|------|------|
| 4.1 | `src/Middleware/AuthMiddleware.php` | Implement session-based auth check |
| 4.2 | `src/Middleware/CsrfMiddleware.php` | Implement CSRF token verification |
| 4.3 | `src/Middleware/RoleMiddleware.php` | Implement role check against session user |

### Phase 5 — Service Layer
Depends on User model (Phase 3.2), Session (Phase 1.1).

| # | File | Task |
|---|------|------|
| 5.1 | `src/Services/AuthService.php` | Implement `login()` (password_verify, session), `logout()` |
| 5.2 | `src/Services/UserService.php` | Implement all CRUD delegation methods |
| 5.3 | `src/Services/FileUploadService.php` | Implement `upload()` and `delete()` |

### Phase 6 — Controller Layer
Depends on Services (Phase 5), BaseController (already implemented), Validator (Phase 2.2).

| # | File | Task |
|---|------|------|
| 6.1 | `src/Controllers/BaseController.php` | Implement `redirect()`, `redirectWithFlash()`, `currentUser()`, `back()`, `withOldInput()` |
| 6.2 | `src/Controllers/AuthController.php` | Implement `index()`, `login()`, `logout()` |
| 6.3 | `src/Controllers/UserController.php` | Implement all 6 action methods |

### Phase 7 — View Templates
Depends on Controllers (Phase 6), helpers (Phase 1.6), Csrf (Phase 2.1).

| # | File | Task |
|---|------|------|
| 7.1 | `src/Views/layouts/main.php` | Implement full admin layout (navbar, sidebar, flash, content) |
| 7.2 | `src/Views/layouts/auth.php` | Implement auth layout (centered form layout) |
| 7.3 | `src/Views/partials/navbar.php` | Implement navigation bar |
| 7.4 | `src/Views/partials/sidebar.php` | Implement sidebar |
| 7.5 | `src/Views/partials/flash.php` | Implement flash message display |
| 7.6 | `src/Views/auth/login.php` | Implement login form |
| 7.7 | `src/Views/user/index.php` | Implement user listing table |
| 7.8 | `src/Views/user/create.php` | Implement user creation form |
| 7.9 | `src/Views/user/edit.php` | Implement user edit form |

### Phase 8 — Static Assets
No strict dependency.

| # | File | Task |
|---|------|------|
| 8.1 | `public/assets/css/app.css` | Implement application stylesheet |
| 8.2 | `public/assets/js/app.js` | Implement client-side JavaScript |

### Phase 9 — Data Seeding
Depends on Database (Phase 1.3), User model (Phase 3.2).

| # | File | Task |
|---|------|------|
| 9.1 | `database/seeds/AdminSeeder.php` | Implement admin user insertion |

---

## File-by-File Dependency Ordering

```
Level 0 (no deps):
  .env, config/app.php, config/database.php
  src/Exceptions/AppException.php        ← DONE
  src/Exceptions/HttpException.php       ← DONE
  src/Exceptions/NotFoundException.php   ← DONE
  database/migrations/001_create_users_table.sql

Level 1 (depends on Level 0):
  src/Core/Session.php
  src/Core/Logger.php
  src/Core/Database.php
  src/Core/Request.php
  src/Core/Response.php
  src/Helpers/functions.php
  src/Exceptions/ValidationException.php

Level 2 (depends on Level 1):
  src/Core/Csrf.php                      ← needs Session
  src/Core/Validator.php                 ← needs PDO (optional)

Level 3 (depends on Level 1):
  src/Models/BaseModel.php               ← needs Database
  src/Models/User.php                    ← needs BaseModel

Level 4 (depends on Levels 1-2):
  src/Middleware/AuthMiddleware.php       ← needs Session, Request
  src/Middleware/CsrfMiddleware.php       ← needs Csrf, Request
  src/Middleware/RoleMiddleware.php       ← needs Session, Request

Level 5 (depends on Level 3):
  src/Services/AuthService.php           ← needs User
  src/Services/UserService.php           ← needs User
  src/Services/FileUploadService.php     ← needs filesystem

Level 6 (depends on Levels 1-5):
  src/Controllers/BaseController.php     ← needs View, Session, Response
  src/Controllers/AuthController.php     ← needs AuthService, BaseController
  src/Controllers/UserController.php     ← needs UserService, BaseController
  src/Controllers/WelcomeController.php  ← DONE

Level 7 (depends on Level 6):
  src/Views/**                           ← template files

Level 8 (independent):
  public/assets/css/app.css
  public/assets/js/app.js
  database/seeds/AdminSeeder.php
```

---

## Contract Validation Matrix

| Contract | Source File | Validation Method | Status |
|----------|-----------|-------------------|--------|
| PSR-4 autoloading | `composer.json` | `composer dump-autoload` | ✅ Verifiable |
| `declare(strict_types=1)` | All `.php` files | grep scan | ✅ Present in all files |
| `BASE_PATH` defined | `public/index.php` | Runtime check | ✅ Implemented |
| `.env` → `$_ENV` | `public/index.php` | Runtime check | ✅ Implemented |
| Constants defined | `config/app.php` | Runtime check | ✅ Implemented |
| Container singleton behavior | `Container::make()` | Unit test | ✅ Implemented |
| Container auto-wiring | `Container::makeNew()` | Unit test | ✅ Implemented |
| Router first-match dispatch | `Router::dispatch()` | Unit test | ✅ Implemented |
| Router group nesting | `Router::group()` | Unit test | ✅ Implemented |
| Middleware boolean gate | `Router::runMiddlewarePipeline()` | Unit test | ✅ Implemented |
| Middleware parameterized resolution | `Router::resolveMiddleware()` | Unit test | ✅ Implemented |
| `MiddlewareInterface` contract | `MiddlewareInterface.php` | `php -l` | ✅ Defined |
| `Response::redirect()` terminates | `Response.php` | Code review | ✅ Implemented |
| `View::e()` escaping | `View.php` | Code review | ✅ Implemented |
| `BaseController::__construct()` | `BaseController.php` | Code review | ✅ Implemented |
| Exception hierarchy | `Exceptions/*.php` | Reflection | ✅ Implemented |
| `HttpException::defaultMessage()` | `HttpException.php` | Code review | ✅ Implemented |
| `NotFoundException` fixed 404 | `NotFoundException.php` | Code review | ✅ Implemented |
| `ValidationException::getErrors()` | `ValidationException.php` | Code review | ✅ Implemented |
| `WelcomeController::index()` renders | `WelcomeController.php` | Browser test | ✅ Implemented |
| Session all methods | `Session.php` | Runtime test | ❌ All TODO |
| Logger all methods | `Logger.php` | Runtime test | ❌ All TODO |
| Database singleton | `Database.php` | Runtime test | ❌ TODO (returns SQLite) |
| Request body methods | `Request.php` | Runtime test | ❌ All TODO |
| Response JSON methods | `Response.php` | Runtime test | ❌ All TODO |
| Csrf all methods | `Csrf.php` | Runtime test | ❌ All TODO |
| Validator all methods | `Validator.php` | Runtime test | ❌ All TODO |
| BaseModel all methods | `BaseModel.php` | Runtime test | ❌ All TODO |
| User model | `User.php` | Runtime test | ❌ TODO |
| All Services | `Services/*.php` | Runtime test | ❌ All TODO |
| AuthController actions | `AuthController.php` | Runtime test | ❌ All TODO |
| UserController actions | `UserController.php` | Runtime test | ❌ All TODO |
| BaseController helpers | `BaseController.php` | Runtime test | ❌ All TODO |
| All Middleware | `Middleware/*.php` | Runtime test | ❌ All TODO (Auth returns true, others false) |
| Helper functions | `functions.php` | Runtime test | ❌ All TODO |
| View templates | `Views/**` | Browser test | ❌ Most TODO |
| Database migration | `001_create_users_table.sql` | SQL parse | ❌ TODO |
| Admin seeder | `AdminSeeder.php` | Runtime test | ❌ TODO |

---

## Risk Analysis

```
┌─────────────────────────────────────────┬──────────┬────────────┬──────────────────────────────────────┐
│ Risk                                    │ Severity │ Likelihood │ Mitigation                           │
├─────────────────────────────────────────┼──────────┼────────────┼──────────────────────────────────────┤
│ Database singleton returns SQLite       │ CRITICAL │ CERTAIN    │ Phase 1.3 — implement getInstance() │
│ in-memory instead of MySQL              │          │            │ with config/database.php             │
├─────────────────────────────────────────┼──────────┼────────────┼──────────────────────────────────────┤
│ Request::setRouteParams() is no-op      │ CRITICAL │ CERTAIN    │ Phase 1.4 — route params never      │
│ → all route params lost                 │          │            │ stored, controllers get nulls        │
├─────────────────────────────────────────┼──────────┼────────────┼──────────────────────────────────────┤
│ Session::start() is no-op              │ CRITICAL │ CERTAIN    │ Phase 1.1 — no session available     │
│ → auth, flash, CSRF all broken         │          │            │ for any feature                      │
├─────────────────────────────────────────┼──────────┼────────────┼──────────────────────────────────────┤
│ AuthMiddleware returns true always     │ HIGH     │ CERTAIN    │ Phase 4.1 — all auth-protected       │
│ → no authentication enforcement        │          │            │ routes accessible without login       │
├─────────────────────────────────────────┼──────────┼────────────┼──────────────────────────────────────┤
│ CsrfMiddleware returns false always    │ HIGH     │ CERTAIN    │ Phase 4.2 — if csrf middleware is    │
│ → any route with csrf MW will 403      │          │            │ applied, all POST requests fail      │
├─────────────────────────────────────────┼──────────┼────────────┼──────────────────────────────────────┤
│ RoleMiddleware returns false always    │ HIGH     │ CERTAIN    │ Phase 4.3 — all admin routes         │
│ → /users/* routes always blocked       │          │            │ inaccessible (even when authed)      │
├─────────────────────────────────────────┼──────────┼────────────┼──────────────────────────────────────┤
│ No migration DDL exists                │ HIGH     │ CERTAIN    │ Phase 0.1 — no database schema       │
│ → cannot seed or query users           │          │            │ to create                            │
├─────────────────────────────────────────┼──────────┼────────────┼──────────────────────────────────────┤
│ ValidationException constructor does   │ MEDIUM   │ CERTAIN    │ Phase 1.7 — parent::__construct()    │
│ not call parent::__construct()         │          │            │ never called, message not set        │
├─────────────────────────────────────────┼──────────┼────────────┼──────────────────────────────────────┤
│ Logger::error() called in exception    │ MEDIUM   │ CERTAIN    │ Phase 1.2 — exception handler logs   │
│ handler but is no-op                   │          │            │ nothing; errors silently lost        │
├─────────────────────────────────────────┼──────────┼────────────┼──────────────────────────────────────┤
│ Container make() caches singletons     │ LOW      │ POSSIBLE   │ Document — if service holds state    │
│ → stateful services share state        │          │            │ across requests (not typical in PHP) │
├─────────────────────────────────────────┼──────────┼────────────┼──────────────────────────────────────┤
│ View layout receives both $content     │ LOW      │ POSSIBLE   │ Document — name collision if $data   │
│ and extract($data) — name collision    │          │            │ contains key 'content'               │
└─────────────────────────────────────────┴──────────┴────────────┴──────────────────────────────────────┘
```

---

## Verification Pipeline

### 1. Autoload Verification

```bash
# Dump and verify PSR-4 autoload map
composer dump-autoload

# Verify all classes can be autoloaded
php -r "require 'vendor/autoload.php'; new App\Core\Container();"
php -r "require 'vendor/autoload.php'; new App\Core\Router(new App\Core\Container());"
```

### 2. Syntax Verification

```bash
# Lint all PHP files
find src/ bootstrap/ config/ database/ public/ -name '*.php' -exec php -l {} \;
```

### 3. Namespace Audit

```bash
# Verify every class in src/ has namespace App\...
grep -rn 'namespace ' src/ | grep -v 'namespace App\\'

# Verify database/ classes have namespace Database\...
grep -rn 'namespace ' database/ | grep -v 'namespace Database\\'
```

### 4. Strict Types Audit

```bash
# Verify every PHP file has declare(strict_types=1)
find src/ bootstrap/ config/ database/ -name '*.php' -exec grep -L 'declare(strict_types=1)' {} \;
```

### 5. Signature Audit

```bash
# Verify MiddlewareInterface compliance
php -r "
require 'vendor/autoload.php';
\$ref = new ReflectionClass(App\Middleware\AuthMiddleware::class);
assert(\$ref->implementsInterface(App\Middleware\MiddlewareInterface::class));
echo 'AuthMiddleware: OK' . PHP_EOL;
\$ref = new ReflectionClass(App\Middleware\CsrfMiddleware::class);
assert(\$ref->implementsInterface(App\Middleware\MiddlewareInterface::class));
echo 'CsrfMiddleware: OK' . PHP_EOL;
\$ref = new ReflectionClass(App\Middleware\RoleMiddleware::class);
assert(\$ref->implementsInterface(App\Middleware\MiddlewareInterface::class));
echo 'RoleMiddleware: OK' . PHP_EOL;
"
```

### 6. Contract Audit

```bash
# Verify Response never-return methods
php -r "
require 'vendor/autoload.php';
\$ref = new ReflectionClass(App\Core\Response::class);
foreach (\$ref->getMethods() as \$m) {
    \$rt = \$m->getReturnType();
    if (\$rt && \$rt->getName() === 'never') {
        echo \$m->getName() . ': never ✓' . PHP_EOL;
    }
}
"

# Verify exception hierarchy
php -r "
require 'vendor/autoload.php';
assert(is_subclass_of(App\Exceptions\HttpException::class, App\Exceptions\AppException::class));
assert(is_subclass_of(App\Exceptions\NotFoundException::class, App\Exceptions\HttpException::class));
assert(is_subclass_of(App\Exceptions\ValidationException::class, App\Exceptions\AppException::class));
echo 'Exception hierarchy: OK' . PHP_EOL;
"

# Verify View::e() contract
php -r "
require 'vendor/autoload.php';
assert(App\Core\View::e('<script>') === '&lt;script&gt;');
assert(App\Core\View::e('\"quotes\"') === '&quot;quotes&quot;');
echo 'View::e(): OK' . PHP_EOL;
"
```

### 7. Runtime Integration Test

```bash
# Start dev server
composer run dev &
sleep 2

# Test welcome page (should return 200)
curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/
# Expected: 200

# Test 404
curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/nonexistent
# Expected: 404

# Kill server
kill %1
```

---

## Definition of Done

All of the following conditions MUST be satisfied:

### Code Completeness
- [ ] Every `// TODO: implement` comment in `src/` has been replaced with working implementation
- [ ] Every `<!-- TODO: implement -->` comment in `src/Views/` has been replaced with working markup
- [ ] Migration DDL in `database/migrations/001_create_users_table.sql` creates the users table
- [ ] AdminSeeder inserts a default admin user with hashed password
- [ ] `public/assets/css/app.css` contains application styles
- [ ] `public/assets/js/app.js` contains client-side scripts (if needed)

### Contract Compliance
- [ ] All method signatures preserved exactly as scaffolded
- [ ] All return types honored
- [ ] All `never`-return methods terminate with `exit;`
- [ ] `View::e()` uses exactly `htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8')`
- [ ] `BaseController::__construct()` instantiates `new View(BASE_PATH . '/src/Views')`
- [ ] Validator rule parsing uses `explode(':', $rule, 3)`
- [ ] Validator `in:` parsing uses `explode(',', $argument)`
- [ ] Container `make()` maintains singleton semantics
- [ ] Database maintains singleton PDO pattern
- [ ] Middleware `handle()` returns `bool`
- [ ] Exception hierarchy unchanged

### Verification Passed
- [ ] `composer dump-autoload` succeeds with no errors
- [ ] `php -l` passes on every PHP file with no syntax errors
- [ ] Namespace audit finds no violations
- [ ] `declare(strict_types=1)` present in every PHP file
- [ ] Signature audit confirms interface compliance
- [ ] Contract audit confirms type contracts
- [ ] Welcome page returns HTTP 200
- [ ] Unknown routes return HTTP 404
- [ ] Login page renders
- [ ] Login/logout flow works end-to-end
- [ ] Admin user can access `/users` routes
- [ ] Non-admin users are rejected from `/users` routes
- [ ] CSRF token validation works on POST requests
- [ ] User CRUD operations work end-to-end
