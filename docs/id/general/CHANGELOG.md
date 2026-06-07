# Changelog

Semua perubahan penting pada proyek Siwayut Catering akan didokumentasikan dalam file ini.

Format ini didasarkan pada [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), dan proyek ini mematuhi [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

### Added
- Rangkaian dokumentasi komprehensif yang mencakup API, Architecture, Controllers, Services, Models, CLI, dan Deployment.
- Diagram ERD menggunakan sintaks Mermaid dalam dokumentasi Models.

### Changed
- Refactoring ekstensif pada layer Service untuk mengenkapsulasi logika bisnis yang sebelumnya tersebar di Controllers.
- Refactoring `OrderService` untuk menangani kalkulasi multi-item yang kompleks dan transisi status.
- Restrukturisasi arsitektur CSS untuk menggunakan direktif `@theme` pada Tailwind v4.
- Konsolidasi file CSS dan JS yang duplikat menjadi modul terpadu.

---

## [1.0.0] - 2024-10-27 (Base Release)

### Added
- **Authentication & Authorization:** Sistem login aman dengan perlindungan brute-force penundaan progresif. Kontrol akses berbasis peran (Admin vs User).
- **Menu Management:** CRUD penuh untuk menu, kategori, dan acara. Unggah gambar dengan pembuatan thumbnail LQIP (Low-Quality Image Placeholder) otomatis.
- **AI Integration:** Pembuatan deskripsi menu menggunakan endpoint yang kompatibel dengan OpenAI melalui `AiService`.
- **Order System:** Formulir pemesanan publik multi-item dengan validasi kuantitas terhadap porsi minimum.
- **Dashboard:** Kartu KPI, visualisasi pendapatan dengan Chart.js, dan pelacakan menu terlaris.
- **Reporting:** Laporan pendapatan dan laporan profitabilitas menu dengan kemampuan ekspor CSV.
- **Security:** Integrasi Cloudflare Turnstile untuk formulir publik, perlindungan SSRF pada unggahan gambar dari URL.
- **Deployment:** Dukungan Docker melalui FrankenPHP dan MariaDB.
- **CLI:** Tool `php vanilla` kustom untuk migrasi, seeding, serving, dan scaffolding.

### Fixed
- Migrasi struktur pemesanan single-item lama ke struktur multi-item menggunakan pivot table `order_items`.