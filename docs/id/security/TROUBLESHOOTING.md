# Panduan Pemecahan Masalah (Troubleshooting Guide)

Dokumen ini mencantumkan masalah umum yang ditemui selama pengembangan dan deployment Siwayut Catering, beserta solusinya.

---

## 1. Masalah Aplikasi Umum

### CSS / Styling Tidak Terbarui
**Masalah:** Anda telah melakukan perubahan pada file `.css` atau menambahkan class Tailwind baru ke dalam view PHP, namun browser tidak menampilkan perubahan tersebut.
**Solusi:**
1. Pastikan compiler Tailwind sedang berjalan: `npm run css:watch` atau `npm run dev`.
2. Bersihkan cache browser Anda atau lakukan *hard refresh* (`Ctrl + F5` atau `Cmd + Shift + R`).
3. Jika melakukan deployment ke production, pastikan Anda telah menjalankan `npm run css:build`.

### Error "Class not found"
**Masalah:** Muncul pesan `Fatal error: Uncaught Error: Class 'App\Models\Something' not found`.
**Solusi:**
Kemungkinan Anda baru saja menambahkan class baru namun autoloader Composer belum mengenalinya. Jalankan perintah:
```bash
composer dump-autoload
```

### Layar Putih Kosong (Blank White Screen)
**Masalah:** Aplikasi menampilkan halaman putih kosong alih-alih pesan error.
**Solusi:**
Pelaporan error sedang dinonaktifkan. Atur `APP_DEBUG=true` di dalam file `.env` Anda untuk melihat *stack trace*. **Jangan pernah membiarkan pengaturan ini aktif di lingkungan production.**

---

## 2. Masalah Server & Lingkungan

### Keanehan `HEAD /` 404 pada PHP Built-in Server
**Masalah:** Saat menggunakan `php vanilla serve`, monitor uptime otomatis atau request curl yang mengirimkan request `HEAD` ke `/` mengembalikan `404 Not Found`.
**Solusi:**
Ini adalah keanehan/bug yang sudah diketahui pada server pengembangan bawaan PHP (`php -S`) terkait skrip Routing `index.php`. Hal ini tidak memengaruhi server web production (Nginx/FrankenPHP). Untuk menguji endpoint secara lokal, selalu gunakan request `GET`.

### Koneksi Database Ditolak (Connection Refused)
**Masalah:** `PDOException: SQLSTATE[HY000] [2002] Connection refused`
**Solusi:**
1. Periksa apakah server MySQL/MariaDB Anda sedang berjalan.
2. Jika menggunakan Docker, pastikan `DB_HOST` di dalam `.env` tertulis tepat `db` (nama service), bukan `localhost`.
3. Periksa apakah port (default `3306`) sudah benar dan tidak diblokir oleh firewall.

---

## 3. Masalah Fitur Spesifik

### Upload Gambar Gagal (Permission Denied)
**Masalah:** Anda mencoba mengunggah gambar menu dan mendapatkan error "failed to move uploaded file".
**Solusi:**
Server web tidak memiliki izin tulis (*write permissions*) ke folder storage.
```bash
chmod -R 775 storage/uploads
chown -R www-data:www-data storage/uploads  # (Sesuaikan user/group untuk OS Anda)
```

### AI Description Generator Tidak Berfungsi
**Masalah:** Mengklik "Generate AI Description" memunculkan popup error.
**Solusi:**
1. Periksa file `.env` Anda untuk konfigurasi AI.
2. Pastikan `AI_API_URL` adalah endpoint yang kompatibel dengan OpenAI yang valid.
3. Pastikan `AI_API_KEY` valid dan memiliki kuota/kredit yang mencukupi.
4. Jika menggunakan LLM lokal (seperti Ollama), pastikan LLM tersebut sedang berjalan dan dapat diakses dari container/server PHP.

### Turnstile CAPTCHA Gagal atau Tidak Muncul
**Masalah:** Formulir pemesanan tidak dapat dikirim, atau widget CAPTCHA menampilkan pesan "Invalid Domain".
**Solusi:**
1. Periksa Cloudflare Dashboard Anda. Pastikan domain yang Anda gunakan untuk pengujian (contoh: `localhost`) telah ditambahkan ke domain yang diizinkan pada widget Turnstile.
2. Verifikasi bahwa `TURNSTILE_SITE_KEY_MANAGED` dan `TURNSTILE_SECRET_KEY_MANAGED` di dalam `.env` sudah benar tanpa spasi tambahan.
3. Untuk menonaktifkan CAPTCHA sepenuhnya selama pengembangan lokal, atur `TURNSTILE_ENABLED=false` di dalam `.env`.

### Sesi Admin Cepat Logout
**Masalah:** Anda sering dipaksa untuk login kembali saat bekerja di dashboard.
**Solusi:**
Masa berlaku sesi (*session lifetime*) mungkin terlalu singkat atau *garbage collection* terlalu agresif. Periksa masa berlaku sesi yang dikonfigurasi di `php.ini` (`session.gc_maxlifetime`) atau session handler framework. Perlu dicatat bahwa Siwayut menetapkan parameter cookie sesi yang ketat.

---

## 4. Masalah Docker

### Tidak Dapat Terhubung ke Database dari Host
**Masalah:** Anda mencoba menghubungkan alat GUI (seperti DBeaver atau DataGrip) ke database Docker, namun gagal.
**Solusi:**
Pastikan service `db` di dalam `docker-compose.yml` memiliki pemetaan port yang benar (`- "3306:3306"`). Hubungkan menggunakan `localhost` pada port `3306` dengan kredensial yang didefinisikan di dalam file compose (`root`/`root`).

### Perubahan pada `.env` Tidak Terdeteksi di Docker
**Masalah:** Anda telah mengubah file `.env` tetapi container Docker masih menggunakan nilai lama.
**Solusi:**
Variabel lingkungan sering kali di-cache atau diteruskan saat container dimulai. Restart container Anda:
```bash
docker compose down
docker compose up -d
```