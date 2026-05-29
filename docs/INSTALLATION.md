# Installation

## Prerequisites

| Requirement | Version |
|-------------|---------|
| PHP | ^8.2 |
| Composer | ^2.0 |
| MySQL / MariaDB | ^8.0 / ^10.6 |
| PHP Extensions | `pdo`, `pdo_mysql`, `mbstring` |

Verify prerequisites:

```bash
php -v                          # Must show 8.2+
composer -V                     # Must show 2.x
php -m | grep -E 'pdo|mbstring' # Must list pdo, pdo_mysql, mbstring
mysql --version                 # Must show 8.0+ or MariaDB 10.6+
```

## Clone & Install

```bash
git clone <repository-url> siwayut-catering
cd siwayut-catering
composer install
```

## Environment Configuration

```bash
cp .env.example .env
```

Edit `.env` with your settings:

| Variable | Description | Default |
|----------|-------------|---------|
| `APP_NAME` | Application display name | `My App` |
| `APP_ENV` | Environment (`local`, `production`) | `local` |
| `APP_DEBUG` | Show detailed errors (`true`/`false`) | `true` |
| `APP_TIMEZONE` | PHP timezone | `Asia/Jakarta` |
| `APP_URL` | Base URL for asset/url helpers | `http://localhost` |
| `DB_DRIVER` | PDO driver | `mysql` |
| `DB_HOST` | Database host | `127.0.0.1` |
| `DB_PORT` | Database port | `3306` |
| `DB_DATABASE` | Database name | `myapp` |
| `DB_USERNAME` | Database username | `root` |
| `DB_PASSWORD` | Database password | *(empty)* |
| `AI_API_URL` | OpenAI-compatible API base URL (e.g. `https://generativelanguage.googleapis.com/v1beta/openai/`) | *(empty)* |
| `AI_API_KEY` | API key for the AI provider | *(empty)* |
| `AI_MODEL` | Model name (e.g. `gemini-2.0-flash`, `gpt-4o-mini`) | *(empty)* |

> **Important**: Set `APP_DEBUG=false` in production to prevent exposing stack traces.

## Database Setup

Use the `vanilla` CLI to create the database, run migrations, and seed:

```bash
php vanilla db:create
php vanilla migrate
php vanilla db:seed --class=AdminSeeder
```

Expected output:

```
  DONE    Database 'myapp' created successfully.
  DONE    Migrated:  001_create_users_table.sql
  DONE    Ran 1 migration(s).
  DONE    Database seeding completed.
```

The seeder creates a default admin account:
- **Email**: `admin@admin.com`
- **Password**: `password`

## Start Development Server

```bash
php vanilla serve
```

Or use Composer:

```bash
composer run dev
```

Both start PHP's built-in server at `http://localhost:8000`.

Expected output:

```
  Vanilla Framework v1.0.0 (PHP 8.x.x)
  INFO    Starting development server on http://localhost:8000
```

## Verify Installation

1. Open `http://localhost:8000` — you should see the welcome page
2. Open `http://localhost:8000/login` — you should see the login form
3. Login with `admin@admin.com` / `password` — you should be redirected to the users page

## Troubleshooting

| Problem | Solution |
|---------|----------|
| `Unknown database 'myapp'` | Run `php vanilla db:create` |
| `Table 'myapp.users' doesn't exist` | Run `php vanilla migrate` |
| `Class not found` | Run `composer dump-autoload` |
| `pdo_mysql not found` | Install: `sudo apt install php-mysql` |
| Port 8000 in use | Use `php vanilla serve --port=8080` |

---

Next: [QUICKSTART.md](QUICKSTART.md)
