# Konvensi

## Konvensi File & Penamaan

| Item | Konvensi | Contoh |
|------|-----------|---------|
| File PHP | `declare(strict_types=1)` pada semua file class | — |
| Controller | PascalCase + akhiran `Controller` | `UserController.php` |
| Model | PascalCase, kata benda tunggal | `User.php`, `Product.php` |
| Service | PascalCase + akhiran `Service` | `AuthService.php` |
| Middleware | PascalCase + akhiran `Middleware` | `AuthMiddleware.php` |
| Exception | PascalCase + akhiran `Exception` | `ValidationException.php` |
| Views | Direktori snake_case + file | `user/index.php` |
| Migrations | `NNN_snake_case.sql` | `001_create_users_table.sql` |
| Seeders | PascalCase + akhiran `Seeder` | `AdminSeeder.php` |
| File Config | snake_case | `app.php`, `database.php` |
| Aset CSS/JS | snake_case | `app.css`, `app.js` |

## Peta Namespace

| Namespace | Direktori | Auto-load |
|-----------|-----------|-----------|
| `App\` | `src/` | PSR-4 via Composer |
| `Database\Seeds\` | `database/seeds/` | Manual require |

## Penamaan Tabel Database

| Konvensi | Contoh |
|-----------|---------|
| Jamak, snake_case | `users`, `products`, `order_items` |
| Primary key | `id` (INT UNSIGNED AUTO_INCREMENT) |
| Timestamps | `created_at`, `updated_at` (TIMESTAMP) |
| Foreign keys | `{singular}_id` → `user_id`, `product_id` |
| Charset | `utf8mb4` / `utf8mb4_unicode_ci` |
| Engine | `InnoDB` |

## Standar Pengkodean PHP

### Strictness Tipe

```php
<?php
declare(strict_types=1);
```

Wajib ada pada **setiap** file class PHP. Views dikecualikan (file template).

### Constructor Promotion

Gunakan promoted properties jika memungkinkan:

```php
// Disarankan
public function __construct(private string $requiredRole) {}

// Tidak disarankan
private string $requiredRole;
public function __construct(string $requiredRole) {
    $this->requiredRole = $requiredRole;
}
```

### Return Types

Semua method harus mendeklarasikan return type:

```php
public function find(int $id): ?array
public function create(array $data): int
public function delete(int $id): bool
```

Gunakan return type `never` untuk method yang diakhiri dengan `exit`:

```php
public static function redirect(string $url, int $code = 302): never
```

### Static vs Instance

| Pola | Penggunaan |
|---------|-------|
| Static methods | Utilitas infrastruktur: `Session`, `Csrf`, `Response`, `Logger`, `Database::getInstance()`, `View::e()` |
| Instance methods | Logika bisnis: Services, Models, Controllers |

### Access Modifiers

| Modifier | Penggunaan |
|----------|-------|
| `public` | Method API, action controller |
| `protected` | Query/execute BaseModel, render/redirect BaseController |
| `private` | Helper internal (contoh: `validateSortColumn()`) |

## Pola Controller

### Action Methods

Selalu terima `Request` dan kembalikan `void`:

```php
public function index(Request $request): void
public function store(Request $request): void
```

### Action Resource Controller

| Action | HTTP | URI | Tujuan |
|--------|------|-----|---------|
| `index` | GET | `/resources` | Daftar semua |
| `create` | GET | `/resources/create` | Tampilkan form buat |
| `store` | POST | `/resources` | Simpan baru |
| `edit` | GET | `/resources/{id}/edit` | Tampilkan form edit |
| `update` | POST | `/resources/{id}` | Simpan perubahan |
| `destroy` | POST | `/resources/{id}/delete` | Hapus |

> Catatan: PUT/PATCH/DELETE tidak digunakan untuk pengiriman form. Framework menggunakan POST untuk semua operasi tulis melalui form.

## Konvensi View

| Konvensi | Aturan |
|-----------|------|
| Escaping | Selalu gunakan `View::e()` atau `e()` untuk data pengguna |
| Variabel Layout | `$content` dicadangkan — jangan masukkan ke dalam `$data` |
| Partial inclusion | Gunakan `require __DIR__ . '/../partials/name.php'` |
| Halaman Error | Bahasa Indonesia secara default |

## Komentar Header File

Setiap file class PHP menyertakan komentar path file:

```php
<?php
declare(strict_types=1);
// File: src/Controllers/UserController.php
```

---

Lihat: [ARCHITECTURE.md](../core/ARCHITECTURE.md) · [CONTRIBUTING.md](CONTRIBUTING.md)