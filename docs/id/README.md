# Siwayut Catering — Vanilla PHP MVC Framework

> Framework mikro MVC PHP 8.2+ yang ringan dan tanpa dependensi untuk manajemen katering.

![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green)

## Fitur

### Terimplementasi

- IoC Container dengan auto-wiring berbasis refleksi
- Router dengan metode HTTP verb, parameter route, dan middleware berkelompok
- Pipeline Middleware (Auth, CSRF, akses berbasis Role)
- Manajemen Session dengan flash message dan input lama
- Proteksi CSRF dengan verifikasi timing-safe
- Validasi input dengan 10 aturan bawaan
- BaseModel dengan CRUD gaya ActiveRecord dan pagination
- Rendering View dengan layout, partial, dan escaping XSS
- Hierarki Exception dengan penangan error global
- Logger file yang berotasi harian
- Migrasi database dan seeding
- Tool CLI `vanilla` (mirip artisan)

### Sumber Daya yang Dibuat / Diimplementasikan

- Layanan unggah file
- Pola CRUD multi-sumber daya (Users, Categories, Events, Menus, dan Orders telah diimplementasikan)

## Tech Stack

- **Bahasa**: PHP 8.2+ (strict_types, promoted properties, match expressions, never return type)
- **Database**: MySQL / MariaDB melalui PDO
- **Template Engine**: Template PHP native
- **Dependensi**: Tanpa dependensi runtime pihak ketiga (Composer hanya untuk autoloading)

## Instalasi Cepat

```bash
git clone <repository-url> && cd siwayut-catering
composer install
cp .env.example .env   # kemudian edit kredensial database
```

→ Panduan lengkap: [INSTALLATION.md](general/INSTALLATION.md)

## Memulai Cepat

```bash
php vanilla key:generate
php vanilla db:create
php vanilla migrate
php vanilla db:seed --class=AdminSeeder
php vanilla serve
```

Kemudian buka `http://localhost:8000/login` — login dengan `admin@admin.com` / `password`.

→ Panduan lengkap: [QUICKSTART.md](general/QUICKSTART.md)

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
│   ├── Core/           # Class inti framework
│   ├── Exceptions/     # Hierarki exception
│   ├── Helpers/        # Fungsi helper global
│   ├── Middleware/      # Middleware request
│   ├── Models/         # Model database
│   ├── Services/       # Layanan logika bisnis
│   └── Views/          # Template PHP
├── storage/            # Log dan unggahan
└── vanilla             # Tool CLI
```

## Dokumentasi

| #   | Dokumen                                | Deskripsi                                   |
| --- | -------------------------------------- | ------------------------------------------- |
| 1   | [INSTALLATION.md](general/INSTALLATION.md)     | Pengaturan dari nol hingga server berjalan  |
| 2   | [QUICKSTART.md](general/QUICKSTART.md)         | Membangun fitur pertama Anda dalam 5 menit  |
| 3   | [ARCHITECTURE.md](core/ARCHITECTURE.md)     | Siklus hidup request dan desain sistem      |
| 4   | [CONTAINER.md](core/CONTAINER.md)           | IoC container dan auto-wiring               |
| 5   | [ROUTING.md](core/ROUTING.md)               | Route, parameter, dan grup                  |
| 6   | [MIDDLEWARE.md](core/MIDDLEWARE.md)         | Pipeline middleware dan middleware bawaan   |
| 7   | [DATABASE.md](database/DATABASE.md)             | Koneksi database dan API BaseModel          |
| 8   | [VALIDATION.md](backend/VALIDATION.md)         | Aturan validasi input                       |
| 9   | [VIEWS.md](frontend/VIEWS.md)                   | Template, layout, dan partial               |
| 10  | [ERROR_HANDLING.md](security/ERROR_HANDLING.md) | Hierarki exception dan halaman error        |
| 11  | [SECURITY.md](security/SECURITY.md)             | XSS, CSRF, SQL injection, auth              |
| 12  | [CONVENTIONS.md](guides/CONVENTIONS.md)       | Aturan penamaan dan gaya kode               |
| 13  | [EXAMPLES.md](guides/EXAMPLES.md)             | Resep salin-tempel                          |
| 14  | [CONTRIBUTING.md](guides/CONTRIBUTING.md)     | Panduan kontributor                         |

## Lisensi

MIT