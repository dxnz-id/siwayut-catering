# Quick Start

> Build a minimal feature end-to-end in 5 minutes.

**Prerequisites**: Complete [INSTALLATION.md](INSTALLATION.md) first.

## Goal

Add a new page at `/dashboard` that displays a greeting to the logged-in user.

## Step 1: Define a Route

Edit `config/routes.php` — add inside the `auth` middleware group:

```php
// config/routes.php — inside the $router->group(['middleware' => ['auth', ...]], ...) block
$router->get('/dashboard', [\App\Controllers\DashboardController::class, 'index']);
```

## Step 2: Create a Controller

Create `src/Controllers/DashboardController.php`:

```php
<?php
declare(strict_types=1);

namespace App\Controllers;
use App\Core\Request;

class DashboardController extends BaseController {
    public function __construct() {
        parent::__construct();
    }

    public function index(Request $request): void {
        $user = $this->currentUser();
        $this->render('dashboard/index', [
            'title' => 'Dashboard',
            'currentUser' => $user,
        ]);
    }
}
```

Or scaffold it:

```bash
php vanilla make:controller Dashboard
```

## Step 3: Create a View Template

Create `src/Views/dashboard/index.php`:

```php
<div class="content-header">
    <h1 class="content-title">Dashboard</h1>
</div>

<div class="card">
    <div class="card-body">
        <h2>Welcome, <?= \App\Core\View::e($currentUser['name']) ?>!</h2>
        <p>You are logged in as <strong><?= \App\Core\View::e($currentUser['role']) ?></strong>.</p>
    </div>
</div>
```

## Step 4: Register Container Binding

Edit `config/bindings.php` — add:

```php
$container->bind(\App\Controllers\DashboardController::class, function ($c) {
    return new \App\Controllers\DashboardController();
});
```

## Step 5: Test in Browser

1. Start the server: `php vanilla serve`
2. Login at `http://localhost:8000/login`
3. Navigate to `http://localhost:8000/dashboard`
4. You should see: **"Welcome, Administrator!"**

## What's Next

| Topic | Document |
|-------|----------|
| How the request lifecycle works | [ARCHITECTURE.md](../core/ARCHITECTURE.md) |
| Route parameters and groups | [ROUTING.md](../core/ROUTING.md) |
| Template rendering and layouts | [VIEWS.md](../frontend/VIEWS.md) |
| Container auto-wiring | [CONTAINER.md](../core/CONTAINER.md) |
| All CLI commands | Run `php vanilla help` |

## Full File Listing

Files created or modified in this guide:

| Action | File |
|--------|------|
| Modified | `config/routes.php` |
| Modified | `config/bindings.php` |
| Created | `src/Controllers/DashboardController.php` |
| Created | `src/Views/dashboard/index.php` |

---

Next: [ARCHITECTURE.md](../core/ARCHITECTURE.md)
