# Financial Features Roadmap — Siwayut Catering

Priority levels: **P0** (core) → **P1** (improvement) → **P2** (nice to have)

---

## P0 — Core Business

### 1. Cost / COGS Tracking

**Goal:** Know profit per menu item and per order.

| File | Change |
|------|--------|
| `database/migrations/014_add_cost_price_to_menus.php` | Add `cost_price` DECIMAL(12,2) DEFAULT 0 to `menus` |
| `src/Models/Menu.php` | Add `cost_price` to fillable |
| `src/Views/menu/index.php` | Add cost column to table |
| `src/Views/menu/show.php` | Display cost + profit margin |
| `src/Services/MenuService.php` | Map cost field through service |
| `lang/en.php` + `lang/id.php` | Keys: `cost_price`, `profit_margin`, `markup` |

**Also:** `database/migrations/015_add_cost_price_to_order_items.php` — snapshot `cost_price_at_time` in `order_items` so historical profit can be calculated.

### 2. Order Profit Calculation

**Goal:** `OrderService` calculates cost per line item and stores total cost on the order.

| File | Change |
|------|--------|
| `database/migrations/016_add_cost_to_orders.php` | Add `total_cost` DECIMAL(12,2) DEFAULT 0 to `orders` |
| `src/Services/OrderService.php` | In `createOrder()`: sum `cost_price_at_time * qty` → `total_cost` |
| `src/Models/Order.php` | Add `total_cost` to fillable/sortable |
| `src/Views/order/show.php` | Display total cost + profit (total_price − total_cost) |

### 3. Dashboard KPI Page

**Goal:** Admin sees revenue snapshot when visiting `/` or `/dashboard`.

| File | Change |
|------|--------|
| `src/Controllers/DashboardController.php` | New controller with `index()` — query aggregates |
| `src/Views/dashboard/index.php` | KPI cards: total revenue, total orders, unpaid count, avg order, today's orders |
| `config/routes.php` | `GET /` → `DashboardController::index` (admin, auth) |
| `src/Models/Order.php` | Add `DashboardController` query methods for aggregation |

**KPI cards to display:**
- Total revenue (all time)
- Revenue this month
- Total orders
- Unpaid orders count
- Average order value
- Orders today
- Top 5 menu items by revenue

### 4. Revenue Report Page

**Goal:** Filterable revenue report with date range, export-ready.

| File | Change |
|------|--------|
| `src/Controllers/ReportController.php` | New controller: `revenue()` — filter date, group by period, total |
| `src/Views/report/revenue.php` | Table + chart (monthly/weekly/daily revenue) |
| `config/routes.php` | `GET /reports/revenue` → auth+admin |
| `lang/en.php` + `lang/id.php` | Keys: `revenue`, `report`, `date_from`, `date_to`, `apply_filter`, `export_csv` |

---

## P1 — Improvements

### 5. Invoice / Receipt Number

**Goal:** Every order gets a formal invoice number (separate from `order_number`).

| File | Change |
|------|--------|
| `database/migrations/017_add_invoice_number_to_orders.php` | Add `invoice_number` VARCHAR(50) nullable to `orders`; auto-generate on `paid` status change |
| `src/Services/OrderService.php` | In `updateOrder()`: if `payment_status` changed to `paid`, generate invoice number |
| `src/Views/order/show.php` | Display invoice number, add "Print Receipt" button |

### 6. Tax (PPN) Support

**Goal:** Apply VAT/tax to orders for proper billing.

| File | Change |
|------|--------|
| `database/migrations/018_add_tax_to_orders.php` | Add `tax_rate` DECIMAL(5,2) DEFAULT 11, `tax_amount` DECIMAL(12,2) DEFAULT 0, `grand_total` DECIMAL(12,2) to `orders` |
| `src/Services/OrderService.php` | `createOrder()`: calculate `tax_amount = (total_price - discount) * tax_rate / 100`, `grand_total = total_price - discount + tax_amount` |
| `src/Views/order/show.php` | Display tax breakdown |
| `lang/en.php` + `lang/id.php` | Keys: `tax`, `ppn`, `tax_rate`, `grand_total`, `subtotal_before_tax` |

### 7. Discount Support

**Goal:** Apply discounts to orders (percentage or fixed amount).

