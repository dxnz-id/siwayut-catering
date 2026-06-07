# Roadmap Fitur Keuangan — Siwayut Catering

Tingkat prioritas: **P0** (inti) → **P1** (peningkatan) → **P2** (opsional)

---

## P0 — Bisnis Inti

### 1. Pelacakan Biaya / COGS

**Tujuan:** Mengetahui laba per item menu dan per pesanan.

| File | Perubahan |
|------|--------|
| `database/migrations/014_add_cost_price_to_menus.php` | Tambahkan `cost_price` DECIMAL(12,2) DEFAULT 0 ke `menus` |
| `src/Models/Menu.php` | Tambahkan `cost_price` ke fillable |
| `src/Views/menu/index.php` | Tambahkan kolom biaya ke tabel |
| `src/Views/menu/show.php` | Tampilkan biaya + margin laba |
| `src/Services/MenuService.php` | Petakan field biaya melalui service |
| `lang/en.php` + `lang/id.php` | Keys: `cost_price`, `profit_margin`, `markup` |

**Tambahan:** `database/migrations/015_add_cost_price_to_order_items.php` — simpan snapshot `cost_price_at_time` di `order_items` agar laba historis dapat dihitung.

### 2. Perhitungan Laba Pesanan

**Tujuan:** `OrderService` menghitung biaya per item baris dan menyimpan total biaya pada pesanan.

| File | Perubahan |
|------|--------|
| `database/migrations/016_add_cost_to_orders.php` | Tambahkan `total_cost` DECIMAL(12,2) DEFAULT 0 ke `orders` |
| `src/Services/OrderService.php` | Di `createOrder()`: jumlahkan `cost_price_at_time * qty` → `total_cost` |
| `src/Models/Order.php` | Tambahkan `total_cost` ke fillable/sortable |
| `src/Views/order/show.php` | Tampilkan total biaya + laba (total_price − total_cost) |

### 3. Halaman KPI Dashboard

**Tujuan:** Admin melihat ringkasan pendapatan saat mengunjungi `/` atau `/dashboard`.

| File | Perubahan |
|------|--------|
| `src/Controllers/DashboardController.php` | Controller baru dengan `index()` — query agregat |
| `src/Views/dashboard/index.php` | Kartu KPI: total pendapatan, total pesanan, jumlah belum bayar, rata-rata pesanan, pesanan hari ini |
| `config/routes.php` | `GET /` → `DashboardController::index` (admin, auth) |
| `src/Models/Order.php` | Tambahkan metode query `DashboardController` untuk agregasi |

**Kartu KPI yang ditampilkan:**
- Total pendapatan (sepanjang waktu)
- Pendapatan bulan ini
- Total pesanan
- Jumlah pesanan belum bayar
- Nilai rata-rata pesanan
- Pesanan hari ini
- 5 menu teratas berdasarkan pendapatan

### 4. Halaman Laporan Pendapatan

**Tujuan:** Laporan pendapatan yang dapat difilter berdasarkan rentang tanggal, siap untuk diekspor.

| File | Perubahan |
|------|--------|
| `src/Controllers/ReportController.php` | Controller baru: `revenue()` — filter tanggal, grup berdasarkan periode, total |
| `src/Views/report/revenue.php` | Tabel + grafik (pendapatan bulanan/mingguan/harian) |
| `config/routes.php` | `GET /reports/revenue` → auth+admin |
| `lang/en.php` + `lang/id.php` | Keys: `revenue`, `report`, `date_from`, `date_to`, `apply_filter`, `export_csv` |

---

## P1 — Peningkatan

### 5. Nomor Invoice / Kuitansi

**Tujuan:** Setiap pesanan mendapatkan nomor invoice formal (terpisah dari `order_number`).

| File | Perubahan |
|------|--------|
| `database/migrations/017_add_invoice_number_to_orders.php` | Tambahkan `invoice_number` VARCHAR(50) nullable ke `orders`; buat otomatis saat status berubah ke `paid` |
| `src/Services/OrderService.php` | Di `updateOrder()`: jika `payment_status` berubah ke `paid`, buat nomor invoice |
| `src/Views/order/show.php` | Tampilkan nomor invoice, tambahkan tombol "Cetak Kuitansi" |

### 6. Dukungan Pajak (PPN)

**Tujuan:** Menerapkan PPN/pajak pada pesanan untuk penagihan yang tepat.

| File | Perubahan |
|------|--------|
| `database/migrations/018_add_tax_to_orders.php` | Tambahkan `tax_rate` DECIMAL(5,2) DEFAULT 11, `tax_amount` DECIMAL(12,2) DEFAULT 0, `grand_total` DECIMAL(12,2) ke `orders` |
| `src/Services/OrderService.php` | `createOrder()`: hitung `tax_amount = (total_price - discount) * tax_rate / 100`, `grand_total = total_price - discount + tax_amount` |
| `src/Views/order/show.php` | Tampilkan rincian pajak |
| `lang/en.php` + `lang/id.php` | Keys: `tax`, `ppn`, `tax_rate`, `grand_total`, `subtotal_before_tax` |

### 7. Dukungan Diskon

**Tujuan:** Menerapkan diskon pada pesanan (persentase atau jumlah tetap).

