# Keamanan (Security)

## Matriks Pertahanan (Defense Matrix)

| Vektor Serangan | Mitigasi | Implementasi |
|-----------------|----------|--------------|
| **SQL Injection** | Prepared statements | `PDO::EMULATE_PREPARES = false`, semua kueri menggunakan placeholder `?` |
| **XSS** | Output escaping | `View::e()` → `htmlspecialchars(ENT_QUOTES, UTF-8)` |
| **CSRF** | Verifikasi token | `Csrf::token()` + `Csrf::verify()` via `CsrfMiddleware` |
| **Session Hijacking** | Regenerasi sesi | `Session::regenerate()` saat login |
| **Password Cracking** | Hashing Bcrypt | `password_hash(PASSWORD_DEFAULT)` / `password_verify()` |

## Perlindungan CSRF

### Pembuatan Token

```php
use App\Core\Csrf;

$token = Csrf::token();   // Mengembalikan token yang sudah ada atau membuat token baru
```

Kontrak: `bin2hex(random_bytes(32))` — 64 karakter heksadesimal disimpan di dalam `$_SESSION['_csrf_token']`.

### Verifikasi Token

```php
Csrf::verify(string $token): bool
```

Menggunakan `hash_equals()` untuk perbandingan waktu-konstan (constant-time comparison) — mencegah timing attacks.

### Pembantu Bidang Tersembunyi (Hidden Field Helper)

```php
Csrf::field(): string
```

Mengembalikan: `<input type="hidden" name="_csrf_token" value="...">`.

Gunakan di dalam formulir HTML:

```html
<form method="POST" action="/users">
    <?= \App\Core\Csrf::field() ?>
    <!-- kolom formulir -->
</form>
```

### Penegakan Middleware

`CsrfMiddleware` secara otomatis menerapkan verifikasi CSRF pada semua permintaan non-GET:

```php
// Hanya diterapkan ketika alias middleware 'csrf' ada di grup rute
$router->group(['middleware' => ['csrf']], function ($router) {
    $router->post('/submit', ...);
});
```

Ketika token hilang atau tidak cocok: `HttpException(419, 'Page Expired — CSRF token mismatch.')`.

### Regenerasi Token

```php
Csrf::regenerate(): string  // buat token baru, lalu kembalikan
```

Dipanggil setelah login berhasil untuk mencegah serangan fiksasi sesi (session fixation attacks).

## Perlindungan SQL Injection

Semua kueri database menggunakan prepared statement PDO dengan pengikatan parameter (parameter binding):

```php
// BaseModel::query() dan BaseModel::execute()
$stmt = $this->db()->prepare($sql);
$stmt->execute($bindings);
```

Opsi PDO `EMULATE_PREPARES = false` memastikan prepared statement yang **sebenarnya** berjalan di sisi server database.

**Jangan pernah** menggabungkan input pengguna secara langsung ke dalam SQL:

```php
// SALAH — Rentan terhadap SQL injection
$db->query("SELECT * FROM users WHERE id = " . $_GET['id']);

// BENAR
$model->find((int) $request->param('id'));
```

## Perlindungan XSS

Semua output yang dikirimkan ke peramban (browser) harus di-escape:

```php
<?= \App\Core\View::e($user['name']) ?>
<?= e($user['name']) ?>        // helper pintasan
```

Kontrak: `htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8')`.

## Penanganan Kata Sandi

| Operasi | Fungsi | Digunakan Di |
|---------|--------|--------------|
| Hash saat buat/edit | `password_hash($password, PASSWORD_DEFAULT)` | `UserService::create()`, `UserService::update()` |
| Verifikasi saat login | `password_verify($input, $hash)` | `AuthService::login()` |

Faktor biaya Bcrypt: default PHP (saat ini bernilai 10).

## Keamanan Sesi (Session Security)

| Langkah Keamanan | Implementasi |
|------------------|--------------|
| Regenerasi sesi saat login | `Session::regenerate(true)` — menghapus sesi lama |
| Penghapusan sesi saat logout | `Session::destroy()` — membersihkan semua data sesi |
| HTTP-only cookies | Default PHP `session.cookie_httponly` |

## Alur Autentikasi (Auth Flow)

```
Formulir Login (GET /login)
     │
     ▼
POST /login (email + password + _csrf_token)
     │
     ├── CsrfMiddleware::handle() → verifikasi token
     │
     ├── AuthController::login()
     │     ├── Validasi input (email wajib, kata sandi wajib)
     │     ├── AuthService::login(email, password)
     │     │     ├── User::findByEmail(email)
     │     │     ├── password_verify(input, hash)
     │     │     │     ├── LOLOS → Session::regenerate()
     │     │     │     │           Session::set('user', dataPengguna)
     │     │     │     │           return true
     │     │     │     └── GAGAL → return false
     │     ├── Berhasil → redirect /users
     │     └── Gagal → flash error → redirect /login
     │
     ▼
Sesi Terautentikasi (Authenticated Session)
     │
     ├── AuthMiddleware::handle()
     │     └── Session::has('user') → izinkan/tolak
     │
     ├── RoleMiddleware::handle()
     │     └── Pemeriksaan Session::get('user')['role']
     │
     ▼
POST /logout
     ├── Session::destroy()
     └── redirect /login
```

## Data Sesi yang Disimpan

Setelah login berhasil, `$_SESSION['user']` berisi data berikut:

```php
[
    'id'    => int,
    'name'  => string,
    'email' => string,
    'role'  => string,    // 'admin' atau 'user'
]
```

> Hash kata sandi **tidak pernah** disimpan di dalam sesi.

## Daftar Periksa Produksi (Production Checklist)

| Pengaturan | Nilai | Berkas |
|------------|-------|--------|
| `APP_DEBUG` | `false` | `.env` |
| `APP_ENV` | `production` | `.env` |
| Sandi DB yang kuat | Diatur | `.env` |
| Pemaksaan HTTPS | Konfigurasi di web server | konfigurasi nginx/Apache |
| Cookie sesi aman | `session.cookie_secure = 1` | `php.ini` |
| Izin Berkas | `storage/` dapat ditulis, `src/` hanya-baca | Konfigurasi Server |

---

Lihat: [MIDDLEWARE.md](MIDDLEWARE.md) · [ERROR_HANDLING.md](ERROR_HANDLING.md) · [CONVENTIONS.md](CONVENTIONS.md)
