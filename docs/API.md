# SIFKA API Documentation (v1)

Sistem Informasi Fasilitas Kampus (SIFKA) menyediakan API untuk pelaporan kerusakan fasilitas kampus berbasis koordinat GPS.

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

> Hanya untuk tujuan tes.  

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
`GET /categories` (Auth required) - List all categories.

`POST /categories` (Auth (Admin) required) - Create new category.
**Payload:**
```json
{
    "name": "Kelistrikan",
    "icon_marker": "bolt",
    "color_code": "#FFD700"
}
```

`PUT/PATCH /categories/{id}` (Auth (Admin) required) - Update category.
**Payload:**
```json
{
    "name": "Kelistrikan & Air",
    "icon_marker": "droplet",
    "color_code": "#0000FF"
}
```

`DELETE /categories/{id}` (Auth (Admin) required) - Delete category.  

> *Note: Deletion will fail if there are facilities linked to this category.*

---

### Facilities
`GET /facilities` (Auth required)
`GET /facilities/{id}` (Auth required)

**Response (List):**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Gedung A - Lt 1",
            "description": "Area lobby utama",
            "latitude": -6.12345678,
            "longitude": 106.12345678,
            "category": { "id": 1, "name": "Kelistrikan" }
        }
    ]
}
```

### Reports
`GET /reports` (Auth required)  

- Mahasiswa: Hanya melihat laporan miliknya sendiri.
- Staff/Admin: Melihat semua laporan.

`POST /reports` (Auth required)  
**Payload (Multipart/Form-Data):**  

- `facility_id`: integer (required)
- `title`: string (required)
- `description`: string (required)
- `image`: file (optional, max 2MB)
- `latitude`: numeric (required) - Posisi pelapor
- `longitude`: numeric (required) - Posisi pelapor

`PATCH /reports/{id}` (Auth (Staff/Admin) required)  
**Payload:**  

- `status`: string (`pending`, `in_progress`, `resolved`, `rejected`)

`DELETE /reports/{id}` (Auth (Admin) required)  

- Melakukan Soft Delete pada laporan.

---

## Status Codes
- `200 OK`: Request berhasil.
- `201 Created`: Resource berhasil dibuat.
- `401 Unauthorized`: Token tidak valid atau tidak ada.
- `403 Forbidden`: Tidak memiliki izin akses (Role tidak sesuai).
- `422 Unprocessable Entity`: Validasi gagal (misal: kategori masih digunakan fasilitas).
