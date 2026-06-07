# Changelog

All notable changes to the Siwayut Catering project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

### Added
- Comprehensive documentation suite covering API, Architecture, Controllers, Services, Models, CLI, and Deployment.
- ERD diagrams using Mermaid syntax in the Models documentation.

### Changed
- Extensive refactoring of the Service layer to encapsulate business logic previously scattered in Controllers.
- Refactored `OrderService` to handle complex multi-item calculations and status transitions.
- Restructured CSS architecture to use Tailwind v4 `@theme` directives.
- Consolidated duplicate CSS and JS files into unified modules.

---

## [1.0.0] - 2024-10-27 (Base Release)

### Added
- **Authentication & Authorization:** Secure login system with progressive delay brute-force protection. Role-based access control (Admin vs User).
- **Menu Management:** Full CRUD for menus, categories, and events. Image upload with automatic LQIP (Low-Quality Image Placeholder) thumbnail generation.
- **AI Integration:** Menu description generation using OpenAI-compatible endpoints via `AiService`.
- **Order System:** Multi-item public order form with quantity validation against minimum portions.
- **Dashboard:** KPI cards, Chart.js revenue visualization, and top-selling menu tracking.
- **Reporting:** Revenue reports and Menu Profitability reports with CSV export capabilities.
- **Security:** Cloudflare Turnstile integration for public forms, SSRF protection on image uploads from URLs.
- **Deployment:** Docker support via FrankenPHP and MariaDB.
- **CLI:** Custom `php vanilla` tool for migrations, seeding, serving, and scaffolding.

### Fixed
- Migrated legacy single-item order structure to multi-item structure using `order_items` pivot table.
