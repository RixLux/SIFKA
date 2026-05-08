#  Campus Facility API & Dashboard Plan (Refined)

Dokumentasi rencana pembangunan sistem pelaporan kerusakan fasilitas kampus berbasis koordinat GPS.

##  Phase 1: Inisialisasi & Authentication (The Foundation)
*Fokus pada keamanan dan identitas pengguna.*

* [x] **Environment Check**: Pastikan menggunakan Laravel 13 & PHP 8.5.
* [x] **Sanctum Integration**: Jalankan `php artisan install:api` untuk autentikasi token.
* [x] **User Role System**: 
    * Migrasi tabel `users` dengan kolom `role` (Enum: `student`, `staff`, `admin`).
    * Default role adalah `student`.
* [x] **Auth Controller**:
    * `POST /api/register`: Validasi email kampus & role.
    * `POST /api/login`: Return Bearer Token & User Profile.
    * `POST /api/logout`: Revoke token saat ini.
* [x] **CORS & Security**: Konfigurasi `config/cors.php` untuk mengizinkan frontend React.
* [x] **Super Admin Bootstrap**: 
    * Gunakan `php artisan app:create-super-admin` untuk membuat akun admin pertama dari `.env`.

##  Phase 2: Geo-Spatial Database Schema
* [x] Migration `categories`
* [x] Migration `facilities`
* [x] Migration `reports` (Updated: `facility_id` nullable for pinpoint accuracy)
* [x] Eloquent Relationships

##  Phase 3: Core Logic & Security (The Engine)
* [x] API Resources (Report, Facility, Category, Building)
* [x] Authorization (Policies for all entities)
* [x] Image Processing
* [x] **Phase 3.5: Category Management** (Admin CRUD)
* [x] **Phase 3.6: Secure Registration** (Strict student default)
* [x] **Phase 3.7: Facility Management & Pinpoint Accuracy**
* [x] **Phase 3.8: Database Normalization & API Optimization**
    * Introduced `buildings` table for location grouping.
    * Implemented nested JSON structure (Building -> Amenities).
    * Enforced consistent pagination across all GET endpoints.

---

##  Phase 4: Frontend React & Maps Integration
* Visualisasi data dan pengalaman pengguna.
* [ ] **State Management**: Zustand untuk global store (Auth & Filters).
* [ ] **Google Maps SDK**: Custom Markers & Pinpoint Picker.
* [ ] **Form Handling**: React Hook Form + Zod.

##  Phase 5: Monitoring, Notifications & Analytics
* [ ] **Real-time Notifications**
* [ ] **Advanced Filtering**
* [ ] **Admin Dashboard Statistics**

---

##  Daftar Endpoint Utama (API v1)

| Method | Endpoint | Fungsi | Akses |
| --- | --- | --- | --- |
| **AUTH** | | | |
| `POST` | `/api/login` | Login | Public |
| **BUILDINGS** | | | |
| `GET` | `/api/buildings` | List Gedung & Fasilitas | Auth |
| `POST` | `/api/buildings` | Create Gedung | Admin |
| **CATEGORIES** | | | |
| `GET` | `/api/categories` | List Kategori | Auth |
| **FACILITIES** | | | |
| `GET` | `/api/facilities` | List Semua Aset | Auth |
| `POST` | `/api/facilities` | Create Fasilitas | Admin |
| **REPORTS** | | | |
| `POST` | `/api/reports` | Create (Pinpoint GPS) | User |
| `PATCH` | `/api/reports/{id}` | Update Status | Staff/Admin |
