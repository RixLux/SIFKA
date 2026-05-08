#  Campus Facility API & Dashboard Plan

Dokumentasi rencana pembangunan sistem pelaporan kerusakan fasilitas kampus berbasis koordinat GPS.

##  Phase 1: Inisialisasi & Authentication (The Foundation)

Fase ini membangun gerbang keamanan bagi pengguna.

* [ ] **Setup Laravel 11**: `laravel new campus-facility-api`.
* [ ] **Install & Config Sanctum**: `php artisan install:api`.
* [ ] **User Role System**: Tambahkan kolom `role` (`student`, `staff`, `admin`) pada migrasi tabel `users`.
* [ ] **Auth Logic**:
* Implementasi `Register` dan `Login` di `AuthController`.
* Return `token` dan `user_data` (termasuk role) untuk disimpan di **LocalStorage/Cookies** React.


* [ ] **CORS Configuration**: Pastikan `config/cors.php` mengizinkan domain frontend React (biasanya `localhost:5173`).

##  Phase 2: Database & Geo-Spatial Schema

Menangani data relasional dan koordinat lokasi Google Maps.

* [ ] **Migration `categories**`: `id`, `name`, `icon_marker`.
* [ ] **Migration `facilities**`:
* `id`, `category_id`, `name`, `latitude`, `longitude`.


* [ ] **Migration `reports**`:
* `id`, `user_id`, `facility_id`.
* `title`, `description`, `image_path`.
* `status` (enum: `pending`, `in_progress`, `resolved`, `rejected`).
* `lat_report`, `long_report` (untuk posisi spesifik saat melapor).


* [ ] **Model Relationship**:
* `Facility` belongsTo `Category`.
* `Report` belongsTo `User` & `Facility`.



##  Phase 3: Backend Logic & API Resources

Membangun engine untuk memproses data dan file gambar.

* [ ] **API Resources**: Gunakan `php artisan make:resource ReportResource` untuk standarisasi JSON response.
* [ ] **Image Handling**: Implementasi upload gambar menggunakan `Storage::disk('public')`.
* [ ] **Validation Logic**:
* `StoreReportRequest`: Validasi `image` (max 2MB) dan koordinat `numeric`.


* [ ] **RBAC Middleware**: Buat middleware `CheckRole` untuk membatasi akses (Contoh: Hanya Staff yang bisa `PUT /reports/{id}` untuk update status).

##  Phase 4: Frontend React Integration

Optimisasi UX dengan Google Maps API.

* [ ] **Setup React**: Menggunakan Vite + Tailwind CSS.
* [ ] **State Management**: Gunakan `Zustand` atau `Context API` untuk menyimpan token auth.
* [ ] **Google Maps Integration**:
* Install `@react-google-maps/api`.
* **Feature**: Tampilkan marker `facilities` dari API.
* **Feature**: Map Picker (User klik peta untuk mengisi koordinat laporan secara otomatis).


* [ ] **Dynamic Dropdown**: Fetching data `/api/categories` dan `/api/facilities` untuk form pelaporan.

##  Phase 5: Filtering, Search & Monitoring

Fitur untuk memudahkan Staff memantau laporan.

* [ ] **Eager Loading**: Gunakan `with(['user', 'facility'])` untuk menghindari N+1 problem.
* [ ] **Filter System**: Filter berdasarkan `status` dan `category_id`.
* [ ] **Search**: Implementasi pencarian berdasarkan judul laporan atau nama fasilitas.
* [ ] **Admin Dashboard**: Tabel ringkasan status laporan (Total Pending, Total Resolved).

---

##  Daftar Endpoint Utama

| Method | Endpoint | Fungsi | Akses |
| --- | --- | --- | --- |
| `POST` | `/api/login` | Mendapatkan Bearer Token | Public |
| `GET` | `/api/categories` | Data untuk dropdown kategori | Auth |
| `GET` | `/api/facilities` | List fasilitas & koordinat map | Auth |
| `GET` | `/api/reports` | List laporan (Filter & Search) | Auth |
| `POST` | `/api/reports` | Kirim laporan (Multiform/Data) | Auth (User) |
| `PATCH` | `/api/reports/{id}` | Update status laporan | Auth (Staff) |
| `DELETE` | `/api/reports/{id}` | Menghapus data laporan | Auth (Admin) |

