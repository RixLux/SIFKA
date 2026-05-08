# SIFKA - Sistem Informasi Fasilitas Kampus

## 1. Latar Belakang
Pemeliharaan fasilitas kampus seringkali terhambat oleh lambatnya proses pelaporan dan sulitnya menentukan lokasi kerusakan yang akurat. Mahasiswa dan staf seringkali bingung harus melapor ke mana, dan pihak pemeliharaan sulit menemukan titik koordinat kerusakan yang dilaporkan secara deskriptif saja.

## 2. Tujuan Project
- Menyediakan platform pelaporan kerusakan fasilitas yang cepat dan terintegrasi.
- Menggunakan teknologi Geo-Spatial (GPS) untuk akurasi lokasi kerusakan.
- Mempermudah pemantauan status perbaikan bagi pelapor maupun pihak pengelola (Staff/Admin).

## 3. Gambaran Sistem
SIFKA terdiri dari Backend API berbasis Laravel dan Frontend Dashboard berbasis React. Pengguna dapat memilih fasilitas yang tersedia di peta, mengirimkan laporan berupa deskripsi dan foto, serta menyertakan lokasi GPS mereka saat itu. Staff akan menerima notifikasi dan dapat memperbarui status perbaikan secara real-time. Admin memiliki kendali penuh untuk mengelola master data kategori fasilitas.

## 4. Fitur Utama
- **Geo-Tagged Reporting**: Pelaporan dengan koordinat GPS otomatis.
- **Role-based Access Control**: Pemisahan hak akses antara Mahasiswa (Pelapor), Staff (Teknisi), dan Admin.
- **Category Management**: Admin dapat menambah, mengubah, atau menghapus kategori fasilitas.
- **Map Visualization**: Visualisasi sebaran fasilitas dan titik kerusakan di Google Maps.
- **Image Upload**: Lampiran foto bukti kerusakan.
- **Status Tracking**: Pemantauan tahapan perbaikan dari *Pending* hingga *Resolved*.

## 5. Teknologi yang Digunakan
- **Backend**: Laravel 13, Sanctum (Auth), SQLite/MySQL.
- **Frontend**: React, Vite, Tailwind CSS, Zustand (State Management).
- **Maps**: Google Maps JavaScript SDK.
- **Documentation**: MkDocs.

---

## Navigasi
- [Dokumentasi API](API.md)
- [Rencana Pengembangan](README-Plan.md)
