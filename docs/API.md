# SIFKA API Documentation (v1)

Sistem Informasi Fasilitas Kampus (SIFKA) menyediakan API untuk pelaporan kerusakan fasilitas kampus berbasis koordinat GPS.

## API Reference

<swagger-ui src="./assets/api.json"/>

## Base URL
`http://localhost:8000/api`

## Authentication
Sebagian besar endpoint memerlukan autentikasi menggunakan **Laravel Sanctum** (Bearer Token).

| Header | Value |
| --- | --- |
| `Accept` | `application/json` |
| `Authorization` | `Bearer {token}` |

---

## Auth Endpoints

### Register
`POST /register`

**Payload:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password",
    "password_confirmation": "password"
}
```

> *Note: All new registrations are assigned the `student` role by default.*

### Login
`POST /login`

**Payload:**
```json
{
    "email": "john@example.com",
    "password": "password"
}
```

### Super Admin

Gunakan  
```
php artisan app:create-super-admin
```
untuk membuat akun admin pertama dari `.env`.  

Admin Credential from `.env.example`:  
```
# Super Admin Credentials
SUPER_ADMIN_NAME="Admin SIFKA"
SUPER_ADMIN_EMAIL="admin@sifka.test"
SUPER_ADMIN_PASSWORD="AdminSIFKA"
```

**Payload:**
```json
{
    "email": "admin@sifka.test",
    "password": "AdminSIFKA"
}
```

**Response:**
```json
{
    "message": "Login successful",
    "access_token": "1|xxxxxxxxxxxx",
    "token_type": "Bearer",
    "user": { ... }
}
```

### Logout
`POST /logout` (Auth required)

### Get Profile
`GET /user` (Auth required)

---

## Resource Endpoints

### Categories
`GET /categories` (Auth required) - Paginated list of categories.

`POST /categories` (Auth (Admin) required) - Create new category.
**Payload:**
```json
{
    "name": "Kelistrikan",
    "icon_marker": "bolt",
    "color_code": "#FFD700"
}
```

---

### Buildings
`GET /buildings` (Auth required) - Paginated list of physical locations with nested amenities.

`POST /buildings` (Auth (Admin) required) - Create new building.
**Payload:**
```json
{
    "name": "Gedung Rektorat",
    "description": "Kantor Pusat",
    "latitude": -6.1234,
    "longitude": 106.1234
}
```

**Response Example:**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Gedung Rektorat",
            "description": "Kantor pusat",
            "coordinate": {
                "lat": -6.1234,
                "lng": 106.1234
            },
            "amenities": [
                {
                    "id": 10,
                    "name": "Lampu LED Lobby",
                    "category": { "name": "Kelistrikan" }
                }
            ]
        }
    ]
}
```

---

### Facilities
`GET /facilities` (Auth required) - Paginated list of all assets.

`POST /facilities` (Auth (Admin) required) - Create new facility.
**Payload:**
```json
{
    "building_id": 1,
    "category_id": 1,
    "name": "Lampu LED Lobby",
    "description": "Lampu utama area lobby"
}
```

---

### Reports
`GET /reports` (Auth required)  

`POST /reports` (Auth required)
**Payload (Multipart/Form-Data):**  

- `facility_id`: integer (optional) - Link to a specific asset.
- `title`: string (required)
- `description`: string (required)
- `image`: file (optional, max 2MB)
- `latitude`: numeric (required) - Pinpoint GPS location.
- `longitude`: numeric (required) - Pinpoint GPS location.

---

## Pagination
All `GET` resource endpoints return paginated data with the following meta structure:
```json
{
    "data": [...],
    "links": { ... },
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "total": 75
    }
}
```
