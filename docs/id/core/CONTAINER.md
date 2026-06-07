# Container

Container IoC (Inversion of Control) mengelola pembuatan objek dan resolusi dependensi. Container ini mendukung binding factory eksplisit dan resolusi constructor otomatis melalui PHP reflection.

## API Reference

### `bind(string $abstract, callable $factory): void`

Mendaftarkan closure factory untuk sebuah class. Factory menerima container sebagai argumennya.

```php
$container->bind(UserService::class, function (Container $c) {
    return new UserService($c->make(User::class));
});
```

### `make(string $abstract): object`

Melakukan resolusi class. Mengembalikan **cached singleton** — instance yang sama akan dikembalikan pada pemanggilan berikutnya.

```php
$service = $container->make(UserService::class);
```

Urutan resolusi:
1. Periksa cache instance → kembalikan jika ditemukan
2. Periksa binding yang terdaftar → panggil factory, cache hasilnya, kembalikan
3. Coba auto-wiring melalui reflection → cache hasilnya, kembalikan
4. Lempar `\ReflectionException` jika class tidak dapat di-instansiasi

### `makeNew(string $abstract): object`

Melakukan resolusi class sebagai **fresh instance** — tidak ada caching, selalu membuat instance baru.

```php
$fresh = $container->makeNew(Validator::class);
```

### `has(string $abstract): bool`

Memeriksa apakah sebuah binding sudah terdaftar.

```php
if ($container->has(UserService::class)) {
    // binding ada
}
```

## Auto-Wiring

Ketika tidak ada binding eksplisit, container menggunakan reflection untuk menyelesaikan dependensi constructor secara otomatis:

```
make('SomeClass')
    │
    ├── Ada binding? → panggil factory
    │
    └── Tidak ada binding → Reflection
            │
            ├── Constructor memiliki parameter?
            │     │
            │     ├── Parameter adalah tipe non-builtin → make() rekursif
            │     │
            │     ├── Parameter adalah tipe builtin dengan default → gunakan default
            │     │
            │     └── Parameter adalah tipe builtin tanpa default → lempar Exception
            │
            └── Tidak ada constructor → new SomeClass()
```

Contoh — `AuthService` memiliki `User $userModel` di dalam constructor-nya. Saat Anda memanggil `make(AuthService::class)` tanpa binding, container akan:
1. Melakukan reflection pada `AuthService::__construct`
2. Menemukan parameter `User $userModel`
3. Memanggil `make(User::class)` secara rekursif
4. Mengonstruksi `new AuthService($userModel)`

## Registration

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

| Method | Perilaku | Gunakan Saat |
|--------|----------|----------|
| `make()` | Mengembalikan instance yang di-cache (singleton) | Default — services, models, controllers |
| `makeNew()` | Mengembalikan instance baru (transient) | Saat Anda membutuhkan state yang terisolasi |

## Penggunaan di Router

Router menggunakan container untuk melakukan resolusi controller saat melakukan dispatching:

```php
// Di dalam Router::runHandler()
$controller = $this->container->make($handlerClass);
$controller->$method($request);
```

## Catatan Penting (Gotchas)

- **Stateful singletons**: `make()` mengembalikan instance yang sama di sepanjang request. Jika sebuah service mengubah internal state, semua pemanggil akan melihat perubahan tersebut.
- **Circular dependencies**: Container tidak mendeteksi circular auto-wiring. `A → B → A` akan menyebabkan stack overflow.

---

Lihat: [ARCHITECTURE.md](ARCHITECTURE.md) · [ROUTING.md](ROUTING.md) · [MIDDLEWARE.md](MIDDLEWARE.md)