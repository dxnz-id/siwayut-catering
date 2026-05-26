# BRIEF_RECONSTRUCTED.md — Siwayut Catering

> Reverse-engineered from repository source on 2026-05-26.
> **SOURCE OF TRUTH**: Code wins. This document describes what EXISTS.

---

## Project Objective

A **vanilla PHP 8.2+ MVC micro-framework** ("Vanilla Framework v1.0.0") purpose-built for a catering management application ("Siwayut Catering"). The framework provides:

- Custom IoC container with auto-wiring
- File-based routing with grouped middleware
- Session-backed authentication and role-based authorization
- PDO-based database abstraction (MySQL via singleton)
- CSRF protection layer
- Server-side rendered views with layout/partial composition
- File upload service
- Structured exception hierarchy with HTTP-aware error handling
- Flat-file logging
- Global helper functions auto-loaded via Composer

The application is **partially scaffolded** — core architectural contracts (classes, method signatures, interfaces) are defined, but the majority of method bodies are stubbed with `// TODO: implement`.

---

## Exact Directory Tree

```
siwayut-catering/
├── .env
├── .env.example
├── .gitignore
├── composer.json
├── composer.lock
├── generate.py
├── bootstrap/
│   └── app.php
├── config/
│   ├── app.php
│   ├── bindings.php
│   ├── database.php
│   └── routes.php
├── database/
│   ├── migrations/
│   │   └── 001_create_users_table.sql
│   └── seeds/
│       └── AdminSeeder.php
├── public/
│   ├── .htaccess
│   ├── index.php
│   └── assets/
│       ├── css/
│       │   └── app.css
│       └── js/
│           └── app.js
├── src/
│   ├── Controllers/
│   │   ├── BaseController.php
│   │   ├── AuthController.php
│   │   ├── UserController.php
│   │   └── WelcomeController.php
│   ├── Core/
│   │   ├── Container.php
│   │   ├── Csrf.php
│   │   ├── Database.php
│   │   ├── Logger.php
│   │   ├── Request.php
│   │   ├── Response.php
│   │   ├── Router.php
│   │   ├── Session.php
│   │   ├── Validator.php
│   │   └── View.php
│   ├── Exceptions/
│   │   ├── AppException.php
│   │   ├── HttpException.php
│   │   ├── NotFoundException.php
│   │   └── ValidationException.php
│   ├── Helpers/
│   │   └── functions.php
│   ├── Middleware/
│   │   ├── MiddlewareInterface.php
│   │   ├── AuthMiddleware.php
│   │   ├── CsrfMiddleware.php
│   │   └── RoleMiddleware.php
│   ├── Models/
│   │   ├── BaseModel.php
│   │   └── User.php
│   ├── Services/
│   │   ├── AuthService.php
│   │   ├── FileUploadService.php
│   │   └── UserService.php
│   └── Views/
│       ├── welcome.php
│       ├── auth/
│       │   └── login.php
│       ├── errors/
│       │   ├── 404.php
│       │   └── 500.php
│       ├── layouts/
│       │   ├── auth.php
│       │   └── main.php
│       ├── partials/
│       │   ├── flash.php
│       │   ├── navbar.php
│       │   └── sidebar.php
│       └── user/
│           ├── create.php
│           ├── edit.php
│           └── index.php
├── storage/
│   ├── logs/
│   └── uploads/
└── vendor/
```

---

## Architectural Flow Diagram

