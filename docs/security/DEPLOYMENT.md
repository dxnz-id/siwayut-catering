# Deployment Guide

Siwayut Catering is designed to be easily deployable using Docker, but can also run on any standard PHP hosting environment (VPS, bare-metal).

## Option A: Docker Deployment (Recommended)

The project includes a robust, production-ready `docker-compose.yml` and a multi-stage `Dockerfile` based on **FrankenPHP** (a modern, high-performance PHP app server written in Go).

### Prerequisites
- Docker Engine
- Docker Compose v2

### Architecture Overview
The Docker Compose setup defines four services:
1. **`app`**: The FrankenPHP application server (Alpine-based, runs PHP 8.2+).
2. **`db`**: MariaDB 10.11 database.
3. **`phpmyadmin`**: Database management interface.
4. **`updater`**: A lightweight Node.js Alpine container that runs a cron job (`scripts/cron-update-repo.sh`) to automatically pull Git updates.

### Step-by-Step Guide

1. **Clone the Repository**
   ```bash
   git clone https://github.com/your-repo/siwayut-catering.git
   cd siwayut-catering
   ```

2. **Configure Environment**
   ```bash
   cp .env.example .env
   ```
   Edit the `.env` file. For Docker, the database host must match the service name (`db`).
   ```ini
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.com

   DB_HOST=db
   DB_PORT=3306
   DB_DATABASE=siwayut_catering
   DB_USERNAME=root
   DB_PASSWORD=root

   # Set a secure random string!
   APP_KEY="your-secure-random-key"
   ```

3. **Start the Containers**
   ```bash
   docker compose up -d --build
   ```
   *Note: The first build will take a few minutes as it installs PHP extensions (pdo_mysql, gd, intl, zip), runs Composer, and builds the Tailwind CSS.*

4. **Initialize the Database**
   Once the containers are running, execute the migrations and seeders inside the `app` container:
   ```bash
   # Enter the container
   docker exec -it siwayut-app bash

   # Run migrations
   php vanilla migrate

   # Seed default admin and data (Optional)
   php vanilla db:seed

   exit
   ```

5. **Access the Application**
   - App: `http://localhost:8080` (or your reverse proxy domain)
   - phpMyAdmin: `http://localhost:8081`

---

## Option B: Manual Deployment (VPS / Shared Hosting)

If you prefer to deploy without Docker, you need a web server (Nginx/Apache), PHP, and MySQL.

### Prerequisites
- PHP 8.2 or higher
- PHP Extensions: `pdo`, `pdo_mysql`, `mbstring`, `curl`, `gd`, `intl`, `zip`
- MySQL 8.0+ or MariaDB 10.4+
- Composer 2.x
- Node.js 18+ (only needed for the initial CSS build)

### Step-by-Step Guide

1. **Upload Files & Install Dependencies**
   Upload the project files to your server (e.g., `/var/www/siwayut-catering`).
   ```bash
   cd /var/www/siwayut-catering
   composer install --no-dev --optimize-autoloader
   npm install
   npm run css:build
   ```

2. **File Permissions**
   Ensure the web server (e.g., `www-data`) has write access to the storage directory.
   ```bash
   chmod -R 775 storage/uploads
   chown -R www-data:www-data storage/uploads
   ```

3. **Configure Environment**
   ```bash
   cp .env.example .env
   # Edit .env with your production database credentials and set APP_DEBUG=false
   ```

4. **Database Setup**
   Create the database in MySQL, then run:
   ```bash
   php vanilla migrate
   ```

5. **Web Server Configuration (Nginx Example)**
   Point your document root to the `public/` directory.

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

       # Block access to hidden files
       location ~ /\.ht {
           deny all;
       }
   }
   ```

---

## Post-Deployment Checklist

- [ ] **APP_KEY**: Ensure `APP_KEY` in `.env` is set to a long, random string. This is used by the `Encryptor` for session and password security.
- [ ] **APP_DEBUG**: Ensure `APP_DEBUG=false` to prevent sensitive stack traces from leaking to users.
- [ ] **Turnstile**: If using CAPTCHA, ensure `TURNSTILE_SITE_KEY_MANAGED` and `TURNSTILE_SECRET_KEY_MANAGED` are configured.
- [ ] **AI API**: If using the AI description generator, ensure `AI_API_URL`, `AI_API_KEY`, and `AI_MODEL` are set correctly.
- [ ] **HTTPS**: Configure an SSL certificate (e.g., Let's Encrypt / Certbot) for your domain.

## Updating the Application

If using Docker, to pull the latest code and restart:

```bash
git pull origin main
docker compose build app
docker compose up -d
docker exec -it siwayut-app php vanilla migrate
```

*(Note: If you are using the built-in `updater` service in `docker-compose.yml`, it will attempt to pull from Git automatically every 15 minutes).*
