# Panduan Pengguna (Manual Admin)

Panduan ini menjelaskan cara menggunakan Dashboard Admin Siwayut Catering untuk mengelola operasional harian Anda.

## 1. Mengakses Dashboard
- Navigasikan ke `your-domain.com/login` (atau `/auth`).
- Masukkan email dan password admin Anda. (Default seeded admin: `admin@admin.com` / `password`).
- Setelah berhasil login, Anda akan diarahkan ke ringkasan Dashboard.

---

## 2. Ringkasan Dashboard
Dashboard memberikan pandangan menyeluruh mengenai bisnis Anda:
- **KPI Cards:** Menampilkan Total Orders, Total Revenue, Total Profit, dan Average Order Value.
- **Revenue Chart:** Grafik garis 7 hari yang menunjukkan tren pendapatan.
- **Order Status:** Grafik donat yang menunjukkan berapa banyak pesanan yang saat ini berstatus pending, processing, delivering, dll.
- **Top Menus:** Daftar 5 item menu terlaris Anda.

---

## 3. Mengelola Menu

Menu adalah inti dari aplikasi Anda.

### Membuat Menu Baru
1. Buka **Menus** di sidebar dan klik **Add Menu**.
2. Isi detail yang diperlukan: Name, Category, Event Type.
3. Atur **Price** (harga yang dibayar pelanggan) dan **Cost Price** (biaya internal Anda untuk memproduksinya). Ini sangat penting untuk pelaporan profit yang akurat.
4. **Image Upload:** Tarik dan lepas (drag and drop) gambar. Sistem akan mengoptimalkannya secara otomatis.
5. **AI Description:** Jika Anda kesulitan menulis deskripsi, klik tombol **"Generate AI Description"**. Sistem akan menulis deskripsi yang menarik berdasarkan nama menu dan kategori.
6. Klik **Save**.

### Mengelola Categories & Events
Sebelum membuat menu, pastikan Anda memiliki Categories (misalnya, Main Course, Dessert) dan Events (misalnya, Wedding, Corporate) yang sesuai.
- Navigasikan ke **Categories** atau **Events** di sidebar.
- Anda dapat menambahkan yang baru, atau mengedit yang sudah ada secara langsung menggunakan modal popup inline.

---

## 4. Mengelola Orders

Ketika pelanggan melakukan pemesanan di website publik, pesanan tersebut akan muncul di sini.

### Melihat Orders
1. Buka **Orders** di sidebar.
2. Gunakan filter di bagian atas untuk mempersempit daftar (misalnya, hanya menampilkan pesanan "Pending", atau mencari berdasarkan Order Number `ORD-...`).
3. Klik **ikon Mata** (View) untuk melihat detail pesanan lengkap.

### Memperbarui Order Status
Pesanan mengalir melalui siklus hidup tertentu:
`Pending` → `Processing` → `Delivering` → `Completed`
1. Buka halaman detail Order.
2. Di bawah "Update Status", pilih status baru dari dropdown.
3. Klik **Update Status**.
*(Catatan: Mengubah pesanan ke 'Processing' akan secara otomatis menghasilkan Invoice Number).*

### Mengelola Payments, Taxes, dan Discounts
1. Pada halaman detail Order, gulir ke bagian "Admin Action".
2. Anda dapat memperbarui **Payment Status** (Unpaid, Partial, Paid) dan menambahkan **Payment Method/Reference**.
3. Anda dapat menerapkan **Discount** (baik jumlah tetap maupun persentase).
4. Anda dapat menerapkan **Tax Rate** (misalnya, 10% atau 11%).
5. Grand Total akan dihitung ulang secara otomatis.

### Mencetak Resi
Klik tombol **Print Receipt** di bagian atas halaman detail Order untuk menghasilkan invoice yang bersih dan ramah cetak untuk pelanggan.

---

## 5. Laporan

### Revenue Report
1. Buka **Reports > Revenue**.
2. Pilih Date Range (Start Date dan End Date).
3. Lihat rincian harian mengenai pesanan, pendapatan, dan profit.
4. Klik **Export CSV** untuk mengunduh data ke Excel atau perangkat lunak akuntansi.

### Menu Revenue Report
1. Buka **Reports > Menu Revenue**.
2. Laporan ini menunjukkan profitabilitas dari masing-masing item menu selama periode yang dipilih.
3. Anda dapat melihat berapa banyak porsi yang terjual, total pendapatan yang dihasilkan, dan persentase margin profit yang tepat.

---

## 6. Pengaturan Sistem

### Mengelola Users
- Buka **Users**. Di sini Anda dapat membuat akun tambahan untuk staf Anda.
- Tetapkan role `admin` untuk memberikan mereka akses penuh ke dashboard.

### Memperbarui Profil Anda
- Klik nama Anda di sudut kanan atas dan pilih **Profile**.
- Di sini Anda dapat memperbarui nama, email, dan mengubah password Anda.