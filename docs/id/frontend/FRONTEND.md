# Arsitektur Frontend

Siwayut Catering menggunakan stack frontend yang modern dan ringan tanpa framework JavaScript yang berat (seperti React atau Vue). Aplikasi ini mengandalkan Vanilla JavaScript dan Tailwind CSS v4 untuk menghadirkan antarmuka pengguna yang cepat, dinamis, dan responsif.

## Tech Stack
- **Styling:** Tailwind CSS v4 (`@tailwindcss/cli`)
- **JavaScript:** Vanilla JS (ES6 Modules)
- **Icons:** Phosphor Icons (dimuat melalui CDN)
- **Charts:** Chart.js (untuk analitik dashboard)

---

## Arsitektur CSS

CSS diatur ke dalam file modular yang terletak di `public/assets/css/` dan dikompilasi oleh Tailwind menjadi satu file `app.css`.

### Source Files

1. **`input.css`**
   Titik masuk utama. Mengimpor tema Tailwind (`@theme`) dan semua modul CSS kustom.
2. **`tokens.css`**
   Mendefinisikan variabel CSS inti untuk palet warna, spasi, dan border radii.
   - Contoh warna: `--color-primary` (Emas), `--color-bg` (Gelap), `--color-surface` (Latar belakang kartu).
3. **`base.css`**
   Reset global dan pengaturan tipografi (misalnya, menstandarisasi font `html`, `body`).
4. **`utilities.css`**
   Utility class kustom yang tidak dicakup oleh Tailwind secara default, seperti `glassmorphism` dan `text-gradient`.
5. **Components**
   - `components/file-upload.css`: Styling untuk zona drag-and-drop unggah file.
   - `components/progressive-image.css`: Efek transisi blur untuk LQIP.
6. **Pages**
   - `pages/landing.css`: Animasi spesifik dan override untuk halaman landing publik (parallax, floating blobs).

### Build Pipeline
Tailwind CSS v4 tidak menggunakan `tailwind.config.js`. Sebagai gantinya, variabel tema didefinisikan langsung di dalam CSS menggunakan direktif `@theme`.

**Untuk mengompilasi CSS untuk produksi:**
```bash
npm run css:build
```

**Untuk memantau perubahan selama pengembangan:**
```bash
npm run css:watch
```
*Catatan: Perintah ini harus dijalankan di tab terminal terpisah bersamaan dengan `php vanilla serve`, atau Anda dapat menjalankan `npm run dev` untuk menjalankan keduanya secara bersamaan.*

---

## Modul JavaScript

Kode JavaScript dimodularisasi di dalam `public/assets/js/modules/` dan diorkestrasi oleh `public/assets/js/app.js`.

### 1. `app.js` (Main Entry)
- Menginisialisasi tooltip, dropdown, dan event listener global.
- Menangani smooth scrolling dan efek parallax pada halaman landing.

### 2. `toast.js`
- Menyediakan sistem notifikasi kustom (popup sukses/error) yang menghilang secara otomatis setelah beberapa detik.
- Menggantikan `alert()` standar.

### 3. `modal.js` & `create-modal.js`
- Mengelola pembukaan, penutupan, dan data-binding dialog modal (digunakan secara intensif di dashboard admin untuk mengedit data tanpa meninggalkan halaman).

### 4. `progressive-image.js`
- Mengimplementasikan lazy-loading untuk gambar.
- Menggunakan `IntersectionObserver` untuk menukar thumbnail kualitas rendah (src) dengan gambar resolusi tinggi (`data-full`) hanya saat gambar memasuki viewport.

### 5. `file-upload.js`
- Meningkatkan elemen `<input type="file">` standar.
- Menyediakan dukungan drag-and-drop, validasi ukuran/tipe file di sisi klien, dan pratinjau gambar secara real-time.

### 6. `load-more-menu.js`
- Digunakan pada halaman landing untuk mengimplementasikan tombol "Infinite Scroll" atau "Load More".
- Mengambil HTML atau JSON terpaginasi dari `/api/menus` dan menambahkannya ke DOM.

### 7. `ai-description.js`
- Menangani permintaan AJAX ke endpoint backend `/menus/generate-description`.
- Menampilkan loading spinner dan mengisi textarea dengan konten AI yang dihasilkan.

### 8. `dashboard-charts.js`
- Menginisialisasi instance Chart.js pada dashboard admin (Revenue Line Chart dan Order Status Doughnut Chart).

### 9. `turnstile.js`
- Menangani inisialisasi dan validasi widget Cloudflare Turnstile untuk perlindungan spam pada formulir publik.

---

## Prinsip Design System

- **Default Dark Theme:** Aplikasi menggunakan mode gelap yang elegan (latar belakang `#09090b`) agar terlihat premium.
- **Gold Accents:** Tombol utama dan highlight menggunakan rona emas/oranye yang khas (`#e58e26`) untuk merangsang selera dan menandakan keanggunan.
- **Glassmorphism:** Penggunaan latar belakang transparan secara ekstensif dengan backdrop-filter (`backdrop-filter: blur(12px)`) untuk kartu, navbar, dan sidebar guna menciptakan kedalaman.
- **Micro-interactions:** Tombol dan kartu sedikit membesar saat di-hover, dan transisi status dibuat halus.