```
                        HTTP REQUEST
                             │
                             ▼
                    ┌─────────────────┐
                    │  public/.htaccess │  ← mod_rewrite: all non-file
                    │  RewriteRule     │    requests → index.php
                    └────────┬────────┘
                             │
                             ▼
                    ┌─────────────────┐
                    │ public/index.php │  ← ENTRY POINT
                    │                 │
                    │  1. define(BASE_PATH)
                    │  2. require vendor/autoload.php
                    │  3. parse .env → $_ENV
                    │  4. require config/app.php (defines constants)
                    │  5. require bootstrap/app.php (returns Container)
                    │  6. new Request()
                    │  7. new Router($container)
                    │  8. register middleware aliases
                    │  9. load config/routes.php → $registerRoutes($router)
                    │ 10. $router->dispatch($request)
                    └────────┬────────┘
                             │
    ┌────────────────────────┘
    │
    ▼
┌──────────────────┐
│ bootstrap/app.php │
│                  │
│  1. Logger::setPath(storage/logs)
│  2. set_exception_handler(global handler)
│  3. Session::start()
│  4. $container = new Container()
│  5. require config/bindings.php
│  6. return $container
└──────────────────┘
                             │
                             ▼
              ┌──────────────────────────┐
              │     Router::dispatch()    │
              │                          │
              │  foreach $routes:        │
              │    match method + URI    │
              │    extract {params}      │
              │    $request->setRouteParams()
              │                          │
              │    ┌──────────────────┐  │
              │    │ Middleware Pipeline │
              │    │  foreach aliases: │  │
              │    │   resolve(alias)  │  │
              │    │   ->handle($req)  │  │
              │    │   if false: HALT  │  │
              │    └────────┬─────────┘  │
              │             │ all pass   │
              │             ▼            │
              │    ┌──────────────────┐  │
              │    │  runHandler()     │  │
              │    │  container->make  │  │
              │    │  ->$method($req)  │  │
              │    └──────────────────┘  │
              │                          │
              │  No match → throw        │
              │  NotFoundException()     │
              └──────────────────────────┘
                             │
                             ▼
              ┌──────────────────────────┐
              │     Controller Layer      │
              │                          │
              │  BaseController:         │
              │    $this->view = new View(...)
              │    render() → View::render()
              │    redirect() → Response::redirect()
              │    currentUser() → Session::get()
              │                          │
              │  Concrete Controllers:   │
              │    inject Service via     │
              │    promoted constructor  │
              │    call parent::__construct()
              └──────────────────────────┘
                             │
                             ▼
              ┌──────────────────────────┐
              │      Service Layer        │
              │                          │
              │  Inject Model via        │
              │  promoted constructor    │
              │  Orchestrate business    │
              │  logic over Model        │
              └──────────────────────────┘
                             │
                             ▼
              ┌──────────────────────────┐
              │      Model Layer          │
              │                          │
              │  BaseModel:              │
              │    $db = Database::getInstance()
              │    $table, $primaryKey   │
              │    CRUD + paginate       │
              │    query() / execute()   │
              └──────────────────────────┘
                             │
                             ▼
              ┌──────────────────────────┐
              │   Database Singleton      │
              │                          │
              │  PDO via config/database.php
              │  mysql, utf8mb4          │
              │  ERRMODE_EXCEPTION       │
              │  FETCH_ASSOC             │
              └──────────────────────────┘
```

### Error Propagation Path

```
Exception thrown anywhere
        │
        ▼
set_exception_handler (bootstrap/app.php)
        │
        ├── Logger::error(message, file, line, trace)
        │
        ├── Is HttpException?
        │   ├── YES → statusCode = $e->getStatusCode()
        │   └── NO  → statusCode = 500
        │
        ├── http_response_code($statusCode)
        │
        ├── APP_DEBUG === true?
        │   ├── YES → Dump class, message, file, line, trace (HTML escaped)
        │   └── NO  → Friendly error page
        │       ├── 404? → require Views/errors/404.php
        │       ├── else → require Views/errors/500.php
        │       └── fallback → inline HTML
        │
        └── exit(1)
```

---

## Technical Contracts

### 1. Autoload Contracts

```
PSR-4:
  "App\\"       → src/
  "Database\\"  → database/   (autoload-dev)

Files:
  src/Helpers/functions.php  (always loaded)
```

### 2. Environment Loading Contract

```
.env file parsed via parse_ini_file()
Each key/value assigned to $_ENV[$key]
No putenv(), no getenv() — purely $_ENV superglobal
```

### 3. Constant Definition Contract (config/app.php)

```php
define('APP_NAME',  $_ENV['APP_NAME']  ?? 'My App');
define('APP_ENV',   $_ENV['APP_ENV']   ?? 'production');
define('APP_DEBUG', filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN));
define('APP_URL',   $_ENV['APP_URL']   ?? 'http://localhost');
```

