# Mulai Cepat (Quick Start)

> Buat fitur minimal dari ujung ke ujung dalam 5 menit.

**Prasyarat**: Selesaikan [INSTALLATION.md](INSTALLATION.md) terlebih dahulu.

## Tujuan

Menambahkan halaman baru di `/dashboard` yang menampilkan sapaan kepada pengguna yang sedang login.

## Langkah 1: Definisikan Rute

Edit `config/routes.php` — tambahkan di dalam grup middleware `auth`:

```php
// config/routes.php — di dalam blok $router->group(['middleware' => ['auth', ...]], ...)
$router->get('/dashboard', [\App\Controllers\DashboardController::class, 'index']);
```

## Langkah 2: Buat Controller

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

Atau gunakan perintah scaffold (CLI):

```bash
php vanilla make:controller Dashboard
```

## Langkah 3: Buat Template View

Buat `src/Views/dashboard/index.php`:

```php
<div class="content-header">
    <h1 class="content-title">Dashboard</h1>
</div>

<div class="card">
    <div class="card-body">
        <h2>Selamat datang, <?= \App\Core\View::e($currentUser['name']) ?>!</h2>
        <p>Anda masuk sebagai <strong><?= \App\Core\View::e($currentUser['role']) ?></strong>.</p>
    </div>
</div>
```

## Langkah 4: Daftarkan Binding Container

Edit `config/bindings.php` — tambahkan:

```php
$container->bind(\App\Controllers\DashboardController::class, function ($c) {
    return new \App\Controllers\DashboardController();
});
```

## Langkah 5: Uji di Browser

1. Jalankan server: `php vanilla serve`
2. Login di `http://localhost:8000/login`
3. Navigasi ke `http://localhost:8000/dashboard`
4. Anda akan melihat: **"Selamat datang, Administrator!"**

## Apa Selanjutnya?

| Topik | Dokumen |
|-------|----------|
| Bagaimana siklus hidup request bekerja | [ARCHITECTURE.md](ARCHITECTURE.md) |
| Parameter rute dan grup | [ROUTING.md](ROUTING.md) |
| Rendering templat dan layout | [VIEWS.md](VIEWS.md) |
| Auto-wiring pada Container | [CONTAINER.md](CONTAINER.md) |
| Semua perintah CLI | Jalankan `php vanilla help` |

## Daftar Lengkap File

File yang dibuat atau diubah dalam panduan ini:

| Tindakan | File |
|--------|------|
| Diubah | `config/routes.php` |
| Diubah | `config/bindings.php` |
| Dibuat | `src/Controllers/DashboardController.php` |
| Dibuat | `src/Views/dashboard/index.php` |

---

Selanjutnya: [ARCHITECTURE.md](ARCHITECTURE.md)
