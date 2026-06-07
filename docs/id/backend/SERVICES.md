# Referensi Service

Service Layer merangkum semua logika bisnis. Controller harus mengekstrak input, meneruskannya ke Service, dan memformat output. Service berinteraksi dengan Model dan API eksternal lainnya.

## Arsitektur

Service terletak di `src/Services/`. Service diinstansiasi secara otomatis oleh Dependency Injection Container dan diinjeksi ke dalam Controller.

---

## 1. AuthService
Menangani kompleksitas autentikasi.

- **Dependencies:** Model `User`, model `Customer`.
- **Methods:**
  - `login(string $email, string $password): bool`
    - Memvalidasi kredensial terhadap hash password.
    - Mengimplementasikan **progressive delay** (anti-brute-force): Jika login gagal, sistem akan menunda respons berdasarkan jumlah percobaan gagal sebelumnya yang tersimpan di session.
    - Mengatur variabel session (`user_id`, `role`, `name`).
  - `logout(): void` — Menghancurkan session.
  - `register(array $data): int`
    - Membuat record `User` baru.
    - Memeriksa apakah record `Customer` sudah ada dengan nomor telepon yang diberikan. Jika ada, maka akan menautkan ID User baru ke record Customer yang sudah ada.

---

## 2. OrderService
Service terbesar dan paling kompleks (400+ baris), yang mengorkestrasi seluruh siklus hidup pesanan (order lifecycle).

- **Dependencies:** Model `Order`, `Customer`, `Menu`.
- **Methods:**
  - `createOrder(array $data, ?int $userId = null): int`
    - Menghitung total secara akurat dengan mengambil `price` real-time dari model `Menu` (mencegah manipulasi harga dari sisi frontend).
    - Membuat record `Customer` jika belum ada.
    - Membuat record `Order` induk (menghasilkan nomor `ORD-XXXX` secara otomatis).
    - Menyisipkan beberapa record `order_items` dan menghitung subtotal.
  - `updateOrder(int $id, array $data): bool`
    - Menangani transisi status. Jika status berubah menjadi `processing`, sistem secara otomatis menghasilkan `invoice_number` jika belum ada.
    - Menghitung ulang `grand_total` jika nilai `tax` atau `discount` diperbarui.
  - **Analytics Methods (digunakan oleh Dashboard & Reports):**
    - `getKpis()`: Mengembalikan Total Orders, Revenue, Profit, dan Avg Order Value.
    - `getTopMenus(int $limit = 5)`: Mengagregasi item pesanan untuk menemukan produk terlaris.
    - `getRevenueChartData()`: Menghitung pendapatan 7 hari terakhir untuk Chart.js.
    - `getOrderStatusBreakdown()`: Mengelompokkan pesanan berdasarkan status.
    - `getRevenueByPeriod(string $startDate, string $endDate)`: Menghasilkan laporan pendapatan terperinci.
    - `getMenuRevenueReport(string $startDate, string $endDate)`: Menghitung profit dan margin per item menu.

---

## 3. MenuService
Menangani CRUD menu dan mengoordinasikan unggahan gambar.

- **Dependencies:** Model `Menu`, `FileUploadService`.
- **Methods:**
  - `create(array $data, ?array $file = null): int` — Jika file gambar disediakan, memanggil `FileUploadService` untuk menyimpannya sebelum membuat record database.
  - `update(int $id, array $data, ?array $file = null): bool` — Menangani penghapusan gambar lama dan mengunggah yang baru jika file baru disediakan.
  - `delete(int $id): bool` — Memastikan file gambar terkait dan thumbnail dihapus dari disk saat menu dihapus.

---

## 4. FileUploadService
Mengelola penyimpanan file lokal untuk gambar.

- **Storage Location:** `storage/uploads/`
- **Methods:**
  - `uploadImage(array $file, string $directory = 'menus'): string`
    - Memvalidasi MIME type (`image/jpeg`, `image/png`, `image/webp`) dan ukuran.
    - Menghasilkan nama file acak yang aman.
    - Memindahkan file dan memanggil `generateThumbnail()`.
  - `generateThumbnail(string $filePath, string $directory): void`
    - Membuat Low-Quality Image Placeholder (LQIP) berukuran 20px yang sangat terkompresi.
    - Menyimpannya di `storage/uploads/{directory}/thumbs/`.
  - `uploadFromUrl(string $url, string $directory = 'menus'): string`
    - Mengunduh gambar eksternal melalui cURL.
    - Termasuk **SSRF Protection**: Memvalidasi bahwa IP target tidak berada dalam rentang privat/lokal (`127.0.0.0/8`, `10.0.0.0/8`, dll.).
  - `deleteFile(string $path): bool` — Menghapus gambar utama beserta thumbnail-nya.

---

## 5. AiService
Berintegrasi dengan LLM eksternal untuk menghasilkan deskripsi menu.

- **Configuration:** Membaca `AI_API_URL`, `AI_API_KEY`, dan `AI_MODEL` dari `.env`. (Kompatibel dengan OpenAI, wrapper Google Gemini OpenAI, atau Ollama lokal).
- **Methods:**
  - `generateMenuDescription(array $menuData): string`
    - Menyusun system prompt yang mendefinisikan persona ("penulis konten makanan ahli").
    - Mengirim permintaan cURL ke endpoint AI.
    - Mengurai respons JSON dan mengembalikan teks yang dihasilkan.

---

## 6. CategoryService, EventService, UserService, ProfileService
Ini adalah service wrapper yang lebih ringan yang memetakan langsung ke model masing-masing, menyediakan abstraksi CRUD standar dan logika validasi.