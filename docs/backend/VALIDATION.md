# Validation

## Validator API

### `__construct(?PDO $db = null)`

Optional PDO instance for database-dependent rules (e.g., `unique`).

```php
use App\Core\{Validator, Database};

$validator = new Validator();                    // without DB rules
$validator = new Validator(Database::getInstance()); // with DB rules
```

### `validate(array $data, array $rules): bool`

Validate data against rules. Returns `true` if all pass, `false` if any fail.

```php
$passed = $validator->validate(
    ['name' => 'John', 'email' => 'john@example.com'],
    ['name' => 'required|min:2|max:255', 'email' => 'required|email']
);
```

### `errors(): array`

Return all errors as `['field' => 'message']`.

### `error(string $field): ?string`

Return the error message for a specific field, or `null`.

### `fails(): bool`

Return `true` if validation failed (errors exist).

## Rule Syntax

Rules are pipe-delimited strings:

```php
'field_name' => 'rule1|rule2|rule3:argument'
```

**CONTRACT**: Rule parsing uses `explode(':', $rule, 3)` — first part is rule name, second is argument.

Validation stops at the first failing rule per field.

## Available Rules

| Rule | Description | Example |
|------|-------------|---------|
| `required` | Value must not be null or empty string | `'name' => 'required'` |
| `email` | Must be a valid email (filter_var) | `'email' => 'required\|email'` |
| `min:N` | String must be at least N characters | `'password' => 'required\|min:6'` |
| `max:N` | String must not exceed N characters | `'name' => 'required\|max:255'` |
| `confirmed` | Must match `{field}_confirmation` | `'password' => 'confirmed'` |
| `unique:table,column` | Must not exist in DB table | `'email' => 'unique:users,email'` |
| `unique:table,column,exceptId` | Unique except for given ID (for updates) | `'email' => 'unique:users,email,5'` |
| `in:val1,val2,...` | Must be one of the listed values | `'role' => 'in:admin,user'` |
| `numeric` | Must be numeric | `'age' => 'numeric'` |
| `string` | Must be a string type | `'name' => 'string'` |
| `nullable` | If empty/null, skip remaining rules | `'bio' => 'nullable\|max:500'` |

**CONTRACT**: For `in:` rules, argument parsing uses `explode(',', $argument)`.

## The `unique` Rule

Requires PDO in constructor:

```php
$validator = new Validator(Database::getInstance());

// Create — check email is unique
$validator->validate($data, [
    'email' => 'required|email|unique:users,email',
]);

// Update — exclude current user's ID
$validator->validate($data, [
    'email' => 'required|email|unique:users,email,' . $userId,
]);
```

## ValidationException

When validation fails, controllers can throw a `ValidationException`:

```php
use App\Exceptions\ValidationException;

if ($validator->fails()) {
    throw new ValidationException($validator->errors());
}
```

API:
```php
$e->getErrors(): array   // returns ['field' => 'message', ...]
$e->getMessage(): string  // returns 'Validation failed'
```

## Usage Pattern in Controllers

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
        $this->withOldInput($data);                              // repopulate form
        Session::flash('errors', json_encode($validator->errors())); // flash errors
        $this->redirect('/users/create');                        // back to form
    }

    $this->userService->create($data);
    $this->redirectWithFlash('/users', 'success', 'User created.');
}
```

## Error Message Format

Messages follow the pattern: `"The {field} field {rule description}."` where `{field}` is the field name with underscores replaced by spaces.

Examples:
- `"The name field is required."`
- `"The password field must be at least 6 characters."`
- `"The email has already been taken."`
- `"The selected role is invalid."`

---

See: [DATABASE.md](../database/DATABASE.md) · [ERROR_HANDLING.md](../security/ERROR_HANDLING.md) · [VIEWS.md](../frontend/VIEWS.md)
