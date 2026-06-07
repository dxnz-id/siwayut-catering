# Contributing

## Project Structure

```
siwayut-catering/
├── bootstrap/app.php      # Exception handler, session, container
├── config/                 # App config, DB config, bindings, routes
├── database/               # Migrations (.sql), seeders (.php)
├── docs/                   # Documentation (you are here)
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

## Getting Started

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

## Git Branching Strategy

We follow a simplified GitHub Flow model:
1. `main` is always deployable.
2. Branch off `main` for all features/fixes: `feature/name-of-feature` or `bugfix/issue-description`.
3. Open a Pull Request (PR) against `main`.

---

## Coding Standards

### Required

- `declare(strict_types=1);` on all PHP class files
- Return types on all methods
- File header comment: `// File: path/to/file.php`
- PDO prepared statements for all SQL
- `View::e()` escaping for all output

### Naming

| Item | Convention |
|------|-----------|
| Controllers | PascalCase + `Controller` suffix |
| Models | PascalCase, singular |
| Services | PascalCase + `Service` suffix |
| Middleware | PascalCase + `Middleware` suffix |
| Exceptions | PascalCase + `Exception` suffix |
| Database tables | plural, snake_case |
| Migrations | `NNN_description.sql` |
| Views | `feature/action.php` |

See [CONVENTIONS.md](CONVENTIONS.md) for full details.

## Adding a Feature

1. **Migration**: `php vanilla make:migration create_x_table` → write SQL → `php vanilla migrate`
2. **Model**: `php vanilla make:model X` → set `$table`, add custom finders
3. **Service**: `php vanilla make:service X` → inject model, add business logic
4. **Controller**: `php vanilla make:controller X` → inject service, add actions
5. **Binding**: Register factory in `config/bindings.php`
6. **Routes**: Register endpoints in `config/routes.php`
7. **Views**: Create templates in `src/Views/x/`

Full example: [EXAMPLES.md](EXAMPLES.md)

## Testing & Code Review Checklist

Before submitting a Pull Request, ensure you pass the following:

- [ ] `php -l` passes on all modified files (no syntax errors)
- [ ] `grep -r 'TODO: implement'` shows no unimplemented stubs
- [ ] All new PHP classes have `declare(strict_types=1);`
- [ ] All user-visible output uses `View::e()` escaping
- [ ] All SQL uses prepared statements with parameter binding
- [ ] Forms include CSRF token via `Csrf::field()`
- [ ] `php vanilla routes` shows correct route table
- [ ] Manual browser test of affected pages

### Pull Request Template

Use this format for your PR description:

```markdown
## Description
Describe what this PR does and why it is needed.

## Related Issue
Fixes #123

## Verification
- [ ] I have manually tested the workflow in the browser.
- [ ] I have verified database operations using `php vanilla tinker`.
```

## Documentation

When adding a component, update the relevant doc in `docs/`:

| Component | Document |
|-----------|----------|
| Routes | [ROUTING.md](../core/ROUTING.md) |
| Middleware | [MIDDLEWARE.md](../core/MIDDLEWARE.md) |
| Models / DB | [DATABASE.md](../database/DATABASE.md) |
| Validation rules | [VALIDATION.md](../backend/VALIDATION.md) |
| Views / Layouts | [VIEWS.md](../frontend/VIEWS.md) |
| Exceptions | [ERROR_HANDLING.md](../security/ERROR_HANDLING.md) |
| Security measures | [SECURITY.md](../security/SECURITY.md) |

---

See: [CONVENTIONS.md](CONVENTIONS.md) · [EXAMPLES.md](EXAMPLES.md) · [ARCHITECTURE.md](../core/ARCHITECTURE.md)
