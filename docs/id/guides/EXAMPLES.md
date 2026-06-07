# Contoh

> Resep salin-tempel untuk tugas-tugas umum. Setiap contoh bersifat mandiri (self-contained).

## 1. Menambahkan Resource CRUD Baru (Products)

### 1.1 Membuat Migration

```bash
php vanilla make:migration create_products_table
```

Edit `database/migrations/002_create_products_table.sql`:

```sql
CREATE TABLE IF NOT EXISTS `products` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `price` DECIMAL(12,2) NOT NULL DEFAULT 0,
    `image` VARCHAR(255),
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Jalankan: `php vanilla migrate`

### 1.2 Membuat Model

```bash
php vanilla make:model Product
```

Edit `src/Models/Product.php`:

```php
<?php
declare(strict_types=1);

namespace App\Models;

class Product extends BaseModel {
    public function __construct() {
        parent::__construct();
        $this->table = 'products';
        $this->sortableColumns = ['id', 'name', 'price', 'created_at'];
    }

    public function findByName(string $name): ?array {
        return $this->findWhere(['name' => $name]);
    }
}
```

### 1.3 Membuat Service

```bash
php vanilla make:service Product
```

Edit `src/Services/ProductService.php`:

```php
<?php
declare(strict_types=1);

namespace App\Services;
use App\Models\Product;

class ProductService {
    public function __construct(private Product $product) {}

    public function all(): array {
        return $this->product->all();
    }

    public function find(int $id): ?array {
        return $this->product->find($id);
    }