- `APP_DEBUG` uses `FILTER_VALIDATE_BOOLEAN` — accepts string `"true"`/`"false"`.
- Timezone set via `date_default_timezone_set()` from `$_ENV['APP_TIMEZONE']`, default `'Asia/Jakarta'`.
- Debug mode enables `display_errors=1` and `E_ALL`; production disables both.

### 4. Bootstrap Contract (bootstrap/app.php)

Exact execution order:
1. `Logger::setPath(BASE_PATH . '/storage/logs')`
2. `set_exception_handler(...)` — global handler
3. `Session::start()`
4. `$container = new Container()`
5. `require config/bindings.php`
6. `return $container`

### 5. Entry Point Contract (public/index.php)

Exact execution order:
1. `define('BASE_PATH', dirname(__DIR__))`
2. `require vendor/autoload.php`
3. Parse `.env` → `$_ENV`
4. `require config/app.php`
5. `$app = require bootstrap/app.php`
6. `$request = new Request()`
7. `$router = new Router($app)`
8. Register middleware aliases: `auth`, `role`, `csrf`
9. `$registerRoutes = require config/routes.php`
10. `$registerRoutes($router)`
11. `$router->dispatch($request)`

### 6. Container Contract

- `bind(string $abstract, callable $factory): void` — registers factory
- `make(string $abstract): object` — singleton semantics (cached in `$instances`)
- `makeNew(string $abstract): object` — always creates fresh instance
  - Checks `$bindings` first
  - Falls back to reflection-based auto-wiring
  - Resolves non-builtin typed constructor params via `$this->make()`
  - Uses default values for builtin-typed params
  - Throws `\Exception` for unresolvable non-class dependencies
- `has(string $abstract): bool` — checks both bindings and instances

**CRITICAL**: `make()` caches on first call. All subsequent calls return same instance. `makeNew()` does NOT cache.

### 7. Router Contract

- Constructor: `__construct(private Container $container)`
- HTTP verb methods: `get()`, `post()`, `put()`, `patch()`, `delete()`
  - Each accepts `string $path, array|callable $handler`
  - Path is prefixed by `$currentGroupPrefix`, trimmed with `rtrim(..., '/')`, defaulting to `'/'`
  - Returns `$this` (fluent)
- `group(array $options, callable $callback): void`
  - Saves previous prefix + middleware state
  - Merges `$options['prefix']` (concatenation) and `$options['middleware']` (array_merge)
  - Invokes `$callback($this)`
  - Restores previous state
- `addMiddleware(string $alias, string $class): self` — registers named alias
- `dispatch(Request $request): void`
  - Iterates `$routes` sequentially (first match wins)
  - Matches on method AND URI pattern
  - Sets route params on Request
  - Runs middleware pipeline (boolean gate — all must return `true`)
  - Runs handler via `runHandler()`
  - No match → throws `NotFoundException()`

#### Route Pattern Matching

```
{paramName} → (?P<paramName>[^/]+)
Full regex: #^<pattern>$#
Named captures extracted as route params
```

#### Middleware Resolution

```
alias format: "name" or "name:argument"
Split via: explode(':', $alias, 3)

With argument → new $class($argument)   [direct instantiation]
Without argument → $container->make($class)
```

#### Handler Execution

```
callable → call_user_func($handler, $request)
array    → [$class, $method]
           $controller = $container->make($class)
           $controller->$method($request)
```

### 8. Middleware Contract

```php
interface MiddlewareInterface {
    public function handle(Request $request): bool;
}
```

- Returns `true` → pipeline continues
- Returns `false` → pipeline halts, handler NOT executed
- Side effects (redirect, throw) may occur inside `handle()`

**Registered aliases (index.php)**:
| Alias | Class | Constructor |
|-------|-------|-------------|
| `auth` | `AuthMiddleware` | none |
| `role` | `RoleMiddleware` | `(string $requiredRole)` — promoted |
| `csrf` | `CsrfMiddleware` | none |

### 9. Request Contract

