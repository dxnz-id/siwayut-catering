# Customer Flow & Journey

This document describes the end-to-end experience of a customer interacting with the Siwayut Catering application.

---

## 1. Discovery & Browsing

1. **Landing Page (`/`)**: The customer arrives at the homepage. They are greeted by a hero banner with a clear call-to-action ("Order Now" or "Explore Menu").
2. **Featured Menus**: Scrolling down, they see a grid of the most popular or recently added menus. 
3. **Filtering**: The customer can click on category badges (e.g., "Wedding", "Corporate Box") to filter the menu list.
4. **Infinite Scroll**: If there are many items, clicking "Load More" smoothly fetches and appends more menus to the page without a full reload.
5. **Menu Details (`/menu/{code}`)**: Clicking on a specific menu item opens a detailed view showing the full description, price per portion, minimum order quantity, and related items.

---

## 2. Placing an Order

1. **Order Form (`/order-form`)**: When the customer clicks "Order Now" from the menu detail page, they are taken to the central order form. The system auto-selects the menu they were viewing.
2. **Multi-Item Selection**: The customer can click "+ Add Menu" to select additional dishes from a dropdown list.
3. **Quantity Input**: For each selected menu, the customer inputs the desired quantity. The form actively validates this against the `minimum_portions` rule for that specific item.
4. **Auto-Calculation**: As menus are added or quantities change, the frontend instantly recalculates the subtotal and Estimated Grand Total.
5. **Event Details**: The customer fills in:
   - Date and Time of the event.
   - Occasion (e.g., "Birthday Party").
   - Full Delivery Address.
   - Any special notes (e.g., "Less spicy").
6. **Customer Details**: The customer provides their Full Name and Phone Number (used as their primary identifier).
7. **Submission**: The customer completes the Turnstile CAPTCHA and submits the form.

---

## 3. Order Processing (Backend Auto-Link)

When the form is submitted, the backend performs "Auto-Linking":
- It checks if a `Customer` record already exists with that Phone Number.
- If **Yes**: It attaches the new order to the existing customer profile.
- If **No**: It creates a new `Customer` profile (as a Guest, without a password).
- An `Order` record is created, and the items are saved into `order_items`. Prices are locked in at the time of creation.
- An Order Number is generated (e.g., `ORD-2310-0042`).

---

## 4. Post-Order & Tracking

1. **Confirmation Page (`/track-order/{id}`)**: Immediately after submission, the customer is redirected to a success page showing their Order Number. They are instructed to save this number.
2. **Order Tracking (`/track-order`)**: Days later, the customer wants to check their status.
   - They go to the "Track Order" page.
   - They enter their **Order Number** and their **Phone Number**.
   - If they match, a session is created allowing them to view the real-time status of that specific order.
3. **Status Updates**: They can see if the order is `Pending`, `Processing`, or `Delivering`. They can also see the Payment Status and download the Invoice once the admin approves it.

---

## 5. Registration & Account Linking (Optional Upgrade)

A guest customer decides they want to order more frequently and create an account.
1. **Register (`/register`)**: They fill out the registration form, crucially providing the **same phone number** they used previously.
2. **Automatic Claiming**: The `AuthService` detects the existing Guest `Customer` profile matching that phone number and links it to the newly created `User` account.
3. **My Orders (`/my-orders`)**: When the customer logs in, they navigate to "My Orders" and instantly see all their past guest orders populated in their history.
4. **Profile Management**: They can update their default address in their profile to speed up future checkouts.
