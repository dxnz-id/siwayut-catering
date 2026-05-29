# Conventions

## File & Naming Conventions

| Item | Convention | Example |
|------|-----------|---------|
| PHP files | `declare(strict_types=1)` on all class files | — |
| Controller | PascalCase + `Controller` suffix | `UserController.php` |
| Model | PascalCase, singular noun | `User.php`, `Product.php` |
| Service | PascalCase + `Service` suffix | `AuthService.php` |
| Middleware | PascalCase + `Middleware` suffix | `AuthMiddleware.php` |
| Exception | PascalCase + `Exception` suffix | `ValidationException.php` |
| Views | snake_case directory + file | `user/index.php` |
| Migrations | `NNN_snake_case.sql` | `001_create_users_table.sql` |
| Seeders | PascalCase + `Seeder` suffix | `AdminSeeder.php` |
| Config files | snake_case | `app.php`, `database.php` |
| CSS/JS assets | snake_case | `app.css`, `app.js` |

## Namespace Map

| Namespace | Directory | Auto-load |
|-----------|-----------|-----------|
| `App\` | `src/` | PSR-4 via Composer |
| `Database\Seeds\` | `database/seeds/` | Manual require |

## Database Table Naming

| Convention | Example |
|-----------|---------|
| Plural, snake_case | `users`, `products`, `order_items` |
| Primary key | `id` (INT UNSIGNED AUTO_INCREMENT) |
| Timestamps | `created_at`, `updated_at` (TIMESTAMP) |
| Foreign keys | `{singular}_id` → `user_id`, `product_id` |
| Charset | `utf8mb4` / `utf8mb4_unicode_ci` |
| Engine | `InnoDB` |

## PHP Coding Standards

### Type Strictness

```php
<?php
declare(strict_types=1);
```

Required on **every** PHP class file. Views are exempt (template files).

### Constructor Promotion

Prefer promoted properties where applicable:

```php
// Preferred
public function __construct(private string $requiredRole) {}

// Not
private string $requiredRole;
public function __construct(string $requiredRole) {
    $this->requiredRole = $requiredRole;
}
```

### Return Types

All methods must declare return types:

```php
public function find(int $id): ?array
public function create(array $data): int
public function delete(int $id): bool
```

`never` return type for methods that terminate with `exit`:

```php
public static function redirect(string $url, int $code = 302): never
```

### Static vs Instance

| Pattern | Usage |
|---------|-------|
| Static methods | Infrastructure utilities: `Session`, `Csrf`, `Response`, `Logger`, `Database::getInstance()`, `View::e()` |
| Instance methods | Business logic: Services, Models, Controllers |

### Access Modifiers

| Modifier | Usage |
|----------|-------|
| `public` | API methods, controller actions |
| `protected` | BaseModel query/execute, BaseController render/redirect |
| `private` | Internal helpers (e.g., `validateSortColumn()`) |

## Controller Patterns

### Action Methods

Always accept `Request` and return `void`:

```php
public function index(Request $request): void
public function store(Request $request): void
```

### Resource Controller Actions

| Action | HTTP | URI | Purpose |
|--------|------|-----|---------|
| `index` | GET | `/resources` | List all |
| `create` | GET | `/resources/create` | Show create form |
| `store` | POST | `/resources` | Save new |
| `edit` | GET | `/resources/{id}/edit` | Show edit form |
| `update` | POST | `/resources/{id}` | Save changes |
| `destroy` | POST | `/resources/{id}/delete` | Delete |

> Note: PUT/PATCH/DELETE are not used for form submissions. The framework uses POST for all write operations via forms.

## View Conventions

| Convention | Rule |
|-----------|------|
| Escaping | Always use `View::e()` or `e()` for user data |
| Layout variable | `$content` is reserved — do not pass in `$data` |
| Partial inclusion | Use `require __DIR__ . '/../partials/name.php'` |
| Error pages | Indonesian language by default |

## File Header Comment

Every PHP class file includes a file path comment:

```php
<?php
declare(strict_types=1);
// File: src/Controllers/UserController.php
```

---

See: [ARCHITECTURE.md](ARCHITECTURE.md) · [CONTRIBUTING.md](CONTRIBUTING.md)
