# Controllers Reference

Siwayut Catering uses a thin-controller architecture. Controllers are strictly responsible for receiving HTTP requests, validating input, passing data to the Service Layer, and returning a Response (HTML view, JSON, or Redirect). **Business logic should not reside in the controller.**

All controllers inherit from `BaseController`.

---

## BaseController

Located at `src/Controllers/BaseController.php`.

### Key Methods
- `protected function render(string $view, array $data = [], string $layout = 'main'): void`
  - Loads a PHP template from `src/Views/`.
  - Automatically injects shared data (e.g., flash messages, active route, current user).
  - Valid layouts: `main` (admin dashboard), `auth` (login screen), `public` (landing page layout overrides).
- `protected function redirect(string $url, int $code = 302): never`
- `protected function redirectWithFlash(string $url, string $key, string $message): never`
- `protected function withOldInput(array $data): void`
  - Flashes failed form submission data to the session so inputs can be repopulated.
- `protected function currentUser(): ?array`
  - Helper to get the authenticated user from the session.

---

## Public Controllers

### WelcomeController
Handles the public-facing landing page and API for loading menus.
- `index(Request $request)` → `GET /` — Renders the landing page.
- `publicShow(Request $request)` → `GET /menu/{code}` — Displays the detailed view for a single menu.
- `apiMenus(Request $request)` → `GET /api/menus` — JSON API for infinite scrolling menus.

### AuthController
Handles authentication and registration.
- `index(Request $request)` → `GET /login` — Shows the login form.
- `login(Request $request)` → `POST /login` — Authenticates a user.
- `logout(Request $request)` → `POST /logout` — Destroys the session.
- `register(Request $request)` → `POST /register` — Handles customer registration.

---

## Admin Controllers

These controllers require the `auth` and `role:admin` middleware. They typically implement standard CRUD operations.

### DashboardController
- `index(Request $request)` → `GET /dashboard` — Aggregates data from `OrderService` to display KPI cards, charts, and top menus.

### MenuController
Injected with `MenuService`, `CategoryService`, `EventService`.
- `index` → List menus (paginated).
- `create` / `store` → Form to add a menu and process the insertion (including image upload).
- `edit` / `update` → Form to modify a menu.
- `destroy` → Delete a menu.
- `apiShow` → Fetch menu data as JSON (used for inline editing/modals).
- `generateDescription(Request $request)` → `POST /menus/generate-description` — Proxies request to `AiService` for AI-generated text.

### OrderController
Split into Public (for guests/customers) and Admin sections.
**Public:**
- `publicCreate` / `publicStore` → The public multi-item order form.
- `trackOrder` / `processTrackOrder` → The order tracking page.
- `myOrders` → Authenticated customer order history.
**Admin:**
- `index` → Admin order list with filters.
- `show` → Order detail page.
- `updateStatus` / `updatePayment` → Admin mutations for order state.
- `updateAdminNotes` → Update internal admin notes, invoice, tax, and discount.
- `printReceipt` → Generates a printable receipt view.

### CategoryController & EventController
Standard CRUD wrappers for Category and Event management.
- Provide `index`, `store`, `update`, `destroy`, and `apiShow` for AJAX interactions.

### UserController
Injected with `UserService`. Manages both `admin` and `user` (staff) accounts.
- Similar CRUD structure. Includes logic to prevent an admin from deleting themselves.

### ReportController
Handles data analysis and CSV exports.
- `revenue(Request $request)` → Renders the revenue report with date filters.
- `exportRevenue(Request $request)` → Forces a CSV download of the revenue report.
- `menuRevenue(Request $request)` → Renders profitability analysis per menu.
- `exportMenuRevenue(Request $request)` → Forces a CSV download of the menu profitability.

### ProfileController
Allows the currently logged-in admin to manage their own account.
- `index` → Shows the profile form.
- `update` → Updates name/email.
- `changePassword` → Validates old password and sets a new one.

---

## Dependency Injection

The application uses an automatic, reflection-based Dependency Injection Container (configured in `config/bindings.php` and `src/Core/Container.php`). 

When a route is dispatched, the router asks the Container to instantiate the target Controller. The Container reads the Controller's constructor parameters (e.g., `__construct(private OrderService $orderService)`) and automatically instantiates and injects the required Services.
