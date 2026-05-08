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
    * `Category` hasMany `Facility`.

##  Phase 3: Core Logic & Security (The Engine)
*Membangun API yang aman dan efisien.*

* [ ] **API Resources**: Implementasi `ReportResource` dan `FacilityResource` untuk transformasi data JSON yang konsisten.
* [ ] **Authorization (Policies)**:
    * Gunakan `ReportPolicy` untuk membatasi aksi (contoh: Mahasiswa hanya bisa melihat laporannya sendiri, Staff bisa update status).
* [ ] **Image Processing**:
    * Upload ke `Storage::disk('public')`.
    * Validasi file (Mimes: jpg, png; Max: 2MB).
* [ ] **Service Layer (Optional)**: Jika logic pelaporan kompleks (misal: kirim email otomatis), pindahkan dari Controller ke Service.

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
    * Export data laporan ke CSV/Excel (Optional).

---

##  Daftar Endpoint Utama (API v1)

| Method | Endpoint | Fungsi | Akses |
| --- | --- | --- | --- |
| `POST` | `/api/login` | Login & Get Token | Public |
| `GET` | `/api/user` | Get Profile | Auth |
| `GET` | `/api/categories` | List Kategori | Auth |
| `GET` | `/api/facilities` | List Fasilitas & Map Markers | Auth |
| `GET` | `/api/reports` | List Laporan (Scoped by Role) | Auth |
| `POST` | `/api/reports` | Buat Laporan Baru | Auth (User) |
| `PATCH` | `/api/reports/{id}` | Update Status (Staff Only) | Auth (Staff) |
| `DELETE` | `/api/reports/{id}` | Soft Delete Laporan | Auth (Admin) |

---
**Tech Stack Summary:**
- **Backend**: Laravel 13, MySQL/SQLite, Sanctum.
- **Frontend**: React (Vite), Tailwind CSS, Zustand.
- **Maps**: Google Maps Platform API.