- `method(): string` — from `$_SERVER['REQUEST_METHOD']`, default `'GET'`
- `uri(): string` — from `$_SERVER['REQUEST_URI']`, strips query string, trims trailing `/`, defaults to `'/'`
- `input(string $key, mixed $default = null): mixed` — **[TODO]**
- `only(array $keys): array` — **[TODO]**
- `all(): array` — **[TODO]**
- `file(string $key): ?array` — **[TODO]**
- `has(string $key): bool` — **[TODO]**
- `setRouteParams(array $params): void` — **[TODO]**
- `param(string $key, mixed $default = null): mixed` — **[TODO]**
- `isAjax(): bool` — **[TODO]**
- `expectsJson(): bool` — **[TODO]**
- `ip(): string` — **[TODO]**

Private property: `array $routeParams = []`

### 10. Response Contract

**All `never`-return methods MUST terminate with exactly `exit;`**

- `redirect(string $url, int $code = 302): never` — **IMPLEMENTED**: sets code, `Location` header, exit
- `json(mixed $data, int $code = 200): never` — **[TODO]**
- `jsonSuccess(mixed $data = null, string $message = 'OK', int $code = 200): never` — **[TODO]**
- `jsonError(string $message, array $errors = [], int $code = 400): never` — **[TODO]**
- `setStatusCode(int $code): void` — **[TODO]**
- `text(string $text, int $code = 200): never` — **[TODO]**
- `download(string $filePath, string $filename): never` — **[TODO]**

All methods are `static`.

### 11. View Contract

- Constructor: `__construct(string $viewsPath)` — sets `$viewsPath` and derives `$layoutsPath = $viewsPath . '/layouts'`
- `render(string $template, array $data = [], string $layout = 'main'): void`
  - Calls `partial()` to get `$content`
  - If `$layout` is truthy: `extract($data)` + `require layouts/$layout.php`
  - If `$layout` is falsy: `echo $content`
- `partial(string $template, array $data = []): string`
  - `extract($data)`, output-buffered `require`
- `static e(mixed $value): string` — **MUST** use `htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8')`

**Layout convention**: Layout files receive `$content` variable (rendered template) and all `$data` keys via `extract()`.

**WelcomeController special case**: Renders with layout `''` (empty string = falsy) → no layout wrapping.

### 12. Database Contract (Singleton)

- `static getInstance(): PDO` — **[TODO]** (currently returns in-memory SQLite)
- Private constructor, private `__clone()`, `__wakeup(): never` throws
- Intended singleton pattern via `static ?PDO $instance`
- Config from `config/database.php`:
  - Driver: `mysql` (default)
  - Charset: `utf8mb4`
  - PDO options: `ERRMODE_EXCEPTION`, `FETCH_ASSOC`, `EMULATE_PREPARES=false`

### 13. Session Contract (Static Facade)

All methods static. **All [TODO].**

- `start(): void`
- `set(string $key, mixed $value): void`
- `get(string $key, mixed $default = null): mixed`
- `forget(string $key): void`
- `has(string $key): bool`
- `destroy(): void`
- `flash(string $key, string $message): void`
- `getFlash(string $key): ?string`
- `regenerate(bool $deleteOld = true): void`
- `old(): array`
- `setOld(array $data): void`

### 14. Logger Contract (Static Facade)

- `static setPath(string $path): void` — **[TODO]**
- `static info(string $message, array $context = []): void` — **[TODO]**
- `static warning(string $message, array $context = []): void` — **[TODO]**
- `static error(string $message, array $context = []): void` — **[TODO]**
- `static debug(string $message, array $context = []): void` — **[TODO]**
- `private static write(string $level, string $message, array $context): void` — **[TODO]**

Private property: `static string $logPath = ''`

### 15. CSRF Contract (Static Facade)

- Session key constant: `_csrf_token`
- `static token(): string` — **[TODO]**
- `static verify(string $token): bool` — **[TODO]**
- `static field(): string` — **[TODO]** (expected: HTML hidden input)
- `static regenerate(): string` — **[TODO]**

### 16. Validator Contract