| File | Change |
|------|--------|
| `database/migrations/019_add_discount_to_orders.php` | Add `discount_type` ENUM('none','percentage','fixed') DEFAULT 'none', `discount_value` DECIMAL(12,2) DEFAULT 0, `discount_amount` DECIMAL(12,2) DEFAULT 0 to `orders` |
| `src/Controllers/OrderController.php` | Add discount fields to `update()` validation + merge logic |
| `src/Views/order/show.php` | Edit modal: discount fields; display: discount line item |

### 8. Payment Method & Date

**Goal:** Track how and when customers pay.

| File | Change |
|------|--------|
| `database/migrations/020_add_payment_details_to_orders.php` | Add `payment_method` ENUM('cash','transfer','qris','other') DEFAULT 'cash', `paid_at` DATETIME nullable |
| `src/Controllers/OrderController.php` | `update()`: set `paid_at` when status changes to `paid` |
| `src/Views/order/show.php` | Display + edit payment method |
| `src/Views/order/index.php` | Add payment method column |
| `lang/en.php` + `lang/id.php` | Keys: `payment_method`, `cash`, `transfer`, `qris`, `paid_at` |

### 9. Down Payment / Deposit

**Goal:** Support DP payments (common for catering).

| File | Change |
|------|--------|
| `database/migrations/021_add_dp_to_orders.php` | Add `down_payment` DECIMAL(12,2) DEFAULT 0, `down_payment_due` DATE nullable, `remaining_balance` DECIMAL(12,2) DEFAULT 0 |
| `src/Services/OrderService.php` | Calculate remaining balance on create/update |
| `src/Views/order/show.php` | Display DP info, add DP tracking to payment workflow |
| `lang/en.php` + `lang/id.php` | Keys: `down_payment`, `dp`, `remaining_balance`, `dp_due_date` |

### 10. Menu Revenue Report

**Goal:** See which menu items generate the most revenue.

| File | Change |
|------|--------|
| `src/Controllers/ReportController.php` | Add `menuRevenue()` action |
| `src/Views/report/menu-revenue.php` | Table: menu name, total qty sold, total revenue, total cost, profit, margin % |
| `config/routes.php` | `GET /reports/menu-revenue` |
| `src/Models/Menu.php` | Revenue aggregation query (SUM of `price_at_time * qty`) |

---

## P2 — Nice to Have

### 11. Order Item Editing

**Goal:** Admin can add/remove/change items after order creation.

| File | Change |
|------|--------|
| `src/Services/OrderService.php` | Add `updateOrderItems()` — delete + re-insert items; recalculate `total_price`, `total_cost`, `grand_total` |
| `src/Views/order/show.php` | Add edit button per item row, or a modal |

### 12. Printable Receipt / Invoice

**Goal:** Generate a printable or PDF version of the order.

| File | Change |
|------|--------|
| `src/Views/order/receipt.php` | Clean, print-friendly template (no sidebar/navbar) |
| `src/Controllers/OrderController.php` | Add `receipt()` action — renders receipt view |
| `config/routes.php` | `GET /orders/{id}/receipt` |
| Use CSS `@media print` for print layout |

### 13. CSV Export

**Goal:** Export order list or revenue report to CSV.

| File | Change |
|------|--------|
| `src/Controllers/ReportController.php` | Add `exportCsv()` — set headers, stream CSV |
| Common utility: columns, date range, filters |

### 14. Public Form Running Total

**Goal:** Customers see total price update as they select items.

| File | Change |
|------|--------|
| `src/Views/order/public-form.php` | Add JS calculation, display dynamic total below menu items |
| `public/assets/js/app.js` or inline script | Listen to quantity/menu changes, recalculate |

---

## Execution Order (Recommended)

```
Phase 1 (Core — 3–5 days)
├── P0.1 Cost / COGS
├── P0.2 Order Profit
├── P0.3 Dashboard KPI
└── P0.4 Revenue Report

Phase 2 (Billing — 3–4 days)
├── P1.5 Invoice Number
├── P1.6 Tax (PPN)
├── P1.7 Discount
└── P1.8 Payment Method & Date

Phase 3 (Polish — 2–3 days)
├── P1.9 Down Payment
├── P1.10 Menu Revenue Report
├── P2.11 Order Item Editing
├── P2.12 Printable Receipt
├── P2.13 CSV Export
└── P2.14 Public Form Total
```
