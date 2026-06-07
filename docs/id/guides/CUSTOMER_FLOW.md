# Customer Flow & Journey

Dokumen ini menjelaskan pengalaman *end-to-end* pelanggan saat berinteraksi dengan aplikasi Siwayut Catering.

---

## 1. Discovery & Browsing

1. **Landing Page (`/`)**: Pelanggan tiba di halaman beranda. Mereka disambut oleh *hero banner* dengan *call-to-action* yang jelas ("Order Now" atau "Explore Menu").
2. **Featured Menus**: Saat menggulir ke bawah, mereka melihat *grid* menu yang paling populer atau yang baru saja ditambahkan.
3. **Filtering**: Pelanggan dapat mengeklik *category badges* (contoh: "Wedding", "Corporate Box") untuk memfilter daftar menu.
4. **Infinite Scroll**: Jika terdapat banyak item, mengeklik "Load More" akan mengambil dan menambahkan menu ke halaman secara mulus tanpa perlu memuat ulang halaman secara penuh.
5. **Menu Details (`/menu/{code}`)**: Mengeklik item menu tertentu akan membuka tampilan detail yang menampilkan deskripsi lengkap, harga per porsi, jumlah minimum pemesanan, dan item terkait.

---

## 2. Placing an Order

1. **Order Form (`/order-form`)**: Saat pelanggan mengeklik "Order Now" dari halaman detail menu, mereka diarahkan ke formulir pemesanan utama. Sistem secara otomatis memilih menu yang sedang mereka lihat.
2. **Multi-Item Selection**: Pelanggan dapat mengeklik "+ Add Menu" untuk memilih hidangan tambahan dari daftar *dropdown*.
3. **Quantity Input**: Untuk setiap menu yang dipilih, pelanggan memasukkan jumlah yang diinginkan. Formulir akan memvalidasi input ini terhadap aturan `minimum_portions` untuk item tersebut.
4. **Auto-Calculation**: Saat menu ditambahkan atau jumlah diubah, *frontend* akan langsung menghitung ulang subtotal dan Estimated Grand Total.
5. **Event Details**: Pelanggan mengisi:
   - Tanggal dan Waktu acara.
   - Jenis Acara (contoh: "Birthday Party").
   - Alamat Pengiriman Lengkap.
   - Catatan khusus (contoh: "Less spicy").
6. **Customer Details**: Pelanggan memberikan Nama Lengkap dan Nomor Telepon (digunakan sebagai pengenal utama mereka).
7. **Submission**: Pelanggan menyelesaikan Turnstile CAPTCHA dan mengirimkan formulir.

---

## 3. Order Processing (Backend Auto-Link)

Saat formulir dikirimkan, *backend* melakukan "Auto-Linking":
- Sistem memeriksa apakah *record* `Customer` sudah ada dengan Nomor Telepon tersebut.
- Jika **Ya**: Sistem melampirkan pesanan baru ke profil pelanggan yang sudah ada.
- Jika **Tidak**: Sistem membuat profil `Customer` baru (sebagai Guest, tanpa kata sandi).
- *Record* `Order` dibuat, dan item disimpan ke dalam `order_items`. Harga dikunci pada saat pembuatan.
- Nomor Pesanan dihasilkan (contoh: `ORD-2310-0042`).

---

## 4. Post-Order & Tracking

1. **Confirmation Page (`/track-order/{id}`)**: Segera setelah pengiriman, pelanggan diarahkan ke halaman sukses yang menampilkan Nomor Pesanan mereka. Mereka diinstruksikan untuk menyimpan nomor tersebut.
2. **Order Tracking (`/track-order`)**: Beberapa hari kemudian, pelanggan ingin memeriksa status pesanan mereka.
   - Mereka menuju halaman "Track Order".
   - Mereka memasukkan **Order Number** dan **Phone Number** mereka.
   - Jika cocok, sesi akan dibuat yang memungkinkan mereka melihat status *real-time* dari pesanan spesifik tersebut.
3. **Status Updates**: Mereka dapat melihat apakah pesanan berstatus `Pending`, `Processing`, atau `Delivering`. Mereka juga dapat melihat Status Pembayaran dan mengunduh Invoice setelah admin menyetujuinya.

---

## 5. Registration & Account Linking (Optional Upgrade)

Pelanggan tamu memutuskan untuk lebih sering memesan dan membuat akun.
1. **Register (`/register`)**: Mereka mengisi formulir registrasi, dengan catatan penting harus menggunakan **nomor telepon yang sama** dengan yang mereka gunakan sebelumnya.
2. **Automatic Claiming**: `AuthService` mendeteksi profil `Customer` Guest yang ada yang cocok dengan nomor telepon tersebut dan menautkannya ke akun `User` yang baru dibuat.
3. **My Orders (`/my-orders`)**: Saat pelanggan masuk (*login*), mereka menavigasi ke "My Orders" dan langsung melihat semua riwayat pesanan tamu mereka sebelumnya.
4. **Profile Management**: Mereka dapat memperbarui alamat *default* di profil mereka untuk mempercepat proses *checkout* di masa mendatang.