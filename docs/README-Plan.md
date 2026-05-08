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
*Menangani data relasional dengan presisi koordinat tinggi.*

* [x] **Migration `categories`**: `id`, `name`, `icon_marker` (URL/SVG Path), `color_code`.
* [x] **Migration `facilities`**:
    * `id`, `category_id`, `name`, `description`.
    * `latitude` (decimal 10, 8), `longitude` (decimal 11, 8).
* [x] **Migration `reports`**:
    * `id`, `user_id`, `facility_id`.
    * `title`, `description`, `image_path` (nullable).
    * `status` (Enum: `pending`, `in_progress`, `resolved`, `rejected`).
    * `lat_report`, `long_report` (Posisi spesifik pelapor saat mengirim).
    * `softDeletes()`: Untuk keamanan data (audit trail).
* [x] **Eloquent Relationships**:
    * `User` hasMany `Report`.
    * `Facility` belongsTo `Category` & hasMany `Report`.

##  Phase 3: Core Logic & Security (The Engine)
*Membangun API yang aman dan efisien.*

* [x] **API Resources**: Implementasi `ReportResource` dan `FacilityResource` untuk transformasi data JSON yang konsisten.
* [x] **Authorization (Policies)**:
    * Gunakan `ReportPolicy` untuk membatasi aksi.
* [x] **Image Processing**:
    * Upload ke `Storage::disk('public')`.
    * Validasi file (Mimes: jpg, png; Max: 2MB).
* [ ] **Service Layer (Optional)**: Jika logic pelaporan kompleks (misal: kirim email otomatis), pindahkan dari Controller ke Service.

## Phase 3.5: Category Management (New - Admin Only)
*Fokus pada pengelolaan master data kategori oleh Admin.*

* [x] **CategoryPolicy**: Implementasi restriksi akses khusus role `admin`.
* [x] **Category CRUD API**:
    * `POST /api/categories`: Tambah kategori baru dengan validasi unik.
    * `PUT/PATCH /api/categories/{id}`: Update info kategori.
    * `DELETE /api/categories/{id}`: Hapus kategori dengan proteksi (cegah hapus jika ada fasilitas yang terhubung).
* [x] **Feature Testing**: Pastikan proteksi role admin berjalan (Mahasiswa/Staff tidak bisa akses CRUD).

## Phase 3.6: Secure Registration Role Assignment(New)

## Objective
Prevent "Role Elevation" vulnerability where a public user can register themselves as an `admin` or `staff`. We will enforce a strict default role of `student` for all new registrations and ignore any `role` fields sent in the request payload.

## Key Files & Context
- [x] `app/Http/Controllers/AuthController.php`: Handle registration logic.
- [x] `tests/Feature/AuthTest.php`: Verify security measures.
- [x] `docs/API.md`: Update documentation to reflect the removal of the `role` parameter.

---

##  Phase 4: Frontend React & Maps Integration
*Visualisasi data dan pengalaman pengguna.*

* [ ] **State Management**: Zustand untuk global store (Auth & Filters).
* [ ] **Google Maps SDK**:
    * Implementasi Custom Markers berdasarkan kategori fasilitas.
    * **Map Picker**: Fitur klik peta untuk mendapatkan koordinat otomatis saat melapor.
* [ ] **Form Handling**: React Hook Form + Zod untuk validasi client-side.
* [ ] **PWA Support (Optional)**: Agar pelaporan bisa dilakukan langsung dari mobile browser dengan akses kamera/GPS yang lebih baik.

##  Phase 5: Monitoring, Notifications & Analytics
*Fitur lanjutan untuk administrasi.*

* [ ] **Real-time Notifications**: Gunakan Laravel Database/Mail Notifications untuk memberitahu user jika status laporan berubah.
* [ ] **Advanced Filtering**: Filter laporan berdasarkan `status`, `date_range`, dan `category`.
* [ ] **Admin Dashboard**:
    * Statistik: "Jumlah laporan pending bulan ini", "Fasilitas paling sering rusak".
    * Export data laporan ke CSV/Excel.

---

##  Daftar Endpoint Utama (API v1)

| Method | Endpoint | Fungsi | Akses |
| --- | --- | --- | --- |
| **AUTH** | | | |
| `POST` | `/api/login` | Login & Get Token | Public |
| `GET` | `/api/user` | Get Profile | Auth |
| **CATEGORIES** | | | |
| `GET` | `/api/categories` | List Kategori | Auth |
| `POST` | `/api/categories` | Create Category | **Admin** |
| `PUT` | `/api/categories/{id}` | Update Category | **Admin** |
| `DELETE` | `/api/categories/{id}` | Delete Category | **Admin** |
| **FACILITIES** | | | |
| `GET` | `/api/facilities` | List Fasilitas & Markers | Auth |
| **REPORTS** | | | |
| `GET` | `/api/reports` | List Laporan (Scoped) | Auth |
| `POST` | `/api/reports` | Buat Laporan Baru | User |
| `PATCH` | `/api/reports/{id}` | Update Status | Staff/Admin |
| `DELETE` | `/api/reports/{id}` | Soft Delete Laporan | Admin |

---
**Tech Stack Summary:**
- **Backend**: Laravel 13, MySQL/SQLite, Sanctum.
- **Frontend**: React (Vite), Tailwind CSS, Zustand.
- **Maps**: Google Maps Platform API.
