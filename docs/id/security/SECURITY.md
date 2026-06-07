# Keamanan

## Matriks Pertahanan

| Vektor Serangan | Mitigasi | Implementasi |
|--------------|------------|----------------|
| **SQL Injection** | Prepared statements | `PDO::EMULATE_PREPARES = false`, semua query menggunakan placeholder `?` |
| **XSS** | Output escaping | `View::e()` → `htmlspecialchars(ENT_QUOTES, UTF-8)` |
| **CSRF** | Verifikasi token | `Csrf::token()` + `Csrf::verify()` melalui `CsrfMiddleware` |
| **Session Hijacking** | Regenerasi sesi | `Session::regenerate()` saat login |
| **Password Cracking** | Bcrypt hashing | `password_hash(PASSWORD_DEFAULT)` / `password_verify()` |
| **Brute Force** | Progressive Delay / Rate Limit | Delay `AuthService::login()` + middleware `rate.limit` |
| **Spam / Bot Submissions** | CAPTCHA | Integrasi Cloudflare Turnstile pada formulir publik |
| **SSRF (Server-Side Request Forgery)** | Validasi IP | Pengecekan subnet IP privat di `FileUploadService::uploadFromUrl` |

## Perlindungan CSRF

### Pembuatan Token

```php
use App\Core\Csrf;

$token = Csrf::token();   // Mengembalikan token yang ada atau membuat yang baru
```

Kontrak: `bin2hex(random_bytes(32))` — 64 karakter heksadesimal yang disimpan di `$_SESSION['_csrf_token']`.

### Verifikasi Token

```php
Csrf::verify(string $token): bool
```

Menggunakan `hash_equals()` untuk perbandingan waktu konstan — mencegah serangan *timing*.

### Helper Hidden Field

```php
Csrf::field(): string
```

Mengembalikan: `<input type="hidden" name="_csrf_token" value="...">`.

Gunakan dalam formulir:

```php
<form method="POST" action="/users">
    <?= \App\Core\Csrf::field() ?>
    <!-- form fields -->
</form>
```

### Penegakan Middleware

`CsrfMiddleware` secara otomatis menegakkan CSRF pada semua request non-GET:

```php
// Hanya diterapkan ketika alias middleware 'csrf' ada di dalam grup route
$router->group(['middleware' => ['csrf']], function ($router) {
    $router->post('/submit', ...);
});
```

Ketika token hilang atau tidak cocok: `HttpException(419, 'Page Expired — CSRF token mismatch.')`.

### Regenerasi Token

```php
Csrf::regenerate(): string  // membuat token baru, mengembalikannya
```

Dipanggil setelah login untuk mencegah serangan *session fixation*.

## Perlindungan SQL Injection

Semua query database menggunakan PDO prepared statements dengan parameter binding:

```php
// BaseModel::query() dan BaseModel::execute()
$stmt = $this->db()->prepare($sql);
$stmt->execute($bindings);
```

Opsi PDO `EMULATE_PREPARES = false` memastikan prepared statements sisi server yang **sebenarnya**.

**Jangan pernah** menggabungkan (concatenate) input pengguna ke dalam SQL:

```php
// SALAH — SQL injection
$db->query("SELECT * FROM users WHERE id = " . $_GET['id']);

// BENAR
$model->find((int) $request->param('id'));
```

## Perlindungan XSS

Semua output yang menghadap pengguna harus di-escape:

```php
<?= \App\Core\View::e($user['name']) ?>
<?= e($user['name']) ?>        // shorthand helper
```

Kontrak: `htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8')`.

## Penanganan Kata Sandi

| Operasi | Fungsi | Digunakan Di |
|-----------|----------|---------|
| Hash saat buat/ubah | `password_hash($password, PASSWORD_DEFAULT)` | `UserService::create()`, `UserService::update()` |
| Verifikasi saat login | `password_verify($input, $hash)` | `AuthService::login()` |

Faktor biaya Bcrypt: Default PHP (saat ini 10).

## Anti-Brute Force & Rate Limiting