    public function create(array $data): int {
        return $this->product->create([
            'name'        => $data['name'],
            'description' => $data['description'] ?? '',
            'price'       => $data['price'],
            'image'       => $data['image'] ?? null,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);
    }

    public function update(int $id, array $data): bool {
        return $this->product->update($id, [
            'name'        => $data['name'],
            'description' => $data['description'] ?? '',
            'price'       => $data['price'],
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);
    }

    public function delete(int $id): bool {
        return $this->product->delete($id);
    }

    public function paginate(int $page, int $perPage = 15): array {
        return $this->product->paginate($page, $perPage);
    }
}
```

### 1.4 Membuat Controller

```bash
php vanilla make:controller Product
```

Edit `src/Controllers/ProductController.php`:

```php
<?php
declare(strict_types=1);

namespace App\Controllers;
use App\Core\{Request, Validator, Database, Session};
use App\Services\ProductService;

class ProductController extends BaseController {
    public function __construct(private ProductService $productService) {
        parent::__construct();
    }

    public function index(Request $request): void {
        $page = (int) ($request->input('page', 1));
        $result = $this->productService->paginate($page);
        $this->render('product/index', [
            'title' => 'Products',
            'products' => $result,
        ]);
    }

    public function create(Request $request): void {
        $this->render('product/create', ['title' => 'Add Product']);
    }

    public function store(Request $request): void {
        $data = $request->only(['name', 'description', 'price']);
        $validator = new Validator(Database::getInstance());
        $validator->validate($data, [
            'name'  => 'required|min:2|max:255',
            'price' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $this->withOldInput($data);
            Session::flash('errors', json_encode($validator->errors()));
            $this->redirect('/products/create');
        }

        $this->productService->create($data);
        $this->redirectWithFlash('/products', 'success', 'Product created.');
    }
}
```

### 1.5 Mendaftarkan Binding

Di `config/bindings.php`:

```php
$container->bind(\App\Models\Product::class, fn($c) => new \App\Models\Product());
$container->bind(\App\Services\ProductService::class, fn($c) =>
    new \App\Services\ProductService($c->make(\App\Models\Product::class))
);
$container->bind(\App\Controllers\ProductController::class, fn($c) =>
    new \App\Controllers\ProductController($c->make(\App\Services\ProductService::class))
);
```

### 1.6 Mendaftarkan Routes

Di `config/routes.php`, di dalam grup auth:

```php
$router->get('/products', [\App\Controllers\ProductController::class, 'index']);
$router->get('/products/create', [\App\Controllers\ProductController::class, 'create']);
$router->post('/products', [\App\Controllers\ProductController::class, 'store']);
```

---

## 2. Menambahkan Field Unggah File

Gunakan `FileUploadService` bawaan:

```php
use App\Services\FileUploadService;

$uploadService = new FileUploadService();
$file = $request->file('image');

if ($file && $file['error'] === UPLOAD_ERR_OK) {
    $path = $uploadService->upload(
        $file,
        'products',                              // subdirektori
        ['image/jpeg', 'image/png', 'image/webp'], // tipe MIME yang diizinkan
        5 * 1024 * 1024                           // ukuran maksimal: 5MB
    );
    // $path = 'products/random_filename.jpg' — relatif terhadap storage/uploads/
}
```

### Menghapus file yang diunggah

```php
$uploadService->delete('products/random_filename.jpg');
```

---

## 3. Menambahkan Middleware Kustom

Contoh pembatasan laju (rate-limiting):

```php
<?php
declare(strict_types=1);

namespace App\Middleware;
use App\Core\{Request, Session};
use App\Exceptions\HttpException;

class ThrottleMiddleware implements MiddlewareInterface {
    public function handle(Request $request): bool {
        $key = 'throttle_' . $request->ip();
        $attempts = Session::get($key, 0);

        if ($attempts >= 60) {
            throw new HttpException(429, 'Too many requests.');
        }

        Session::set($key, $attempts + 1);
        return true;
    }
}
```

Daftarkan dengan: `$router->addMiddleware('throttle', ThrottleMiddleware::class);`

---

## 4. Mengembalikan Response JSON

Untuk endpoint bergaya API:

```php
use App\Core\Response;

public function apiUsers(Request $request): void {
    $users = $this->userService->all();
    Response::jsonSuccess($users, 'Users retrieved');
    // Output: {"success": true, "message": "Users retrieved", "data": [...]}
}

public function apiError(Request $request): void {
    Response::jsonError('Validation failed', ['email' => 'Required'], 422);
    // Output: {"success": false, "message": "Validation failed", "errors": {...}}
}
```

---

## 5. Menambahkan Tautan Sidebar

Edit `src/Views/partials/sidebar.php`:

```php
<li>
    <a href="/products" class="sidebar-link <?= ($_SERVER['REQUEST_URI'] === '/products') ? 'active' : '' ?>">
        <span class="sidebar-icon">📦</span>
        <span>Products</span>
    </a>
</li>
```

---

## 6. Flash Messages

### Mengatur di controller

```php
Session::flash('success', 'Record saved!');
Session::flash('error', 'Something went wrong.');
```

Atau gunakan helper BaseController:

```php
$this->redirectWithFlash('/users', 'success', 'User created.');
```

### Dirender secara otomatis

Partial `partials/flash.php` (disertakan dalam layout utama) merender flash messages:

```php
<?php $success = \App\Core\Session::getFlash('success'); ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>
```

---

## 7. Seeder dengan Banyak Record

```bash
php vanilla make:seeder ProductSeeder
```

```php
class ProductSeeder {
    public static function run(): void {
        $db = Database::getInstance();
        $products = [
            ['Nasi Goreng', 'Fried rice', 25000],
            ['Mie Ayam', 'Chicken noodle', 20000],
            ['Sate Ayam', 'Chicken satay', 30000],
        ];

        $stmt = $db->prepare(
            "INSERT INTO `products` (`name`, `description`, `price`, `created_at`, `updated_at`) VALUES (?, ?, ?, NOW(), NOW())"
        );

        foreach ($products as [$name, $desc, $price]) {
            $stmt->execute([$name, $desc, $price]);
            echo "  Seeded: {$name}\n";
        }
    }
}

ProductSeeder::run();
```

Jalankan: `php vanilla db:seed --class=ProductSeeder`

---

## 8. Referensi Cepat Vanilla CLI

```bash
# Server
php vanilla serve --port=8080

# Database
php vanilla db:create
php vanilla migrate
php vanilla migrate:fresh
php vanilla migrate:status
php vanilla db:seed
php vanilla db:seed --class=AdminSeeder

# Scaffolding
php vanilla make:controller Name
php vanilla make:model Name
php vanilla make:middleware Name
php vanilla make:service Name
php vanilla make:exception Name
php vanilla make:migration create_table_name
php vanilla make:seeder Name

# Utilitas
php vanilla routes
php vanilla env
php vanilla key:generate
php vanilla cache:clear
php vanilla tinker
```

---

## 9. Contoh Alur Pesanan Lengkap

Berikut adalah bagaimana sebuah pesanan bertransisi melalui `OrderService`.

```php
// 1. Pelanggan membuat pesanan (Multi-item)
$orderId = $orderService->createOrder([
    'phone' => '08123456789',
    'name' => 'John Doe',
    'event_date' => '2023-12-31 10:00:00',
    'delivery_address' => 'Jl. Sudirman No 1',
    'items' => [
        ['menu_id' => 1, 'quantity' => 50],
        ['menu_id' => 2, 'quantity' => 10]
    ]
]);

// 2. Admin mengonfirmasi dan mengubah status ke Processing
// Ini secara otomatis menghasilkan Nomor Faktur.
$orderService->updateOrder($orderId, [
    'status' => 'processing'
]);

// 3. Admin menambahkan pajak 10% dan mencatat pembayaran
$orderService->updateOrder($orderId, [
    'tax_rate' => 10,
    'payment_status' => 'paid',
    'payment_method' => 'Bank Transfer'
]);

// Sistem secara otomatis menghitung ulang grand_total.
```

---

## 9. Pembuatan Deskripsi Berbasis AI

Secara otomatis membuat deskripsi menu menggunakan API apa pun yang kompatibel dengan OpenAI.

### 9.1 Pengaturan

Tambahkan ke `.env`:

```env
AI_API_URL=https://generativelanguage.googleapis.com/v1beta/openai/
AI_API_KEY=your_gemini_api_key
AI_MODEL=gemini-2.0-flash
```

`AiService` mendukung endpoint apa pun yang kompatibel dengan OpenAI:

| Provider | API URL | Auth |
|----------|---------|------|
| Google Gemini | `https://generativelanguage.googleapis.com/v1beta/openai/` | Free API key |
| FreeTheAi | `https://api.freetheai.xyz/v1` | Discord signup |
| Free.ai | `https://api.free.ai/v1` | 30K token/hari gratis |
| Ollama (lokal) | `http://localhost:11434/v1` | Tidak ada |

Jika `AI_API_URL` atau `AI_MODEL` kosong, tombol generate akan menampilkan pesan kesalahan yang jelas.

### 9.2 Penggunaan

Pada form buat/edit menu, tombol **"Generate with AI"** akan muncul di bawah textarea deskripsi. Mengkliknya akan:

1. Mengumpulkan konteks form (nama, kategori, acara, harga, porsi minimum)
2. Mengirim `POST /menus/generate-description` dengan token CSRF
3. Memanggil API AI dengan prompt dalam Bahasa Indonesia
4. Mengisi textarea deskripsi dengan hasilnya

### 9.3 Menambahkan AI ke form Anda sendiri

```php
// View — tambahkan tombol setelah textarea
<button type="button" class="btn btn-sm btn-secondary"
        onclick="generateDescription(this)">
    Generate with AI
</button>
```

```php
// Controller — buat endpoint
use App\Services\AiService;
use App\Core\Response;

public function __construct(
    private AiService $aiService,
    // ... dependensi lainnya
) {}

public function generateDescription(Request $request): void {
    $description = $this->aiService->generateDescription([
        'name' => $request->input('name'),
        'category' => $request->input('category'),
        'event' => $request->input('event'),
        'price' => $request->input('price'),
        'minimum_portions' => $request->input('minimum_portions'),
    ]);
    Response::json(['description' => $description]);
}
```

```php
// Routes
$r->post('/menus/generate-description', [MenuController::class, 'generateDescription']);
```

### 9.4 Memperluas AiService

Prompt dibangun di dalam `AiService::buildPrompt()`. Timpa atau modifikasi untuk mengubah bahasa, nada, atau panjang teks:

```php
private function buildPrompt(array $ctx): string {
    return "Buat deskripsi menu catering yang menggugah selera "
         . "dalam Bahasa Indonesia berdasarkan data berikut:\n"
         . "Menu: {$ctx['name']}\n"
         . "Kategori: {$ctx['category']}\n"
         . "Deskripsi:";
}
```

---

Lihat: [DATABASE.md](../database/DATABASE.md) · [ROUTING.md](../core/ROUTING.md) · [VALIDATION.md](../backend/VALIDATION.md)