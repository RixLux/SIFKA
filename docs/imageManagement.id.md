# Panduan Manajemen Gambar

Dokumen ini menjelaskan bagaimana SIFKA mengelola gambar laporan dan cara mengonfigurasi atau memigrasikannya di berbagai lingkungan lingkungan.

## Gambaran Umum

<details markdown="1">
<summary>Click to see details</summary>

SIFKA menggunakan pendekatan terpisah (decoupled) untuk manajemen gambar. Alih-alih menuliskan path penyimpanan atau URL secara langsung (hardcoding), kami menggunakan kombinasi abstraksi Filesystem Laravel dan aksesor Model Eloquent.

### Komponen Utama

1.  **Database:** Hanya menyimpan *path relatif* ke gambar (misalnya, `reports/filename.jpg`) di kolom `image_path`.
2.  **Konfigurasi:** Disk penyimpanan dikendalikan via `config/filesystems.php` menggunakan variabel lingkungan `REPORT_DISK`.
3.  **Model Accessor (Aksesor Model):** Model `Report` menyediakan atribut dinamis `image_url` yang menghasilkan URL absolut lengkap berdasarkan konfigurasi saat ini.

</details>

## Konfigurasi

<details markdown="1">
<summary>Click to see details</summary>

Anda dapat mengontrol disk penyimpanan mana yang digunakan untuk gambar laporan di file `.env` Anda:

```env
REPORT_DISK=public
```
> Pilihan: public, s3, dll.

-   **`public` (Default):** Penyimpanan lokal di `storage/app/public`. Memerlukan perintah `php artisan storage:link`.
-   **`s3`:** Amazon S3 atau layanan yang kompatibel (seperti Cloudflare R2, DigitalOcean Spaces, MinIO).

</details>

## Mengakses Gambar

<details markdown="1">
<summary>Click to see details</summary>

### Di PHP/Laravel
Selalu gunakan properti `image_url` pada model `Report`:

```php
$report = Report::find(1);
echo $report->image_url; // https://sifka.test/storage/reports/abc.jpg
```

### Di API Responses
`ReportResource` secara otomatis menyertakan kolom `image_url`. Frontend harus selalu menggunakan kolom ini untuk merender gambar.

</details>

## Panduan Migrasi

<details markdown="1">
<summary>Click to see details</summary>

### 1. Berpindah dari Penyimpanan Lokal ke Cloud (misalnya S3 / Cloudflare R2)

Saat memindahkan sistem dari pengembangan lokal ke produksi dengan cloud storage:

1.  **Unggah file yang ada:** Unggah semua file secara manual dari `storage/app/public/reports/` ke direktori `reports/` pada bucket cloud Anda.
2.  **Perbarui `.env`:**
    ```env
    REPORT_DISK=s3
    AWS_ACCESS_KEY_ID=kunci_anda
    AWS_SECRET_ACCESS_KEY=rahasia_anda
    AWS_DEFAULT_REGION=wilayah_anda
    AWS_BUCKET=bucket_anda
    AWS_URL=https://bucket-anda.s3.amazonaws.com
    ```
3.  **Hapus Cache:** `php artisan config:clear`

### 2. Memperbarui Jalur (Path) Database
Jika Anda mengubah struktur direktori pada disk penyimpanan, Anda mungkin perlu memperbarui `image_path` di database:

```sql
UPDATE reports SET image_path = REPLACE(image_path, 'old-dir/', 'new-dir/') WHERE image_path IS NOT NULL;
```

</details>

## Praktik Terbaik

<details markdown="1">
<summary>Click to see details</summary>

- **Jangan pernah menyimpan URL lengkap di database.** Hal ini membuat migrasi lingkungan menjadi sangat sulit.
- **Gunakan atribut `image_url`.** Hindari memanggil `Storage::url()` secara manual di controller atau view.
- **Periksa Visibilitas.** Jika menggunakan penyedia cloud, pastikan visibilitas disetel ke `public` di `config/filesystems.php` untuk disk tersebut.

---

</details>

## Migrasi ke Cloudflare R2

<details markdown="1">
<summary>Click to see details</summary>

Untuk bermigrasi ke Cloudflare R2 dan beralih dengan mudah antara penyimpanan lokal dan cloud, ikuti langkah-langkah di bawah ini. Kami menyediakan perintah Artisan kustom untuk mengotomatiskan proses migrasi dan pengalihan disk ini.

### 1. Instal Driver S3
Cloudflare R2 kompatibel dengan S3, tetapi Laravel memerlukan driver AWS S3 Flysystem untuk berinteraksi dengannya.
```bash
composer require league/flysystem-aws-s3-v3
```

### 2. Konfigurasi Cloudflare R2 di .env
Tambahkan variabel berikut ke file `.env` Anda. Ganti nilai contoh dengan kredensial Cloudflare R2 Anda yang sebenarnya.
```env

</details>

# Kredensial Cloudflare R2
AWS_ACCESS_KEY_ID=id_akses_r2_anda
AWS_SECRET_ACCESS_KEY=kunci_rahasia_r2_anda
AWS_DEFAULT_REGION=auto
AWS_BUCKET=nama_bucket_anda
AWS_ENDPOINT=https://<account_id>.r2.cloudflarestorage.com
AWS_USE_PATH_STYLE_ENDPOINT=true
```

> **Catatan:** `AWS_USE_PATH_STYLE_ENDPOINT` harus disetel ke `true` untuk kompatibilitas dengan R2.

### 3. Migrasi & Pengalihan Otomatis
Dua perintah Artisan kustom berikut digunakan untuk menangani transisi penyimpanan:

#### A. Migrasi File (`storage:migrate`)
Perintah ini menyalin semua file dari penyimpanan lokal Anda ke R2 (atau sebaliknya).
```bash
# Migrasi dari disk lokal 'public' ke 's3' (R2)
php artisan storage:migrate public s3
```

#### B. Pengalihan Penyimpanan (`storage:switch`)
Perintah ini memperbarui file `.env` Anda untuk beralih di antara disk penyimpanan yang digunakan.
```bash
# Beralih ke Cloudflare R2
php artisan storage:switch s3

# Beralih kembali ke penyimpanan lokal
php artisan storage:switch public
```

### 4. Bagaimana Kode Menanganinya
Aplikasi dikonfigurasi untuk menggunakan variabel lingkungan `REPORT_DISK` untuk gambar laporan (sebagaimana didefinisikan dalam `config/filesystems.php`). Perintah `storage:switch` secara otomatis memperbarui variabel ini, memastikan bahwa:

- `ReportController.php` menyimpan gambar baru pada disk yang aktif.
- `Report.php` menghasilkan URL yang benar dan menangani penghapusan pada disk yang aktif.

### Ringkasan Perintah

| Tugas                   |Perintah                                    |
| --- | --- |
| Instalasi Driver         | composer require league/flysystem-aws-s3-v3 
| Migrasi File             |php artisan storage:migrate public s3       
| Beralih ke Cloudflare R2 |php artisan storage:switch s3               
| Beralih ke Lokal         |php artisan storage:switch public          

