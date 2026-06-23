# Laporan Kemajuan Kelompok 1

## **Deskripsi Project**

| No | Aspek | Uraian |
| :--- | :--- | :--- |
| 1 | Latar Belakang | Pemeliharaan fasilitas kampus seringkali terhambat oleh lambatnya proses pelaporan dan sulitnya menentukan lokasi kerusakan yang akurat. Mahasiswa dan staf seringkali bingung harus melapor ke mana, dan pihak pemeliharaan sulit menemukan titik koordinat kerusakan yang dilaporkan secara deskriptif saja. |
| 2 | Tujuan Project | - Menyediakan platform pelaporan kerusakan fasilitas yang cepat dan terintegrasi.<br>- Menggunakan teknologi Geo-Spatial (GPS) untuk akurasi lokasi kerusakan.<br>- Mempermudah pemantauan status perbaikan bagi pelapor maupun pihak pengelola (Staff/Admin). |
| 3 | Gambaran Sistem | SIFKA terdiri dari Backend API berbasis Laravel dan Frontend Dashboard berbasis React. Pengguna dapat memilih fasilitas yang tersedia di peta, mengirimkan laporan berupa deskripsi dan foto, serta menyertakan lokasi GPS mereka saat itu. Staff akan menerima notifikasi dan dapat memperbarui status perbaikan secara real-time. Admin memiliki kendali penuh untuk mengelola master data kategori fasilitas. |
| 4 | Fitur Utama | - **Geo-Tagged Reporting**: Pelaporan dengan koordinat GPS otomatis.<br>- **Role-based Access Control**: Pemisahan hak akses antara Mahasiswa (Pelapor), Staff (Teknisi), dan Admin.<br>- **Category Management**: Admin dapat menambah, mengubah, atau menghapus kategori fasilitas.<br>- **Map Visualization**: Visualisasi sebaran fasilitas dan titik kerusakan di peta interaktif.<br>- **Image Upload**: Lampiran foto bukti kerusakan.<br>- **Status Tracking**: Pemantauan tahapan perbaikan dari *Pending* hingga *Resolved*. |
| 5 | Teknologi yang Digunakan | - **Backend**: Laravel 13, Sanctum (Auth), MariaDB (POINT Spatial).<br>- **Frontend**: React, Vite, Tailwind CSS, Zustand (State Management).<br>- **Maps**: `mapcn` component wrapper.<br>- **Documentation**: MkDocs. |

## **Kemajuan Project**

| No | Bagian yang Dikerjakan | Status | Keterangan |
| --- | --- | --- | --- |
| 1 | **Inisialisasi & Authentication** | Selesai | Persiapan Projek dan Token API |
| 2 | **Geo-Spatial Database Schema** | Selesai | Migrasi data latitude & longitude lama ke tipe spasial POINT |
| 3 | **Core Logic & Security** | Selesai | Membangun API yang aman, FormRequests, dan pembatasan laju (rate limiting) |
| 4 | **Perintah Artisan Kustom** | Selesai | Otomasi reset, switch storage, manajemen token, dan migrasi storage |

## **Dokumentasi**

| No | Jenis Dokumentasi | Keterangan |
| --- | --- | --- |
| 1 | Flowchart / Diagram | Tersedia di direktori dokumentasi |
| 2 | Use Case / Wireframe | Tersedia di direktori dokumentasi |
| 3 | Tampilan Aplikasi | Terintegrasi di frontend dashboard |
| 4 | Lainnya | Situs dokumentasi statis MkDocs: <https://rixlux.github.io/SIFKA/> |

**Kendala dan Solusi**

| No | Kendala | Solusi |
| --- | --- | --- |
| 1 | Keamanan Endpoint | Secara eksplisit membatasi akses pendaftaran dan menetapkan peran default user |
| 2 | Kurangnya cara menambah fasilitas | Menambahkan endpoint gedung dan fasilitas baru untuk manajemen data |
| 3 | Integrasi Cloud Storage | Membuat perintah `storage:switch` dan `storage:migrate` untuk Cloudflare R2 |

## **Progress Keseluruhan**

| Keterangan | Nilai |
| --- | --- |
| Persentase Kemajuan | 100 % |
| Status Project | On Track |
