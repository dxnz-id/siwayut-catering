# Views

## Overview

Sistem view menggunakan file PHP asli sebagai template — tanpa template engine. Output escaping dilakukan secara manual melalui `View::e()`.

## View Class API

### `__construct(string $viewsPath)`

Mengatur direktori dasar untuk resolusi template.

```php
$view = new View(BASE_PATH . '/src/Views');
```

> Controller mendapatkan ini secara otomatis melalui `BaseController::__construct()`.

### `render(string $template, array $data = [], string $layout = 'main'): void`

Merender template di dalam sebuah layout. Output template ditangkap dan disuntikkan sebagai `$content` ke dalam layout.

```php
$this->render('user/index', ['users' => $users], 'main');
```

- `$template` — path relatif terhadap direktori Views, tanpa `.php` → `src/Views/user/index.php`
- `$data` — associative array, key akan menjadi variabel template melalui `extract()`
- `$layout` — nama layout → `src/Views/layouts/{layout}.php`. Berikan `''` jika tidak menggunakan layout.

### `partial(string $template, array $data = []): string`

Merender partial dan mengembalikan string HTML (tidak langsung di-output).

```php
$html = $this->view->partial('partials/flash', ['success' => $msg]);
```

### `static e(mixed $value): string`

Melakukan escape pada nilai untuk output HTML yang aman.

```php
<?= \App\Core\View::e($user['name']) ?>
```

Kontrak: `htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8')`

## Template Path Resolution

```
Templates:   src/Views/{template}.php
Layouts:     src/Views/layouts/{layout}.php
Partials:    src/Views/partials/{name}.php (berdasarkan konvensi)
```

## Layout System

### Bagaimana `$content` disuntikkan

1. `render()` menangkap output template melalui `ob_start()` / `ob_get_clean()`
2. Output template disimpan dalam `$content`
3. File layout di-include — `$content` tersedia dalam scope layout
4. Semua key `$data` juga tersedia dalam scope layout melalui `extract()`

### Contoh file layout

```php
<!-- src/Views/layouts/main.php -->
<!DOCTYPE html>
<html>
<head>
    <title><?= e($title ?? '') ?> — <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="icon" type="image/svg+xml" href="/assets/icon/favicon.svg">
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

### Layout yang tersedia

| Layout | File                         | Tujuan                               |
| ------ | ---------------------------- | ------------------------------------ |
| `main` | `src/Views/layouts/main.php` | Panel admin dengan sidebar + navbar  |
| `auth` | `src/Views/layouts/auth.php` | Kartu terpusat pada latar belakang gradien |

### Tanpa layout

Berikan string kosong untuk merender tanpa layout:

```php
$this->render('raw-page', $data, '');
```

## Partials

Partial adalah fragmen template yang dapat digunakan kembali di `src/Views/partials/`:

| Partial       | Tujuan                                        |
| ------------- | --------------------------------------------- |
| `sidebar.php` | Sidebar navigasi dengan link dan form logout  |
| `navbar.php`  | Bar atas dengan judul halaman dan info user   |
| `flash.php`   | Pesan peringatan sukses/error                 |

Include dari layout:

```php
<?php require __DIR__ . '/../partials/flash.php'; ?>
```

## Output Escaping

**Setiap** nilai yang disediakan pengguna WAJIB di-escape di dalam template:

```php
<!-- BENAR -->
<?= \App\Core\View::e($user['name']) ?>
<?= e($user['name']) ?>

<!-- SALAH — Kerentanan XSS -->
<?= $user['name'] ?>
```

Fungsi helper `e()` mendelegasikan ke `View::e()`.

## Struktur Direktori

```
src/Views/
├── auth/
│   └── auth.php          # Form login
├── errors/
│   ├── 404.php            # Halaman tidak ditemukan
│   └── 500.php            # Halaman error server
├── layouts/
│   ├── auth.php           # Layout autentikasi
│   └── main.php           # Layout admin
├── partials/
│   ├── flash.php          # Alert flash
│   ├── navbar.php         # Navigasi atas
│   └── sidebar.php        # Navigasi samping
├── user/
│   ├── create.php         # Form buat user
│   ├── edit.php           # Form edit user
│   └── index.php          # Daftar user
└── welcome.php            # Halaman selamat datang
```

## Integrasi BaseController

Controller melakukan extend terhadap `BaseController` yang menyediakan `$this->render()`:

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

## Catatan Penting (Gotchas)

- **Nama variabel `$content`**: Jangan berikan key `content` di dalam `$data` — key tersebut akan ditimpa oleh output template.
- **`extract()` menimpa**: Semua key `$data` menjadi variabel. Hindari penggunaan key yang bertabrakan dengan variabel global PHP atau variabel layout.
- **Template error**: Pesan error default di `src/Views/errors/` menggunakan bahasa **Indonesia**.

---

Lihat: [ARCHITECTURE.md](../core/ARCHITECTURE.md) · [SECURITY.md](../security/SECURITY.md)