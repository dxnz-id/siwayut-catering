# Container

The IoC (Inversion of Control) container manages object creation and dependency resolution. It supports explicit factory bindings and automatic constructor resolution via PHP reflection.

## API Reference

### `bind(string $abstract, callable $factory): void`

Register a factory closure for a class. The factory receives the container as its argument.

```php
$container->bind(UserService::class, function (Container $c) {
    return new UserService($c->make(User::class));
});
```

### `make(string $abstract): object`

Resolve a class. Returns a **cached singleton** — the same instance is returned on subsequent calls.

```php
$service = $container->make(UserService::class);
```

Resolution order:
1. Check instance cache → return if found
2. Check registered binding → call factory, cache result, return
3. Attempt auto-wiring via reflection → cache result, return
4. Throw `\ReflectionException` if class is not instantiable

### `makeNew(string $abstract): object`

Resolve a class as a **fresh instance** — no caching, always creates new.

```php
$fresh = $container->makeNew(Validator::class);
```

### `has(string $abstract): bool`

Check if a binding is registered.

```php
if ($container->has(UserService::class)) {
    // binding exists
}
```

## Auto-Wiring

When no explicit binding exists, the container uses reflection to resolve constructor dependencies automatically:

```
make('SomeClass')
    │
    ├── Has binding? → call factory
    │
    └── No binding → Reflection
            │
            ├── Constructor has params?
            │     │
            │     ├── Param is non-builtin type → recursive make()
            │     │
            │     ├── Param is builtin with default → use default
            │     │
            │     └── Param is builtin without default → throw Exception
            │
            └── No constructor → new SomeClass()
```

Example — `AuthService` has `User $userModel` in its constructor. When you call `make(AuthService::class)` without a binding, the container:
1. Reflects `AuthService::__construct`
2. Finds parameter `User $userModel`
3. Recursively calls `make(User::class)`
4. Constructs `new AuthService($userModel)`

## Registration

All bindings are registered in `config/bindings.php`:

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

| Method | Behavior | Use When |
|--------|----------|----------|
| `make()` | Returns cached instance (singleton) | Default — services, models, controllers |
| `makeNew()` | Returns fresh instance (transient) | When you need isolated state |

## Usage in Router

The router uses the container to resolve controllers when dispatching:

```php
// Inside Router::runHandler()
$controller = $this->container->make($handlerClass);
$controller->$method($request);
```

## Gotchas

- **Stateful singletons**: `make()` returns the same instance across the entire request. If a service mutates internal state, all callers see the mutation.
- **Circular dependencies**: The container does not detect circular auto-wiring. `A → B → A` will cause a stack overflow.

---

See: [ARCHITECTURE.md](ARCHITECTURE.md) · [ROUTING.md](ROUTING.md) · [MIDDLEWARE.md](MIDDLEWARE.md)