### 1. Progressive Delay (Login)
Di dalam `AuthService::login()`, upaya login yang gagal akan menambah penghitung di sesi. Setiap kegagalan berikutnya menambahkan delay `sleep()` (misalnya, 0.5s, 1s, 2s, 4s) untuk menghabiskan sumber daya komputasi serangan kamus otomatis.

### 2. Route Rate Limiting
Penyalahgunaan endpoint global dimitigasi melalui `RateLimitMiddleware` (`rate.limit:requests,minutes`). Middleware ini melacak request per alamat IP di dalam sesi (atau cache) dan memberikan respons 429 Too Many Requests jika batas terlampaui.

## Cloudflare Turnstile CAPTCHA

Formulir publik (seperti Formulir Pemesanan) mengintegrasikan Turnstile untuk mencegah spam bot.
- Dikendalikan melalui `TURNSTILE_ENABLED=true` di `.env`.
- Frontend memuat widget (`modules/turnstile.js`).
- Backend memvalidasi token melalui endpoint `/siteverify` Cloudflare di Controller sebelum memproses data.

## Keamanan Upload File (Perlindungan SSRF)

Saat mengunduh gambar menu dari URL (misalnya, dari generator AI), `FileUploadService` memvalidasi alamat IP yang di-resolve dari URL target untuk memastikan bahwa itu bukan alamat IP privat (`10.0.0.0/8`, `192.168.0.0/16`, `127.0.0.0/8`). Hal ini mencegah serangan Server-Side Request Forgery yang menargetkan layanan internal.

## Keamanan Sesi

| Tindakan | Implementasi |
|---------|----------------|
| Regenerasi sesi saat login | `Session::regenerate(true)` — menghapus sesi lama |
| Hapus sesi saat logout | `Session::destroy()` — membersihkan semua data sesi |
| Cookie HTTP-only | Default PHP `session.cookie_httponly` |
| **Idle Timeout** | `SessionTimeoutMiddleware` membatasi waktu idle (Admin: 30m, User: 2h) |

## Alur Autentikasi

```
Form Login (GET /login)
     │
     ▼
POST /login (email + password + _csrf_token)
     │
     ├── CsrfMiddleware::handle() → verifikasi token
     │
     ├── AuthController::login()
     │     ├── Validasi input (email wajib, password wajib)
     │     ├── AuthService::login(email, password)
     │     │     ├── User::findByEmail(email)
     │     │     ├── password_verify(input, hash)
     │     │     │     ├── LULUS → Session::regenerate()
     │     │     │     │         Session::set('user', userData)
     │     │     │     │         return true
     │     │     │     └── GAGAL → return false
     │     ├── Sukses → redirect /users
     │     └── Gagal → flash error → redirect /login
     │
     ▼
Sesi Terautentikasi
     │
     ├── AuthMiddleware::handle()
     │     └── Session::has('user') → izinkan/tolak
     │
     ├── RoleMiddleware::handle()
     │     └── Cek Session::get('user')['role']
     │
     ▼
POST /logout
     ├── Session::destroy()
     └── redirect /login
```

## Data Sesi yang Disimpan

Setelah login, `$_SESSION['user']` berisi:

```php
[
    'id'    => int,
    'name'  => string,
    'email' => string,
    'role'  => string,    // 'admin' atau 'user'
]
```

> Hash kata sandi **tidak pernah** disimpan di dalam sesi.

## Daftar Periksa Produksi

| Pengaturan | Nilai | File |
|---------|-------|------|
| `APP_DEBUG` | `false` | `.env` |
| `APP_ENV` | `production` | `.env` |
| Kata sandi DB kuat | Set | `.env` |
| Penegakan HTTPS | Konfigurasi di web server | nginx/Apache config |
| Cookie sesi aman | `session.cookie_secure = 1` | `php.ini` |
| Izin file | `storage/` dapat ditulis, `src/` read-only | Server config |

---

Lihat: [MIDDLEWARE.md](../core/MIDDLEWARE.md) · [ERROR_HANDLING.md](ERROR_HANDLING.md) · [CONVENTIONS.md](../guides/CONVENTIONS.md)