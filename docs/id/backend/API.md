# Referensi API

Dokumen ini menguraikan endpoint JSON API yang disediakan oleh aplikasi Siwayut Catering.

## Base URL

Semua permintaan API bersifat relatif terhadap URL root aplikasi (contoh: `http://localhost:8000`).

## Autentikasi

- **Public Endpoints**: Tidak memerlukan autentikasi.
- **Admin Endpoints**: Memerlukan pengguna untuk terautentikasi dan memiliki role `admin`. Endpoint ini bergantung pada autentikasi sesi berbasis cookie.

## Rate Limiting

Endpoint API mungkin dilindungi oleh rate limiting. Header akan menyertakan informasi mengenai status rate limit saat ini (jika terlampaui).
- `rate.limit:5,60` - 5 permintaan per 60 detik.
- `rate.limit:10,60` - 10 permintaan per 60 detik.

## Format Respons

Semua respons API mengembalikan objek JSON dengan struktur standar berikut:

### Success Response (HTTP 200)

```json
{
  "success": true,
  "message": "OK",
  "data": {
    // Data yang diminta atau payload
  }
}
```

### Error Response (HTTP 400, 401, 403, 404, dll.)

```json
{
  "success": false,
  "message": "Deskripsi error",
  "errors": {
    // Error validasi spesifik field (opsional)
  }
}
```

---

## Public Endpoints

### 1. List Menus (Paginated)

Mengambil daftar menu aktif yang terpaginasi.

- **URL:** `/api/menus`
- **Method:** `GET`
- **Auth Required:** Tidak

#### Query Parameters

| Parameter | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `page` | `integer` | Tidak | Nomor halaman (default: 1). |
| `category_id` | `integer` | Tidak | Filter berdasarkan ID kategori. |

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

Endpoint berikut memerlukan autentikasi admin.

### 2. Get User Details

- **URL:** `/api/users/{id}`
- **Method:** `GET`
- **Auth Required:** Ya (Admin)

#### Path Variables

| Variable | Type | Description |
| :--- | :--- | :--- |
| `id` | `integer` | ID Pengguna. |

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
*(Catatan: Hash `password` dihilangkan dari respons)*

---

### 3. Get Event Details

- **URL:** `/api/events/{id}`
- **Method:** `GET`
- **Auth Required:** Ya (Admin)

#### Path Variables

| Variable | Type | Description |
| :--- | :--- | :--- |
| `id` | `integer` | ID Event. |

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
- **Auth Required:** Ya (Admin)

#### Path Variables

| Variable | Type | Description |
| :--- | :--- | :--- |
| `id` | `integer` | ID Kategori. |

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
- **Auth Required:** Ya (Admin)

#### Path Variables

| Variable | Type | Description |
| :--- | :--- | :--- |
| `code` | `string` | Kode Menu (contoh: `MNU-0001`). |

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

Menghasilkan deskripsi menu yang menggugah selera dalam bahasa Indonesia menggunakan layanan AI (contoh: OpenAI, Gemini).

- **URL:** `/menus/generate-description`
- **Method:** `POST`
- **Auth Required:** Ya (Admin)

#### Request Body

| Field | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `name` | `string` | Ya | Nama menu. |
| `category` | `string` | Tidak | Nama kategori menu. |
| `event` | `string` | Tidak | Nama event target. |
| `price` | `number` | Tidak | Harga menu. |
| `minimum_portions` | `integer` | Tidak | Persyaratan porsi minimum. |

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