# Siwayut Catering — Vanilla PHP MVC Framework

> A lightweight, zero-dependency PHP 8.2+ micro MVC framework for catering management.

![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green)

> *Also available in: [Indonesian](id/README.md)*

## Features

### Implemented

- IoC Container with reflection-based auto-wiring
- Router with HTTP verb methods, route parameters, and grouped middleware
- Middleware Pipeline (Auth, CSRF, Role-based access)
- Session management with flash messages and old input
- CSRF protection with timing-safe verification
- Input validation with 10 built-in rules
- BaseModel with ActiveRecord-style CRUD and pagination
- View rendering with layouts, partials, and XSS escaping
- Exception hierarchy with global error handling
- Daily rotating file logger
- Database migrations and seeding
- `vanilla` CLI tool (Artisan-like)

### Resources Built / Implemented

- File upload service
- Multi-resource CRUD (Users, Categories, Events, Menus, and Orders implemented)

## Tech Stack

- **Language**: PHP 8.2+ (strict_types, promoted properties, match expressions, never return type)
- **Database**: MySQL / MariaDB via PDO
- **Template Engine**: Native PHP templates
- **Dependencies**: No third-party runtime dependencies (Composer is for autoloading only)

## Quick Install

```bash
git clone <repository-url> && cd siwayut-catering
composer install
cp .env.example .env   # then edit database credentials
```

→ Full guide: [INSTALLATION.md](general/INSTALLATION.md)

## Quick Start

```bash
php vanilla key:generate
php vanilla db:create
php vanilla migrate
php vanilla db:seed --class=AdminSeeder
php vanilla serve
```

Then open `http://localhost:8000/login` — login with `admin@admin.com` / `password`.

→ Full guide: [QUICKSTART.md](general/QUICKSTART.md)

## Project Structure

```
siwayut-catering/
├── bootstrap/          # Application bootstrap
├── config/             # Configuration files
├── database/           # Migrations and seeders
├── docs/               # Documentation
├── public/             # Web root (index.php, assets)
├── src/
│   ├── Controllers/    # HTTP controllers
│   ├── Core/           # Framework core classes
│   ├── Exceptions/     # Exception hierarchy
│   ├── Helpers/        # Global helper functions
│   ├── Middleware/     # Request middleware
│   ├── Models/         # Database models
│   ├── Services/       # Business logic layer
│   └── Views/          # PHP templates
├── storage/            # Logs and uploads
└── vanilla             # CLI tool
```

## Documentation

| #   | Document                                | Description                                    |
| --- | --------------------------------------- | ---------------------------------------------- |
| 1   | [INSTALLATION.md](general/INSTALLATION.md)     | Setup from scratch to running server           |
| 2   | [QUICKSTART.md](general/QUICKSTART.md)         | Build your first feature in 5 minutes          |
| 3   | [ARCHITECTURE.md](core/ARCHITECTURE.md)     | Request lifecycle and system design            |
| 4   | [CONTAINER.md](core/CONTAINER.md)           | IoC container and auto-wiring                  |
| 5   | [ROUTING.md](core/ROUTING.md)               | Routes, parameters, and groups                 |
| 6   | [MIDDLEWARE.md](core/MIDDLEWARE.md)         | Middleware pipeline and built-in middleware     |
| 7   | [DATABASE.md](database/DATABASE.md)             | Database connection and BaseModel API          |
| 8   | [VALIDATION.md](backend/VALIDATION.md)         | Input validation rules                         |
| 9   | [VIEWS.md](frontend/VIEWS.md)                   | Templates, layouts, and partials               |
| 10  | [ERROR_HANDLING.md](security/ERROR_HANDLING.md) | Exception hierarchy and error pages            |
| 11  | [SECURITY.md](security/SECURITY.md)             | XSS, CSRF, SQL injection, auth                 |
| 12  | [CONVENTIONS.md](guides/CONVENTIONS.md)       | Naming conventions and code style              |
| 13  | [EXAMPLES.md](guides/EXAMPLES.md)             | Copy-paste recipes                             |
| 14  | [CONTRIBUTING.md](guides/CONTRIBUTING.md)     | Contributor guide                              |

## License

MIT
