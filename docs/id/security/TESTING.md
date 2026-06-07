# Pengujian & Jaminan Kualitas (Quality Assurance)

Saat ini, Siwayut Catering tidak menyertakan framework pengujian otomatis (seperti PHPUnit atau Pest). Jaminan kualitas bergantung pada protokol pengujian manual yang ketat dan praktik pemrograman defensif (seperti *strong typing* dan validasi ketat).

Dokumen ini menguraikan strategi pengujian dan daftar periksa QA manual yang harus dijalankan sebelum menerapkan perubahan besar.

---

## 1. Pemrograman Defensif

Untuk memitigasi kurangnya pengujian otomatis, basis kode menerapkan:
- `declare(strict_types=1);` di setiap file PHP.
- Tipe pengembalian (*return types*) eksplisit untuk semua method (contoh: `function foo(): array`).
- *Prepared statements* untuk semua interaksi basis data guna mencegah SQL injection.
- Validasi terpusat melalui class `Validator` sebelum setiap mutasi basis data.

---

## 2. Daftar Periksa QA Manual

Sebelum melakukan *merge* ke `main` atau melakukan *deploy* ke produksi, jalankan daftar periksa ini di lingkungan pengembangan lokal (`php vanilla serve`).

### 2.1 Alur Publik & Tamu
- [ ] **Landing Page**: Verifikasi menu dimuat, gambar ditampilkan (*progressive blur* berfungsi), dan "Load More" menambahkan data dengan benar.
- [ ] **Language Switcher**: Beralih antara ID dan EN. Pastikan string yang diterjemahkan diperbarui tanpa menyebabkan *crash*.
- [ ] **Validasi Formulir Pemesanan**: 
  - Coba kirim formulir dengan kolom kosong.
  - Coba kirim kuantitas di bawah `minimum_portions`.
  - Coba kirim tanpa menyelesaikan CAPTCHA (jika diaktifkan).
- [ ] **Penempatan Pesanan**: Berhasil melakukan pemesanan multi-item. Verifikasi bahwa Estimasi Total sesuai dengan `grand_total` akhir di basis data.
- [ ] **Pelacakan Pesanan**: Gunakan Nomor Pesanan dan Nomor Telepon yang dihasilkan untuk melacak pesanan. Coba dengan nomor telepon yang salah untuk memverifikasi penolakan.

### 2.2 Alur Autentikasi Pelanggan
- [ ] **Registrasi**: Daftarkan pengguna baru. 
- [ ] **Penautan Akun**: Daftarkan pengguna baru menggunakan nomor telepon yang sudah memiliki pesanan tamu. Verifikasi bahwa pesanan lama muncul di `/my-orders`.
- [ ] **Login & Brute Force**: Coba login dengan kata sandi yang salah sebanyak 5 kali. Verifikasi penundaan progresif (*response* menjadi lebih lambat) atau mekanisme penguncian terpicu.
- [ ] **Timeout Sesi**: Login, tunggu hingga melewati masa berlaku sesi (atau kadaluwarsa cookie secara paksa), dan pastikan tindakan berikutnya mengarahkan kembali ke halaman login.

### 2.3 Dashboard Admin
- [ ] **CRUD Menu**: 
  - Buat menu dengan unggahan gambar. Verifikasi gambar dan *thumbnail* disimpan ke disk.
  - Hasilkan deskripsi AI (memerlukan internet/API key).
  - Edit menu, unggah gambar *baru*, dan verifikasi file gambar lama dihapus dari disk.
  - Hapus menu. Verifikasi file telah dihapus.
- [ ] **Alur Kerja Pesanan**:
  - Buka pesanan berstatus 'Pending'.
  - Ubah status menjadi 'Processing'. Verifikasi Nomor Faktur dihasilkan.
  - Tambahkan diskon tetap dan pajak persentase. Verifikasi Grand Total dihitung ulang dengan benar.
  - Ubah Status Pembayaran menjadi 'Paid'.
  - Klik 'Print Receipt' dan verifikasi tata letaknya.
- [ ] **Laporan**:
  - Muat Laporan Pendapatan untuk rentang tanggal tertentu.
  - Ekspor CSV dan buka di aplikasi spreadsheet untuk memverifikasi penyelarasan kolom.

### 2.4 Pemeriksaan Keamanan
- [ ] **CSRF**: Buka formulir (contoh: Edit Profil), ubah nilai `_token` tersembunyi menggunakan Chrome DevTools, lalu kirim. Verifikasi bahwa sistem mengeluarkan error ketidakcocokan CSRF 403.
- [ ] **Kontrol Akses**: Login sebagai Pelanggan biasa (`role: user`). Coba akses `/dashboard` atau `/orders` langsung melalui URL. Verifikasi bahwa sistem mengarahkan kembali dengan error tidak sah (*unauthorized*).

---

## 3. Strategi Pengujian Masa Depan

Menerapkan pengujian otomatis adalah prioritas tertinggi untuk pengurangan utang teknis (*technical debt*). 

**Stack yang Direncanakan:**
1. **PHPUnit**: Untuk Pengujian Unit pada layer `Service` (contoh: menguji perhitungan `OrderService::createOrder`, melakukan *mocking* pada basis data).
2. **Playwright / Cypress**: Untuk pengujian End-to-End (E2E) pada perjalanan pengguna yang kritis (contoh: melakukan pemesanan, mengunggah gambar menu). 
3. **GitHub Actions**: Untuk menjalankan rangkaian pengujian secara otomatis pada setiap Pull Request.