- Constructor: `__construct(private ?PDO $db = null)`
- `validate(array $data, array $rules): bool` — **[TODO]**
- `errors(): array` — **[TODO]**
- `error(string $field): ?string` — **[TODO]**
- `fails(): bool` — **[TODO]**
- `private applyRule(string $field, mixed $value, string $rule, array $data): bool` — **[TODO]**
  - **CONTRACT**: Rule parsing MUST use `explode(':', $rule, 3)`
  - **CONTRACT**: For `in:` rules, argument parsing MUST use `explode(',', $argument)`

Private property: `array $errors = []`

### 17. BaseModel Contract (Abstract)

- Protected properties:
  - `PDO $db`
  - `string $table`
  - `string $primaryKey = 'id'`
  - `array $sortableColumns = ['id', 'created_at', 'updated_at']`
- Constructor: `__construct()` — **[TODO]** (expected: `$this->db = Database::getInstance()`)
- Public methods (all **[TODO]**):
  - `all(array $conditions = [], string $orderBy = 'created_at', string $direction = 'DESC'): array`
  - `find(int $id): ?array`
  - `findWhere(array $conditions): ?array`
  - `where(array $conditions, string $orderBy = 'created_at', string $direction = 'DESC'): array`
  - `create(array $data): int`
  - `update(int $id, array $data): bool`
  - `delete(int $id): bool`
  - `count(array $conditions = []): int`
  - `exists(array $conditions): bool`
  - `paginate(int $page = 1, int $perPage = 15, array $conditions = []): array`
- Protected methods (all **[TODO]**):
  - `query(string $sql, array $bindings = []): array`
  - `execute(string $sql, array $bindings = []): bool`
- Private methods (all **[TODO]**):
  - `validateSortColumn(string $column): string`

### 18. User Model Contract

- Extends `BaseModel`
- Constructor: calls `parent::__construct()`, **[TODO]** (expected: sets `$this->table = 'users'`)
- `findByEmail(string $email): ?array` — **[TODO]**

### 19. BaseController Contract (Abstract)

- Protected property: `View $view`
- Constructor **CONTRACT**: `$this->view = new View(BASE_PATH . '/src/Views')`
- `protected render(string $template, array $data = [], string $layout = 'main'): void`
  - Delegates to `$this->view->render()`
- `protected redirect(string $url): never` — **[TODO]**
- `protected redirectWithFlash(string $url, string $type, string $message): never` — **[TODO]**
- `protected currentUser(): ?array` — **[TODO]**
- `protected back(string $fallback = '/dashboard'): never` — **[TODO]**
- `protected withOldInput(array $data): void` — **[TODO]**

### 20. AuthController Contract

- Constructor: `__construct(private AuthService $authService)` — calls `parent::__construct()`
- `index(Request $request): void` — **[TODO]** (render login form)
- `login(Request $request): void` — **[TODO]** (authenticate)
- `logout(Request $request): void` — **[TODO]** (destroy session)

### 21. UserController Contract

- Constructor: `__construct(private UserService $userService)` — calls `parent::__construct()`
- `index(Request $request): void` — **[TODO]** (list users)
- `create(Request $request): void` — **[TODO]** (create form)
- `store(Request $request): void` — **[TODO]** (persist new user)
- `edit(Request $request): void` — **[TODO]** (edit form)
- `update(Request $request): void` — **[TODO]** (persist update)
- `destroy(Request $request): void` — **[TODO]** (delete user)

### 22. WelcomeController Contract

- Constructor: inherits from `BaseController` (no override)
- `index(Request $request): void` — **IMPLEMENTED**: renders `welcome` template with `['title' => 'Welcome to Vanilla Framework']`, layout `''` (no layout)

### 23. Service Contracts

#### AuthService
- Constructor: `__construct(private User $userModel)`
- `login(string $email, string $password): bool` — **[TODO]**
- `logout(): void` — **[TODO]**

#### UserService
- Constructor: `__construct(private User $userModel)`
- `getAll(int $page = 1, int $perPage = 15): array` — **[TODO]**
- `getById(int $id): array` — **[TODO]**
- `create(array $data): int` — **[TODO]**
- `update(int $id, array $data): bool` — **[TODO]**
- `delete(int $id): bool` — **[TODO]**

