# Internasionalisasi (i18n)

Siwayut Catering mendukung berbagai bahasa untuk menjangkau audiens yang lebih luas. Bahasa utama yang didukung secara bawaan (*out-of-the-box*) adalah Bahasa Indonesia (`id`) dan Bahasa Inggris (`en`).

## Arsitektur

Inti dari sistem i18n dikelola oleh class `App\Core\Lang`. Sistem ini menggunakan PHP Array sederhana untuk menyimpan pasangan *key-value* untuk terjemahan.

### File Terjemahan
Terjemahan disimpan di direktori `/lang` pada root proyek.
- `/lang/id.php` — String Bahasa Indonesia (Default)
- `/lang/en.php` — String Bahasa Inggris

### Helper Function `__()`
Di seluruh aplikasi (baik di View maupun Controller), terjemahan diambil menggunakan helper function global `__()`, yang berfungsi sebagai wrapper untuk `Lang::get()`.

**Contoh:**
```php
// Di dalam view:
<h1><?= __('welcome_message') ?></h1>
```

---

## Deteksi Bahasa dan State

1. **Deteksi Otomatis Browser:** Saat pengguna mengunjungi situs untuk pertama kalinya, `Lang::detectBrowserLocale()` akan memeriksa header `HTTP_ACCEPT_LANGUAGE` yang dikirim oleh browser mereka. Jika cocok dengan `en`, maka bahasa akan diatur ke Bahasa Inggris. Jika tidak, sistem akan menggunakan `id` sebagai default.
2. **Penyimpanan Sesi:** Setelah locale ditentukan (atau dipilih secara eksplisit oleh pengguna), nilai tersebut disimpan dalam PHP Session (`$_SESSION['locale']`). Permintaan berikutnya akan menggunakan nilai sesi tersebut alih-alih mengevaluasi ulang header browser.

### Mengubah Bahasa
Pengguna dapat mengubah bahasa secara eksplisit menggunakan Language Switcher di bilah navigasi. Hal ini akan memicu permintaan `GET` ke `LangController`:

- `/lang/id` -> Mengatur locale sesi ke Bahasa Indonesia dan melakukan redirect kembali.
- `/lang/en` -> Mengatur locale sesi ke Bahasa Inggris dan melakukan redirect kembali.

---

## Bekerja dengan Terjemahan

### 1. Menambahkan Key Baru
Untuk menambahkan string yang dapat diterjemahkan, Anda harus menambahkannya ke **semua** file bahasa untuk mencegah kegagalan fallback terjemahan.

**Di `lang/en.php`:**
```php
return [
    // ... string yang sudah ada
    'checkout_button' => 'Proceed to Checkout',
];
```

**Di `lang/id.php`:**
```php
return [
    // ... string yang sudah ada
    'checkout_button' => 'Lanjut ke Pembayaran',
];
```

### 2. Variabel & Placeholder
Anda dapat menyisipkan data dinamis ke dalam string terjemahan menggunakan placeholder yang diawali dengan titik dua (`:`).

**Di dalam file bahasa:**
```php
'welcome_user' => 'Welcome back, :name!',
```

**Di dalam Controller atau View:**
```php
echo __('welcome_user', ['name' => $user['name']]);
// Output: Welcome back, John!
```

### 3. Mekanisme Fallback
Jika key terjemahan yang diminta tidak ditemukan dalam array locale saat ini, class `Lang` akan mengembalikan string literal dari key itu sendiri. Sebagai contoh, `__('missing_key')` akan menghasilkan `"missing_key"`. Pastikan semua key telah didefinisikan.

---

## Menambahkan Bahasa Baru

Untuk mendukung bahasa ketiga (contoh: Bahasa Spanyol `es`):

1. **Buat file:** Salin `lang/en.php` ke `lang/es.php`.
2. **Terjemahkan:** Terjemahkan semua nilai di dalam `lang/es.php`.
3. **Perbarui Deteksi:** Edit `src/Core/Lang.php`. Perbarui method `detectBrowserLocale` untuk menyertakan kode bahasa baru ke dalam array yang diizinkan:
   ```php
   if (in_array($lang, ['en', 'id', 'es'], true)) {
   ```
4. **Perbarui UI:** Tambahkan opsi bahasa ke dropdown switcher di navigasi frontend (`src/Views/partials/navbar.php` atau `nav-extra.php`).