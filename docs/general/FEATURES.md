# Features

This document outlines the complete feature set of the Siwayut Catering application from an end-user perspective.

## User Roles

The application supports three types of users:

1. **Guest:** Unauthenticated users who can browse menus, place orders, and track order status.
2. **Customer:** Registered users who can manage their profile and view their order history.
3. **Admin:** Authenticated staff members who manage menus, orders, reports, and system settings via the dashboard.

---

## Public Features

These features are available to Guests and Customers on the public-facing side of the application.

### Landing Page
- **Food Gallery & Hero Section:** Engaging landing area with parallax effects and clear call-to-action buttons.
- **Featured Menus:** Showcase of active menus with lazy-loaded progressive images (LQIP) for fast performance.
- **Infinite Scroll:** "Load More" functionality for browsing menus seamlessly via an AJAX API (`/api/menus`).
- **Category Showcase:** Menus can be filtered by specific categories.

### Menu Detail Page
- **Detailed View:** Displays full menu description, pricing, minimum portion requirements, and associated categories/events.
- **Related Menus:** Shows up to 3 related menus to encourage upselling.

### Order System
- **Multi-Item Order Form:** Customers can select multiple menus in a single order and set individual quantities.
- **Validation:** Enforces minimum portion requirements for each selected menu.
- **Delivery Details:** Collects delivery address, event date, occasion, and special notes.
- **Customer Auto-Link:** Automatically links the order to an existing customer record if the phone number matches, or creates a new customer profile.
- **Spam Protection:** Integrates Cloudflare Turnstile CAPTCHA on the order submission form.

### Order Tracking
- **Secure Tracking:** Customers can track their order status by entering their Order Number (`ORD-YYMM-XXXX`) and matching Phone Number.
- **Session-Based Access:** Once verified, the customer can view the tracking details securely.

### Customer Portal
- **Registration & Login:** Customers can register an account (with brute-force protection).
- **Order History (`/my-orders`):** Authenticated customers can view a list of all their past and current orders.
- **Profile Management:** Customers can update their name, phone, address, and password.

### Localization
- **Language Switcher:** Supports English (`en`) and Indonesian (`id`).
- **Auto-Detect:** Automatically detects the browser's preferred language on the first visit.

---

## Admin Features

These features are restricted to users with the `admin` role and are accessible via the `/auth` login.

### Dashboard
- **KPI Cards:** Overview of Total Orders, Total Revenue, Total Profit, and Average Order Value.
- **Revenue Chart:** Visual representation of revenue trends over the last 7 days using Chart.js.
- **Top Menus:** Lists the 5 best-selling menus.
- **Status Breakdown:** Doughnut chart showing the distribution of order statuses (Pending, Processing, Delivered, etc.).

### Menu Management
- **CRUD Operations:** Create, read, update, and delete menu items.
- **Auto Menu Codes:** Automatically generates unique `MNU-XXXX` codes.
- **Image Upload:** Upload menu images with automatic 20px thumbnail generation (for LQIP) and strict size/MIME validation.
- **AI Description Generation:** Click a button to automatically generate an appetizing menu description in Indonesian using an integrated AI API (Gemini/OpenAI).
- **Costing:** Track both selling `price` and internal `cost_price` to calculate profit margins.

### Order Management
- **Order List:** View all orders with filters for Status, Payment Status, and search by Order Number or Customer Name.
- **Status Workflow:** Update order status through the lifecycle: `pending` → `processing` → `delivering` → `completed` (or `cancelled`).
- **Payment Tracking:** Update payment status (`unpaid`, `partial`, `paid`) and record payment methods/references.
- **Invoice Generation:** Automatically generates an Invoice Number when an order is confirmed.
- **Tax & Discount:** Apply flat or percentage-based discounts and calculate tax amounts.
- **Receipt Printing:** Generate a printable receipt/invoice view for customers.
- **CSV Export:** Export filtered order lists to CSV for external processing.

### Event & Category Management
- **Categories:** Manage menu categories (e.g., Main Course, Dessert) and view the number of menus assigned to each.
- **Events:** Manage event types (e.g., Wedding, Corporate) with start/end dates and active/inactive toggles.

### Reporting
- **Revenue Report:** View daily revenue, orders, and profit within a specific date range. Includes CSV export.
- **Menu Revenue Report:** Analyze profitability per menu item, showing total quantity sold, revenue, profit, and profit margin percentage. Includes CSV export.

### User Management
- **Staff Accounts:** Manage admin and user accounts.
- **Role Assignment:** Assign `admin` or `user` roles to restrict access to the dashboard.
- **Profile:** Admins can edit their own profile and change passwords securely.