#### FileUploadService
- Constructor: `__construct(private string $uploadPath)` — bound with `BASE_PATH . '/storage/uploads'`
- `upload(array $file): string` — **[TODO]**
- `delete(string $filename): bool` — **[TODO]**

### 24. Exception Hierarchy

```
\RuntimeException
  └── AppException
        ├── HttpException
        │     └── NotFoundException
        └── ValidationException
```

- `AppException` — empty extension of `\RuntimeException`
- `HttpException`
  - `__construct(int $code, string $message = '')`
  - Stores `$statusCode` (private)
  - `getStatusCode(): int`
  - Default messages via `match` expression (400, 401, 403, 404, 405, 419, 422, 429, 500)
- `NotFoundException`
  - `__construct(string $message = 'Resource not found')`
  - Hardcodes status code `404`
- `ValidationException`
  - `__construct(private array $errors, string $message = 'Validation failed')` — promoted `$errors`
  - `getErrors(): array` — returns `$this->errors`

### 25. Helper Functions Contract

All wrapped in `if (!function_exists(...))` guards:

| Function | Signature | Status |
|----------|-----------|--------|
| `env` | `(string $key, mixed $default = null): mixed` | TODO |
| `config` | `(string $key, mixed $default = null): mixed` | TODO |
| `base_path` | `(string $path = ''): string` | TODO |
| `asset` | `(string $path): string` | TODO |
| `url` | `(string $path = ''): string` | TODO |
| `old` | `(string $key, mixed $default = ''): mixed` | TODO |
| `csrf_field` | `(): string` | TODO |
| `e` | `(mixed $value): string` | TODO |
| `dd` | `(mixed ...$vars): never` | TODO |
| `now` | `(): string` | TODO |

---

## Component Specifications

### Container (`App\Core\Container`)

- **Purpose**: IoC container with factory registration, singleton caching, and reflection-based auto-wiring
- **Dependencies**: None (uses PHP Reflection API)
- **Methods**: `bind()`, `make()`, `makeNew()`, `has()`
- **Behavioral constraints**:
  - `make()` is singleton — first resolution cached forever
  - `makeNew()` always invokes factory, never caches
  - Auto-wiring resolves non-builtin typed constructor params recursively via `make()`
  - Non-instantiable classes throw `\Exception`
  - Unresolvable builtin params without defaults throw `\Exception`

### Router (`App\Core\Router`)

- **Purpose**: HTTP method + URI pattern routing with middleware pipeline and grouped route registration
- **Dependencies**: `Container` (promoted constructor property)
- **Methods**: `get()`, `post()`, `put()`, `patch()`, `delete()`, `group()`, `addMiddleware()`, `dispatch()`, `resolveMiddleware()`, `matchRoute()`, `runMiddlewarePipeline()`, `runHandler()`
- **Behavioral constraints**:
  - First-match-wins dispatch (sequential)
  - Middleware pipeline is boolean gate (all `true` to proceed)
  - Parameterized middleware alias (e.g. `role:admin`) bypasses container — uses direct `new`
  - Group state is stack-based (save/restore on nesting)
  - No match throws `NotFoundException`

### Request (`App\Core\Request`)

- **Purpose**: HTTP request abstraction
- **Dependencies**: None (reads PHP superglobals)
- **Methods**: `method()`, `uri()`, `input()`, `only()`, `all()`, `file()`, `has()`, `setRouteParams()`, `param()`, `isAjax()`, `expectsJson()`, `ip()`
- **Behavioral constraints**: URI strips query string and trailing slash

### Response (`App\Core\Response`)

- **Purpose**: HTTP response termination (static utility)
- **Dependencies**: None
- **Methods**: `redirect()`, `json()`, `jsonSuccess()`, `jsonError()`, `setStatusCode()`, `text()`, `download()`
- **Behavioral constraints**: All `never`-return methods MUST terminate with `exit;`

### View (`App\Core\View`)

