# Contoh Penggunaan (Examples)

> Kumpulan resep salin-tempel (copy-paste) untuk tugas-tugas umum. Setiap contoh dibuat mandiri (self-contained).

## 1. Menambahkan Sumber Daya CRUD Baru (Produk)

### 1.1 Membuat Migrasi

```bash
php vanilla make:migration create_products_table
```

Edit berkas `database/migrations/002_create_products_table.sql`:

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

Jalankan perintah: `php vanilla migrate`

### 1.2 Membuat Model

```bash
php vanilla make:model Product
```

Edit berkas `src/Models/Product.php`:

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

Edit berkas `src/Services/ProductService.php`:

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

Edit berkas `src/Controllers/ProductController.php`:

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
        ]);Holder

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

Di dalam berkas `config/bindings.php`:

```php
$container->bind(\App\Models\Product::class, fn($c) => new \App\Models\Product());
$container->bind(\App\Services\ProductService::class, fn($c) =>
    new \App\Services\ProductService($c->make(\App\Models\Product::class))
);
$container->bind(\App\Controllers\ProductController::class, fn($c) =>
    new \App\Controllers\ProductController($c->make(\App\Services\ProductService::class))
);
```

### 1.6 Mendaftarkan Rute

Di dalam berkas `config/routes.php`, di dalam grup middleware auth:

```php
$router->get('/products', [\App\Controllers\ProductController::class, 'index']);
$router->get('/products/create', [\App\Controllers\ProductController::class, 'create']);
$router->post('/products', [\App\Controllers\ProductController::class, 'store']);
```

---

## 2. Menambahkan Bidang Unggah Berkas (File Upload)

Gunakan layanan bawaan `FileUploadService`:

```php
use App\Services\FileUploadService;

$uploadService = new FileUploadService();
$file = $request->file('image');

if ($file && $file['error'] === UPLOAD_ERR_OK) {
    $path = $uploadService->upload(
        $file,
        'products',                              // sub-direktori
        ['image/jpeg', 'image/png', 'image/webp'], // tipe MIME yang diizinkan
        5 * 1024 * 1024                           // ukuran maksimum: 5MB
    );
    // $path = 'products/nama_berkas_acak.jpg' — relatif terhadap storage/uploads/
}
```

### Menghapus berkas yang telah diunggah

```php
$uploadService->delete('products/nama_berkas_acak.jpg');
```

---

## 3. Menambahkan Middleware Kustom

Contoh pembatasan laju permintaan (Rate-limiting):

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

Daftarkan middleware: `$router->addMiddleware('throttle', ThrottleMiddleware::class);`

---

## 4. Mengembalikan Respons JSON

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

Edit berkas `src/Views/partials/sidebar.php`:

```php
<li>
    <a href="/products" class="sidebar-link <?= ($_SERVER['REQUEST_URI'] === '/products') ? 'active' : '' ?>">
        <span class="sidebar-icon">📦</span>
        <span>Products</span>
    </a>
</li>
```

---

## 6. Pesan Flash (Flash Messages)

### Setel di controller

```php
Session::flash('success', 'Data berhasil disimpan!');
Session::flash('error', 'Terjadi kesalahan.');
```

Atau gunakan helper dari BaseController:

```php
$this->redirectWithFlash('/users', 'success', 'User created.');
```

### Dirender secara otomatis

Berkas parsial `partials/flash.php` (yang sudah disertakan di tata letak utama) merender pesan flash:

```php
<?php $success = \App\Core\Session::getFlash('success'); ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>
```

---

## 7. Seeder dengan Banyak Baris Data (Multiple Records)

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

Jalankan perintah: `php vanilla db:seed --class=ProductSeeder`

---

## 8. Referensi Cepat CLI Vanilla

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

# Perancah (Scaffolding)
php vanilla make:controller Nama
php vanilla make:model Nama
php vanilla make:middleware Nama
php vanilla make:service Nama
php vanilla make:exception Nama
php vanilla make:migration create_table_name
php vanilla make:seeder Nama

# Utilitas
php vanilla routes
php vanilla env
php vanilla key:generate
php vanilla cache:clear
php vanilla tinker
```

---

Lihat: [DATABASE.md](DATABASE.md) · [ROUTING.md](ROUTING.md) · [VALIDATION.md](VALIDATION.md)
