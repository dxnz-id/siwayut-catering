# Validasi

## Validator API

### `__construct(?PDO $db = null)`

Instance PDO opsional untuk aturan yang bergantung pada database (contoh: `unique`).

```php
use App\Core\{Validator, Database};

$validator = new Validator();                    // tanpa aturan DB
$validator = new Validator(Database::getInstance()); // dengan aturan DB
```

### `validate(array $data, array $rules): bool`

Memvalidasi data berdasarkan aturan yang diberikan. Mengembalikan `true` jika semua validasi berhasil, `false` jika ada yang gagal.

```php
$passed = $validator->validate(
    ['name' => 'John', 'email' => 'john@example.com'],
    ['name' => 'required|min:2|max:255', 'email' => 'required|email']
);
```

### `errors(): array`

Mengembalikan semua error dalam format `['field' => 'message']`.

### `error(string $field): ?string`

Mengembalikan pesan error untuk field tertentu, atau `null`.

### `fails(): bool`

Mengembalikan `true` jika validasi gagal (terdapat error).

## Rule Syntax

Aturan adalah string yang dipisahkan oleh karakter pipe (`|`):

```php
'field_name' => 'rule1|rule2|rule3:argument'
```

**KONTRAK**: Parsing aturan menggunakan `explode(':', $rule, 3)` — bagian pertama adalah nama aturan, bagian kedua adalah argumen.

Validasi akan berhenti pada aturan pertama yang gagal per field.

## Aturan yang Tersedia

| Rule | Deskripsi | Contoh |
|------|-----------|---------|
| `required` | Nilai tidak boleh null atau string kosong | `'name' => 'required'` |
| `email` | Harus berupa email yang valid (filter_var) | `'email' => 'required\|email'` |
| `min:N` | String harus memiliki minimal N karakter | `'password' => 'required\|min:6'` |
| `max:N` | String tidak boleh melebihi N karakter | `'name' => 'required\|max:255'` |
| `confirmed` | Harus cocok dengan `{field}_confirmation` | `'password' => 'confirmed'` |
| `unique:table,column` | Tidak boleh ada di tabel DB | `'email' => 'unique:users,email'` |
| `unique:table,column,exceptId` | Unik kecuali untuk ID yang diberikan (untuk update) | `'email' => 'unique:users,email,5'` |
| `in:val1,val2,...` | Harus salah satu dari nilai yang terdaftar | `'role' => 'in:admin,user'` |
| `numeric` | Harus berupa angka | `'age' => 'numeric'` |
| `string` | Harus bertipe string | `'name' => 'string'` |
| `nullable` | Jika kosong/null, lewati aturan selanjutnya | `'bio' => 'nullable\|max:500'` |

**KONTRAK**: Untuk aturan `in:`, parsing argumen menggunakan `explode(',', $argument)`.

## Aturan `unique`

Memerlukan PDO di dalam constructor:

```php
$validator = new Validator(Database::getInstance());

// Create — periksa apakah email unik
$validator->validate($data, [
    'email' => 'required|email|unique:users,email',
]);

// Update — kecualikan ID user saat ini
$validator->validate($data, [
    'email' => 'required|email|unique:users,email,' . $userId,
]);
```

## ValidationException

Ketika validasi gagal, Controller dapat melempar `ValidationException`:

```php
use App\Exceptions\ValidationException;

if ($validator->fails()) {
    throw new ValidationException($validator->errors());
}
```

API:
```php
$e->getErrors(): array   // mengembalikan ['field' => 'message', ...]
$e->getMessage(): string  // mengembalikan 'Validation failed'
```

## Pola Penggunaan di Controller

```php
public function store(Request $request): void {
    $data = $request->only(['name', 'email', 'password', 'role']);

    $validator = new Validator(Database::getInstance());
    $validator->validate($data, [
        'name'     => 'required|min:2|max:255',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'role'     => 'required|in:admin,user',
    ]);

    if ($validator->fails()) {
        $this->withOldInput($data);                              // mengisi ulang form
        Session::flash('errors', json_encode($validator->errors())); // flash error
        $this->redirect('/users/create');                        // kembali ke form
    }

    $this->userService->create($data);
    $this->redirectWithFlash('/users', 'success', 'User created.');
}
```

## Format Pesan Error

Pesan mengikuti pola: `"The {field} field {rule description}."` di mana `{field}` adalah nama field dengan garis bawah yang diganti menjadi spasi.

Contoh:
- `"The name field is required."`
- `"The password field must be at least 6 characters."`
- `"The email has already been taken."`
- `"The selected role is invalid."`

---

Lihat: [DATABASE.md](../database/DATABASE.md) · [ERROR_HANDLING.md](../security/ERROR_HANDLING.md) · [VIEWS.md](../frontend/VIEWS.md)