- **Purpose**: Template rendering with layout composition and output escaping
- **Dependencies**: None (filesystem access)
- **Methods**: `render()`, `partial()`, `e()`
- **Behavioral constraints**:
  - Layout receives `$content` from partial and all `$data` via `extract()`
  - Escaping uses `htmlspecialchars` with `ENT_QUOTES` and `UTF-8`

### Database (`App\Core\Database`)

- **Purpose**: PDO singleton factory
- **Dependencies**: `config/database.php`
- **Behavioral constraints**: Singleton pattern — private constructor, private `__clone()`, `__wakeup()` throws

### Session (`App\Core\Session`)

- **Purpose**: Session management static facade
- **Dependencies**: PHP `$_SESSION` superglobal
- **Behavioral constraints**: Flash messages are one-time-read pattern; old input support for form repopulation

### Logger (`App\Core\Logger`)

- **Purpose**: File-based logging static facade
- **Dependencies**: Filesystem (storage/logs)
- **Methods**: `setPath()`, `info()`, `warning()`, `error()`, `debug()`, private `write()`
- **Behavioral constraints**: Path must be set before use; log levels: info, warning, error, debug

### Validator (`App\Core\Validator`)

- **Purpose**: Array data validation against rule definitions
- **Dependencies**: Optional `PDO` for database-dependent rules (e.g., unique)
- **Behavioral constraints**:
  - Rule parsing: `explode(':', $rule, 3)`
  - `in:` argument parsing: `explode(',', $argument)`

---

## Naming Conventions

| Aspect | Convention |
|--------|-----------|
| Namespace root | `App\` → `src/` |
| Dev namespace | `Database\` → `database/` |
| Controller naming | `{Entity}Controller` extends `BaseController` |
| Model naming | `{Entity}` extends `BaseModel` |
| Service naming | `{Entity}Service` or `{Feature}Service` |
| Middleware naming | `{Feature}Middleware` implements `MiddlewareInterface` |
| Exception naming | `{Type}Exception` |
| View paths | `{entity}/{action}.php` (e.g., `user/index.php`) |
| Layout paths | `layouts/{name}.php` |
| Partial paths | `partials/{name}.php` |
| Error view paths | `errors/{code}.php` |
| Migration naming | `{NNN}_{description}.sql` (zero-padded 3-digit sequence) |
| Seeder naming | `{Name}Seeder.php` |
| Config files | lowercase, descriptive (`app.php`, `database.php`, `routes.php`, `bindings.php`) |
| Static assets | `public/assets/{type}/{name}.{ext}` |
| Storage | `storage/{category}/` (`logs/`, `uploads/`) |
| `declare(strict_types=1)` | **Every PHP file** |
| Class visibility | Properties default `private`; Model properties `protected` |
| PHP version | `^8.2` (required for promoted properties, `never` return type, `match` expressions) |

---

## Security Contracts

1. **XSS Prevention**: `View::e()` escapes all output via `htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8')`
2. **CSRF Protection**: Token stored in session under `_csrf_token`; `CsrfMiddleware` validates on requests; `csrf_field()` helper generates hidden input
3. **SQL Injection Prevention**: PDO prepared statements with `EMULATE_PREPARES=false`
4. **Session Security**: `Session::regenerate()` for session fixation prevention; `Session::destroy()` for logout
5. **Password Handling**: `AuthService::login()` expected to use `password_verify()` (not yet implemented)
6. **Role-Based Access**: `RoleMiddleware` with constructor-injected `$requiredRole` string; route-level enforcement via middleware alias `role:admin`
7. **Directory Traversal**: `.htaccess` `Options -Indexes` disables directory listing; all requests routed through `index.php`
8. **Error Disclosure**: `APP_DEBUG` controls error detail exposure; production mode shows generic error pages
9. **Database Singleton**: Private constructor prevents uncontrolled instantiation; `__wakeup()` prevents deserialization

---

## Bootstrap Lifecycle

