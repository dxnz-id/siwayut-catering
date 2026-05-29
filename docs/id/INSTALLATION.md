# Instalasi

## Prasyarat

| Kebutuhan | Versi |
|-------------|---------|
| PHP | ^8.2 |
| Composer | ^2.0 |
| MySQL / MariaDB | ^8.0 / ^10.6 |
| Ekstensi PHP | `pdo`, `pdo_mysql`, `mbstring` |

Verifikasi prasyarat:

```bash
php -v                          # Harus menunjukkan versi 8.2+
composer -V                     # Harus menunjukkan versi 2.x
php -m | grep -E 'pdo|mbstring' # Harus mencantumkan pdo, pdo_mysql, mbstring
mysql --version                 # Harus menunjukkan versi 8.0+ atau MariaDB 10.6+
```

## Klon (Clone) & Install

```bash
git clone <repository-url> siwayut-catering
cd siwayut-catering
composer install
```

## Konfigurasi Lingkungan (Environment)

```bash
cp .env.example .env
```

Edit `.env` sesuai dengan pengaturan Anda:

| Variabel | Deskripsi | Bawaan (Default) |
|----------|-------------|---------|
| `APP_NAME` | Nama tampilan aplikasi | `My App` |
| `APP_ENV` | Lingkungan (`local`, `production`) | `local` |
| `APP_DEBUG` | Tampilkan detail error (`true`/`false`) | `true` |
| `APP_TIMEZONE` | Zona waktu PHP | `Asia/Jakarta` |
| `APP_URL` | URL dasar untuk helper aset/url | `http://localhost` |
| `DB_DRIVER` | Driver PDO | `mysql` |
| `DB_HOST` | Host database | `127.0.0.1` |
| `DB_PORT` | Port database | `3306` |
| `DB_DATABASE` | Nama database | `myapp` |
| `DB_USERNAME` | Nama pengguna database | `root` |
| `DB_PASSWORD` | Kata sandi database | *(kosong)* |
| `AI_API_URL` | URL dasar API AI yang kompatibel dengan OpenAI (contoh: `https://generativelanguage.googleapis.com/v1beta/openai/`) | *(kosong)* |
| `AI_API_KEY` | Kunci API untuk penyedia AI | *(kosong)* |
| `AI_MODEL` | Nama model (contoh: `gemini-2.0-flash`, `gpt-4o-mini`) | *(kosong)* |

> **Penting**: Setel `APP_DEBUG=false` pada lingkungan produksi (production) agar tidak mengekspos rincian sistem (stack traces).

## Penyiapan Database

Gunakan CLI `vanilla` untuk membuat database, menjalankan migrasi, dan seed (mengisi data):

```bash
php vanilla db:create
php vanilla migrate
php vanilla db:seed --class=AdminSeeder
```

Output yang diharapkan:

```
  DONE    Database 'myapp' created successfully.
  DONE    Migrated:  001_create_users_table.sql
  DONE    Ran 1 migration(s).
  DONE    Database seeding completed.
```

Seeder akan membuat akun admin bawaan:
- **Email**: `admin@admin.com`
- **Kata Sandi**: `password`

## Menjalankan Server Pengembangan

```bash
php vanilla serve
```

Atau gunakan Composer:

```bash
composer run dev
```

Keduanya akan menjalankan server bawaan PHP pada `http://localhost:8000`.

Output yang diharapkan:

```
  Vanilla Framework v1.0.0 (PHP 8.x.x)
  INFO    Starting development server on http://localhost:8000
```

## Verifikasi Instalasi

1. Buka `http://localhost:8000` — Anda seharusnya melihat halaman beranda.
2. Buka `http://localhost:8000/login` — Anda seharusnya melihat formulir login.
3. Login dengan `admin@admin.com` / `password` — Anda seharusnya dialihkan ke halaman pengguna (users).

## Pemecahan Masalah (Troubleshooting)

| Masalah | Solusi |
|---------|----------|
| `Unknown database 'myapp'` | Jalankan `php vanilla db:create` |
| `Table 'myapp.users' doesn't exist` | Jalankan `php vanilla migrate` |
| `Class not found` | Jalankan `composer dump-autoload` |
| `pdo_mysql not found` | Instal: `sudo apt install php-mysql` |
| Port 8000 in use | Gunakan `php vanilla serve --port=8080` |

---

Selanjutnya: [QUICKSTART.md](QUICKSTART.md)
