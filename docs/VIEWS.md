# Views

## Overview

The view system uses native PHP files as templates — no template engine. Output escaping is manual via `View::e()`.

## View Class API

### `__construct(string $viewsPath)`

Sets the base directory for template resolution.

```php
$view = new View(BASE_PATH . '/src/Views');
```

> Controllers get this automatically via `BaseController::__construct()`.

### `render(string $template, array $data = [], string $layout = 'main'): void`

Render a template inside a layout. The template output is captured and injected as `$content` into the layout.

```php
$this->render('user/index', ['users' => $users], 'main');
```

- `$template` — path relative to Views dir, without `.php` → `src/Views/user/index.php`
- `$data` — associative array, keys become template variables via `extract()`
- `$layout` — layout name → `src/Views/layouts/{layout}.php`. Pass `''` for no layout.

### `partial(string $template, array $data = []): string`

Render a partial and return the HTML string (not output directly).

```php
$html = $this->view->partial('partials/flash', ['success' => $msg]);
```

### `static e(mixed $value): string`

Escape a value for safe HTML output.

```php
<?= \App\Core\View::e($user['name']) ?>
```

Contract: `htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8')`

## Template Path Resolution

```
Templates:   src/Views/{template}.php
Layouts:     src/Views/layouts/{layout}.php
Partials:    src/Views/partials/{name}.php (by convention)
```

## Layout System

### How `$content` is injected

1. `render()` captures template output via `ob_start()` / `ob_get_clean()`
2. Template output is stored in `$content`
3. Layout file is included — `$content` is available in layout scope
4. All `$data` keys are also available in layout scope via `extract()`

### Layout file example

```php
<!-- src/Views/layouts/main.php -->
<!DOCTYPE html>
<html>
<head>
    <title><?= e($title ?? '') ?> — <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
    <div class="app-layout">
        <?php require __DIR__ . '/../partials/sidebar.php'; ?>
        <div class="main-wrapper">
            <?php require __DIR__ . '/../partials/navbar.php'; ?>
            <main class="content">
                <?php require __DIR__ . '/../partials/flash.php'; ?>
                <?= $content ?>
            </main>
        </div>
    </div>
    <script src="/assets/js/app.js"></script>
</body>
</html>
```

### Available layouts

| Layout | File | Purpose |
|--------|------|---------|
| `main` | `src/Views/layouts/main.php` | Admin panel with sidebar + navbar |
| `auth` | `src/Views/layouts/auth.php` | Centered card on gradient background |

### No layout

Pass an empty string to render without a layout:

```php
$this->render('raw-page', $data, '');
```

## Partials

Partials are reusable template fragments in `src/Views/partials/`:

| Partial | Purpose |
|---------|---------|
| `sidebar.php` | Navigation sidebar with links and logout form |
| `navbar.php` | Top bar with page title and user info |
| `flash.php` | Success/error alert messages |

Include from layouts:

```php
<?php require __DIR__ . '/../partials/flash.php'; ?>
```

## Output Escaping

**Every** user-provided value MUST be escaped in templates:

```php
<!-- CORRECT -->
<?= \App\Core\View::e($user['name']) ?>
<?= e($user['name']) ?>

<!-- WRONG — XSS vulnerability -->
<?= $user['name'] ?>
```

The `e()` helper function delegates to `View::e()`.

## Directory Structure

```
src/Views/
├── auth/
│   └── login.php          # Login form
├── errors/
│   ├── 404.php            # Not found page
│   └── 500.php            # Server error page
├── layouts/
│   ├── auth.php           # Auth layout
│   └── main.php           # Admin layout
├── partials/
│   ├── flash.php          # Flash alerts
│   ├── navbar.php         # Top navigation
│   └── sidebar.php        # Side navigation
├── user/
│   ├── create.php         # User create form
│   ├── edit.php           # User edit form
│   └── index.php          # User listing
└── welcome.php            # Welcome page
```

## BaseController Integration

Controllers extend `BaseController` which provides `$this->render()`:

```php
class UserController extends BaseController {
    public function index(Request $request): void {
        $this->render('user/index', [
            'title' => 'Users',
            'users' => $users,
        ]);
    }
}
```

## Gotchas

- **`$content` variable name**: Do not pass a `content` key in `$data` — it will be overwritten by the template output.
- **`extract()` overwrites**: All `$data` keys become variables. Avoid keys that collide with PHP globals or layout variables.
- **Error templates**: Default error messages in `src/Views/errors/` are in **Indonesian**.

---

See: [ARCHITECTURE.md](ARCHITECTURE.md) · [SECURITY.md](SECURITY.md)
