# Panduan Deployment

Siwayut Catering dirancang agar mudah di-deploy menggunakan Docker, namun juga dapat dijalankan pada lingkungan hosting PHP standar (VPS, bare-metal).

## Opsi A: Deployment Docker (Direkomendasikan)

Proyek ini menyertakan `docker-compose.yml` yang tangguh dan siap untuk produksi, serta `Dockerfile` multi-tahap berbasis **FrankenPHP** (server aplikasi PHP modern berkinerja tinggi yang ditulis dalam bahasa Go).

### Prasyarat
- Docker Engine
- Docker Compose v2

### Ringkasan Arsitektur
Pengaturan Docker Compose mendefinisikan empat service:
1. **`app`**: Server aplikasi FrankenPHP (berbasis Alpine, menjalankan PHP 8.2+).
2. **`db`**: Database MariaDB 10.11.
3. **`phpmyadmin`**: Antarmuka manajemen database.
4. **`updater`**: Container Node.js Alpine ringan yang menjalankan cron job (`scripts/cron-update-repo.sh`) untuk menarik (pull) pembaruan Git secara otomatis.

### Panduan Langkah demi Langkah

1. **Clone Repository**
   ```bash
   git clone https://github.com/your-repo/siwayut-catering.git
   cd siwayut-catering
   ```

2. **Konfigurasi Environment**
   ```bash
   cp .env.example .env
   ```
   Edit file `.env`. Untuk Docker, host database harus sesuai dengan nama service (`db`).
   ```ini
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.com

   DB_HOST=db
   DB_PORT=3306
   DB_DATABASE=siwayut_catering
   DB_USERNAME=root
   DB_PASSWORD=root

   # Tetapkan string acak yang aman!
   APP_KEY="your-secure-random-key"
   ```

3. **Jalankan Container**
   ```bash
   docker compose up -d --build
   ```
   *Catatan: Build pertama akan memakan waktu beberapa menit karena proses instalasi ekstensi PHP (pdo_mysql, gd, intl, zip), menjalankan Composer, dan membangun Tailwind CSS.*

4. **Inisialisasi Database**
   Setelah container berjalan, jalankan migration dan seeder di dalam container `app`:
   ```bash
   # Masuk ke dalam container
   docker exec -it siwayut-app bash

   # Jalankan migration
   php vanilla migrate

   # Seed admin default dan data (Opsional)
   php vanilla db:seed

   exit
   ```

5. **Akses Aplikasi**
   - App: `http://localhost:8080` (atau domain reverse proxy Anda)
   - phpMyAdmin: `http://localhost:8081`

---

## Opsi B: Deployment Manual (VPS / Shared Hosting)

Jika Anda lebih memilih untuk melakukan deployment tanpa Docker, Anda memerlukan web server (Nginx/Apache), PHP, dan MySQL.

### Prasyarat
- PHP 8.2 atau lebih tinggi
- Ekstensi PHP: `pdo`, `pdo_mysql`, `mbstring`, `curl`, `gd`, `intl`, `zip`
- MySQL 8.0+ atau MariaDB 10.4+
- Composer 2.x
- Node.js 18+ (hanya diperlukan untuk build CSS awal)

### Panduan Langkah demi Langkah

1. **Upload File & Instal Dependensi**
   Upload file proyek ke server Anda (contoh: `/var/www/siwayut-catering`).
   ```bash
   cd /var/www/siwayut-catering
   composer install --no-dev --optimize-autoloader
   npm install
   npm run css:build
   ```

2. **Izin File (File Permissions)**
   Pastikan web server (contoh: `www-data`) memiliki akses tulis ke direktori storage.
   ```bash
   chmod -R 775 storage/uploads
   chown -R www-data:www-data storage/uploads
   ```

3. **Konfigurasi Environment**
   ```bash
   cp .env.example .env
   # Edit .env dengan kredensial database produksi Anda dan atur APP_DEBUG=false
   ```

4. **Pengaturan Database**
   Buat database di MySQL, kemudian jalankan:
   ```bash
   php vanilla migrate
   ```

5. **Konfigurasi Web Server (Contoh Nginx)**
   Arahkan document root Anda ke direktori `public/`.

   ```nginx
   server {
       listen 80;
       server_name your-domain.com;
       root /var/www/siwayut-catering/public;
       index index.php;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location ~ \.php$ {
           include snippets/fastcgi-php.conf;
           fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
       }

       # Blokir akses ke file tersembunyi
       location ~ /\.ht {
           deny all;
       }
   }
   ```

---

## Daftar Periksa Pasca-Deployment

- [ ] **APP_KEY**: Pastikan `APP_KEY` di dalam `.env` diatur ke string acak yang panjang. Ini digunakan oleh `Encryptor` untuk keamanan sesi dan password.
- [ ] **APP_DEBUG**: Pastikan `APP_DEBUG=false` untuk mencegah kebocoran stack trace sensitif kepada pengguna.
- [ ] **Turnstile**: Jika menggunakan CAPTCHA, pastikan `TURNSTILE_SITE_KEY_MANAGED` dan `TURNSTILE_SECRET_KEY_MANAGED` telah dikonfigurasi.
- [ ] **AI API**: Jika menggunakan generator deskripsi AI, pastikan `AI_API_URL`, `AI_API_KEY`, dan `AI_MODEL` diatur dengan benar.
- [ ] **HTTPS**: Konfigurasikan sertifikat SSL (contoh: Let's Encrypt / Certbot) untuk domain Anda.

## Memperbarui Aplikasi

Jika menggunakan Docker, untuk menarik kode terbaru dan melakukan restart:

```bash
git pull origin main
docker compose build app
docker compose up -d
docker exec -it siwayut-app php vanilla migrate
```

*(Catatan: Jika Anda menggunakan service `updater` bawaan di `docker-compose.yml`, service tersebut akan mencoba melakukan pull dari Git secara otomatis setiap 15 menit).*