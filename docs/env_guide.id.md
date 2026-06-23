# Panduan Konfigurasi Variabel Lingkungan (.env)

Panduan ini menjelaskan setiap variabel konfigurasi yang ada di `.env.example` dan memberikan instruksi tentang cara mengisinya untuk lingkungan pengembangan lokal maupun produksi pada proyek SIFKA.

## Kategori Konfigurasi

<details markdown="1">
<summary>Click to see details</summary>

### 1. Pengaturan Aplikasi
Konfigurasikan informasi dasar dari aplikasi Laravel.

*   **`APP_NAME`**: Nama aplikasi (misalnya, `SIFKA`).
*   **`APP_ENV`**: Lingkungan jalannya aplikasi. Gunakan `local` untuk pengembangan lokal, `testing` untuk menjalankan pengujian (test suite), dan `production` untuk deployment produksi.
*   **`APP_KEY`**: Kunci aplikasi yang digunakan untuk enkripsi. Hasilkan kunci ini menggunakan perintah:
    ```bash
    php artisan key:generate
    ```
*   **`APP_DEBUG`**: Setel ke `true` untuk mengaktifkan halaman kesalahan (error) detail selama pengembangan. Selalu setel ke `false` di lingkungan produksi.
*   **`APP_URL`**: URL dasar utama API Anda (misalnya, `http://localhost:8000`).
*   **`VITE_SERVICES_URL`**: URL dasar untuk endpoint API yang digunakan oleh frontend Vite (sangat berguna ketika diakses melalui tunnel seperti Tailscale).

### 2. Pengaturan Lokalisasi (Localization)
*   **`APP_LOCALE`**: Bahasa default untuk penerjemahan aplikasi (misalnya, `en` atau `id`).
*   **`APP_FALLBACK_LOCALE`**: Bahasa cadangan jika penerjemahan bahasa utama tidak ditemukan (misalnya, `en`).
*   **`APP_FAKER_LOCALE`**: Locale yang digunakan oleh library Faker untuk menghasilkan data palsu/dummy (misalnya, `en_US` atau `id_ID`).

### 3. Pengaturan Keamanan
*   **`BCRYPT_ROUNDS`**: Faktor biaya (cost factor) untuk hashing kata sandi. Default adalah `12` demi alasan keamanan.

### 4. Konfigurasi Logging
*   **`LOG_CHANNEL`**: Saluran tempat log aplikasi akan dikirimkan. Default untuk pengembangan lokal adalah `stack`.
*   **`LOG_STACK`**: Saluran yang dimasukkan ke dalam log stack (misalnya, `single`).
*   **`LOG_LEVEL`**: Tingkat keparahan minimum log yang akan dicatat (misalnya, `debug`, `info`, `warning`, `error`).

### 5. Koneksi Database
*   **`DB_CONNECTION`**: Driver database. Proyek SIFKA menggunakan fitur spasial, sehingga memerlukan database yang mendukung seperti `mysql` atau `mariadb`.
*   **`DB_HOST`**: Hostname server database (biasanya `127.0.0.1` atau `localhost`).
*   **`DB_PORT`**: Port database (default untuk MySQL/MariaDB adalah `3306`).
*   **`DB_DATABASE`**: Nama database yang digunakan (misalnya, `SIFKA`).
*   **`DB_USERNAME`**: Username database.
*   **`DB_PASSWORD`**: Password database.

### 6. Sesi & Cache
*   **`SESSION_DRIVER`**: Driver yang digunakan untuk menyimpan sesi (misalnya, `database`, `cookie`, `file`).
*   **`SESSION_LIFETIME`**: Durasi sesi aktif dalam hitungan menit (misalnya, `120`).
*   **`SESSION_ENCRYPT`**: Menentukan apakah data sesi dienkripsi (`true` atau `false`).
*   **`SESSION_PATH`**: Path cookie sesi (default: `/`).
*   **`SESSION_DOMAIN`**: Domain cookie sesi (default: `null`).
*   **`CACHE_STORE`**: Driver penyimpanan untuk caching. Default adalah `database` atau `file`.
*   **`QUEUE_CONNECTION`**: Koneksi yang digunakan untuk antrean pekerjaan (queue jobs) asinkron. Default adalah `database` (atau `sync` untuk sinkron/tanpa antrean background).

### 7. Redis & Memcached (Opsional)
Digunakan untuk performa tinggi pada sesi, cache, atau antrean.

