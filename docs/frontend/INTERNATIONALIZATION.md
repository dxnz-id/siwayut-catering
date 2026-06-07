# Internationalization (i18n)

Siwayut Catering supports multiple languages to cater to a broader audience. The primary languages supported out-of-the-box are Indonesian (`id`) and English (`en`).

## Architecture

The core of the i18n system is managed by the `App\Core\Lang` class. It uses simple PHP arrays to store key-value pairs for translations.

### Translation Files
Translations are stored in the `/lang` directory at the root of the project.
- `/lang/id.php` — Indonesian strings (Default)
- `/lang/en.php` — English strings

### The `__()` Helper Function
Throughout the application (both in Views and Controllers), translations are retrieved using the global `__()` helper function, which acts as a wrapper for `Lang::get()`.

**Example:**
```php
// In a view:
<h1><?= __('welcome_message') ?></h1>
```

---

## Language Detection and State

1. **Browser Auto-detect:** When a user visits the site for the first time, `Lang::detectBrowserLocale()` inspects the `HTTP_ACCEPT_LANGUAGE` header sent by their browser. If it matches `en`, the language is set to English. Otherwise, it defaults to `id`.
2. **Session Storage:** Once a locale is determined (or explicitly chosen by the user), it is saved in the PHP Session (`$_SESSION['locale']`). Subsequent requests will use the session value instead of re-evaluating the browser headers.

### Changing the Language
Users can explicitly change the language using the Language Switcher in the navigation bar. This triggers a `GET` request to the `LangController`:

- `/lang/id` -> Sets session locale to Indonesian and redirects back.
- `/lang/en` -> Sets session locale to English and redirects back.

---

## Working with Translations

### 1. Adding a New Key
To add a new translatable string, you must add it to **all** language files to prevent missing translation fallbacks.

**In `lang/en.php`:**
```php
return [
    // ... existing strings
    'checkout_button' => 'Proceed to Checkout',
];
```

**In `lang/id.php`:**
```php
return [
    // ... existing strings
    'checkout_button' => 'Lanjut ke Pembayaran',
];
```

### 2. Variables & Placeholders
You can inject dynamic data into your translation strings using placeholders prefixed with a colon (`:`).

**In the language file:**
```php
'welcome_user' => 'Welcome back, :name!',
```

**In the Controller or View:**
```php
echo __('welcome_user', ['name' => $user['name']]);
// Output: Welcome back, John!
```

### 3. Fallback Mechanism
If a requested translation key is not found in the current locale's array, the `Lang` class will return the literal string of the key itself. For example, `__('missing_key')` will output `"missing_key"`. Ensure all keys are defined.

---

## Adding a New Language

To support a third language (e.g., Spanish `es`):

1. **Create the file:** Copy `lang/en.php` to `lang/es.php`.
2. **Translate:** Translate all the values in `lang/es.php`.
3. **Update Detection:** Edit `src/Core/Lang.php`. Update the `detectBrowserLocale` method to include the new language code in the allowed array:
   ```php
   if (in_array($lang, ['en', 'id', 'es'], true)) {
   ```
4. **Update UI:** Add the language option to the dropdown switcher in the frontend navigation (`src/Views/partials/navbar.php` or `nav-extra.php`).
