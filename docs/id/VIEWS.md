# Tampilan (Views)

## Gambaran Umum

Sistem tampilan menggunakan file PHP asli (native) sebagai templat — tanpa mesin templat (template engine) pihak ketiga. Escaping output dilakukan secara manual via `View::e()`.

## API Kelas View

### `__construct(string $viewsPath)`

Mengatur direktori dasar untuk resolusi templat.

```php
$view = new View(BASE_PATH . '/src/Views');
```

> Controller mendapatkan hal ini secara otomatis melalui `BaseController::__construct()`.

### `render(string $template, array $data = [], string $layout = 'main'): void`

Merender templat di dalam sebuah tata letak (layout). Output templat ditangkap dan disisipkan sebagai variabel `$content` ke dalam tata letak.

```php
$this->render('user/index', ['users' => $users], 'main');
```

- `$template` — jalur relatif terhadap direktori Views, tanpa akhiran `.php` → `src/Views/user/index.php`
- `$data` — array asosiatif, di mana kunci array diubah menjadi variabel templat melalui `extract()`
- `$layout` — nama tata letak → `src/Views/layouts/{layout}.php`. Lewatkan string kosong `''` jika tidak ingin menggunakan tata letak.

### `partial(string $template, array $data = []): string`

Merender templat parsial (partial) dan mengembalikan string HTML (tidak langsung dikirim ke browser).

```php
$html = $this->view->partial('partials/flash', ['success' => $msg]);
```

### `static e(mixed $value): string`

Melakukan escape pada suatu nilai agar aman saat di-output ke HTML.

```php
<?= \App\Core\View::e($user['name']) ?>
```

Kontrak: `htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8')`

## Resolusi Jalur Templat

```
Templat:     src/Views/{template}.php
Tata Letak:  src/Views/layouts/{layout}.php
Parsial:     src/Views/partials/{name}.php (berdasarkan konvensi)
```

## Sistem Tata Letak (Layout System)

### Bagaimana `$content` disisipkan

1. `render()` menangkap output templat melalui fungsi penampung output `ob_start()` / `ob_get_clean()`
2. Output templat disimpan ke dalam variabel `$content`
3. Berkas tata letak disertakan (included) — variabel `$content` tersedia di dalam cakupan tata letak
4. Semua kunci di dalam array `$data` juga tersedia di dalam cakupan tata letak melalui `extract()`

### Contoh berkas tata letak (layout)

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

### Tata letak yang tersedia

| Tata Letak | Berkas | Tujuan |
|------------|--------|--------|
| `main` | `src/Views/layouts/main.php` | Panel admin dengan sidebar + navbar |
| `auth` | `src/Views/layouts/auth.php` | Kartu yang berada di tengah dengan latar belakang gradasi |

### Tanpa tata letak

Kirimkan string kosong untuk merender tanpa tata letak:

```php
$this->render('raw-page', $data, '');
```

## Parsial (Partials)

Parsial adalah fragmen templat yang dapat digunakan kembali di `src/Views/partials/`:

| Parsial | Tujuan |
|---------|--------|
| `sidebar.php` | Sidebar navigasi yang berisi tautan dan formulir logout |
| `navbar.php` | Batang atas (top bar) dengan judul halaman dan info pengguna |
| `flash.php` | Pesan peringatan (alert) berhasil/gagal |

Cara menyertakan (include) dari tata letak:

```php
<?php require __DIR__ . '/../partials/flash.php'; ?>
```

## Output Escaping

**Setiap** nilai yang disediakan oleh pengguna WAJIB di-escape di dalam templat:

```php
<!-- BENAR -->
<?= \App\Core\View::e($user['name']) ?>
<?= e($user['name']) ?>

<!-- SALAH — Kerentanan terhadap XSS -->
<?= $user['name'] ?>
```

Fungsi pembantu `e()` mendelegasikan tugasnya ke `View::e()`.

## Struktur Direktori

```
src/Views/
├── auth/
│   └── login.php          # Formulir login
├── errors/
│   ├── 404.php            # Halaman tidak ditemukan
│   └── 500.php            # Halaman kesalahan server
├── layouts/
│   ├── auth.php           # Tata letak Auth
│   └── main.php           # Tata letak Admin
├── partials/
│   ├── flash.php          # Peringatan flash (Flash alerts)
│   ├── navbar.php         # Navigasi atas
│   └── sidebar.php        # Navigasi samping
├── user/
│   ├── create.php         # Formulir pembuatan pengguna
│   ├── edit.php           # Formulir penyuntingan pengguna
│   └── index.php          # Daftar pengguna
└── welcome.php            # Halaman selamat datang (Welcome page)
```

## Integrasi BaseController

Controller mewarisi kelas `BaseController` yang menyediakan metode `$this->render()`:

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

## Hal yang Perlu Diperhatikan (Gotchas)

- **Nama variabel `$content`**: Jangan mengirimkan kunci `content` di dalam array `$data` — karena nilainya akan ditimpa oleh output templat yang sesungguhnya.
- **Peleburan oleh `extract()`**: Semua kunci di dalam array `$data` akan diubah menjadi variabel tersendiri. Hindari penggunaan kunci yang berbenturan dengan variabel global PHP atau variabel tata letak.
- **Templat kesalahan (Error templates)**: Pesan kesalahan bawaan dalam `src/Views/errors/` ditulis dalam **Bahasa Indonesia**.

---

Lihat: [ARCHITECTURE.md](ARCHITECTURE.md) · [SECURITY.md](SECURITY.md)
