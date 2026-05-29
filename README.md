# Siwayut Catering

Aplikasi manajemen katering berbasis **Vanilla PHP MVC** — landing page publik, formulir pesanan, pelacakan pesanan, dan panel admin.

## Persyaratan

- PHP 8.2+ (`pdo`, `pdo_mysql`, `mbstring`, `curl`, `gd`)
- Composer 2.x
- MySQL / MariaDB 8.0+
- Node.js 18+ (untuk build Tailwind CSS)

## Mulai cepat

```bash
composer install
npm install
cp .env.example .env   # sesuaikan DB, APP_KEY, opsional AI & Turnstile
npm run css:build
php vanilla db:create
php vanilla migrate
php vanilla db:seed
php vanilla serve
```

- Landing: [http://localhost:8000](http://localhost:8000)
- Admin: [http://localhost:8000/auth](http://localhost:8000/auth) — default `admin@admin.com` / `password` (setelah seed)

Pengembangan dengan hot-reload CSS:

```bash
npm run dev
```

## Dokumentasi

| Bahasa | Indeks |
|--------|--------|
| English | [docs/README.md](docs/README.md) |
| Indonesia | [docs/id/README.md](docs/id/README.md) |

Panduan untuk agen AI: [AGENTS.md](AGENTS.md)

## Lisensi

MIT
