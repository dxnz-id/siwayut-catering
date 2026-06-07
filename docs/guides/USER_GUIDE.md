# User Guide (Admin Manual)

This guide explains how to use the Siwayut Catering Admin Dashboard to manage your day-to-day operations.

## 1. Accessing the Dashboard
- Navigate to `your-domain.com/login` (or `/auth`).
- Enter your admin email and password. (Default seeded admin: `admin@admin.com` / `password`).
- Upon successful login, you will be redirected to the Dashboard overview.

---

## 2. Dashboard Overview
The dashboard provides a bird's-eye view of your business:
- **KPI Cards:** Shows Total Orders, Total Revenue, Total Profit, and Average Order Value.
- **Revenue Chart:** A 7-day line chart showing revenue trends.
- **Order Status:** A doughnut chart showing how many orders are currently pending, processing, delivering, etc.
- **Top Menus:** A list of your 5 best-selling menu items.

---

## 3. Managing Menus

Menus are the core of your application.

### Creating a New Menu
1. Go to **Menus** in the sidebar and click **Add Menu**.
2. Fill in the required details: Name, Category, Event Type.
3. Set the **Price** (what the customer pays) and **Cost Price** (your internal cost to produce it). This is vital for accurate profit reporting.
4. **Image Upload:** Drag and drop an image. The system will automatically optimize it.
5. **AI Description:** If you are stuck writing a description, click the **"Generate AI Description"** button. The system will write an appetizing description based on the menu name and category.
6. Click **Save**.

### Managing Categories & Events
Before creating menus, ensure you have appropriate Categories (e.g., Main Course, Dessert) and Events (e.g., Wedding, Corporate). 
- Navigate to **Categories** or **Events** in the sidebar.
- You can add new ones, or edit existing ones directly using the inline popup modals.

---

## 4. Managing Orders

When a customer places an order on the public website, it appears here.

### Viewing Orders
1. Go to **Orders** in the sidebar.
2. Use the filters at the top to narrow down the list (e.g., show only "Pending" orders, or search by Order Number `ORD-...`).
3. Click the **Eye icon** (View) to see full order details.

### Updating Order Status
Orders flow through a specific lifecycle:
`Pending` → `Processing` → `Delivering` → `Completed`
1. Open an Order's detail page.
2. Under "Update Status", select the new status from the dropdown.
3. Click **Update Status**. 
*(Note: Moving an order to 'Processing' automatically generates an Invoice Number).*

### Managing Payments, Taxes, and Discounts
1. On the Order detail page, scroll to the "Admin Action" section.
2. You can update the **Payment Status** (Unpaid, Partial, Paid) and add a **Payment Method/Reference**.
3. You can apply a **Discount** (either a fixed amount or a percentage).
4. You can apply a **Tax Rate** (e.g., 10% or 11%).
5. The Grand Total will recalculate automatically.

### Printing Receipts
Click the **Print Receipt** button at the top of the Order detail page to generate a clean, printer-friendly invoice for the customer.

---

## 5. Reports

### Revenue Report
1. Go to **Reports > Revenue**.
2. Select a Date Range (Start Date and End Date).
3. View the daily breakdown of orders, revenue, and profit.
4. Click **Export CSV** to download the data for Excel or accounting software.

### Menu Revenue Report
1. Go to **Reports > Menu Revenue**.
2. This report shows the profitability of individual menu items over a selected period.
3. You can see how many portions were sold, total revenue generated, and the exact profit margin percentage.

---

## 6. System Settings

### Managing Users
- Go to **Users**. Here you can create additional accounts for your staff.
- Assign the `admin` role to give them full access to the dashboard.

### Updating Your Profile
- Click your name in the top right corner and select **Profile**.
- Here you can update your name, email, and change your password.
