# Kontribusi (Contributing)

## Struktur Proyek

```
siwayut-catering/
├── bootstrap/app.php      # Penangan exception, session, kontainer
├── config/                 # Konfigurasi aplikasi, konfigurasi DB, bindings, rute
├── database/               # Migrasi (.sql), seeder (.php)
├── docs/                   # Dokumentasi (Anda berada di sini)
├── public/                 # Web root — index.php, aset/ (assets)
├── src/
│   ├── Controllers/        # HTTP controller
│   ├── Core/               # Internal framework
│   ├── Exceptions/         # Hierarki exception (pengecualian)
│   ├── Helpers/            # Fungsi pembantu global (helpers)
│   ├── Middleware/          # Request middleware
│   ├── Models/             # Model database (mewarisi BaseModel)
│   ├── Services/           # Logika bisnis
│   └── Views/              # Templat PHP
├── storage/                # Log, unggahan berkas (diabaikan oleh git)
└── vanilla                 # Alat CLI (command line tool)
```

## Memulai

```bash
git clone <repository-url>
cd siwayut-catering
composer install
cp .env.example .env        # edit kredensial DB
php vanilla db:create
php vanilla migrate
php vanilla db:seed --class=AdminSeeder
php vanilla serve
```

## Standar Pengodean (Coding Standards)

### Wajib (Required)

- `declare(strict_types=1);` pada semua berkas kelas PHP
- Menyediakan tipe kembalian (return types) pada semua metode
- Komentar header berkas: `// File: path/to/file.php`
- Menggunakan prepared statement PDO untuk semua kueri SQL
- Melakukan escaping dengan `View::e()` untuk semua output ke browser

### Penamaan

| Item | Konvensi |
|------|-----------|
| Controller | PascalCase + akhiran `Controller` |
| Model | PascalCase, tunggal (singular) |
| Service | PascalCase + akhiran `Service` |
| Middleware | PascalCase + akhiran `Middleware` |
| Exception | PascalCase + akhiran `Exception` |
| Tabel Database | jamak (plural), snake_case |
| Migrasi | `NNN_deskripsi.sql` |
| View | `fitur/tindakan.php` |

Lihat [CONVENTIONS.md](CONVENTIONS.md) untuk detail lengkap.

## Menambahkan Fitur baru

1. **Migrasi**: `php vanilla make:migration create_x_table` → tulis SQL → `php vanilla migrate`
2. **Model**: `php vanilla make:model X` → atur `$table`, tambahkan pencari kustom (custom finders)
3. **Service**: `php vanilla make:service X` → suntikkan model, tambahkan logika bisnis
4. **Controller**: `php vanilla make:controller X` → suntikkan service, tambahkan aksi (actions)
5. **Binding**: Daftarkan pabrik (factory) di `config/bindings.php`
6. **Rute**: Daftarkan endpoint di `config/routes.php`
7. **View**: Buat templat di `src/Views/x/`

Contoh lengkap: [EXAMPLES.md](EXAMPLES.md)

## Daftar Periksa Pengujian (Testing Checklist)

Sebelum mengirimkan perubahan:

- [ ] `php -l` lolos pada semua berkas yang dimodifikasi (tidak ada kesalahan sintaksis)
- [ ] `grep -r 'TODO: implement'` tidak menampilkan rintisan yang belum diimplementasikan
- [ ] Semua kelas PHP baru memiliki `declare(strict_types=1);`
- [ ] Semua output yang terlihat oleh pengguna menggunakan escaping `View::e()`
- [ ] Semua SQL menggunakan prepared statement dengan pengikatan parameter (parameter binding)
- [ ] Formulir menyertakan token CSRF melalui `Csrf::field()`
- [ ] `php vanilla routes` menampilkan tabel rute yang benar
- [ ] Melakukan uji coba manual di peramban (browser) untuk halaman yang terpengaruh

## Dokumentasi

Saat menambahkan komponen, perbarui dokumentasi yang relevan di `docs/`:

| Komponen | Dokumen |
|-----------|----------|
| Rute | [ROUTING.md](ROUTING.md) |
| Middleware | [MIDDLEWARE.md](MIDDLEWARE.md) |
| Model / DB | [DATABASE.md](DATABASE.md) |
| Aturan Validasi | [VALIDATION.md](VALIDATION.md) |
| View / Tata Letak (Layout) | [VIEWS.md](VIEWS.md) |
| Exception (Pengecualian) | [ERROR_HANDLING.md](ERROR_HANDLING.md) |
| Langkah Keamanan | [SECURITY.md](SECURITY.md) |

---

Lihat: [CONVENTIONS.md](CONVENTIONS.md) · [EXAMPLES.md](EXAMPLES.md) · [ARCHITECTURE.md](ARCHITECTURE.md)
