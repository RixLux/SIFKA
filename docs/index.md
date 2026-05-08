# SIFKA - Sistem Informasi Fasilitas Kampus

## 1. Latar Belakang
Pemeliharaan fasilitas kampus seringkali terhambat oleh lambatnya proses pelaporan dan sulitnya menentukan lokasi kerusakan yang akurat. Mahasiswa dan staf seringkali bingung harus melapor ke mana, dan pihak pemeliharaan sulit menemukan titik koordinat kerusakan yang dilaporkan secara deskriptif saja.

## 2. Tujuan Project
- Menyediakan platform pelaporan kerusakan fasilitas yang cepat dan terintegrasi.
- Menggunakan teknologi Geo-Spatial (GPS) untuk akurasi lokasi kerusakan.
- Mempermudah pemantauan status perbaikan bagi pelapor maupun pihak pengelola (Staff/Admin).

## 3. Gambaran Sistem
SIFKA menggunakan arsitektur berbasis lokasi (**Building-Centric**). Setiap aset/fasilitas dikelompokkan berdasarkan gedung atau lokasi fisiknya. Pengguna dapat memberikan laporan kerusakan dengan akurasi pinpoint GPS, baik yang terhubung langsung ke fasilitas tertentu maupun laporan area terbuka. Admin memiliki kontrol penuh untuk mengelola master data Gedung, Kategori, dan Fasilitas.

## 4. Fitur Utama
- **Geo-Tagged Reporting**: Pelaporan dengan koordinat GPS otomatis (Pinpoint Accuracy).
- **Building Management**: Pengelompokan aset berdasarkan lokasi fisik (Gedung/Area).
- **Role-based Access Control**: Pemisahan hak akses antara Mahasiswa (Pelapor), Staff (Teknisi), dan Admin.
- **Map Visualization**: Integrasi Google Maps untuk visualisasi sebaran laporan dan fasilitas.
- **Status Tracking**: Pemantauan tahapan perbaikan dari *Pending* hingga *Resolved*.

## 5. Teknologi yang Digunakan
- **Backend**: Laravel 13 (PHP 8.5), Sanctum, SQLite/MySQL.
- **Frontend**: React (Vite), Tailwind CSS, Zustand.
- **Maps**: Google Maps Platform API.

---

## Navigasi
- [Dokumentasi API](API.md)
- [Rencana Pengembangan](README-Plan.md)
