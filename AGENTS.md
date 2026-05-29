# AGENTS.md — Siwayut Catering

## Project overview

Custom PHP 8.2+ MVC ("Vanilla Framework"). Catering order management: public landing page, customer auth/register, public order form + tracking, and admin dashboard (categories, events, menus, orders, users).

## Dev commands

```bash
php vanilla serve              # Dev server (default :8000)
php vanilla serve --port=8080
php vanilla db:create          # Create MySQL database from .env
php vanilla migrate            # Run PHP migrations in database/migrations/
php vanilla migrate:fresh      # Drop all tables + re-migrate
php vanilla db:seed            # Run DatabaseSeeder (Admin, Menu, Order seeders)
php vanilla db:seed --class=AdminSeeder
php vanilla routes             # List registered routes
php vanilla make:controller    # Scaffold controllers/models/services/etc.
php vanilla help

npm install                    # Tailwind CLI (first time)
npm run css:build              # Build public/assets/css/app.css
npm run css:watch              # Watch + rebuild CSS
npm run serve                  # Alias for php vanilla serve
npm run dev                    # CSS watch + PHP server (concurrently)
composer run dev               # CSS watch + PHP server (background watch)
```

## Build setup

- **Tailwind CSS v4** via `@tailwindcss/cli`
- Entry: `public/assets/css/input.css` — imports `tokens.css`, `base.css`, `utilities.css`, component CSS, `pages/landing.css`, and `@theme` tokens
- Output: `public/assets/css/app.css` (generated — do not edit)
- No `tailwind.config.js` — v4 uses `@theme` in `input.css` + split CSS files under `public/assets/css/`
- Cache busting: manual `?v=N` on asset links in layouts

## Architecture

### Directory layout

| Path | Role |
|------|------|
| `public/index.php` | Entry: `.env` → `config/app.php` → `bootstrap/app.php` → routes |
| `public/uploads` | Symlink → `storage/uploads` (served as `/uploads/...`) |
| `config/routes.php` | All routes (single closure) |
| `config/bindings.php` | DI: Model → Service → Controller |
| `src/Controllers/` | Thin HTTP layer |
| `src/Services/` | Business logic (`AuthService`, `OrderService`, `MenuService`, `AiService`, …) |
| `src/Models/` | `BaseModel` + PDO; `Order` has joins / `order_items` helpers |
| `src/Views/` | Plain PHP templates, `components/`, `layouts/`, `partials/` |
| `public/assets/js/app.js` | Bootstraps `window.AppModules.*` on `DOMContentLoaded` |
| `public/assets/js/modules/` | `modal`, `toast`, `turnstile`, `file-upload`, `progressive-image`, `load-more-menu`, `ai-description` |
| `database/migrations/*.php` | Classes extending `App\Core\BaseMigration` |
| `database/seeds/` | `AdminSeeder`, `MenuSeeder`, `OrderSeeder`, `DatabaseSeeder` |

### Key patterns

- **CSRF**: `<?= csrf_field() ?>` or `Csrf::field()` in POST forms. Middleware alias `csrf` exists but is **not** on route groups by default — tokens are present for future/opt-in enforcement.
- **Components**: `<?php component('progressive-image', ['src' => 'menus/foo.jpg', 'alt' => '...']) ?>`
- **Flash**: `Session::flash()` / redirects via `BaseController::redirectWithFlash()`
- **JSON**: `Response::jsonSuccess($data)` / `Response::jsonError($msg)`
- **Passwords**: `password_hash(Encryptor::hmac($plain), PASSWORD_DEFAULT)` — requires non-empty `APP_KEY` in `.env`
- **Admin CRUD**: Modal + `POST` on index pages (no separate `/create` or `/edit` routes)

### Layouts

1. **Landing** — `welcome.php`, layout `''`, inline/landing CSS in `pages/landing.css`
2. **Admin** — `layouts/main.php` (sidebar, navbar, toast, modal)
3. **Auth** — `layouts/auth.php` + `auth/auth.php` (login + register tabs)

### Routes (summary)

**Public**

| Method | URI | Notes |
|--------|-----|--------|
| GET | `/` | Landing, menu gallery, load-more |
| GET/POST | `/auth`, `/auth/login`, `/auth/register` | Customer/admin auth UI |
| GET | `/login` | Redirects to `/auth` |
| POST | `/login`, `/logout` | Legacy aliases |
| GET/POST | `/order-form` | Public catering order |
| GET/POST | `/track-order`, GET `/track-order/{id}` | Order lookup |
| GET | `/api/menus` | Paginated active menus (JSON) |

**Admin** (`middleware: ['auth', 'role:admin']`)

- `/users`, `/events`, `/categories` — index + POST store/update/delete + `GET /api/{resource}/{id}`
- `/menus`, `/menus/{id}` — CRUD + `POST /menus/generate-description` (AI)
- `/orders`, `/orders/{id}` — list, create (admin), show, update status

Run `php vanilla routes` for the full table (39 routes).

### Progressive images (LQIP)

- Files: `storage/uploads/{subdir}/{file}` and `.../thumbs/{file}`
- URLs: `/uploads/{subdir}/{file}` via `public/uploads` symlink
- Component: `src/Views/components/progressive-image.php`
- Module: `public/assets/js/modules/progressive-image.js`

### Turnstile (optional)

- Env: `TURNSTILE_ENABLED`, `TURNSTILE_SITE_KEY_MANAGED`, `TURNSTILE_SECRET_KEY_MANAGED`
- Verified on login, register, public order submit, track-order (`App\Core\Turnstile`)
- When disabled, submit buttons are not gated

### AI menu descriptions

- Env: `AI_API_URL`, `AI_API_KEY`, `AI_MODEL` (OpenAI-compatible)
- `MenuController::generateDescription` → `AiService` (English descriptions)
- Frontend: `public/assets/js/modules/ai-description.js`

## Database

- MySQL via PDO (`FETCH_ASSOC`, real prepares)
- Migrations: `database/migrations/{NNN}_{name}.php` → class `Database\Migrations\{PascalCase}`
- `up()` / `down()` return SQL `string` or `string[]` for multi-statement migrations
- Core tables: `users`, `categories`, `events`, `menus`, `customers` (+ `user_id`), `orders`, `order_items`
- Orders are **multi-line**: line items in `order_items` (migration 009+); `orders.menu_id` / `quantity` removed in 010
- `orders.payment_status`: `unpaid` | `paid` | `refunded`
- `BaseModel::paginate()` → `{ data, total, per_page, current_page, last_page }`

## CSS (admin + shared)

- Design tokens in `public/assets/css/tokens.css` and `@theme` in `input.css`
- Gold accent `#e58e26`, dark bg `#09090b`
- Utility/component classes: `.card`, `.btn`, `.table-wrapper`, `.form-input`, Tailwind `@apply` in `input.css`

## Gotchas

- **No automated tests** configured
- **Mutations use POST only** (no REST DELETE/PUT/PATCH routes)
- **`php vanilla serve`** uses `-t public`
- **HEAD /** may 404 on PHP built-in server — use GET for checks
- **`.env`** loaded with `parse_ini_file()` in `public/index.php`; helpers: `env()`, `config()`
- **No route model binding** — `find($id)` in controllers/services
- **Set `APP_KEY`** before login/seed — `Encryptor::hmac()` throws if empty
- **Upload symlink** `public/uploads` → `../storage/uploads` must exist for image URLs
- **CSRF middleware** not applied globally — forms still emit tokens

## Documentation

Full framework + app docs: [docs/README.md](docs/README.md) (EN) · [docs/id/README.md](docs/id/README.md) (ID)
