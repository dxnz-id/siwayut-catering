# Instalasi

## Prasyarat

| Kebutuhan | Versi |
|-----------|-------|
| PHP | ^8.2 |
| Composer | ^2.0 |
| Node.js | ^18 (build Tailwind CSS) |
| MySQL / MariaDB | ^8.0 / ^10.6 |
| Ekstensi PHP | `pdo`, `pdo_mysql`, `mbstring`, `curl`, `gd` |

```bash
php -v
composer -V
php -m | grep -E 'pdo|mbstring|curl|gd'
mysql --version
```

## Klon & install

```bash
git clone <repository-url> siwayut-catering
cd siwayut-catering
composer install
npm install
npm run css:build
```

## Konfigurasi `.env`

```bash
cp .env.example .env
```

| Variabel | Deskripsi | Default |
|----------|-----------|---------|
| `APP_NAME` | Nama aplikasi | `Siwayut` |
| `APP_KEY` | Rahasia HMAC password (**wajib**) | kosong |
| `APP_DEBUG` | Tampilkan error detail | `true` |
| `DB_DATABASE` | Nama database | `siwayut_catering` |
| `AI_*` | API deskripsi menu (opsional) | lihat `.env.example` |
| `TURNSTILE_*` | Cloudflare Turnstile (opsional) | `TURNSTILE_ENABLED=false` |

Generate `APP_KEY`:

```bash
php -r "echo 'APP_KEY=base64:' . base64_encode(random_bytes(32)) . PHP_EOL;"
```

> Produksi: set `APP_DEBUG=false`.

## Database

```bash
php vanilla db:create
php vanilla migrate
php vanilla db:seed --class=AdminSeeder
```

Akun admin bawaan: `admin@admin.com` / `password`

## Server pengembangan

```bash
php vanilla serve          # hanya PHP
npm run dev                # PHP + watch Tailwind
composer run dev           # sama seperti npm run dev
```

### Symlink upload

```
public/uploads → ../storage/uploads
```

Jika hilang: `ln -sfn ../storage/uploads public/uploads`

## Verifikasi

1. `http://localhost:8000` — landing page
2. `http://localhost:8000/auth` — login
3. Login admin → `/users`

## Troubleshooting

| Masalah | Solusi |
|---------|--------|
| Database tidak ada | `php vanilla db:create` |
| Tabel tidak ada | `php vanilla migrate` |
| `APP_KEY is not set` | Isi `APP_KEY` di `.env` |
| Tampilan tanpa CSS | `npm run css:build` |
| Port 8000 dipakai | `php vanilla serve --port=8080` |

---

Selanjutnya: [QUICKSTART.md](QUICKSTART.md)
