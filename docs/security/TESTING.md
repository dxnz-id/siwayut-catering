# Testing & Quality Assurance

Currently, Siwayut Catering does not include an automated testing framework (like PHPUnit or Pest). Quality assurance relies on a strict manual testing protocol and defensive programming practices (like strong typing and strict validation).

This document outlines the testing strategy and the manual QA checklist to be executed before deploying major changes.

---

## 1. Defensive Programming

To mitigate the lack of automated tests, the codebase enforces:
- `declare(strict_types=1);` in every PHP file.
- Explicit return types for all methods (e.g., `function foo(): array`).
- Prepared statements for all database interactions to prevent SQL injection.
- Centralized validation via the `Validator` class before any database mutation.

---

## 2. Manual QA Checklist

Before merging to `main` or deploying to production, run through this checklist in a local development environment (`php vanilla serve`).

### 2.1 Public & Guest Flows
- [ ] **Landing Page**: Verify menus load, images display (progressive blur works), and "Load More" appends data correctly.
- [ ] **Language Switcher**: Toggle between ID and EN. Ensure translated strings update without crashing.
- [ ] **Order Form Validation**: 
  - Try submitting empty fields.
  - Try submitting quantities below the `minimum_portions`.
  - Try submitting without completing the CAPTCHA (if enabled).
- [ ] **Order Placement**: Successfully place a multi-item order. Verify the Estimated Total matches the final database `grand_total`.
- [ ] **Order Tracking**: Use the generated Order Number and Phone Number to track the order. Try with an incorrect phone number to verify rejection.

### 2.2 Customer Auth Flows
- [ ] **Registration**: Register a new user. 
- [ ] **Account Linking**: Register a new user using a phone number that already has guest orders. Verify the old orders appear in `/my-orders`.
- [ ] **Login & Brute Force**: Try logging in with the wrong password 5 times. Verify the progressive delay (response gets slower) or lockout mechanism triggers.
- [ ] **Session Timeout**: Log in, wait past the session lifetime (or artificially expire the cookie), and ensure the next action redirects to the login page.

### 2.3 Admin Dashboard
- [ ] **Menu CRUD**: 
  - Create a menu with an image upload. Verify the image and thumbnail are saved to disk.
  - Generate an AI description (requires internet/API key).
  - Edit the menu, upload a *new* image, and verify the old image file is deleted from disk.
  - Delete the menu. Verify the file is deleted.
- [ ] **Order Workflow**:
  - Open a 'Pending' order.
  - Change status to 'Processing'. Verify an Invoice Number is generated.
  - Add a flat discount and a percentage tax. Verify the Grand Total recalculates correctly.
  - Change Payment Status to 'Paid'.
  - Click 'Print Receipt' and verify the layout.
- [ ] **Reports**:
  - Load the Revenue Report for a specific date range.
  - Export CSV and open it in a spreadsheet application to verify column alignment.

### 2.4 Security Checks
- [ ] **CSRF**: Open a form (e.g., Edit Profile), modify the hidden `_token` value using Chrome DevTools, and submit. Verify it throws a 403 CSRF mismatch error.
- [ ] **Access Control**: Log in as a regular Customer (`role: user`). Attempt to access `/dashboard` or `/orders` directly via URL. Verify it redirects with an unauthorized error.

---

## 3. Future Testing Strategy

Implementing automated testing is the highest priority for technical debt reduction. 

**Planned Stack:**
1. **PHPUnit**: For Unit Testing the `Service` layer (e.g., testing `OrderService::createOrder` calculations, mocking the database).
2. **Playwright / Cypress**: For End-to-End (E2E) testing of critical user journeys (e.g., placing an order, uploading a menu image). 
3. **GitHub Actions**: To run the test suite automatically on every Pull Request.
