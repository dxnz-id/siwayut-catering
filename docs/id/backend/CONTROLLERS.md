# Referensi Controller

Siwayut Catering menggunakan arsitektur *thin-controller*. Controller bertanggung jawab secara ketat untuk menerima HTTP request, melakukan validasi input, meneruskan data ke Service Layer, dan mengembalikan Response (HTML view, JSON, atau Redirect). **Business logic tidak boleh berada di dalam controller.**

Semua controller mewarisi dari `BaseController`.

---

## BaseController

Berlokasi di `src/Controllers/BaseController.php`.

### Key Methods
- `protected function render(string $view, array $data = [], string $layout = 'main'): void`
  - Memuat template PHP dari `src/Views/`.
  - Secara otomatis menyuntikkan data bersama (misalnya, flash messages, active route, current user).
  - Layout yang valid: `main` (admin dashboard), `auth` (layar login), `public` (penggantian layout landing page).
- `protected function redirect(string $url, int $code = 302): never`
- `protected function redirectWithFlash(string $url, string $key, string $message): never`
- `protected function withOldInput(array $data): void`
  - Menyimpan data pengiriman form yang gagal ke dalam session agar input dapat diisi kembali.
- `protected function currentUser(): ?array`
  - Helper untuk mendapatkan user yang terautentikasi dari session.

---

## Public Controllers

### WelcomeController
Menangani landing page publik dan API untuk memuat menu.
- `index(Request $request)` → `GET /` — Merender landing page.
- `publicShow(Request $request)` → `GET /menu/{code}` — Menampilkan tampilan detail untuk satu menu.
- `apiMenus(Request $request)` → `GET /api/menus` — JSON API untuk menu dengan infinite scrolling.

### AuthController
Menangani autentikasi dan registrasi.
- `index(Request $request)` → `GET /login` — Menampilkan form login.
- `login(Request $request)` → `POST /login` — Mengautentikasi user.
- `logout(Request $request)` → `POST /logout` — Menghancurkan session.
- `register(Request $request)` → `POST /register` — Menangani registrasi pelanggan.

---

## Admin Controllers

Controller ini memerlukan middleware `auth` dan `role:admin`. Controller ini biasanya mengimplementasikan operasi CRUD standar.

### DashboardController
- `index(Request $request)` → `GET /dashboard` — Mengagregasi data dari `OrderService` untuk menampilkan kartu KPI, grafik, dan menu terpopuler.

### MenuController
Disuntikkan dengan `MenuService`, `CategoryService`, `EventService`.
- `index` → Daftar menu (paginated).
- `create` / `store` → Form untuk menambah menu dan memproses penyisipan (termasuk unggah gambar).
- `edit` / `update` → Form untuk mengubah menu.
- `destroy` → Menghapus menu.
- `apiShow` → Mengambil data menu sebagai JSON (digunakan untuk pengeditan inline/modal).
- `generateDescription(Request $request)` → `POST /menus/generate-description` — Meneruskan request ke `AiService` untuk teks yang dihasilkan AI.

### OrderController
Dibagi menjadi bagian Publik (untuk tamu/pelanggan) dan Admin.
**Publik:**
- `publicCreate` / `publicStore` → Form pemesanan multi-item publik.
- `trackOrder` / `processTrackOrder` → Halaman pelacakan pesanan.
- `myOrders` → Riwayat pesanan pelanggan yang terautentikasi.
**Admin:**
- `index` → Daftar pesanan admin dengan filter.
- `show` → Halaman detail pesanan.
- `updateStatus` / `updatePayment` → Mutasi admin untuk status pesanan.
- `updateAdminNotes` → Memperbarui catatan internal admin, invoice, pajak, dan diskon.
- `printReceipt` → Menghasilkan tampilan resi yang dapat dicetak.

### CategoryController & EventController
Wrapper CRUD standar untuk manajemen Category dan Event.
- Menyediakan `index`, `store`, `update`, `destroy`, dan `apiShow` untuk interaksi AJAX.

### UserController
Disuntikkan dengan `UserService`. Mengelola akun `admin` dan `user` (staf).
- Struktur CRUD serupa. Termasuk logika untuk mencegah admin menghapus akun mereka sendiri.

### ReportController
Menangani analisis data dan ekspor CSV.
- `revenue(Request $request)` → Merender laporan pendapatan dengan filter tanggal.
- `exportRevenue(Request $request)` → Memaksa unduhan CSV dari laporan pendapatan.
- `menuRevenue(Request $request)` → Merender analisis profitabilitas per menu.
- `exportMenuRevenue(Request $request)` → Memaksa unduhan CSV dari profitabilitas menu.

### ProfileController
Mengizinkan admin yang sedang login untuk mengelola akun mereka sendiri.
- `index` → Menampilkan form profil.
- `update` → Memperbarui nama/email.
- `changePassword` → Memvalidasi password lama dan menetapkan yang baru.

---

## Dependency Injection

Aplikasi ini menggunakan Dependency Injection Container otomatis berbasis refleksi (dikonfigurasi di `config/bindings.php` dan `src/Core/Container.php`). 

Ketika sebuah route dijalankan, router meminta Container untuk membuat instance Controller target. Container membaca parameter constructor Controller (misalnya, `__construct(private OrderService $orderService)`) dan secara otomatis membuat instance serta menyuntikkan Service yang diperlukan.