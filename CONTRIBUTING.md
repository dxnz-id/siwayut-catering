# Contributing

## Project Structure

```
siwayut-catering/
├── bootstrap/app.php      # Exception handler, session, container
├── config/                 # App config, DB config, bindings, routes
├── database/               # Migrations, seeders
├── docs/                   # Documentation
├── public/                 # Web root — index.php, assets/
├── src/
│   ├── Controllers/        # HTTP controllers
│   ├── Core/               # Framework internals
│   ├── Exceptions/         # Exception hierarchy
│   ├── Helpers/            # Global helper functions
│   ├── Middleware/          # Request middleware
│   ├── Models/             # Database models
│   ├── Services/           # Business logic
│   └── Views/              # PHP templates
├── storage/                # Logs, uploads
└── vanilla                 # CLI tool
```

## Getting Started

```bash
git clone <repository-url>
cd siwayut-catering
composer install
cp .env.example .env
php vanilla key:generate
php vanilla db:create
php vanilla migrate
php vanilla db:seed --class=AdminSeeder
php vanilla serve
```

## Git Branching Strategy

Follow a simplified GitHub Flow model:
1. `main` is always deployable.
2. Branch off `main` for all features/fixes: `feature/name-of-feature` or `bugfix/issue-description`.
3. Open a Pull Request against `main`.

## Coding Standards

### Required
- `declare(strict_types=1);` on all PHP class files
- Return types on all methods
- File header comment: `// File: path/to/file.php`
- PDO prepared statements for all SQL
- `e()` escaping for all output

### Naming
| Item | Convention |
|------|-----------|
| Controllers | PascalCase + `Controller` suffix |
| Models | PascalCase, singular |
| Services | PascalCase + `Service` suffix |
| Middleware | PascalCase + `Middleware` suffix |
| Exceptions | PascalCase + `Exception` suffix |
| Database tables | plural, snake_case |
| Migrations | `NNN_description.php` |
| Views | `feature/action.php` |

## PR Checklist

- [ ] `php -l` passes on all modified files
- [ ] All new PHP classes have `declare(strict_types=1);`
- [ ] All user-visible output uses `e()` escaping
- [ ] All SQL uses prepared statements with parameter binding
- [ ] Forms include CSRF token via `Csrf::field()`
- [ ] `php vanilla routes` shows correct route table
- [ ] Manual browser test of affected pages

See the full [Contributing Guide](docs/guides/CONTRIBUTING.md) for more details.
