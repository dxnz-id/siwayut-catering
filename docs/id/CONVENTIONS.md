# Konvensi (Conventions)

## Konvensi Berkas & Penamaan

| Item | Konvensi | Contoh |
|------|-----------|---------|
| Berkas PHP | `declare(strict_types=1)` pada semua berkas kelas | — |
| Controller | PascalCase + akhiran `Controller` | `UserController.php` |
| Model | PascalCase, kata benda tunggal | `User.php`, `Product.php` |
| Service | PascalCase + akhiran `Service` | `AuthService.php` |
| Middleware | PascalCase + akhiran `Middleware` | `AuthMiddleware.php` |
| Exception | PascalCase + akhiran `Exception` | `ValidationException.php` |
| View | direktori snake_case + berkas | `user/index.php` |
| Migrasi | `NNN_snake_case.sql` | `001_create_users_table.sql` |
| Seeder | PascalCase + akhiran `Seeder` | `AdminSeeder.php` |
| Berkas Konfigurasi | snake_case | `app.php`, `database.php` |
| Aset CSS/JS | snake_case | `app.css`, `app.js` |

## Peta Namespace

| Namespace | Direktori | Auto-load |
|-----------|-----------|-----------|
| `App\` | `src/` | PSR-4 via Composer |
| `Database\Seeds\` | `database/seeds/` | Pemuatan manual (manual require) |

## Penamaan Tabel Database

| Konvensi | Contoh |
|-----------|---------|
| Jamak (plural), snake_case | `users`, `products`, `order_items` |
| Kunci utama (primary key) | `id` (INT UNSIGNED AUTO_INCREMENT) |
| Stempel waktu (timestamps) | `created_at`, `updated_at` (TIMESTAMP) |
| Kunci asing (foreign keys) | `{tunggal}_id` → `user_id`, `product_id` |
| Karakter set (charset) | `utf8mb4` / `utf8mb4_unicode_ci` |
| Mesin (engine) | `InnoDB` |

## Standar Pengodean PHP (PHP Coding Standards)

### Ketegasan Tipe (Type Strictness)

```php
<?php
declare(strict_types=1);
```

Wajib ada di **setiap** berkas kelas PHP. View dikecualikan (berkas templat).

### Promosi Konstruktor (Constructor Promotion)

Lebih disukai mempromosikan properti (constructor promotion) jika memungkinkan:

```php
// Lebih disukai
public function __construct(private string $requiredRole) {}

// Tidak disukai
private string $requiredRole;
public function __construct(string $requiredRole) {
    $this->requiredRole = $requiredRole;
}
```

### Tipe Kembalian (Return Types)

Semua metode harus mendeklarasikan tipe kembalian:

```php
public function find(int $id): ?array
public function create(array $data): int
public function delete(int $id): bool
```

Tipe kembalian `never` digunakan untuk metode yang diakhiri dengan `exit`:

```php
public static function redirect(string $url, int $code = 302): never
```

### Statis vs Instansiasi (Static vs Instance)

| Pola (Pattern) | Penggunaan |
|----------------|------------|
| Metode Statis | Utilitas infrastruktur: `Session`, `Csrf`, `Response`, `Logger`, `Database::getInstance()`, `View::e()` |
| Metode Instansiasi | Logika bisnis: Service, Model, Controller |

### Pengubah Akses (Access Modifiers)

| Pengubah | Penggunaan |
|----------|------------|
| `public` | Metode API, aksi controller (controller actions) |
| `protected` | Metode kueri/eksekusi BaseModel, perenderan/pengalihan BaseController |
| `private` | Fungsi pembantu internal (misalnya, `validateSortColumn()`) |

## Pola Controller (Controller Patterns)

### Metode Aksi (Action Methods)

Selalu menerima `Request` dan mengembalikan `void`:

```php
public function index(Request $request): void
public function store(Request $request): void
```

### Aksi Controller Sumber Daya (Resource Controller Actions)

| Aksi | HTTP | URI | Tujuan |
|------|------|-----|--------|
| `index` | GET | `/resources` | Menampilkan semua |
| `create` | GET | `/resources/create` | Menampilkan formulir tambah baru |
| `store` | POST | `/resources` | Menyimpan data baru |
| `edit` | GET | `/resources/{id}/edit` | Menampilkan formulir edit |
| `update` | POST | `/resources/{id}` | Menyimpan perubahan |
| `destroy` | POST | `/resources/{id}/delete` | Menghapus data |

> Catatan: PUT/PATCH/DELETE tidak digunakan untuk pengiriman formulir (form submission). Framework ini menggunakan POST untuk semua operasi penulisan via formulir.

## Konvensi View

| Konvensi | Aturan |
|-----------|--------|
| Escaping | Selalu gunakan `View::e()` atau `e()` untuk data pengguna |
| Variabel Tata Letak | `$content` bersifat dipesan (reserved) — jangan lewatkan dalam `$data` |
| Pemuatan Parsial | Gunakan `require __DIR__ . '/../partials/name.php'` |
| Halaman Kesalahan | Menggunakan Bahasa Indonesia secara bawaan (default) |

## Komentar Header Berkas

Setiap berkas kelas PHP menyertakan komentar jalur berkas (file path comment):

```php
<?php
declare(strict_types=1);
// File: src/Controllers/UserController.php
```

---

Lihat: [ARCHITECTURE.md](ARCHITECTURE.md) · [CONTRIBUTING.md](CONTRIBUTING.md)
