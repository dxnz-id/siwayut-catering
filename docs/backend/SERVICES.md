# Services Reference

The Service Layer encapsulates all business logic. Controllers should extract input, pass it to a Service, and format the output. Services interact with Models and other external APIs.

## Architecture

Services are located in `src/Services/`. They are instantiated automatically by the Dependency Injection Container and injected into Controllers.

---

## 1. AuthService
Handles the complexities of authentication.

- **Dependencies:** `User` model, `Customer` model.
- **Methods:**
  - `login(string $email, string $password): bool`
    - Validates credentials against password hashes.
    - Implements **progressive delay** (anti-brute-force): If a login fails, it delays the response based on the number of previous failed attempts stored in the session.
    - Sets session variables (`user_id`, `role`, `name`).
  - `logout(): void` — Destroys the session.
  - `register(array $data): int`
    - Creates a new `User` record.
    - Checks if a `Customer` record already exists with the provided phone number. If so, it links the new User ID to the existing Customer record.

---

## 2. OrderService
The largest and most complex service (400+ lines), orchestrating the entire order lifecycle.

- **Dependencies:** `Order`, `Customer`, `Menu` models.
- **Methods:**
  - `createOrder(array $data, ?int $userId = null): int`
    - Calculates totals accurately by fetching the real-time `price` from the `Menu` model (prevents frontend price manipulation).
    - Creates the `Customer` record if it doesn't exist.
    - Creates the parent `Order` record (auto-generating the `ORD-XXXX` number).
    - Inserts multiple `order_items` records and calculates subtotals.
  - `updateOrder(int $id, array $data): bool`
    - Handles status transitions. If status moves to `processing`, it automatically generates an `invoice_number` if one doesn't exist.
    - Recalculates the `grand_total` if `tax` or `discount` values are updated.
  - **Analytics Methods (used by Dashboard & Reports):**
    - `getKpis()`: Returns Total Orders, Revenue, Profit, and Avg Order Value.
    - `getTopMenus(int $limit = 5)`: Aggregates order items to find best sellers.
    - `getRevenueChartData()`: Calculates the last 7 days of revenue for Chart.js.
    - `getOrderStatusBreakdown()`: Groups orders by status.
    - `getRevenueByPeriod(string $startDate, string $endDate)`: Generates the detailed revenue report.
    - `getMenuRevenueReport(string $startDate, string $endDate)`: Calculates profit and margin per menu item.

---

## 3. MenuService
Handles menu CRUD and coordinates image uploads.

- **Dependencies:** `Menu` model, `FileUploadService`.
- **Methods:**
  - `create(array $data, ?array $file = null): int` — If an image file is provided, calls `FileUploadService` to store it before creating the database record.
  - `update(int $id, array $data, ?array $file = null): bool` — Handles deleting the old image and uploading a new one if a new file is provided.
  - `delete(int $id): bool` — Ensures the associated image file and thumbnail are deleted from disk when a menu is removed.

---

## 4. FileUploadService
Manages local file storage for images.

- **Storage Location:** `storage/uploads/`
- **Methods:**
  - `uploadImage(array $file, string $directory = 'menus'): string`
    - Validates MIME type (`image/jpeg`, `image/png`, `image/webp`) and size.
    - Generates a random secure filename.
    - Moves the file and calls `generateThumbnail()`.
  - `generateThumbnail(string $filePath, string $directory): void`
    - Creates a highly compressed, 20px wide Low-Quality Image Placeholder (LQIP).
    - Saves it in `storage/uploads/{directory}/thumbs/`.
  - `uploadFromUrl(string $url, string $directory = 'menus'): string`
    - Downloads an external image via cURL.
    - Includes **SSRF Protection**: Validates that the target IP is not within private/local ranges (`127.0.0.0/8`, `10.0.0.0/8`, etc.).
  - `deleteFile(string $path): bool` — Deletes both the main image and its thumbnail.

---

## 5. AiService
Integrates with external LLMs to generate menu descriptions.

- **Configuration:** Reads `AI_API_URL`, `AI_API_KEY`, and `AI_MODEL` from `.env`. (Compatible with OpenAI, Google Gemini OpenAI wrapper, or local Ollama).
- **Methods:**
  - `generateMenuDescription(array $menuData): string`
    - Constructs a system prompt defining the persona ("expert food copywriter").
    - Sends a cURL request to the AI endpoint.
    - Parses the JSON response and returns the generated text.

---

## 6. CategoryService, EventService, UserService, ProfileService
These are thinner wrapper services that map directly to their respective models, providing standard CRUD abstraction and validation logic.
