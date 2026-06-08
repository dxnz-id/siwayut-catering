# Siwayut Catering

A catering management application based on **Vanilla PHP MVC** — featuring a public landing page, order forms, order tracking, and an admin dashboard.

## Key Features

- **Smart Order System:** Multi-item order validation with dynamic price calculation.
- **Dashboard Management:** Revenue visualization with Chart.js and performance metrics.
- **AI Integration:** Menu description generation using OpenAI/Gemini API.
- **Progressive Image Loading:** LQIP (Low-Quality Image Placeholder) technique to improve LCP.
- **Security:** Brute-force protection, Turnstile CAPTCHA, SSRF-protection, and CSRF.

## Requirements

- PHP 8.2+ (`pdo`, `pdo_mysql`, `mbstring`, `curl`, `gd`)
- Composer 2.x
- MySQL / MariaDB 8.0+
- Node.js 18+ (for building Tailwind CSS)

## Quickstart (Manual)

```bash
composer install
npm install
cp .env.example .env   # configure DB, APP_KEY, optional AI & Turnstile
php vanilla key:generate
npm run css:build
php vanilla db:create
php vanilla migrate
php vanilla db:seed
php vanilla serve
```

- Landing: [http://localhost:8000](http://localhost:8000)
- Admin: [http://localhost:8000/auth](http://localhost:8000/auth) — default `admin@admin.com` / `password` (after seeding)

Development with CSS hot-reload:

```bash
npm run dev
```

## 📚 Documentation

Welcome to Siwayut's extensive documentation! Instead of overwhelming you here, we've organized everything neatly inside the **[`docs/`](docs/)** directory. 

_Prefer reading in Indonesian? Check out our [Indonesian Documentation](docs/id/README.md)._

### 🧭 Where should you start?

- 🚀 **First time here?** Get up and running smoothly with our [Installation](docs/general/INSTALLATION.md) and [Quickstart Guide](docs/general/QUICKSTART.md).
- 🧩 **Curious about the engine?** Read our [Architecture Overview](docs/core/ARCHITECTURE.md) to understand how our Reflection-based DI Container and MVC layers dance together.
- 🚦 **Handling Requests:** Dive into [Routing](docs/core/ROUTING.md) and [Middleware](docs/core/MIDDLEWARE.md) to see how we intercept, validate, and route HTTP traffic.
- 🗄️ **Data Management:** Learn about our lightweight ActiveRecord implementation in [Database Setup](docs/database/DATABASE.md), and visualize relationships in the [ERD](docs/database/MODELS.md).
- 🔐 **Security:** Review our robust [Security Features](docs/security/SECURITY.md) like Turnstile CAPTCHA, brute-force mitigation, CSRF, and SSRF prevention.

For a complete and comprehensive index covering topics like Controllers, File Uploads, Deployment, and Contributing, head over to the **[Full Documentation Index](docs/README.md)**.

**AI Agent Prompting Guide:** [AGENTS.md](AGENTS.md)

