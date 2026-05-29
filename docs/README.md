# Siwayut Catering — Vanilla PHP MVC Framework

> A lightweight PHP 8.2+ MVC micro-framework for catering order management — public site, customer accounts, and admin dashboard.

![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green)

## Application features

| Area | Capabilities |
|------|----------------|
| **Public site** | Landing page with parallax hero, featured menus, infinite scroll (`GET /api/menus`) |
| **Customer auth** | Login / register at `/auth` (links to `customers` via `user_id`) |
| **Orders** | Public order form (multi-menu line items), order tracking by ID |
| **Admin** | CRUD for categories, events, menus (image upload + AI description), orders (status + payment), users |
| **Security** | Session auth, role middleware, optional Cloudflare Turnstile, CSRF tokens in forms, `APP_KEY`-backed password HMAC |
| **Media** | Image upload with LQIP thumbnails (`FileUploadService` + progressive-image component) |

## Framework features

- IoC Container with reflection-based auto-wiring
- Router with HTTP verbs, route parameters, nested middleware groups
- Middleware pipeline (`auth`, `role:{name}`, `csrf` — opt-in per group)
- Session with flash messages and old input
- CSRF token generation and timing-safe verification
- Input validation with built-in rules (+ `unique` table checks)
- `BaseModel` with CRUD, pagination, and sort-column whitelist
- View rendering with layouts, partials, `component()` helper, XSS escaping
- Exception hierarchy with global error handler
- Daily rotating file logger
- PHP class migrations and seeders
- `vanilla` CLI (serve, migrate, seed, make:*, routes)

## Tech stack

| Layer | Technology |
|-------|------------|
| Backend | PHP 8.2+ (`strict_types`), zero Composer runtime deps |
| Database | MySQL / MariaDB via PDO |
| Templates | Native PHP |
| CSS | Tailwind CSS v4 (`@tailwindcss/cli`), split source files under `public/assets/css/` |
| Frontend JS | Vanilla modules under `public/assets/js/modules/` |
| Optional | OpenAI-compatible API (menu descriptions), Cloudflare Turnstile |

## Quick install

```bash
git clone <repository-url> siwayut-catering && cd siwayut-catering
composer install
npm install
cp .env.example .env   # set APP_KEY, DB_*, optional AI & Turnstile
npm run css:build
```

→ Full guide: [INSTALLATION.md](INSTALLATION.md)

## Quick start

```bash
php vanilla db:create
php vanilla migrate
php vanilla db:seed --class=AdminSeeder
php vanilla serve
```

- Home: `http://localhost:8000`
- Admin login: `http://localhost:8000/auth` — `admin@admin.com` / `password`

→ [QUICKSTART.md](QUICKSTART.md)

## Project structure

```
siwayut-catering/
├── bootstrap/app.php       # Container, session, exception handler
├── config/                 # app, database, bindings, routes
├── database/
│   ├── migrations/         # PHP migration classes (*.php)
│   └── seeds/              # AdminSeeder, MenuSeeder, OrderSeeder
├── docs/                   # Documentation (EN + id/)
├── public/
│   ├── index.php           # Front controller
│   ├── uploads → ../storage/uploads
│   └── assets/css|js       # Built CSS + JS modules
├── src/
│   ├── Controllers/        # Auth, Welcome, User, Category, Event, Menu, Order
│   ├── Core/               # Framework primitives
│   ├── Middleware/
│   ├── Models/             # User, Category, Event, Menu, Customer, Order
│   ├── Services/
│   └── Views/
├── storage/logs|uploads
├── vanilla                 # CLI entrypoint
├── package.json            # Tailwind build scripts
└── composer.json
```

## Documentation index

| # | Document | Description |
|---|----------|-------------|
| 1 | [INSTALLATION.md](INSTALLATION.md) | Setup from zero to running server |
| 2 | [QUICKSTART.md](QUICKSTART.md) | Build your first feature in 5 minutes |
| 3 | [ARCHITECTURE.md](ARCHITECTURE.md) | Request lifecycle and system design |
| 4 | [CONTAINER.md](CONTAINER.md) | IoC container and auto-wiring |
| 5 | [ROUTING.md](ROUTING.md) | Routes, parameters, and groups |
| 6 | [MIDDLEWARE.md](MIDDLEWARE.md) | Middleware pipeline |
| 7 | [DATABASE.md](DATABASE.md) | Schema, migrations, BaseModel API |
| 8 | [VALIDATION.md](VALIDATION.md) | Input validation rules |
| 9 | [VIEWS.md](VIEWS.md) | Templates, layouts, components |
| 10 | [ERROR_HANDLING.md](ERROR_HANDLING.md) | Exceptions and error pages |
| 11 | [SECURITY.md](SECURITY.md) | XSS, CSRF, auth, Turnstile, APP_KEY |
| 12 | [CONVENTIONS.md](CONVENTIONS.md) | Naming and code style |
| 13 | [EXAMPLES.md](EXAMPLES.md) | Copy-paste recipes |
| 14 | [CONTRIBUTING.md](CONTRIBUTING.md) | Contributor guide |

Bahasa Indonesia: [id/README.md](id/README.md)

## License

MIT
