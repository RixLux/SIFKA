# SIFKA

SIFKA adalah API berkinerja tinggi yang dibangun dengan Laravel 13, dirancang untuk mengelola dan melaporkan fasilitas kampus. Proyek ini menampilkan penyelarasan arsitektur yang mendalam, pengerasan keamanan tingkat lanjut, dan kemampuan pencarian global.

## Stack Teknis

<details markdown="1">
<summary>Click to see details</summary>

- **Framework:** Laravel 13 (PHP 8.3)
- **Keamanan:** Laravel Sanctum (Auth), Pembatasan Laju (Rate Limiting), Otorisasi berbasis Policy.
- **Mesin Pencari:** Laravel Scout dengan Meilisearch.
- **Dokumentasi:** Scramble (OpenAPI) dan MKDocs.
- **Pengujian:** PHPUnit (pengujian fitur & unit yang diperkeras).

</details>

## Fitur Arsitektur Utama

<details markdown="1">
<summary>Click to see details</summary>

### 1. Pencarian Global (Meilisearch)
Pencarian fuzzy berkecepatan tinggi yang diimplementasikan di semua sumber daya utama.

### 2. Penyelarasan Payload & Validasi Ketat
API mempertahankan kontrak permintaan/tanggapan yang dapat diprediksi. Semua input sumber daya distandarisasi melalui **FormRequests**, memastikan bahwa otorisasi dan validasi terjadi sebelum eksekusi database apa pun.

- **Pemetaan Asimetris:** Secara otomatis mengonversi koordinat datar frontend menjadi geometri backend terstruktur.
- **Keamanan:** Permintaan yang tidak sah ditolak (403) sebelum validasi (422) untuk mencegah kebocoran metadata.

### 3. Pengerasan Keamanan
- **Throttling (Pembatasan Laju):** Semua rute autentikasi (`login`, `register`, `logout`) dilindungi oleh pembatas laju `5 permintaan / menit`.
- **Kontrol Admin:** Manajemen peran granular yang memungkinkan Admin untuk mendaftarkan akun Staff melalui API.

</details>

## Dokumentasi Proyek

<details markdown="1">
<summary>Click to see details</summary>

Untuk penjelasan mendalam tentang desain dan peta jalan proyek, silakan merujuk ke direktori `docs/`.

</details>

## Memulai

<details markdown="1">
<summary>Click to see details</summary>

### Prasyarat
- PHP 8.3+
- Composer
- Server Meilisearch (Lokal atau Cloud)

### Instalasi
1. **Kloning & Instal Dependensi:**
   ```bash
   composer install
   npm install && npm run build
   ```
2. **Pengaturan Lingkungan (.env):**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. **Database & Pengindeksan:**
   ```bash
   php artisan migrate --seed
   php artisan scout:import "App\Models\Building"
   php artisan scout:import "App\Models\Category"
   php artisan scout:import "App\Models\Facility"
   php artisan scout:import "App\Models\Report"
   php artisan scout:import "App\Models\User"
   ```

</details>

## Pengujian

<details markdown="1">
<summary>Click to see details</summary>

Jalankan rangkaian pengujian komprehensif untuk memastikan stabilitas:
```bash
php artisan test --compact
```

</details>

## Laravel Brain

<details markdown="1">
<summary>Click to see details</summary>

Proyek ini menggunakan **LaraMint Brain** untuk analisis arsitektur. Untuk menjelajahi grafik kode sumber atau mengekspor snapshot konteks:
```bash
php artisan brain:scan
```

</details>

