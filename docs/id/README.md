# Siwayut Catering — Framework MVC PHP Vanilla

> Framework mikro MVC PHP 8.2+ yang ringan dan tanpa dependensi untuk manajemen katering.

![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green)

## Fitur

### Sudah Diimplementasikan
- IoC Container dengan auto-wiring berbasis reflection
- Router dengan metode HTTP, parameter rute, dan grup middleware
- Pipeline Middleware (Otentikasi, CSRF, Akses berbasis peran)
- Manajemen Session dengan pesan flash dan input lama (old input)
- Perlindungan CSRF dengan verifikasi aman (timing-safe)
- Validasi Input dengan 10 aturan bawaan
- BaseModel dengan gaya ActiveRecord untuk CRUD dan paginasi
- Rendering View dengan layout, partial, dan perlindungan (escaping) XSS
- Hierarki Exception dengan penangan kesalahan (error handler) global
- Logger file bergulir harian
- Migrasi database dan seeder
- Alat CLI `vanilla` (mirip artisan)

### Sumber Daya yang Disediakan / Diimplementasikan (Scaffolded)
- Layanan unggah (upload) file
- Pola CRUD multi-sumber daya (Pengguna, Kategori, Hari Raya (Events), Menu, dan Pesanan telah diimplementasikan)

## Tumpukan Teknologi (Tech Stack)

- **Bahasa**: PHP 8.2+ (strict_types, promoted properties, match expressions, never return type)
- **Database**: MySQL / MariaDB via PDO
- **Mesin Templat**: Templat PHP bawaan (Native)
- **Dependensi**: Nol dependensi runtime pihak ketiga (Composer hanya digunakan untuk autoloading)

## Instalasi Cepat

```bash
git clone <repository-url> && cd siwayut-catering
composer install
cp .env.example .env   # lalu edit kredensial database
```

→ Panduan lengkap: [INSTALLATION.md](INSTALLATION.md)

## Mulai Cepat (Quick Start)

```bash
php vanilla db:create
php vanilla migrate
php vanilla db:seed --class=AdminSeeder
php vanilla serve
```

Lalu buka `http://localhost:8000/login` — masuk dengan `admin@admin.com` / `password`.

→ Panduan lengkap: [QUICKSTART.md](QUICKSTART.md)

## Struktur Proyek

```
siwayut-catering/
├── bootstrap/          # Bootstrap aplikasi
├── config/             # File konfigurasi
├── database/           # Migrasi dan seeder
├── docs/               # Dokumentasi
├── public/             # Web root (index.php, aset)
├── src/
│   ├── Controllers/    # Controller HTTP
│   ├── Core/           # Kelas inti framework
│   ├── Exceptions/     # Hierarki exception
│   ├── Helpers/        # Fungsi helper global
│   ├── Middleware/      # Middleware request
│   ├── Models/         # Model database
│   ├── Services/       # Layanan logika bisnis (Business logic services)
│   └── Views/          # Templat PHP
├── storage/            # Log dan hasil unggahan
└── vanilla             # Alat CLI
```

## Dokumentasi

| # | Dokumen | Deskripsi |
|---|----------|-------------|
| 1 | [INSTALLATION.md](INSTALLATION.md) | Persiapan dari nol hingga menjalankan server |
| 2 | [QUICKSTART.md](QUICKSTART.md) | Buat fitur pertama Anda dalam 5 menit |
| 3 | [ARCHITECTURE.md](ARCHITECTURE.md) | Siklus hidup request dan desain sistem |
| 4 | [CONTAINER.md](CONTAINER.md) | IoC container dan auto-wiring |
| 5 | [ROUTING.md](ROUTING.md) | Rute, parameter, dan grup |
| 6 | [MIDDLEWARE.md](MIDDLEWARE.md) | Pipeline middleware dan middleware bawaan |
| 7 | [DATABASE.md](DATABASE.md) | Koneksi database dan API BaseModel |
| 8 | [VALIDATION.md](VALIDATION.md) | Aturan validasi input |
| 9 | [VIEWS.md](VIEWS.md) | Templat, layout, dan partial |
| 10 | [ERROR_HANDLING.md](ERROR_HANDLING.md) | Hierarki exception dan halaman error |
| 11 | [SECURITY.md](SECURITY.md) | XSS, CSRF, injeksi SQL, auth |
| 12 | [CONVENTIONS.md](CONVENTIONS.md) | Aturan penamaan dan gaya penulisan kode |
| 13 | [EXAMPLES.md](EXAMPLES.md) | Resep siap salin-tempel (copy-paste) |
| 14 | [CONTRIBUTING.md](CONTRIBUTING.md) | Panduan kontributor |

## Lisensi

MIT
