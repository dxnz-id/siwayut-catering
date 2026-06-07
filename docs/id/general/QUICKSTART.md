# Memulai Cepat

> Bangun fitur minimal secara end-to-end dalam 5 menit.

**Prasyarat**: Selesaikan [INSTALLATION.md](INSTALLATION.md) terlebih dahulu.

## Tujuan

Menambahkan halaman baru di `/dashboard` yang menampilkan salam kepada pengguna yang telah masuk (logged-in).

## Langkah 1: Mendefinisikan Route

Edit `config/routes.php` — tambahkan di dalam grup middleware `auth`:

```php
// config/routes.php — di dalam blok $router->group(['middleware' => ['auth', ...]], ...)
$router->get('/dashboard', [\App\Controllers\DashboardController::class, 'index']);
```

## Langkah 2: Membuat Controller

Buat `src/Controllers/DashboardController.php`:

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

Atau buat secara otomatis (scaffold):

```bash
php vanilla make:controller Dashboard
```

## Langkah 3: Membuat View Template

Buat `src/Views/dashboard/index.php`:

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

## Langkah 4: Mendaftarkan Container Binding

Edit `config/bindings.php` — tambahkan:

```php
$container->bind(\App\Controllers\DashboardController::class, function ($c) {
    return new \App\Controllers\DashboardController();
});
```

## Langkah 5: Menguji di Browser

1. Jalankan server: `php vanilla serve`
2. Login di `http://localhost:8000/login`
3. Navigasikan ke `http://localhost:8000/dashboard`
4. Anda seharusnya melihat: **"Welcome, Administrator!"**

## Langkah Selanjutnya

| Topik | Dokumen |
|-------|----------|
| Cara kerja siklus hidup request | [ARCHITECTURE.md](../core/ARCHITECTURE.md) |
| Route parameters dan groups | [ROUTING.md](../core/ROUTING.md) |
| Template rendering dan layouts | [VIEWS.md](../frontend/VIEWS.md) |
| Container auto-wiring | [CONTAINER.md](../core/CONTAINER.md) |
| Semua perintah CLI | Jalankan `php vanilla help` |

## Daftar File Lengkap

File yang dibuat atau dimodifikasi dalam panduan ini:

| Aksi | File |
|--------|------|
| Dimodifikasi | `config/routes.php` |
| Dimodifikasi | `config/bindings.php` |
| Dibuat | `src/Controllers/DashboardController.php` |
| Dibuat | `src/Views/dashboard/index.php` |

---

Selanjutnya: [ARCHITECTURE.md](../core/ARCHITECTURE.md)