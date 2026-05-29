# Basis Data (Database)

## Konfigurasi

Pengaturan database berada di dalam `config/database.php`, yang membaca nilai dari berkas `.env`:

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

Opsi utama PDO:
- `ERRMODE_EXCEPTION` — melempar exception ketika terjadi kesalahan SQL alih-alih gagal secara senyap
- `FETCH_ASSOC` — mengembalikan array asosiatif
- `EMULATE_PREPARES = false` — menggunakan prepared statement asli (perlindungan terhadap injeksi SQL)

## Singleton Database

`App\Core\Database` menyediakan instansiasi tunggal PDO per permintaan.

```php
use App\Core\Database;

$pdo = Database::getInstance();
```

Penegakan Singleton:
- **Private constructor** — tidak dapat memanggil `new Database()`
- **Private `__clone()`** — tidak dapat digandakan
- **`__wakeup()` throws** — tidak dapat di-unserialize

Koneksi bersifat **lazy** — PDO baru dibuat pada saat panggilan pertama `getInstance()`, bukan pada saat bootstrap aplikasi berjalan.

## API BaseModel

Semua model mewarisi kelas `App\Models\BaseModel`.

### Properti

| Properti | Tipe | Nilai Bawaan | Deskripsi |
|----------|------|--------------|-----------|
| `$db` | `?PDO` | `null` | Instance PDO yang diinisialisasi secara malas (lazy) |
| `$table` | `string` | — | Nama tabel (diatur di konstruktor anak) |
| `$primaryKey` | `string` | `'id'` | Kolom kunci utama (primary key) |
| `$sortableColumns` | `array` | `['id', 'created_at', 'updated_at']` | Daftar putih (whitelist) untuk ORDER BY |

### Metode Membaca (Read Methods)

```php
// Mengambil semua baris (dengan kondisi dan pengurutan opsional)
$model->all(array $conditions = [], string $orderBy = 'created_at', string $direction = 'DESC'): array

// Menemukan data berdasarkan primary key
$model->find(int $id): ?array

// Menemukan kecocokan pertama berdasarkan kondisi
$model->findWhere(array $conditions): ?array

// Mengambil semua baris yang cocok
$model->where(array $conditions, string $orderBy = 'created_at', string $direction = 'DESC'): array

// Menghitung jumlah baris
$model->count(array $conditions = []): int

// Memeriksa apakah ada baris yang cocok
$model->exists(array $conditions): bool

// Hasil dengan paginasi
$model->paginate(int $page = 1, int $perPage = 15, array $conditions = []): array
```

### Metode Menulis (Write Methods)

```php
// Menyisipkan data baru dan mengembalikan ID baru
$model->create(array $data): int

// Memperbarui data berdasarkan primary key
$model->update(int $id, array $data): bool

// Menghapus data berdasarkan primary key
$model->delete(int $id): bool
```

### Metode Kueri Mentah (Raw Query Methods - protected)

```php
// Menjalankan SELECT dan mengembalikan baris data
$this->query(string $sql, array $bindings = []): array

// Menjalankan kueri INSERT/UPDATE/DELETE
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

Atau menggunakan perancah (scaffold): `php vanilla make:model Product`

## Contoh Penggunaan

```php
// Mencari berdasarkan ID
$user = $userModel->find(1);
// => ['id' => 1, 'name' => 'Admin', 'email' => 'admin@admin.com', ...]

// Membuat data baru
$id = $userModel->create([
    'name' => 'John',
    'email' => 'john@example.com',
    'password' => password_hash('secret', PASSWORD_DEFAULT),
    'role' => 'user',
]);

// Melakukan paginasi
$result = $userModel->paginate(page: 2, perPage: 10);
// => ['data' => [...], 'total' => 50, 'per_page' => 10, 'current_page' => 2, 'last_page' => 5]
```

### Struktur Kembalian Paginasi

```php
[
    'data'         => array,  // baris data untuk halaman ini
    'total'        => int,    // jumlah total baris data
    'per_page'     => int,    // jumlah data per halaman
    'current_page' => int,    // nomor halaman saat ini
    'last_page'    => int,    // nomor halaman terakhir
]
```

## Validasi Kolom Pengurutan (Sort Column Validation)

`$sortableColumns` bertindak sebagai daftar putih (whitelist). Jika kolom yang diberikan pengguna tidak ada dalam daftar tersebut, maka pengurutan akan dialihkan ke primary key:

```php
$model->all(orderBy: 'malicious_column'); // kembali (fallback) ke 'id'
```

## Format migrasi

Migrasi adalah **kelas PHP** di `database/migrations/`, mewarisi `App\Core\BaseMigration`:

```
database/migrations/
└── 001_create_users_table.php   → Database\Migrations\CreateUsersTable
```

| Konvensi | Contoh |
|----------|--------|
| Nama berkas | `{NNN}_{deskripsi_snake}.php` |
| Namespace | `Database\Migrations` |
| `up()` / `down()` | Mengembalikan SQL `string` atau `string[]` |

```bash
php vanilla make:migration create_products_table
php vanilla migrate
php vanilla migrate:fresh
```

## Skema katering (saat ini)

| Tabel | Fungsi |
|-------|--------|
| `users` | Admin & pelanggan (`role`) |
| `categories`, `events` | Pengelompokan menu |
| `menus` | Item menu + gambar + `minimum_portions` |
| `customers` | Data kontak; `user_id` opsional |
| `orders` | Header pesanan + `payment_status` |
| `order_items` | Baris menu per pesanan |

Migrasi `010` menghapus kolom `menu_id` / `quantity` dari `orders` (diganti `order_items`).

---

Lihat: [ARCHITECTURE.md](ARCHITECTURE.md) · [VALIDATION.md](VALIDATION.md)
