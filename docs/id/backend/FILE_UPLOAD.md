# File Upload & Progressive Images

Siwayut Catering mengimplementasikan sistem unggah file yang aman dikombinasikan dengan strategi Progressive Image Loading (LQIP) modern untuk memastikan performa tinggi pada landing page publik.

---

## 1. Arsitektur Penyimpanan

Semua file yang diunggah pengguna disimpan dalam sistem file lokal di dalam direktori `storage/uploads/`.
Akses ke file-file ini dirutekan melalui server bawaan PHP atau konfigurasi web server, dengan melewati eksekusi langsung (untuk mencegah unggahan PHP shell).

### Struktur Direktori
```
storage/uploads/
└── menus/
    ├── 1730000000_randomstring.jpg     (Gambar ukuran penuh)
    └── thumbs/
        └── 1730000000_randomstring.jpg (Thumbnail LQIP 20px)
```

---

## 2. Alur Unggah (FileUploadService)

`FileUploadService` mengelola seluruh siklus hidup gambar yang diunggah.

### Validasi
1. **Pemeriksaan Error:** Memverifikasi `$file['error'] === UPLOAD_ERR_OK`.
2. **Pemeriksaan Ukuran:** Membatasi ukuran file (default: 5MB).
3. **Pemeriksaan MIME:** Mengekstrak tipe MIME yang sebenarnya menggunakan `finfo_open(FILEINFO_MIME_TYPE)` dan membandingkannya dengan tipe yang diizinkan (`image/jpeg`, `image/png`, `image/webp`).

### Pemrosesan
1. Menghasilkan nama file acak yang aman: `time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension`.
2. Memindahkan file yang diunggah ke `storage/uploads/menus/`.
3. Memicu **Thumbnail Generation** secara otomatis.

---

## 3. Pembuatan Thumbnail (LQIP)

Untuk meningkatkan Largest Contentful Paint (LCP) dan pengalaman pengguna, Low-Quality Image Placeholder (LQIP) dibuat untuk setiap gambar yang diunggah.

- **Proses:** Layanan ini menggunakan library GD (`imagecreatefromjpeg`, dll.).
- **Resize:** Gambar diperkecil hingga lebar maksimum **20 piksel** dengan tetap mempertahankan rasio aspek.
- **Kompresi:** Gambar disimpan sebagai JPEG dengan kompresi tinggi (Kualitas: 30) ke dalam subdirektori `thumbs/`.
- **Ukuran File:** Thumbnail yang dihasilkan biasanya berukuran kurang dari 1KB.

---

## 4. Implementasi Frontend

Aplikasi menggunakan Vanilla JavaScript dan CSS untuk menukar thumbnail dengan gambar penuh secara halus.

### Struktur HTML
Backend merender gambar menggunakan struktur bersarang (nested) tertentu:

```html
<span class="progressive-wrap" style="aspect-ratio: 16/9;">
    <!-- SRC adalah thumbnail. data-full menyimpan path gambar asli -->
    <img class="progressive-img blur-up" 
         src="/uploads/menus/thumbs/filename.jpg" 
         data-full="/uploads/menus/filename.jpg" 
         alt="Menu Name">
</span>
```

### CSS (`progressive-image.css`)
- Class `.blur-up` menerapkan CSS `filter: blur(10px)` pada thumbnail.
- Saat gambar penuh dimuat, JavaScript menambahkan class `.loaded`, yang menganimasikan blur secara halus hingga `0` selama `0.4s`.

### JavaScript (`modules/progressive-image.js`)
`IntersectionObserver` memantau gambar progresif yang memasuki viewport.
1. Saat terlihat, ia membuat objek `Image` baru yang terlepas (detached) di memori.
2. Ia menetapkan `src` dari objek gambar tersebut ke URL `data-full`.
3. Setelah gambar penuh selesai diunduh, ia menukar `src` dari tag `<img>` yang terlihat dan menambahkan class `.loaded`.

---

## 5. Langkah-langkah Keamanan

- **Tanpa Eksekusi:** File yang diunggah tidak dapat dieksekusi sebagai skrip PHP karena nama file yang diacak dan tidak adanya izin eksekusi.
- **MIME Sniffing:** Mengandalkan `finfo`, bukan `$_FILES['type']` yang disediakan klien.
- **Perlindungan SSRF:** Metode `uploadFromUrl` memeriksa alamat IP yang di-resolve dari URL target terhadap subnet privat (misalnya, `192.168.x.x`, `10.x.x.x`) untuk mencegah serangan Server-Side Request Forgery.