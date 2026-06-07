# Perintah CLI Vanilla PHP

Siwayut Catering menyertakan alat CLI kustom (`php vanilla`) untuk menangani tugas pengembangan dan deployment umum tanpa bergantung pada framework eksternal seperti Artisan atau Symfony Console.

Untuk melihat semua perintah yang tersedia, jalankan:
```bash
php vanilla
```

---

## Perintah Server

### `serve`
Menjalankan server pengembangan PHP bawaan.
- **Penggunaan:** `php vanilla serve [--port=8000]`
- **Deskripsi:** Mengarahkan semua request melalui `public/index.php`.
- **Catatan:** Jangan gunakan ini di lingkungan production. Gunakan web server yang tepat seperti FrankenPHP, Nginx, atau Apache.

---

## Perintah Database

### `migrate`
Menjalankan semua file SQL migration yang tertunda.
- **Penggunaan:** `php vanilla migrate`
- **Deskripsi:** Membaca file dari `database/migrations/`, memeriksa tabel `migrations` untuk melihat mana yang sudah dijalankan, dan mengeksekusi file yang tertunda sesuai urutan abjad.

### `migrate:fresh`
Menghapus semua tabel dan menjalankan ulang semua migration.
- **Penggunaan:** `php vanilla migrate:fresh`
- **Deskripsi:** Sangat destruktif. Gunakan hanya dalam pengembangan untuk mereset status database.

### `db:create`
Membuat skema database jika belum ada.
- **Penggunaan:** `php vanilla db:create`
- **Deskripsi:** Menggunakan variabel `DB_DATABASE` dari `.env`. Berguna selama pengaturan awal sebelum menjalankan migration.

### `db:seed`
Mengisi database dengan data default atau dummy.
- **Penggunaan:** `php vanilla db:seed`
- **Deskripsi:** Menjalankan class yang didefinisikan dalam `database/seeders/` (contoh: `AdminSeeder`, `MenuSeeder`, `OrderSeeder`). Berguna untuk menyiapkan akun admin awal.

---

## Perintah Generator (Make)

Perintah-perintah ini membuat scaffold file baru untuk mempercepat pengembangan dan memastikan konsistensi.

### `make:controller`
Membuat class Controller baru.
- **Penggunaan:** `php vanilla make:controller NameController`
- **Deskripsi:** Menghasilkan file di `src/Controllers/` yang meng-extend `BaseController`.

### `make:model`
Membuat class Model baru.
- **Penggunaan:** `php vanilla make:model Name`
- **Deskripsi:** Menghasilkan file di `src/Models/` yang meng-extend `BaseModel`.

### `make:service`
Membuat class Service baru.
- **Penggunaan:** `php vanilla make:service NameService`
- **Deskripsi:** Menghasilkan file di `src/Services/`.

---

## Perintah Routing

### `routes`
Menampilkan daftar semua route aplikasi yang terdaftar.
- **Penggunaan:** `php vanilla routes`
- **Deskripsi:** Mencetak tabel terformat yang menampilkan HTTP Method, Route Path, Controller Action, dan Middleware yang diterapkan. Sangat berguna untuk men-debug masalah routing.