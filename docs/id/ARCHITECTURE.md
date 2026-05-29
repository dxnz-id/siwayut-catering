# Arsitektur

## Gambaran Umum Sistem

Siwayut Catering adalah mikro-framework MVC PHP 8.2+ vanilla (murni). Framework ini tidak menggunakan dependensi runtime pihak ketiga sama sekali — Composer hanya digunakan untuk autoloading PSR-4. Framework ini menyediakan kontainer IoC (Inversion of Control) dengan auto-wiring berbasis refleksi, router dengan pipeline middleware, model bergaya ActiveRecord, dan perenderan templat PHP.

## Siklus Hidup Permintaan (Request Lifecycle)

```
Permintaan HTTP
      │
      ▼
┌──────────────────┐
│  .htaccess        │  Mengarahkan semua URL non-file ke index.php
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  public/index.php │  Titik Masuk (Entry point)
│                   │  1. Mendefinisikan BASE_PATH
│                   │  2. Memuat vendor/autoload.php
│                   │  3. Memuat .env → $_ENV
│                   │  4. Memuat config/app.php (konstanta)
│                   │  5. Memuat bootstrap/app.php
│                   │     → set_exception_handler
│                   │     → Session::start()
│                   │     → Container + bindings
│                   │  6. Membuat Router
│                   │  7. Mendaftarkan alias middleware
│                   │  8. Memuat config/routes.php
│                   │  9. Router::dispatch()
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  Router::dispatch │
         │             Pencocokan berurutan: metode + URI
│                   │  Mengekstrak parameter rute → Request
│                   │  Menjalankan pipeline middleware
│                   │  Menyelesaikan handler melalui Container
│                   │  Memanggil handler(Request)
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  Controller       │  Menerima Request
│                   │  Memanggil metode Service
│                   │  Merender View atau mengalihkan (Redirect)
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  Service          │  Logika Bisnis
│                   │  Memanggil metode Model
│                   │  Mengembalikan data
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  Model            │  Prepared statement melalui PDO
│                   │  Mengembalikan array
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  View::render()   │  ob_start → memuat templat
│                   │  Menyisipkan $content ke dalam tata letak (layout)
│                   │  Mengirimkan output ke peramban (browser)
└──────────────────┘
```

## Urutan Bootstrap

Urutan inisialisasi yang tepat dalam `public/index.php` → `bootstrap/app.php`:

| Langkah | File | Tindakan |
|---------|------|----------|
| 1 | `public/index.php` | `define('BASE_PATH', dirname(__DIR__))` |
| 2 | `public/index.php` | `require vendor/autoload.php` |
| 3 | `public/index.php` | `parse_ini_file('.env')` → `$_ENV` |
| 4 | `public/index.php` | `require config/app.php` → mendefinisikan `APP_NAME`, `APP_ENV`, `APP_DEBUG`, `APP_URL` |
| 5 | `bootstrap/app.php` | `Logger::setPath(BASE_PATH . '/storage/logs')` |
| 6 | `bootstrap/app.php` | `set_exception_handler(...)` — penangan kesalahan global |
| 7 | `bootstrap/app.php` | `Session::start()` |
| 8 | `bootstrap/app.php` | `new Container()` |
| 9 | `bootstrap/app.php` | `require config/bindings.php` — mendaftarkan closure pabrik (factory bindings) |
| 10 | `public/index.php` | Membuat `Router`, mendaftarkan alias middleware |
| 11 | `public/index.php` | `require config/routes.php` → mendaftarkan rute |
| 12 | `public/index.php` | `Router::dispatch(new Request())` |

## Peta Komponen

```
┌─────────────────────────────────────────────────────┐
│                    src/Core/                         │
│                                                     │
│  Container ──── Router ──── Request                 │
│      │            │            │                    │
│      │            │            ▼                    │
│      │            │        Response                 │
│      │            │                                 │
│      │            ▼                                 │
│      │       Middleware ──── Session                 │
│      │                        │                     │
│      │                        ▼                     │
│      │                      Csrf                    │
│      │                                              │
│      ▼                                              │
│   Validator     Database     View      Logger        │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│                  Lapisan Aplikasi                   │
│                                                     │
│  Controllers ──→ Services ──→ Models ──→ Database   │
│      │                                              │
│      └──→ Views (render)                            │
└─────────────────────────────────────────────────────┘
```

## Arsitektur Lapisan (Layer Architecture)