| File | Perubahan |
|------|--------|
| `database/migrations/019_add_discount_to_orders.php` | Tambahkan `discount_type` ENUM('none','percentage','fixed') DEFAULT 'none', `discount_value` DECIMAL(12,2) DEFAULT 0, `discount_amount` DECIMAL(12,2) DEFAULT 0 ke `orders` |
| `src/Controllers/OrderController.php` | Tambahkan field diskon ke validasi `update()` + logika penggabungan |
| `src/Views/order/show.php` | Modal edit: field diskon; tampilan: item baris diskon |

### 8. Metode & Tanggal Pembayaran

**Tujuan:** Melacak bagaimana dan kapan pelanggan membayar.

| File | Perubahan |
|------|--------|
| `database/migrations/020_add_payment_details_to_orders.php` | Tambahkan `payment_method` ENUM('cash','transfer','qris','other') DEFAULT 'cash', `paid_at` DATETIME nullable |
| `src/Controllers/OrderController.php` | `update()`: set `paid_at` saat status berubah ke `paid` |
| `src/Views/order/show.php` | Tampilkan + edit metode pembayaran |
| `src/Views/order/index.php` | Tambahkan kolom metode pembayaran |
| `lang/en.php` + `lang/id.php` | Keys: `payment_method`, `cash`, `transfer`, `qris`, `paid_at` |

### 9. Uang Muka / Deposit

**Tujuan:** Mendukung pembayaran DP (umum untuk katering).

| File | Perubahan |
|------|--------|
| `database/migrations/021_add_dp_to_orders.php` | Tambahkan `down_payment` DECIMAL(12,2) DEFAULT 0, `down_payment_due` DATE nullable, `remaining_balance` DECIMAL(12,2) DEFAULT 0 |
| `src/Services/OrderService.php` | Hitung sisa saldo saat create/update |
| `src/Views/order/show.php` | Tampilkan info DP, tambahkan pelacakan DP ke alur kerja pembayaran |
| `lang/en.php` + `lang/id.php` | Keys: `down_payment`, `dp`, `remaining_balance`, `dp_due_date` |

### 10. Laporan Pendapatan Menu

**Tujuan:** Melihat item menu mana yang menghasilkan pendapatan terbanyak.

| File | Perubahan |
|------|--------|
| `src/Controllers/ReportController.php` | Tambahkan aksi `menuRevenue()` |
| `src/Views/report/menu-revenue.php` | Tabel: nama menu, total qty terjual, total pendapatan, total biaya, laba, margin % |
| `config/routes.php` | `GET /reports/menu-revenue` |
| `src/Models/Menu.php` | Query agregasi pendapatan (SUM dari `price_at_time * qty`) |

---

## P2 — Opsional

### 11. Pengeditan Item Pesanan

**Tujuan:** Admin dapat menambah/menghapus/mengubah item setelah pesanan dibuat.

| File | Perubahan |
|------|--------|
| `src/Services/OrderService.php` | Tambahkan `updateOrderItems()` — hapus + masukkan kembali item; hitung ulang `total_price`, `total_cost`, `grand_total` |
| `src/Views/order/show.php` | Tambahkan tombol edit per baris item, atau modal |

### 12. Kuitansi / Invoice yang Dapat Dicetak

**Tujuan:** Menghasilkan versi pesanan yang dapat dicetak atau dalam format PDF.

| File | Perubahan |
|------|--------|
| `src/Views/order/receipt.php` | Template bersih yang ramah cetak (tanpa sidebar/navbar) |
| `src/Controllers/OrderController.php` | Tambahkan aksi `receipt()` — merender view kuitansi |
| `config/routes.php` | `GET /orders/{id}/receipt` |
| Gunakan CSS `@media print` untuk tata letak cetak |

### 13. Ekspor CSV

**Tujuan:** Mengekspor daftar pesanan atau laporan pendapatan ke CSV.

| File | Perubahan |
|------|--------|
| `src/Controllers/ReportController.php` | Tambahkan `exportCsv()` — set header, stream CSV |
| Utilitas umum: kolom, rentang tanggal, filter |

### 14. Total Berjalan Formulir Publik

**Tujuan:** Pelanggan melihat total harga yang diperbarui saat mereka memilih item.

| File | Perubahan |
|------|--------|
| `src/Views/order/public-form.php` | Tambahkan perhitungan JS, tampilkan total dinamis di bawah item menu |
| `public/assets/js/app.js` atau skrip inline | Pantau perubahan kuantitas/menu, hitung ulang |

---

## Urutan Eksekusi (Direkomendasikan)

```
Fase 1 (Inti — 3–5 hari)
├── P0.1 Biaya / COGS
├── P0.2 Laba Pesanan
├── P0.3 KPI Dashboard
└── P0.4 Laporan Pendapatan

Fase 2 (Penagihan — 3–4 hari)
├── P1.5 Nomor Invoice
├── P1.6 Pajak (PPN)
├── P1.7 Diskon
└── P1.8 Metode & Tanggal Pembayaran

Fase 3 (Penyempurnaan — 2–3 hari)
├── P1.9 Uang Muka (DP)
├── P1.10 Laporan Pendapatan Menu
├── P2.11 Pengeditan Item Pesanan
├── P2.12 Kuitansi yang Dapat Dicetak
├── P2.13 Ekspor CSV
└── P2.14 Total Formulir Publik
```