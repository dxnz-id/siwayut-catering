# Fitur

Dokumen ini menguraikan rangkaian fitur lengkap aplikasi Siwayut Catering dari perspektif pengguna akhir.

## User Roles

Aplikasi ini mendukung tiga tipe pengguna:

1. **Guest:** Pengguna yang belum terautentikasi yang dapat menelusuri menu, melakukan pemesanan, dan melacak status pesanan.
2. **Customer:** Pengguna terdaftar yang dapat mengelola profil mereka dan melihat riwayat pesanan mereka.
3. **Admin:** Anggota staf terautentikasi yang mengelola menu, pesanan, laporan, dan pengaturan sistem melalui dashboard.

---

## Public Features

Fitur-fitur ini tersedia bagi Guest dan Customer pada sisi aplikasi yang menghadap publik.

### Landing Page
- **Food Gallery & Hero Section:** Area landing yang menarik dengan efek parallax dan tombol call-to-action yang jelas.
- **Featured Menus:** Menampilkan menu aktif dengan gambar progressive yang dimuat secara lazy-load (LQIP) untuk performa yang cepat.
- **Infinite Scroll:** Fungsionalitas "Load More" untuk menelusuri menu secara mulus melalui AJAX API (`/api/menus`).
- **Category Showcase:** Menu dapat difilter berdasarkan kategori tertentu.

### Menu Detail Page
- **Detailed View:** Menampilkan deskripsi menu lengkap, harga, persyaratan porsi minimum, serta kategori/event terkait.
- **Related Menus:** Menampilkan hingga 3 menu terkait untuk mendorong upselling.

### Order System
- **Multi-Item Order Form:** Customer dapat memilih beberapa menu dalam satu pesanan dan mengatur kuantitas masing-masing.
- **Validation:** Menegakkan persyaratan porsi minimum untuk setiap menu yang dipilih.
- **Delivery Details:** Mengumpulkan alamat pengiriman, tanggal acara, jenis acara, dan catatan khusus.
- **Customer Auto-Link:** Menautkan pesanan secara otomatis ke data customer yang sudah ada jika nomor telepon cocok, atau membuat profil customer baru.
- **Spam Protection:** Mengintegrasikan Cloudflare Turnstile CAPTCHA pada formulir pengiriman pesanan.

### Order Tracking
- **Secure Tracking:** Customer dapat melacak status pesanan mereka dengan memasukkan Nomor Pesanan (`ORD-YYMM-XXXX`) dan Nomor Telepon yang sesuai.
- **Session-Based Access:** Setelah diverifikasi, customer dapat melihat detail pelacakan dengan aman.

### Customer Portal
- **Registration & Login:** Customer dapat mendaftarkan akun (dengan perlindungan brute-force).
- **Order History (`/my-orders`):** Customer yang terautentikasi dapat melihat daftar semua pesanan mereka yang lalu dan saat ini.
- **Profile Management:** Customer dapat memperbarui nama, telepon, alamat, dan kata sandi mereka.

### Localization
- **Language Switcher:** Mendukung bahasa Inggris (`en`) dan bahasa Indonesia (`id`).
- **Auto-Detect:** Mendeteksi bahasa pilihan browser secara otomatis pada kunjungan pertama.

---

## Admin Features

Fitur-fitur ini dibatasi untuk pengguna dengan role `admin` dan dapat diakses melalui login `/auth`.

### Dashboard
- **KPI Cards:** Ringkasan Total Pesanan, Total Pendapatan, Total Keuntungan, dan Nilai Pesanan Rata-rata.
- **Revenue Chart:** Representasi visual tren pendapatan selama 7 hari terakhir menggunakan Chart.js.
- **Top Menus:** Mencantumkan 5 menu terlaris.
- **Status Breakdown:** Diagram donat yang menunjukkan distribusi status pesanan (Pending, Processing, Delivered, dll.).

### Menu Management
- **CRUD Operations:** Membuat, membaca, memperbarui, dan menghapus item menu.
- **Auto Menu Codes:** Menghasilkan kode `MNU-XXXX` unik secara otomatis.
- **Image Upload:** Mengunggah gambar menu dengan pembuatan thumbnail 20px otomatis (untuk LQIP) dan validasi ukuran/MIME yang ketat.
- **AI Description Generation:** Klik tombol untuk secara otomatis menghasilkan deskripsi menu yang menggugah selera dalam bahasa Indonesia menggunakan AI API terintegrasi (Gemini/OpenAI).
- **Costing:** Melacak `price` jual dan `cost_price` internal untuk menghitung margin keuntungan.

### Order Management
- **Order List:** Melihat semua pesanan dengan filter untuk Status, Status Pembayaran, dan pencarian berdasarkan Nomor Pesanan atau Nama Customer.
- **Status Workflow:** Memperbarui status pesanan melalui siklus hidup: `pending` → `processing` → `delivering` → `completed` (atau `cancelled`).
- **Payment Tracking:** Memperbarui status pembayaran (`unpaid`, `partial`, `paid`) dan mencatat metode/referensi pembayaran.
- **Invoice Generation:** Menghasilkan Nomor Faktur secara otomatis saat pesanan dikonfirmasi.
- **Tax & Discount:** Menerapkan diskon flat atau berbasis persentase dan menghitung jumlah pajak.
- **Receipt Printing:** Menghasilkan tampilan tanda terima/faktur yang dapat dicetak untuk customer.
- **CSV Export:** Mengekspor daftar pesanan yang difilter ke CSV untuk pemrosesan eksternal.

### Event & Category Management
- **Categories:** Mengelola kategori menu (misalnya, Main Course, Dessert) dan melihat jumlah menu yang ditetapkan untuk masing-masing kategori.
- **Events:** Mengelola jenis acara (misalnya, Wedding, Corporate) dengan tanggal mulai/selesai dan tombol aktif/tidak aktif.

### Reporting
- **Revenue Report:** Melihat pendapatan harian, pesanan, dan keuntungan dalam rentang tanggal tertentu. Termasuk ekspor CSV.
- **Menu Revenue Report:** Menganalisis profitabilitas per item menu, menampilkan total kuantitas terjual, pendapatan, keuntungan, dan persentase margin keuntungan. Termasuk ekspor CSV.

### User Management
- **Staff Accounts:** Mengelola akun admin dan pengguna.
- **Role Assignment:** Menetapkan role `admin` atau `user` untuk membatasi akses ke dashboard.
- **Profile:** Admin dapat mengedit profil mereka sendiri dan mengubah kata sandi dengan aman.