| Lapisan | Kelas | Tanggung Jawab |
|---------|-------|----------------|
| **Controller** | `AuthController`, `WelcomeController`, `UserController`, `CategoryController`, `EventController`, `MenuController`, `OrderController` | HTTP, validasi, redirect, view/JSON |
| **Service** | `AuthService`, `UserService`, `CategoryService`, `EventService`, `MenuService`, `OrderService`, `FileUploadService`, `AiService` | Logika bisnis, upload, AI, item pesanan |
| **Model** | `User`, `Category`, `Event`, `Menu`, `Customer`, `Order` | PDO; `Order` dengan join & `order_items` |
| **Database** | `Database` (singleton) | Koneksi PDO |

Utilitas inti tambahan: `Encryptor` (HMAC password), `Turnstile` (captcha), `BaseMigration`.

## Sistem Konfigurasi

| File | Tipe | Tujuan |
|------|------|--------|
| `.env` | INI | Variabel lingkungan — dimuat via `parse_ini_file()` ke dalam `$_ENV` |
| `config/app.php` | PHP (konstanta) | Mendefinisikan `APP_NAME`, `APP_ENV`, `APP_DEBUG`, `APP_URL`, menyetel zona waktu |
| `config/database.php` | PHP (mengembalikan array) | Array konfigurasi DSN PDO dengan driver, host, port, database, charset, opsi |
| `config/bindings.php` | PHP (menggunakan `$container`) | Mendaftarkan closure pabrik (factory closures) untuk controller, service, dan model |
| `config/routes.php` | PHP (mengembalikan closure) | Menerima `$router`, mendaftarkan semua rute dan grup |

## Penyebaran Kesalahan (Error Propagation)

```
Lempar Exception (throw)
       │
       ▼
set_exception_handler()        ← bootstrap/app.php
       │
       ├── Logger::error()     ← mencatat log ke storage/logs/YYYY-MM-DD.log
       │
       ├── http_response_code()
       │
       ├── APP_DEBUG = true?
       │      YA → Dump HTML (kelas, pesan, file, baris, trace)
       │      TIDAK  → halaman kesalahan yang ramah pengguna:
       │              404 → src/Views/errors/404.php
       │              *   → src/Views/errors/500.php
       │              fallback → inline HTML
       │
       └── exit(1)
```

## Static Facades vs Injected Dependencies

| Pola (Pattern) | Kelas | Penggunaan |
|----------------|-------|------------|
| **Static facade** | `Session`, `Logger`, `Csrf`, `Response`, `Database` | Dipanggil secara statis: `Session::get('user')` |
| **Constructor injection** | Services → Controllers, Models → Services | Disuntikkan via Kontainer (Container): `new AuthService($userModel)` |

## Referensi Komponen

| Kelas | File | Dokumentasi |
|-------|------|-------------|
| `Container` | `src/Core/Container.php` | [CONTAINER.md](CONTAINER.md) |
| `Router` | `src/Core/Router.php` | [ROUTING.md](ROUTING.md) |
| `Request` | `src/Core/Request.php` | [ROUTING.md](ROUTING.md) |
| `Response` | `src/Core/Response.php` | [ROUTING.md](ROUTING.md) |
| `View` | `src/Core/View.php` | [VIEWS.md](VIEWS.md) |
| `Session` | `src/Core/Session.php` | [MIDDLEWARE.md](MIDDLEWARE.md) |
| `Csrf` | `src/Core/Csrf.php` | [SECURITY.md](SECURITY.md) |
| `Validator` | `src/Core/Validator.php` | [VALIDATION.md](VALIDATION.md) |
| `Database` | `src/Core/Database.php` | [DATABASE.md](DATABASE.md) |
| `Logger` | `src/Core/Logger.php` | [ARCHITECTURE.md](ARCHITECTURE.md) |
| `BaseModel` | `src/Models/BaseModel.php` | [DATABASE.md](DATABASE.md) |
| `BaseController` | `src/Controllers/BaseController.php` | [ROUTING.md](ROUTING.md) |
| `MiddlewareInterface` | `src/Middleware/MiddlewareInterface.php` | [MIDDLEWARE.md](MIDDLEWARE.md) |
| `AppException` | `src/Exceptions/AppException.php` | [ERROR_HANDLING.md](ERROR_HANDLING.md) |

---

Selanjutnya: [CONTAINER.md](CONTAINER.md) · [ROUTING.md](ROUTING.md)
