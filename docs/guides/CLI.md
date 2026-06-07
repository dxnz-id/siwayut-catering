# Vanilla PHP CLI Commands

Siwayut Catering includes a custom CLI tool (`php vanilla`) to handle common development and deployment tasks without relying on external frameworks like Artisan or Symfony Console.

To see all available commands, run:
```bash
php vanilla
```

---

## Server Commands

### `serve`
Starts the built-in PHP development server.
- **Usage:** `php vanilla serve [--port=8000]`
- **Description:** Routes all requests through `public/index.php`.
- **Note:** Do not use this in production. Use a proper web server like FrankenPHP, Nginx, or Apache.

---

## Database Commands

### `migrate`
Runs all pending SQL migration files.
- **Usage:** `php vanilla migrate`
- **Description:** Reads files from `database/migrations/`, checks the `migrations` table to see which ones have already run, and executes the pending ones in alphabetical order.

### `migrate:fresh`
Drops all tables and re-runs all migrations.
- **Usage:** `php vanilla migrate:fresh`
- **Description:** Extremely destructive. Use only in development to reset the database state.

### `db:create`
Creates the database schema if it doesn't exist.
- **Usage:** `php vanilla db:create`
- **Description:** Uses the `DB_DATABASE` variable from `.env`. Useful during initial setup before running migrations.

### `db:seed`
Populates the database with default or dummy data.
- **Usage:** `php vanilla db:seed`
- **Description:** Runs the classes defined in `database/seeders/` (e.g., `AdminSeeder`, `MenuSeeder`, `OrderSeeder`). Useful for setting up the initial admin account.

---

## Generator Commands (Make)

These commands scaffold new files to speed up development and ensure consistency.

### `make:controller`
Creates a new Controller class.
- **Usage:** `php vanilla make:controller NameController`
- **Description:** Generates a file in `src/Controllers/` extending `BaseController`.

### `make:model`
Creates a new Model class.
- **Usage:** `php vanilla make:model Name`
- **Description:** Generates a file in `src/Models/` extending `BaseModel`.

### `make:service`
Creates a new Service class.
- **Usage:** `php vanilla make:service NameService`
- **Description:** Generates a file in `src/Services/`.

---

## Routing Commands

### `routes`
Lists all registered application routes.
- **Usage:** `php vanilla routes`
- **Description:** Prints a formatted table showing the HTTP Method, Route Path, Controller Action, and applied Middlewares. Extremely useful for debugging routing issues.
