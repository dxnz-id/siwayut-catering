# Validasi (Validation)

## API Validator

### `__construct(?PDO $db = null)`

Menerima instance PDO opsional untuk aturan yang bergantung pada database (misalnya, `unique`).

```php
use App\Core\{Validator, Database};

$validator = new Validator();                    // tanpa aturan DB
$validator = new Validator(Database::getInstance()); // dengan aturan DB
```

### `validate(array $data, array $rules): bool`

Memvalidasi data terhadap aturan (rules). Mengembalikan `true` jika semua aturan lolos, `false` jika ada yang gagal.

```php
$passed = $validator->validate(
    ['name' => 'John', 'email' => 'john@example.com'],
    ['name' => 'required|min:2|max:255', 'email' => 'required|email']
);
```

### `errors(): array`

Mengembalikan semua kesalahan validasi dalam bentuk array asosiatif `['field' => 'message']`.

### `error(string $field): ?string`

Mengembalikan pesan kesalahan untuk bidang (field) tertentu, atau `null` jika tidak ada.

### `fails(): bool`

Mengembalikan `true` jika validasi gagal (terdapat kesalahan).

## Sintaksis Aturan (Rule Syntax)

Aturan ditulis sebagai string yang dipisahkan dengan garis tegak (pipe `|`):

```php
'field_name' => 'rule1|rule2|rule3:argument'
```

**KONTRAK**: Analisis aturan menggunakan `explode(':', $rule, 3)` — bagian pertama adalah nama aturan, bagian kedua adalah argumen.

Validasi akan dihentikan pada aturan pertama yang gagal untuk setiap bidang.

## Aturan yang Tersedia (Available Rules)

| Aturan | Deskripsi | Contoh |
|--------|-----------|--------|
| `required` | Nilai tidak boleh null atau string kosong | `'name' => 'required'` |
| `email` | Harus berupa email yang valid (filter_var) | `'email' => 'required\|email'` |
| `min:N` | Panjang string minimal N karakter | `'password' => 'required\|min:6'` |
| `max:N` | Panjang string tidak boleh melebihi N karakter | `'name' => 'required\|max:255'` |
| `confirmed` | Harus cocok dengan bidang `{field}_confirmation` | `'password' => 'confirmed'` |
| `unique:table,column` | Nilai tidak boleh ada dalam tabel database | `'email' => 'unique:users,email'` |
| `unique:table,column,exceptId` | Unik kecuali untuk ID yang diberikan (untuk pembaruan) | `'email' => 'unique:users,email,5'` |
| `in:val1,val2,...` | Harus berupa salah satu dari nilai yang terdaftar | `'role' => 'in:admin,user'` |
| `numeric` | Harus berupa angka | `'age' => 'numeric'` |
| `string` | Harus berupa tipe data string | `'name' => 'string'` |
| `nullable` | Jika kosong/null, lewati aturan berikutnya | `'bio' => 'nullable\|max:500'` |

**KONTRAK**: Untuk aturan `in:`, analisis argumen menggunakan `explode(',', $argument)`.

## Aturan `unique`

Memerlukan PDO di konstruktor validator:

```php
$validator = new Validator(Database::getInstance());

// Membuat data baru — memeriksa apakah email bersifat unik
$validator->validate($data, [
    'email' => 'required|email|unique:users,email',
]);

// Memperbarui data — mengecualikan ID pengguna saat ini
$validator->validate($data, [
    'email' => 'required|email|unique:users,email,' . $userId,
]);
```

## ValidationException

Ketika validasi gagal, controller dapat melempar `ValidationException`:

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
        $this->withOldInput($data);                              // mengisi kembali formulir dengan input sebelumnya
        Session::flash('errors', json_encode($validator->errors())); // menyimpan error ke pesan flash
        $this->redirect('/users/create');                        // kembali ke formulir
    }

    $this->userService->create($data);
    $this->redirectWithFlash('/users', 'success', 'User created.');
}
```

## Format Pesan Kesalahan

Pesan kesalahan mengikuti pola: `"The {field} field {rule description}."` di mana `{field}` adalah nama bidang dengan garis bawah (underscores) digantikan oleh spasi.

Contoh:
- `"The name field is required."`
- `"The password field must be at least 6 characters."`
- `"The email has already been taken."`
- `"The selected role is invalid."`

---

Lihat: [DATABASE.md](DATABASE.md) · [ERROR_HANDLING.md](ERROR_HANDLING.md) · [VIEWS.md](VIEWS.md)
