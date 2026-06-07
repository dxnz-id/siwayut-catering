# File Upload & Progressive Images

Siwayut Catering implements a secure file upload system combined with a modern Progressive Image Loading (LQIP) strategy to ensure high performance on the public landing page.

---

## 1. Storage Architecture

All user-uploaded files are stored in the local filesystem inside the `storage/uploads/` directory. 
Access to these files is routed through the PHP built-in server or web server configuration, bypassing direct execution (to prevent PHP shell uploads).

### Directory Structure
```
storage/uploads/
└── menus/
    ├── 1730000000_randomstring.jpg     (Full size image)
    └── thumbs/
        └── 1730000000_randomstring.jpg (20px LQIP thumbnail)
```

---

## 2. Upload Flow (FileUploadService)

The `FileUploadService` manages the entire lifecycle of an uploaded image.

### Validation
1. **Error Check:** Verifies `$file['error'] === UPLOAD_ERR_OK`.
2. **Size Check:** Limits file size (default: 5MB).
3. **MIME Check:** Extracts the true MIME type using `finfo_open(FILEINFO_MIME_TYPE)` and compares it against allowed types (`image/jpeg`, `image/png`, `image/webp`).

### Processing
1. Generates a safe, random filename: `time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension`.
2. Moves the uploaded file to `storage/uploads/menus/`.
3. Automatically triggers **Thumbnail Generation**.

---

## 3. Thumbnail Generation (LQIP)

To improve Largest Contentful Paint (LCP) and user experience, a Low-Quality Image Placeholder (LQIP) is generated for every uploaded image.

- **Process:** The service uses the GD library (`imagecreatefromjpeg`, etc.).
- **Resize:** The image is scaled down to a maximum width of **20 pixels** while maintaining the aspect ratio.
- **Compression:** It is saved as a highly compressed JPEG (Quality: 30) into the `thumbs/` subdirectory.
- **File Size:** The resulting thumbnail is typically less than 1KB.

---

## 4. Frontend Implementation

The application uses Vanilla JavaScript and CSS to swap the thumbnail for the full image smoothly.

### HTML Structure
The backend renders images using a specific nested structure:

```html
<span class="progressive-wrap" style="aspect-ratio: 16/9;">
    <!-- The SRC is the thumbnail. data-full holds the real image path -->
    <img class="progressive-img blur-up" 
         src="/uploads/menus/thumbs/filename.jpg" 
         data-full="/uploads/menus/filename.jpg" 
         alt="Menu Name">
</span>
```

### CSS (`progressive-image.css`)
- The `.blur-up` class applies a CSS `filter: blur(10px)` to the thumbnail.
- When the full image loads, JavaScript adds the `.loaded` class, which smoothly animates the blur down to `0` over `0.4s`.

### JavaScript (`modules/progressive-image.js`)
An `IntersectionObserver` watches for progressive images entering the viewport.
1. When visible, it creates a new detached `Image` object in memory.
2. It sets the `src` of the detached image to the `data-full` URL.
3. Once the full image finishes downloading, it swaps the `src` of the visible `<img>` tag and adds the `.loaded` class.

---

## 5. Security Measures

- **No Execution:** Uploaded files cannot be executed as PHP scripts due to the randomized filename and missing execution permissions.
- **MIME Sniffing:** Relies on `finfo`, not the client-provided `$_FILES['type']`.
- **SSRF Protection:** The `uploadFromUrl` method checks the resolved IP address of the target URL against private subnets (e.g., `192.168.x.x`, `10.x.x.x`) to prevent Server-Side Request Forgery attacks.
