# Database

## Configuration

Database settings are in `config/database.php`, which reads from `.env`:

```php
// config/database.php — returns array
return [
    'driver'   => $_ENV['DB_DRIVER']   ?? 'mysql',
    'host'     => $_ENV['DB_HOST']     ?? '127.0.0.1',
    'port'     => (int) ($_ENV['DB_PORT'] ?? 3306),
    'database' => $_ENV['DB_DATABASE'] ?? '',
    'username' => $_ENV['DB_USERNAME'] ?? 'root',
    'password' => $_ENV['DB_PASSWORD'] ?? '',
    'charset'  => 'utf8mb4',
    'options'  => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ],
];
```

Key PDO options:
- `ERRMODE_EXCEPTION` — throw on SQL errors instead of silent failure
- `FETCH_ASSOC` — return associative arrays
- `EMULATE_PREPARES = false` — use real prepared statements (SQL injection protection)

## Database Singleton

`App\Core\Database` provides a single PDO instance per request.

```php
use App\Core\Database;

$pdo = Database::getInstance();
```

Singleton enforcement:
- **Private constructor** — cannot `new Database()`
- **Private `__clone()`** — cannot clone
- **`__wakeup()` throws** — cannot unserialize

Connection is **lazy** — PDO is created on first `getInstance()` call, not at bootstrap.

## BaseModel API

All models extend `App\Models\BaseModel`.

### Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `$db` | `?PDO` | `null` | Lazy-initialized PDO instance |
| `$table` | `string` | — | Table name (set in child constructor) |
| `$primaryKey` | `string` | `'id'` | Primary key column |
| `$sortableColumns` | `array` | `['id', 'created_at', 'updated_at']` | Whitelist for ORDER BY |

### Read Methods

```php
// Get all rows (with optional conditions and ordering)
$model->all(array $conditions = [], string $orderBy = 'created_at', string $direction = 'DESC'): array

// Find by primary key
$model->find(int $id): ?array

// Find first match by conditions
$model->findWhere(array $conditions): ?array

// Get all matching rows
$model->where(array $conditions, string $orderBy = 'created_at', string $direction = 'DESC'): array

// Count rows
$model->count(array $conditions = []): int

// Check if any matching rows exist
$model->exists(array $conditions): bool

// Paginated results
$model->paginate(int $page = 1, int $perPage = 15, array $conditions = []): array
```

### Write Methods

```php
// Insert and return new ID
$model->create(array $data): int

// Update by primary key
$model->update(int $id, array $data): bool

// Delete by primary key
$model->delete(int $id): bool
```

### Raw Query Methods (protected)

```php
// Execute SELECT and return rows
$this->query(string $sql, array $bindings = []): array

// Execute INSERT/UPDATE/DELETE
$this->execute(string $sql, array $bindings = []): bool
```

## Creating a Model

```php
<?php
declare(strict_types=1);

namespace App\Models;

class Product extends BaseModel {
    public function __construct() {
        parent::__construct();
        $this->table = 'products';
        $this->sortableColumns = ['id', 'name', 'price', 'created_at'];
    }

    public function findBySlug(string $slug): ?array {
        return $this->findWhere(['slug' => $slug]);
    }
}
```

Or scaffold: `php vanilla make:model Product`

## Usage Examples

```php
// Find by ID
$user = $userModel->find(1);
// => ['id' => 1, 'name' => 'Admin', 'email' => 'admin@admin.com', ...]

// Create
$id = $userModel->create([
    'name' => 'John',
    'email' => 'john@example.com',
    'password' => password_hash('secret', PASSWORD_DEFAULT),
    'role' => 'user',
]);

// Paginate
$result = $userModel->paginate(page: 2, perPage: 10);
// => ['data' => [...], 'total' => 50, 'per_page' => 10, 'current_page' => 2, 'last_page' => 5]
```

### Pagination Return Structure

```php
[
    'data'         => array,  // rows for this page
    'total'        => int,    // total row count
    'per_page'     => int,    // items per page
    'current_page' => int,    // current page number
    'last_page'    => int,    // last page number
]
```

## Sort Column Validation

`$sortableColumns` acts as a whitelist. If a user-supplied column is not in the list, the primary key is used instead:

```php
$model->all(orderBy: 'malicious_column'); // falls back to 'id'
```

## Migration format

Migrations are **PHP classes** in `database/migrations/`, extending `App\Core\BaseMigration`:

```
database/migrations/
└── 001_create_users_table.php   → Database\Migrations\CreateUsersTable
```

| Convention | Example |
|------------|---------|
| Filename | `{NNN}_{snake_case_description}.php` |
| Class name | PascalCase derived from description (`create_users_table` → `CreateUsersTable`) |
| Namespace | `Database\Migrations` |
| `up()` / `down()` | Return a SQL `string`, or `string[]` for multiple statements |

```php
<?php
declare(strict_types=1);

namespace Database\Migrations;

use App\Core\BaseMigration;

class CreateProductsTable extends BaseMigration {
    protected string $filename = '011_create_products_table';

    public function up(): string {
        return "CREATE TABLE IF NOT EXISTS `products` ( ... )";
    }

    public function down(): string {
        return "DROP TABLE IF EXISTS `products`";
    }
}
```

Commands:

```bash
php vanilla make:migration create_products_table
php vanilla migrate
php vanilla migrate:fresh   # drop all tables, re-run
```

Executed migrations are recorded in the `migrations` table.

## Catering schema (current)

| Table | Purpose |
|-------|---------|
| `users` | Admin and customer accounts (`role`: `admin` / `user`) |
| `categories` | Menu groupings |
| `events` | Occasions (e.g. wedding, corporate) — menus are scoped to an event |
| `menus` | Items: price, `minimum_portions`, image, `status` (`active`/`inactive`), FK to category + event |
| `customers` | Contact info; optional `user_id` → `users` for registered customers |
| `orders` | Header: customer, event, `event_date`, `total_price`, address, `status`, `payment_status` |
| `order_items` | Line items: `menu_id`, `quantity`, `price_at_time`, `subtotal` per order |

Order flow: public and admin orders insert one `orders` row plus multiple `order_items`. Migration `010` removed legacy single-menu columns (`menu_id`, `quantity`) from `orders`.

### Order status values

- `status`: `pending`, `processing`, `delivering`, `completed`, `cancelled`
- `payment_status`: `unpaid`, `paid`, `refunded`

---

See: [ARCHITECTURE.md](ARCHITECTURE.md) · [VALIDATION.md](VALIDATION.md)
