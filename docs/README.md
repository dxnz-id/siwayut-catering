# Siwayut Catering — Vanilla PHP MVC Framework

> A lightweight, zero-dependency PHP 8.2+ MVC micro-framework for catering management.

![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green)

## Features

### Implemented

- IoC Container with reflection-based auto-wiring
- Router with HTTP verb methods, route parameters, and grouped middleware
- Middleware pipeline (Auth, CSRF, Role-based access)
- Session management with flash messages and old input
- CSRF protection with timing-safe verification
- Input validation with 10 built-in rules
- BaseModel with ActiveRecord-style CRUD and pagination
- View rendering with layouts, partials, and XSS escaping
- Exception hierarchy with global error handler
- Daily rotating file logger
- Database migrations and seeding
- `vanilla` CLI tool (artisan-like)

### Scaffolded / Implemented Resources

- File upload service
- Multi-resource CRUD pattern (Users, Categories, Events, Menus, and Orders implemented)

## Tech Stack

- **Language**: PHP 8.2+ (strict_types, promoted properties, match expressions, never return type)
- **Database**: MySQL / MariaDB via PDO
- **Template Engine**: Native PHP templates
- **Dependencies**: Zero third-party runtime dependencies (Composer for autoloading only)

## Quick Install

```bash
git clone <repository-url> && cd siwayut-catering
composer install
cp .env.example .env   # then edit database credentials
```

→ Full guide: [INSTALLATION.md](INSTALLATION.md)

## Quick Start

```bash
php vanilla db:create
php vanilla migrate
php vanilla db:seed --class=AdminSeeder
php vanilla serve
```

Then open `http://localhost:8000/login` — login with `admin@admin.com` / `password`.

→ Full guide: [QUICKSTART.md](QUICKSTART.md)

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
│   ├── Middleware/      # Request middleware
│   ├── Models/         # Database models
│   ├── Services/       # Business logic services
│   └── Views/          # PHP templates
├── storage/            # Logs and uploads
└── vanilla             # CLI tool
```

## Documentation

| #   | Document                               | Description                                 |
| --- | -------------------------------------- | ------------------------------------------- |
| 1   | [INSTALLATION.md](INSTALLATION.md)     | Setup from zero to running server           |
| 2   | [QUICKSTART.md](QUICKSTART.md)         | Build your first feature in 5 minutes       |
| 3   | [ARCHITECTURE.md](ARCHITECTURE.md)     | Request lifecycle and system design         |
| 4   | [CONTAINER.md](CONTAINER.md)           | IoC container and auto-wiring               |
| 5   | [ROUTING.md](ROUTING.md)               | Routes, parameters, and groups              |
| 6   | [MIDDLEWARE.md](MIDDLEWARE.md)         | Middleware pipeline and built-in middleware |
| 7   | [DATABASE.md](DATABASE.md)             | Database connection and BaseModel API       |
| 8   | [VALIDATION.md](VALIDATION.md)         | Input validation rules                      |
| 9   | [VIEWS.md](VIEWS.md)                   | Templates, layouts, and partials            |
| 10  | [ERROR_HANDLING.md](ERROR_HANDLING.md) | Exception hierarchy and error pages         |
| 11  | [SECURITY.md](SECURITY.md)             | XSS, CSRF, SQL injection, auth              |
| 12  | [CONVENTIONS.md](CONVENTIONS.md)       | Naming and code style rules                 |
| 13  | [EXAMPLES.md](EXAMPLES.md)             | Copy-paste recipes                          |
| 14  | [CONTRIBUTING.md](CONTRIBUTING.md)     | Contributor guide                           |

## License

MIT
