# API Reference

This document outlines the JSON API endpoints provided by the Siwayut Catering application.

## Base URL

All API requests are relative to the application's root URL (e.g., `http://localhost:8000`).

## Authentication

- **Public Endpoints**: Do not require authentication.
- **Admin Endpoints**: Require the user to be authenticated and have the `admin` role. These rely on cookie-based session authentication.

## Rate Limiting

API endpoints may be protected by rate limiting. The headers will include information about the current rate limit status (if exceeded).
- `rate.limit:5,60` - 5 requests per 60 seconds.
- `rate.limit:10,60` - 10 requests per 60 seconds.

## Response Format

All API responses return a JSON object with the following standard structure:

### Success Response (HTTP 200)

```json
{
  "success": true,
  "message": "OK",
  "data": {
    // Requested data or payload
  }
}
```

### Error Response (HTTP 400, 401, 403, 404, etc.)

```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    // Optional field-specific validation errors
  }
}
```

---

## Public Endpoints

### 1. List Menus (Paginated)

Retrieves a paginated list of active menus.

- **URL:** `/api/menus`
- **Method:** `GET`
- **Auth Required:** No

#### Query Parameters

| Parameter | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `page` | `integer` | No | Page number (default: 1). |
| `category_id` | `integer` | No | Filter by category ID. |

#### Success Response

```json
{
  "success": true,
  "message": "OK",
  "data": {
    "data": [
      {
        "id": 1,
        "menu_code": "MNU-0001",
        "name": "Nasi Goreng Spesial",
        "description": "Nasi goreng dengan bumbu rahasia...",
        "price": "25000.00",
        "category_id": 1,
        "event_id": 1,
        "minimum_portions": 10,
        "image": "menus/image.jpg",
        "status": "active",
        "created_at": "2023-10-27 10:00:00",
        "event_name": "Pernikahan",
        "category_name": "Main Course"
      }
    ],
    "total": 1,
    "per_page": 9,
    "current_page": 1,
    "last_page": 1
  }
}
```

---

## Admin Endpoints

The following endpoints require admin authentication.

### 2. Get User Details

- **URL:** `/api/users/{id}`
- **Method:** `GET`
- **Auth Required:** Yes (Admin)

#### Path Variables

| Variable | Type | Description |
| :--- | :--- | :--- |
| `id` | `integer` | User ID. |

#### Success Response

```json
{
  "success": true,
  "message": "OK",
  "data": {
    "id": 1,
    "user_code": "USR-0001",
    "name": "Admin User",
    "email": "admin@admin.com",
    "phone": "08123456789",
    "address": "Jl. Sudirman",
    "role": "admin",
    "created_at": "2023-10-27 10:00:00",
    "updated_at": "2023-10-27 10:00:00"
  }
}
```
*(Note: `password` hash is omitted from the response)*

---

### 3. Get Event Details

- **URL:** `/api/events/{id}`
- **Method:** `GET`
- **Auth Required:** Yes (Admin)

#### Path Variables

| Variable | Type | Description |
| :--- | :--- | :--- |
| `id` | `integer` | Event ID. |

#### Success Response

```json
{
  "success": true,
  "message": "OK",
  "data": {
    "id": 1,
    "name": "Pernikahan",
    "start_date": "2023-12-01",
    "end_date": "2023-12-31",
    "status": "active",
    "created_at": "2023-10-27 10:00:00",
    "updated_at": "2023-10-27 10:00:00"
  }
}
```

---

### 4. Get Category Details

- **URL:** `/api/categories/{id}`
- **Method:** `GET`
- **Auth Required:** Yes (Admin)

#### Path Variables

| Variable | Type | Description |
| :--- | :--- | :--- |
| `id` | `integer` | Category ID. |

#### Success Response

```json
{
  "success": true,
  "message": "OK",
  "data": {
    "id": 1,
    "name": "Main Course",
    "slug": "main-course",
    "created_at": "2023-10-27 10:00:00",
    "updated_at": "2023-10-27 10:00:00"
  }
}
```

---

### 5. Get Menu Details

- **URL:** `/api/menus/{code}`
- **Method:** `GET`
- **Auth Required:** Yes (Admin)

#### Path Variables

| Variable | Type | Description |
| :--- | :--- | :--- |
| `code` | `string` | Menu Code (e.g., `MNU-0001`). |

#### Success Response

```json
{
  "success": true,
  "message": "OK",
  "data": {
    "id": 1,
    "menu_code": "MNU-0001",
    "name": "Nasi Goreng Spesial",
    "description": "Nasi goreng dengan bumbu rahasia...",
    "price": "25000.00",
    "cost_price": "15000.00",
    "category_id": 1,
    "event_id": 1,
    "minimum_portions": 10,
    "image": "menus/image.jpg",
    "status": "active",
    "created_at": "2023-10-27 10:00:00",
    "updated_at": "2023-10-27 10:00:00"
  }
}
```

---

### 6. Generate Menu Description (AI)

Generates an appetizing menu description in Indonesian using an AI service (e.g., OpenAI, Gemini).

- **URL:** `/menus/generate-description`
- **Method:** `POST`
- **Auth Required:** Yes (Admin)

#### Request Body

| Field | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `name` | `string` | Yes | Menu name. |
| `category` | `string` | No | Menu category name. |
| `event` | `string` | No | Target event name. |
| `price` | `number` | No | Menu price. |
| `minimum_portions` | `integer` | No | Minimum portion requirement. |

#### Success Response

```json
{
  "success": true,
  "message": "OK",
  "data": {
    "description": "Nikmati kelezatan Nasi Goreng Spesial dengan bumbu rahasia yang menggugah selera. Cocok untuk hidangan utama di acara pernikahan Anda. Pesan sekarang!"
  }
}
```

#### Error Response Example (AI Service Unavailable)

```json
{
  "success": false,
  "message": "AI API error: API key not valid",
  "errors": []
}
```
