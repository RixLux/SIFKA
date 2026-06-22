# Panduan Migrasi & Deployment: Perombakan Data Spasial

Dokumen ini memberikan instruksi untuk melakukan deployment fitur data spasial (integrasi mapcn) dan memastikan transisi berjalan lancar dari penyimpanan koordinat lama.

## 1. Migrasi Database

Aplikasi telah bermigrasi dari kolom numerik `latitude` dan `longitude` terpisah ke tipe data spasial asli MariaDB/MySQL yaitu `POINT`.

### Menjalankan Migrasi
Untuk memperbarui database produksi/staging Anda, jalankan perintah:
```bash
php artisan migrate
```

### Apa yang terjadi selama migrasi:
1.  Kolom `location` baru dengan tipe data `GEOMETRY (POINT, 4326)` ditambahkan ke tabel `buildings`, `facilities`, dan `reports`.
2.  Data numerik yang ada secara otomatis dikonversi menjadi titik spasial (spatial points).
3.  Indeks spasial (spatial indexes) dibuat untuk performa kueri kedekatan (proximity) dan kotak pembatas (bounding-box) yang tinggi.
4.  Kolom lama `latitude` dan `longitude` dihapus dari tabel.

## 2. Pengindeksan Meilisearch

Mesin pencari sekarang mendukung pengurutan dan penyaringan berdasarkan jarak menggunakan atribut `_geo`.

### Pengindeksan Ulang Data
Setelah menjalankan migrasi database, Anda **wajib** memperbarui indeks pencarian:
```bash
php artisan scout:import "App\Models\Building"
php artisan scout:import "App\Models\Facility"
php artisan scout:import "App\Models\Report"
```

## 3. Sinkronisasi Frontend & API

API sekarang mengembalikan data JSON standar secara default, tetapi juga mendukung GeoJSON yang dioptimalkan untuk perenderan peta.

### Endpoint GeoJSON
Semua indeks sumber daya dan endpoint pencarian mendukung parameter kueri `format=geojson`:
*   `GET /api/buildings?format=geojson`
*   `GET /api/facilities/search?q=Library&format=geojson`

Klien API frontend (`FE_SIFKA/src/api/client.js`) telah diperbarui untuk menggunakan jalur relatif (`/api`) agar bekerja dengan mulus dengan proksi Vite dan tunnel seperti Tailscale Funnel.

## 4. Akses Eksternal & Tailscale

Untuk mengakses lingkungan pengembangan melalui Tailscale Funnel (misalnya, dari perangkat seluler):

1.  **Frontend (Vite)**: Pastikan `vite.config.js` memiliki opsi `host: '0.0.0.0'` dan domain tunnel Anda terdaftar di `allowedHosts`.
2.  **CORS**: Konfigurasi `config/cors.php` harus memasukkan URL tunnel Anda (misalnya, `https://node-anda.tailscale.net:5173`) dalam `allowed_origins`.
3.  **GPS/Geolokasi**: Fitur GPS browser **memerlukan koneksi HTTPS**. Gunakan Tailscale Funnel atau sertifikat SSL lokal untuk mengaktifkan fitur ini pada perangkat seluler.

## 5. Daftar Periksa Deployment (Deployment Checklist)
*   [ ] Jalankan `php artisan migrate`
*   [ ] Indeks ulang model Scout (Meilisearch)
*   [ ] Perbarui `cors.php` dengan domain produksi atau tunnel Anda
*   [ ] Jalankan `npm install` dan `npm run build` untuk frontend
Profile.