```
1. public/index.php
   │
   ├── define('BASE_PATH', dirname(__DIR__))
   ├── require vendor/autoload.php
   │     └── PSR-4: App\ → src/
   │     └── files: src/Helpers/functions.php
   │
   ├── Parse .env → $_ENV (parse_ini_file)
   │
   ├── require config/app.php
   │     └── define APP_NAME, APP_ENV, APP_DEBUG, APP_URL
   │     └── date_default_timezone_set(Asia/Jakarta)
   │     └── Configure error reporting based on APP_DEBUG
   │
   ├── $app = require bootstrap/app.php
   │     ├── Logger::setPath(BASE_PATH . '/storage/logs')
   │     ├── set_exception_handler(global)
   │     ├── Session::start()
   │     ├── $container = new Container()
   │     ├── require config/bindings.php
   │     │     ├── bind User model
   │     │     ├── bind FileUploadService (with upload path)
   │     │     ├── bind AuthService (with User)
   │     │     ├── bind UserService (with User)
   │     │     ├── bind WelcomeController
   │     │     ├── bind AuthController (with AuthService)
   │     │     └── bind UserController (with UserService)
   │     └── return $container
   │
   ├── $request = new Request()
   ├── $router = new Router($app)
   ├── Register middleware aliases: auth, role, csrf
   │
   ├── $registerRoutes = require config/routes.php
   │     └── Returns Closure(Router): void
   ├── $registerRoutes($router)
   │
   └── $router->dispatch($request)
```

---

## Deterministic Runtime Rules

1. **Config loading order**: `.env` → `config/app.php` → `bootstrap/app.php` → `config/bindings.php` → `config/routes.php`
2. **Container resolution**: Bindings checked first → instances cache checked → reflection auto-wiring fallback
3. **Route matching**: Sequential first-match-wins; no priority or weight system
4. **Middleware execution**: Sequential; any `false` return halts entire pipeline; handler never reached
5. **Exception handling**: Global handler catches all `Throwable`; logs first, then renders
6. **Response termination**: `Response::redirect()` and all `never`-typed methods call `exit;` — no further code runs
7. **View rendering**: Template rendered to string via output buffer → injected as `$content` into layout
8. **Session lifecycle**: Started once in bootstrap; available throughout request
9. **Database lifecycle**: Singleton PDO — single connection per request
10. **Route group nesting**: State stacked and restored — supports arbitrary depth

---

## Route Table (Observed)

| Method | Path | Handler | Middleware |
|--------|------|---------|------------|
| GET | `/` | `WelcomeController::index` | none |
| GET | `/login` | `AuthController::index` | none |
| POST | `/login` | `AuthController::login` | none |
| POST | `/logout` | `AuthController::logout` | none |
| GET | `/users` | `UserController::index` | `auth`, `role:admin` |
| GET | `/users/create` | `UserController::create` | `auth`, `role:admin` |
| POST | `/users` | `UserController::store` | `auth`, `role:admin` |
| GET | `/users/{id}/edit` | `UserController::edit` | `auth`, `role:admin` |
| POST | `/users/{id}` | `UserController::update` | `auth`, `role:admin` |
| POST | `/users/{id}/delete` | `UserController::destroy` | `auth`, `role:admin` |

**Note**: Group prefix is `''` (empty) for auth group — routes are not prefixed.

---

## Database Schema (Intended)

Migration `001_create_users_table.sql` is **[TODO]** — only a comment exists.

Expected columns (inferred from `User` model, `AuthService`, `AdminSeeder`, `$sortableColumns`):
- `id` (primary key, int)
- `email` (for `findByEmail()`)
- `password` (for `AuthService::login()`)
- `role` (for `RoleMiddleware` — e.g., `'admin'`)
- `created_at` (from `$sortableColumns`)
- `updated_at` (from `$sortableColumns`)

```
[UNVERIFIED CONTRACT] — Schema inferred from code context; no DDL exists.
```

---

## Scaffolding Tool

`generate.py` — Python script that reads `.context/BRIEF.md` and:
1. Extracts fenced code blocks preceded by `File: <path>` markers
2. Also detects inline `// File:` / `# File:` / `<!-- File:` comments within code blocks
3. Creates directories and writes files
4. Creates `storage/logs/.gitkeep` and `storage/uploads/.gitkeep`

This script was the **original project generator** — the repository was bootstrapped from a `BRIEF.md` specification document.
