# Troubleshooting Guide

This document lists common issues encountered during development and deployment of Siwayut Catering, along with their solutions.

---

## 1. General Application Issues

### CSS / Styling Not Updating
**Problem:** You made changes to a `.css` file or added new Tailwind classes to a PHP view, but the browser doesn't reflect the changes.
**Solution:**
1. Ensure the Tailwind compiler is running: `npm run css:watch` or `npm run dev`.
2. Clear your browser cache or perform a hard refresh (`Ctrl + F5` or `Cmd + Shift + R`).
3. If deploying to production, ensure you ran `npm run css:build`.

### "Class not found" Error
**Problem:** A `Fatal error: Uncaught Error: Class 'App\Models\Something' not found` is thrown.
**Solution:**
You likely added a new class but Composer's autoloader doesn't know about it yet. Run:
```bash
composer dump-autoload
```

### Blank White Screen
**Problem:** The application shows a blank white page instead of an error message.
**Solution:**
Error reporting is turned off. Set `APP_DEBUG=true` in your `.env` file to see the stack trace. **Never leave this on in production.**

---

## 2. Server & Environment Issues

### PHP Built-in Server `HEAD /` 404 Quirk
**Problem:** When using `php vanilla serve`, automated uptime monitors or curl requests sending a `HEAD` request to `/` return a `404 Not Found`.
**Solution:**
This is a known quirk/bug in PHP's built-in development server (`php -S`) regarding the `index.php` routing script. It does not affect production web servers (Nginx/FrankenPHP). To test endpoints locally, always use `GET` requests.

### Database Connection Refused
**Problem:** `PDOException: SQLSTATE[HY000] [2002] Connection refused`
**Solution:**
1. Check if your MySQL/MariaDB server is running.
2. If using Docker, ensure `DB_HOST` in `.env` is exactly `db` (the service name), not `localhost`.
3. Check if the port (default `3306`) is correct and not blocked by a firewall.

---

## 3. Feature-Specific Issues

### Image Upload Fails (Permission Denied)
**Problem:** You try to upload a menu image and get a "failed to move uploaded file" error.
**Solution:**
The web server lacks write permissions to the storage folder.
```bash
chmod -R 775 storage/uploads
chown -R www-data:www-data storage/uploads  # (Adjust user/group for your OS)
```

### AI Description Generator Not Working
**Problem:** Clicking "Generate AI Description" returns an error popup.
**Solution:**
1. Check your `.env` file for the AI configuration.
2. Ensure `AI_API_URL` is a valid OpenAI-compatible endpoint.
3. Ensure `AI_API_KEY` is valid and has sufficient quota/credits.
4. If using a local LLM (like Ollama), ensure it is running and accessible from the PHP container/server.

### Turnstile CAPTCHA Failing or Not Rendering
**Problem:** The order form cannot be submitted, or the CAPTCHA widget says "Invalid Domain".
**Solution:**
1. Check your Cloudflare Dashboard. Ensure the domain you are testing on (e.g., `localhost`) is added to the Turnstile widget's allowed domains.
2. Verify `TURNSTILE_SITE_KEY_MANAGED` and `TURNSTILE_SECRET_KEY_MANAGED` in `.env` are exactly correct without extra spaces.
3. To disable CAPTCHA entirely during local development, set `TURNSTILE_ENABLED=false` in `.env`.

### Admin Session Logged Out Quickly
**Problem:** You are frequently forced to log in again while working in the dashboard.
**Solution:**
The session lifetime might be too short or garbage collection is aggressive. Check the session lifetime configured in `php.ini` (`session.gc_maxlifetime`) or the framework's session handler. Note that Siwayut sets a strict session cookie parameter.

---

## 4. Docker Issues

### Cannot Connect to Database from Host
**Problem:** You are trying to connect a GUI tool (like DBeaver or DataGrip) to the Docker database, but it fails.
**Solution:**
Ensure the `db` service in `docker-compose.yml` has the ports mapped correctly (`- "3306:3306"`). Connect using `localhost` on port `3306` with the credentials defined in the compose file (`root`/`root`).

### Changes in `.env` Not Reflected in Docker
**Problem:** You modified the `.env` file but the Docker container still uses old values.
**Solution:**
Environment variables are often cached or passed at container startup. Restart the containers:
```bash
docker compose down
docker compose up -d
```
