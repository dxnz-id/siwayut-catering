# Berkontribusi

## Struktur Proyek

```
siwayut-catering/
├── bootstrap/app.php      # Exception handler, session, container
├── config/                 # App config, DB config, bindings, routes
├── database/               # Migrations (.sql), seeders (.php)
├── docs/                   # Dokumentasi (Anda berada di sini)
├── public/                 # Web root — index.php, assets/
├── src/
│   ├── Controllers/        # HTTP controllers
│   ├── Core/               # Framework internals
│   ├── Exceptions/         # Exception hierarchy
│   ├── Helpers/            # Global helper functions
│   ├── Middleware/          # Request middleware
│   ├── Models/             # Database models (extend BaseModel)
│   ├── Services/           # Business logic
│   └── Views/              # PHP templates
├── storage/                # Logs, uploads (gitignored)
└── vanilla                 # CLI tool
```

## Memulai

```bash
git clone <repository-url>
cd siwayut-catering
composer install
cp .env.example .env        # edit DB credentials
php vanilla db:create
php vanilla migrate
php vanilla db:seed --class=AdminSeeder
php vanilla serve
```

## Strategi Git Branching

Kami mengikuti model GitHub Flow yang disederhanakan:
1. `main` selalu dalam kondisi siap deploy.
2. Buat branch dari `main` untuk semua fitur/perbaikan: `feature/nama-fitur` atau `bugfix/deskripsi-masalah`.
3. Buka Pull Request (PR) yang ditujukan ke `main`.

---

## Standar Pengodean

### Wajib

- `declare(strict_types=1);` pada semua file class PHP
- Return types pada semua method
- Komentar header file: `// File: path/to/file.php`
- PDO prepared statements untuk semua SQL
- `View::e()` escaping untuk semua output

### Penamaan

| Item | Konvensi |
|------|-----------|
| Controllers | PascalCase + akhiran `Controller` |
| Models | PascalCase, tunggal |
| Services | PascalCase + akhiran `Service` |
| Middleware | PascalCase + akhiran `Middleware` |
| Exceptions | PascalCase + akhiran `Exception` |
| Database tables | jamak, snake_case |
| Migrations | `NNN_deskripsi.sql` |
| Views | `fitur/aksi.php` |

Lihat [CONVENTIONS.md](CONVENTIONS.md) untuk detail lengkapnya.

## Menambahkan Fitur

1. **Migration**: `php vanilla make:migration create_x_table` → tulis SQL → `php vanilla migrate`
2. **Model**: `php vanilla make:model X` → atur `$table`, tambahkan custom finders
3. **Service**: `php vanilla make:service X` → inject model, tambahkan business logic
4. **Controller**: `php vanilla make:controller X` → inject service, tambahkan aksi
5. **Binding**: Daftarkan factory di `config/bindings.php`
6. **Routes**: Daftarkan endpoint di `config/routes.php`
7. **Views**: Buat template di `src/Views/x/`

Contoh lengkap: [EXAMPLES.md](EXAMPLES.md)

## Checklist Pengujian & Code Review

Sebelum mengirimkan Pull Request, pastikan Anda telah memenuhi hal berikut:

- [ ] `php -l` berhasil pada semua file yang dimodifikasi (tidak ada syntax error)
- [ ] `grep -r 'TODO: implement'` tidak menampilkan stub yang belum diimplementasikan
- [ ] Semua class PHP baru memiliki `declare(strict_types=1);`
- [ ] Semua output yang terlihat oleh pengguna menggunakan `View::e()` escaping
- [ ] Semua SQL menggunakan prepared statements dengan parameter binding
- [ ] Form menyertakan token CSRF melalui `Csrf::field()`
- [ ] `php vanilla routes` menampilkan tabel rute yang benar
- [ ] Pengujian manual di browser pada halaman yang terdampak

### Template Pull Request

Gunakan format ini untuk deskripsi PR Anda:

```markdown
## Deskripsi
Jelaskan apa yang dilakukan PR ini dan mengapa hal tersebut diperlukan.

## Issue Terkait
Memperbaiki #123

## Verifikasi
- [ ] Saya telah menguji alur kerja secara manual di browser.
- [ ] Saya telah memverifikasi operasi database menggunakan `php vanilla tinker`.
```

## Dokumentasi

Saat menambahkan komponen, perbarui dokumen yang relevan di `docs/`:

| Komponen | Dokumen |
|-----------|----------|
| Routes | [ROUTING.md](../core/ROUTING.md) |
| Middleware | [MIDDLEWARE.md](../core/MIDDLEWARE.md) |
| Models / DB | [DATABASE.md](../database/DATABASE.md) |
| Aturan Validasi | [VALIDATION.md](../backend/VALIDATION.md) |
| Views / Layouts | [VIEWS.md](../frontend/VIEWS.md) |
| Exceptions | [ERROR_HANDLING.md](../security/ERROR_HANDLING.md) |
| Langkah Keamanan | [SECURITY.md](../security/SECURITY.md) |

---

Lihat: [CONVENTIONS.md](CONVENTIONS.md) · [EXAMPLES.md](EXAMPLES.md) · [ARCHITECTURE.md](../core/ARCHITECTURE.md)