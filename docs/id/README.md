# Siwayut Catering — Framework MVC PHP Vanilla

> Framework mikro MVC PHP 8.2+ untuk manajemen katering — situs publik, akun pelanggan, dan panel admin.

![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green)

## Fitur aplikasi

| Area | Kemampuan |
|------|-----------|
| **Situs publik** | Landing page, menu unggulan, load-more (`GET /api/menus`) |
| **Auth pelanggan** | Login / daftar di `/auth` (terhubung ke `customers` via `user_id`) |
| **Pesanan** | Formulir pesanan multi-menu, lacak pesanan berdasarkan ID |
| **Admin** | CRUD kategori, event, menu (upload gambar + deskripsi AI), pesanan, pengguna |
| **Keamanan** | Session, middleware peran, Turnstile opsional, token CSRF di form, HMAC password via `APP_KEY` |
| **Media** | Upload gambar + thumbnail LQIP |

## Fitur framework

- IoC Container dengan auto-wiring reflection
- Router, grup middleware, parameter rute
- Session, flash, old input
- Validasi input + aturan `unique`
- BaseModel (CRUD, paginasi)
- View dengan layout, partial, helper `component()`
- Exception handler global, logger harian
- Migrasi & seeder PHP
- CLI `vanilla`

## Tech stack

| Lapisan | Teknologi |
|---------|-----------|
| Backend | PHP 8.2+ |
| Database | MySQL / MariaDB (PDO) |
| Template | PHP native |
| CSS | Tailwind CSS v4 |
| JS | Modul vanilla di `public/assets/js/modules/` |
| Opsional | API kompatibel OpenAI, Cloudflare Turnstile |

## Instalasi cepat

```bash
git clone <repository-url> siwayut-catering && cd siwayut-catering
composer install
npm install
cp .env.example .env
npm run css:build
```

→ [INSTALLATION.md](INSTALLATION.md)

## Mulai cepat

```bash
php vanilla db:create
php vanilla migrate
php vanilla db:seed --class=AdminSeeder
php vanilla serve
```

- Beranda: `http://localhost:8000`
- Admin: `http://localhost:8000/auth` — `admin@admin.com` / `password`

→ [QUICKSTART.md](QUICKSTART.md)

## Struktur proyek

```
siwayut-catering/
├── bootstrap/app.php
├── config/
├── database/migrations/*.php
├── database/seeds/
├── docs/ / docs/id/
├── public/ (index.php, assets, uploads → storage/uploads)
├── src/ (Controllers, Core, Models, Services, Views)
├── storage/logs|uploads
├── vanilla
├── package.json
└── composer.json
```

## Dokumentasi

| # | Dokumen | Deskripsi |
|---|----------|-----------|
| 1 | [INSTALLATION.md](INSTALLATION.md) | Setup lengkap |
| 2 | [QUICKSTART.md](QUICKSTART.md) | Fitur pertama |
| 3 | [ARCHITECTURE.md](ARCHITECTURE.md) | Arsitektur |
| 4 | [CONTAINER.md](CONTAINER.md) | IoC |
| 5 | [ROUTING.md](ROUTING.md) | Rute |
| 6 | [MIDDLEWARE.md](MIDDLEWARE.md) | Middleware |
| 7 | [DATABASE.md](DATABASE.md) | Database & skema |
| 8 | [VALIDATION.md](VALIDATION.md) | Validasi |
| 9 | [VIEWS.md](VIEWS.md) | View & komponen |
| 10 | [ERROR_HANDLING.md](ERROR_HANDLING.md) | Error |
| 11 | [SECURITY.md](SECURITY.md) | Keamanan |
| 12 | [CONVENTIONS.md](CONVENTIONS.md) | Konvensi kode |
| 13 | [EXAMPLES.md](EXAMPLES.md) | Contoh |
| 14 | [CONTRIBUTING.md](CONTRIBUTING.md) | Kontribusi |

English: [../README.md](../README.md)

## Lisensi

MIT
