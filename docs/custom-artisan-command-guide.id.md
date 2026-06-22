# Panduan Perintah Artisan Kustom

Dokumen ini mencantumkan perintah Artisan kustom yang dibuat untuk proyek SIFKA untuk mempermudah pengembangan, pengujian, dan pemeliharaan lingkungan sistem.

## Perintah yang Tersedia

### 1. `app:reset`
Mengatur ulang (reset) secara menyeluruh lingkungan pengembangan lokal.
- **Tujuan**: Menghapus seluruh data database, menjalankan kembali semua migrasi, mengisi ulang data awal (seed), dan menghapus seluruh file gambar laporan yang diunggah dari penyimpanan.
- **Penggunaan**:
  ```bash
  php artisan app:reset
  ```
- **Catatan**: Perintah ini menjalankan seluruh proses pembersihan secara otomatis tanpa meminta konfirmasi.

### 2. `app:create-super-admin`
Membuat akun Super Admin baru menggunakan nilai yang didefinisikan dalam variabel lingkungan (.env).
- **Tujuan**: Secara otomatis menyediakan Super Admin baru menggunakan kredensial yang didefinisikan oleh `SUPER_ADMIN_NAME`, `SUPER_ADMIN_EMAIL`, dan `SUPER_ADMIN_PASSWORD` pada file `.env` Anda.
- **Penggunaan**:
  ```bash
  php artisan app:create-super-admin
  ```

### 3. `seed:data`
Menyediakan opsi pengisian data database (seeding) secara kustom.
- **Tujuan**: Mengizinkan pengisian data pada bagian tertentu dari database.
- **Penggunaan**:
  - Mengisi seluruh data:
    ```bash
    php artisan seed:data -a
    ```
  - Mengisi data user saja:
    ```bash
    php artisan seed:data --user
    ```
  - Mengisi seluruh data *kecuali* user:
    ```bash
    php artisan seed:data --no-user
    ```

### 4. `storage:clear-reports`
Menyediakan cara terarah untuk menghapus gambar laporan dari penyimpanan.
- **Tujuan**: Menghapus seluruh file di dalam direktori `reports` pada disk yang dikonfigurasi (`REPORT_DISK`, baik lokal `public` maupun cloud `s3`).
- **Penggunaan**:
  ```bash
  php artisan storage:clear-reports
  ```
- **Catatan**: Perintah ini akan meminta konfirmasi sebelum menghapus. Untuk melewati konfirmasi (misalnya, dalam skrip CI), gunakan:
  ```bash
  php artisan storage:clear-reports --no-interaction
  ```

### 5. `storage:migrate`
Menyalin file antar disk penyimpanan yang berbeda.
- **Tujuan**: Mentransfer seluruh file dari satu disk penyimpanan (misalnya `public` lokal) ke disk lain (misalnya `s3` / Cloudflare R2).
- **Penggunaan**:
  ```bash
  php artisan storage:migrate public s3
  ```

### 6. `storage:switch`
Mengubah disk penyimpanan aktif untuk laporan.
- **Tujuan**: Memperbarui kunci `REPORT_DISK` di file `.env` yang aktif secara programatik dan menghapus cache konfigurasi.
- **Penggunaan**:
  ```bash
  # Beralih ke Cloudflare R2 / S3
  php artisan storage:switch s3

  # Beralih ke penyimpanan lokal public
  php artisan storage:switch public
  ```

### 7. `token:set-system-expiry`
Mengatur durasi kedaluwarsa token API secara global/sistem.
- **Tujuan**: Menghitung total menit dari opsi yang diberikan dan menuliskan `SANCTUM_EXPIRATION` pada file `.env` Anda.
- **Penggunaan**:
  ```bash
  # Mengatur kedaluwarsa token menjadi 1 hari, 2 jam, dan 30 detik
  php artisan token:set-system-expiry --day=1 --minute=120 --second=30
  ```
- **Catatan**: Jalankan `php artisan config:clear` setelahnya untuk menerapkan perubahan.

### 8. `token:set-expiry`
Memaksa waktu kedaluwarsa tertentu pada token terbaru dari pengguna tertentu.
- **Tujuan**: Mengatur waktu kedaluwarsa langsung pada PersonalAccessToken terbaru milik pengguna target.
- **Penggunaan**:
  ```bash
  # Mengatur token terbaru milik pengguna ID 1 kedaluwarsa dalam 3 jam
  php artisan token:set-expiry 1 --minute=180
  ```

### 9. `token:set-remember`
Mengonfigurasi token terbaru pengguna untuk fungsi "Ingat Saya" (Remember Me).
- **Tujuan**: Memperpanjang masa aktif token terbaru menjadi tepat 3 hari (jika true) atau menyetelnya agar tidak pernah kedaluwarsa (jika false).
- **Penggunaan**:
  ```bash
  # Mengatur token kedaluwarsa dalam 3 hari
  php artisan token:set-remember 1 true

  # Mengatur token agar tidak pernah kedaluwarsa
  php artisan token:set-remember 1 false
  ```
