# Frontend Architecture

Siwayut Catering uses a modern, lightweight frontend stack without heavy JavaScript frameworks (like React or Vue). It relies on Vanilla JavaScript and Tailwind CSS v4 to deliver a fast, dynamic, and responsive user interface.

## Tech Stack
- **Styling:** Tailwind CSS v4 (`@tailwindcss/cli`)
- **JavaScript:** Vanilla JS (ES6 Modules)
- **Icons:** Phosphor Icons (loaded via CDN)
- **Charts:** Chart.js (for dashboard analytics)

---

## CSS Architecture

The CSS is organized into modular files located in `public/assets/css/` and compiled by Tailwind into a single `app.css`.

### Source Files

1. **`input.css`**
   The main entry point. Imports the Tailwind theme (`@theme`) and all custom CSS modules.
2. **`tokens.css`**
   Defines core CSS variables for the color palette, spacing, and border radii.
   - Example colors: `--color-primary` (Gold), `--color-bg` (Dark), `--color-surface` (Card background).
3. **`base.css`**
   Global resets and typography settings (e.g., standardizing `html`, `body` fonts).
4. **`utilities.css`**
   Custom utility classes not covered by Tailwind by default, such as `glassmorphism` and `text-gradient`.
5. **Components**
   - `components/file-upload.css`: Styling for the drag-and-drop file upload zone.
   - `components/progressive-image.css`: Blur transition effects for LQIP.
6. **Pages**
   - `pages/landing.css`: Specific animations and overrides for the public landing page (parallax, floating blobs).

### Build Pipeline
Tailwind CSS v4 does not use a `tailwind.config.js`. Instead, theme variables are defined directly in CSS using the `@theme` directive.

**To compile CSS for production:**
```bash
npm run css:build
```

**To watch for changes during development:**
```bash
npm run css:watch
```
*Note: This must be run in a separate terminal tab alongside `php vanilla serve`, or you can run `npm run dev` to run both concurrently.*

---

## JavaScript Modules

The JavaScript code is modularized inside `public/assets/js/modules/` and orchestrated by `public/assets/js/app.js`.

### 1. `app.js` (Main Entry)
- Initializes tooltips, dropdowns, and global event listeners.
- Handles smooth scrolling and parallax effects on the landing page.

### 2. `toast.js`
- Provides a custom notification system (success/error popups) that automatically disappear after a few seconds.
- Replaces standard `alert()`.

### 3. `modal.js` & `create-modal.js`
- Manages the opening, closing, and data-binding of modal dialogs (used heavily in the admin dashboard for editing records without leaving the page).

### 4. `progressive-image.js`
- Implements lazy-loading for images.
- Uses `IntersectionObserver` to swap the low-quality thumbnail (src) with the high-resolution image (`data-full`) only when the image enters the viewport.

### 5. `file-upload.js`
- Enhances standard `<input type="file">` elements.
- Provides drag-and-drop support, file size/type validation on the client side, and real-time image previews.

### 6. `load-more-menu.js`
- Used on the landing page to implement an "Infinite Scroll" or "Load More" button.
- Fetches paginated HTML or JSON from `/api/menus` and appends it to the DOM.

### 7. `ai-description.js`
- Handles the AJAX request to the backend `/menus/generate-description` endpoint.
- Displays a loading spinner and populates the textarea with the generated AI content.

### 8. `dashboard-charts.js`
- Initializes Chart.js instances on the admin dashboard (Revenue Line Chart and Order Status Doughnut Chart).

### 9. `turnstile.js`
- Handles the initialization and validation of the Cloudflare Turnstile widget for spam protection on public forms.

---

## Design System Principles

- **Dark Theme Default:** The application defaults to a sleek dark mode (`#09090b` background) to look premium.
- **Gold Accents:** Primary buttons and highlights use a distinct gold/orange hue (`#e58e26`) to stimulate appetite and signify elegance.
- **Glassmorphism:** Heavy use of translucent backgrounds with backdrop-filters (`backdrop-filter: blur(12px)`) for cards, navbars, and sidebars to create depth.
- **Micro-interactions:** Buttons and cards scale slightly on hover, and state transitions are smooth.