*   **`REDIS_CLIENT`**: Pustaka klien Redis (misalnya, `phpredis`).
*   **`REDIS_HOST`**: Hostname server Redis (default: `127.0.0.1`).
*   **`REDIS_PASSWORD`**: Password server Redis.
*   **`REDIS_PORT`**: Port Redis (default: `6379`).

### 8. Pengaturan Email
Konfigurasi untuk mengirim email sistem.

*   **`MAIL_MAILER`**: Driver email. Setel ke `log` selama pengembangan lokal untuk menulis email ke file log lokal, atau `smtp` di lingkungan produksi.
*   **`MAIL_HOST`**, **`MAIL_PORT`**, **`MAIL_USERNAME`**, **`MAIL_PASSWORD`**: Kredensial server SMTP Anda.
*   **`MAIL_FROM_ADDRESS`**: Alamat email pengirim.
*   **`MAIL_FROM_NAME`**: Nama pengirim yang tampil.

### 9. Penyimpanan File & Gambar Laporan
*   **`REPORT_DISK`**: Konfigurasi disk penyimpanan yang digunakan untuk gambar laporan.
    *   Setel ke `public` untuk menyimpan gambar secara lokal di dalam folder `storage/app/public/reports/` (memerlukan eksekusi perintah `php artisan storage:link`).
    *   Setel ke `s3` untuk menyimpan gambar di penyimpanan cloud yang kompatibel dengan S3 (seperti AWS S3 atau Cloudflare R2).
*   **`AWS_ACCESS_KEY_ID`**: Access key ID untuk cloud storage.
*   **`AWS_SECRET_ACCESS_KEY`**: Secret access key untuk cloud storage.
*   **`AWS_DEFAULT_REGION`**: Region cloud storage (misalnya, `auto` untuk Cloudflare R2).
*   **`AWS_BUCKET`**: Nama bucket penyimpanan.
*   **`AWS_ENDPOINT`**: URL endpoint cloud storage (wajib untuk Cloudflare R2, misalnya, `https://<account_id>.r2.cloudflarestorage.com`).
*   **`AWS_USE_PATH_STYLE_ENDPOINT`**: Setel ke `true` untuk kompatibilitas Cloudflare R2.
*   **`AWS_URL`**: URL publik bucket untuk akses langsung gambar (misalnya, `https://pub-<id>.r2.dev`).

### 10. Kredensial Super Admin
Kredensial ini digunakan oleh perintah kustom `php artisan app:create-super-admin` untuk membuat user admin awal secara otomatis.

*   **`SUPER_ADMIN_NAME`**: Nama lengkap user admin awal.
*   **`SUPER_ADMIN_EMAIL`**: Alamat email yang digunakan untuk masuk (login).
*   **`SUPER_ADMIN_PASSWORD`**: Kata sandi yang kuat untuk autentikasi.

### 11. Laravel Scout & Meilisearch
Mengonfigurasi kemampuan pencarian teks lengkap (full-text search).

*   **`SCOUT_DRIVER`**: Driver pencarian. Setel ke `meilisearch` untuk mengaktifkan indeks teks lengkap, atau `database` sebagai cadangan.
*   **`MEILISEARCH_KEY`**: Kunci API untuk autentikasi dengan server Meilisearch Anda.

### 12. WebSocket Broadcasting (Laravel Reverb)
Memungkinkan pembaruan data secara real-time pada sisi klien.

*   **`REVERB_APP_ID`**, **`REVERB_APP_KEY`**, **`REVERB_APP_SECRET`**: Kredensial aplikasi yang dihasilkan atau diatur untuk Reverb.
*   **`REVERB_HOST`**, **`REVERB_PORT`**, **`REVERB_SCHEME`**: Detail koneksi server WebSocket.
*   **`VITE_REVERB_APP_KEY`**, **`VITE_REVERB_HOST`**, **`VITE_REVERB_PORT`**, **`VITE_REVERB_SCHEME`**: Variabel cermin (mirror) yang digunakan oleh frontend Vite.

### 13. Autentikasi API (Laravel Sanctum)
*   **`SANCTUM_EXPIRATION`**: Waktu kedaluwarsa token dalam hitungan menit (misalnya, `1440` untuk 24 jam, atau `null` agar tidak pernah kedaluwarsa).
*   **`SANCTUM_STATEFUL_DOMAINS`**: Daftar domain yang diizinkan untuk menggunakan autentikasi sesi stateful (dipisahkan koma, misalnya, `localhost:5173,127.0.0.1:5173`).

</details>

