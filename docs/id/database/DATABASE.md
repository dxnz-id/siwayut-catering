# Database

## Konfigurasi

Pengaturan database berada di `config/database.php`, yang membaca dari `.env`:

```php
// config/database.php — mengembalikan array
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

Opsi PDO utama:
- `ERRMODE_EXCEPTION` — melempar pengecualian pada error SQL alih-alih kegagalan senyap (silent failure)
- `FETCH_ASSOC` — mengembalikan associative arrays
- `EMULATE_PREPARES = false` — menggunakan prepared statements yang sebenarnya (perlindungan terhadap SQL injection)

## Database Singleton

`App\Core\Database` menyediakan satu instance PDO per request.

```php
use App\Core\Database;

$pdo = Database::getInstance();
```

Penegakan Singleton:
- **Private constructor** — tidak dapat melakukan `new Database()`
- **Private `__clone()`** — tidak dapat melakukan clone
- **`__wakeup()` melempar pengecualian** — tidak dapat melakukan unserialize

Koneksi bersifat **lazy** — PDO dibuat pada pemanggilan `getInstance()` pertama, bukan saat bootstrap.

## API BaseModel

Semua model melakukan extend terhadap `App\Models\BaseModel`.

### Properti

| Properti | Tipe | Default | Deskripsi |
|----------|------|---------|-------------|
| `$db` | `?PDO` | `null` | Instance PDO yang diinisialisasi secara lazy |
| `$table` | `string` | — | Nama tabel (diatur dalam constructor child) |
| `$primaryKey` | `string` | `'id'` | Kolom primary key |
| `$sortableColumns` | `array` | `['id', 'created_at', 'updated_at']` | Whitelist untuk ORDER BY |

### Metode Read

```php
// Mendapatkan semua baris (dengan kondisi dan pengurutan opsional)
$model->all(array $conditions = [], string $orderBy = 'created_at', string $direction = 'DESC'): array

// Mencari berdasarkan primary key
$model->find(int $id): ?array

// Mencari kecocokan pertama berdasarkan kondisi
$model->findWhere(array $conditions): ?array

// Mendapatkan semua baris yang cocok
$model->where(array $conditions, string $orderBy = 'created_at', string $direction = 'DESC'): array

// Menghitung jumlah baris
$model->count(array $conditions = []): int

// Memeriksa apakah ada baris yang cocok
$model->exists(array $conditions): bool

// Hasil dengan paginasi
$model->paginate(int $page = 1, int $perPage = 15, array $conditions = []): array
```

### Metode Write

```php
// Menyisipkan data dan mengembalikan ID baru
$model->create(array $data): int

// Memperbarui berdasarkan primary key
$model->update(int $id, array $data): bool

// Menghapus berdasarkan primary key
$model->delete(int $id): bool
```

### Metode Raw Query (protected)

```php
// Menjalankan SELECT dan mengembalikan baris
$this->query(string $sql, array $bindings = []): array

// Menjalankan INSERT/UPDATE/DELETE
$this->execute(string $sql, array $bindings = []): bool
```

## Membuat Model

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

Atau gunakan scaffold: `php vanilla make:model Product`

## Contoh Penggunaan

```php
// Mencari berdasarkan ID
$user = $userModel->find(1);
// => ['id' => 1, 'name' => 'Admin', 'email' => 'admin@admin.com', ...]

// Membuat data
$id = $userModel->create([
    'name' => 'John',
    'email' => 'john@example.com',
    'password' => password_hash('secret', PASSWORD_DEFAULT),
    'role' => 'user',
]);

// Paginasi
$result = $userModel->paginate(page: 2, perPage: 10);
// => ['data' => [...], 'total' => 50, 'per_page' => 10, 'current_page' => 2, 'last_page' => 5]
```

### Struktur Return Paginasi

```php
[
    'data'         => array,  // baris untuk halaman ini
    'total'        => int,    // total jumlah baris
    'per_page'     => int,    // item per halaman
    'current_page' => int,    // nomor halaman saat ini
    'last_page'    => int,    // nomor halaman terakhir
]
```

## Validasi Sort Column

`$sortableColumns` bertindak sebagai whitelist. Jika kolom yang diberikan pengguna tidak ada dalam daftar, maka primary key akan digunakan sebagai gantinya:

```php
$model->all(orderBy: 'malicious_column'); // kembali ke 'id'
```

## Format Migrasi

File migrasi berada di `database/migrations/` sebagai SQL murni:

```
database/migrations/
└── 001_create_users_table.sql
```

Penamaan: `{NNN}_{deskripsi}.sql` — nomor urut + deskripsi dalam format snake_case.

Membuat migrasi: `php vanilla make:migration create_products_table`

Menjalankan migrasi: `php vanilla migrate`

> **Catatan:** Untuk Entity Relationship Diagram (ERD) lengkap dan riwayat kronologis penuh dari semua migrasi yang diterapkan pada proyek, silakan merujuk ke [MODELS.md](MODELS.md).

## Indeks Database

Untuk memastikan performa baca yang cepat, terutama untuk dashboard admin dan API publik, indeks berikut harus dibuat secara implisit atau eksplisit (melalui primary/foreign key):

| Tabel | Kolom | Tipe | Tujuan |
|-------|-----------|------|---------|
| `users` | `email` | UNIQUE | Pencarian login cepat dan pemeriksaan keunikan. |
| `customers` | `phone` | INDEX | Pencarian cepat untuk auto-linking pesanan tamu. |
| `menus` | `category_id`, `status` | INDEX | Pemfilteran cepat untuk `/api/menus` publik. |
| `orders` | `order_number` | UNIQUE | Pencocokan tepat untuk pelacakan pesanan. |
| `orders` | `customer_id` | FK INDEX | Pengambilan cepat riwayat pesanan pelanggan. |
| `order_items` | `order_id` | FK INDEX | Pengambilan cepat item untuk pesanan tertentu. |

---

Lihat: [ARCHITECTURE.md](../core/ARCHITECTURE.md) · [VALIDATION.md](../backend/VALIDATION.md)