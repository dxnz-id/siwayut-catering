# Kontainer (Container)

Kontainer IoC (Inversion of Control) mengelola pembuatan objek dan penyelesaian dependensi (dependency resolution). Kontainer ini mendukung binding pabrik secara eksplisit (explicit factory bindings) dan penyelesaian konstruktor otomatis (automatic constructor resolution) melalui refleksi PHP.

## Referensi API

### `bind(string $abstract, callable $factory): void`

Mendaftarkan closure pabrik untuk sebuah kelas. Pabrik tersebut menerima kontainer sebagai argumennya.

```php
$container->bind(UserService::class, function (Container $c) {
    return new UserService($c->make(User::class));
});
```

### `make(string $abstract): object`

Menyelesaikan (resolve) kelas. Mengembalikan **singleton yang di-cache** — instance yang sama dikembalikan pada panggilan berikutnya.

```php
$service = $container->make(UserService::class);
```

Urutan penyelesaian (Resolution order):
1. Periksa cache instance → kembalikan jika ditemukan
2. Periksa binding yang terdaftar → panggil pabrik (factory), cache hasilnya, lalu kembalikan
3. Lakukan upaya auto-wiring via refleksi → cache hasilnya, lalu kembalikan
4. Lemparkan `\ReflectionException` jika kelas tidak dapat diinstansiasi

### `makeNew(string $abstract): object`

Menyelesaikan kelas sebagai **instance baru** — tanpa caching, selalu membuat baru.

```php
$fresh = $container->makeNew(Validator::class);
```

### `has(string $abstract): bool`

Memeriksa apakah suatu binding sudah terdaftar.

```php
if ($container->has(UserService::class)) {
    // binding ada
}
```

## Penyambungan Otomatis (Auto-Wiring)

Ketika tidak ada binding eksplisit yang terdaftar, kontainer akan menggunakan refleksi untuk menyelesaikan dependensi konstruktor secara otomatis:

```
make('SomeClass')
    │
    ├── Memiliki binding? → panggil pabrik (factory)
    │
    └── Tidak ada binding → Refleksi
            │
            ├── Konstruktor memiliki parameter?
            │     │
            │     ├── Parameter berupa tipe non-builtin → panggil make() secara rekursif
            │     │
            │     ├── Parameter berupa tipe builtin dengan nilai default → gunakan nilai default
            │     │
            │     └── Parameter berupa tipe builtin tanpa nilai default → lemparkan Exception
            │
            └── Tidak ada konstruktor → new SomeClass()
```

Contoh — `AuthService` memiliki `User $userModel` di dalam konstruktornya. Ketika Anda memanggil `make(AuthService::class)` tanpa binding, kontainer akan:
1. Merefleksikan `AuthService::__construct`
2. Menemukan parameter `User $userModel`
3. Memanggil `make(User::class)` secara rekursif
4. Menginstansiasi `new AuthService($userModel)`

## Registrasi

Semua binding didaftarkan di `config/bindings.php`:

```php
// config/bindings.php
$container->bind(User::class, fn(Container $c) => new User());

$container->bind(AuthService::class, fn(Container $c) =>
    new AuthService($c->make(User::class))
);

$container->bind(AuthController::class, fn(Container $c) =>
    new AuthController($c->make(AuthService::class))
);
```

## Singleton vs Transient

| Metode | Perilaku | Digunakan Saat |
|--------|----------|----------------|
| `make()` | Mengembalikan instance yang di-cache (singleton) | Bawaan (Default) — service, model, controller |
| `makeNew()` | Mengembalikan instance baru (transient) | Ketika Anda membutuhkan status yang terisolasi |

## Penggunaan di dalam Router

Router menggunakan kontainer untuk menyelesaikan kelas controller saat mengirimkan permintaan (dispatching):

```php
// Di dalam Router::runHandler()
$controller = $this->container->make($handlerClass);
$controller->$method($request);
```

## Hal yang Perlu Diperhatikan (Gotchas)

- **Stateful singletons**: `make()` mengembalikan instance yang sama di seluruh siklus permintaan. Jika sebuah service mengubah status internalnya (internal state), perubahan tersebut akan memengaruhi semua pemanggil berikutnya.
- **Circular dependencies**: Kontainer tidak mendeteksi auto-wiring yang melingkar (circular auto-wiring). Dependensi `A → B → A` akan menyebabkan penumpukan stack overflow.

---

Lihat: [ARCHITECTURE.md](ARCHITECTURE.md) · [ROUTING.md](ROUTING.md) · [MIDDLEWARE.md](MIDDLEWARE.md)
