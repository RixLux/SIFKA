# Image Management Guide

This document explains how SIFKA manages report images and how to configure or migrate them across different environments.

## Overview

<details markdown="1">
<summary>Click to see details</summary>

SIFKA uses a decoupled approach for image management. Instead of hardcoding storage paths or URLs, we use a combination of Laravel's Filesystem abstraction and Eloquent Model accessors.

### Key Components

1.  **Database:** Only stores the *relative path* to the image (e.g., `reports/filename.jpg`) in the `image_path` column.
2.  **Configuration:** The storage disk is controlled via `config/filesystems.php` using the `REPORT_DISK` environment variable.
3.  **Model Accessor:** The `Report` model provides a dynamic `image_url` attribute that generates the full absolute URL based on the current configuration.

</details>

## Configuration

<details markdown="1">
<summary>Click to see details</summary>

You can control which storage disk is used for report images in your `.env` file:

```env
REPORT_DISK=public
```

> Options: public, s3, etc.

-   **`public` (Default):** Local storage in `storage/app/public`. Requires `php artisan storage:link`.
-   **`s3`:** Amazon S3 or compatible services (DigitalOcean Spaces, MinIO).

</details>

## Accessing Images

<details markdown="1">
<summary>Click to see details</summary>

### In PHP/Laravel
Always use the `image_url` property on the `Report` model:

```php
$report = Report::find(1);
echo $report->image_url; // https://sifka.test/storage/reports/abc.jpg
```

### In API Responses
The `ReportResource` automatically includes the `image_url`. The frontend should always use this field for rendering images.

</details>

## Migration Guide

<details markdown="1">
<summary>Click to see details</summary>

### 1. Moving from Local to Cloud (e.g., S3)

When moving from local development to production with S3:

1.  **Upload existing files:** Manually upload all files from `storage/app/public/reports/` to your S3 bucket's `reports/` directory.
2.  **Update `.env`:**
    ```env
    REPORT_DISK=s3
    AWS_ACCESS_KEY_ID=your_key
    AWS_SECRET_ACCESS_KEY=your_secret
    AWS_DEFAULT_REGION=your_region
    AWS_BUCKET=your_bucket
    AWS_URL=https://your-bucket.s3.amazonaws.com
    ```
3.  **Clear Cache:** `php artisan config:clear`

### 2. Updating Database Paths
If you change the directory structure on your storage disk, you may need to update the `image_path` in the database:

```sql
UPDATE reports SET image_path = REPLACE(image_path, 'old-dir/', 'new-dir/') WHERE image_path IS NOT NULL;
```

### Best Practices
- **Never save full URLs in the database.** This makes migrations extremely difficult.
- **Use the `image_url` attribute.** Avoid calling `Storage::url()` manually in controllers or views.
- **Check visibility.** If using a cloud provider, ensure the `visibility` is set to `public` in `config/filesystems.php` for that disk.

</details>

## Migrate to R2

<details markdown="1">
<summary>Click to see details</summary>

To migrate to Cloudflare R2 and easily switch between local and cloud storage, follow these steps. I
have also created two custom Artisan commands to automate the migration and switching process.

### 1. Install the S3 Driver
Cloudflare R2 is S3-compatible, but Laravel requires the AWS S3 Flysystem driver to interact with it.
```
composer require league/flysystem-aws-s3-v3
```

### 2. Configure Cloudflare R2 in .env
Add the following variables to your .env file. Replace the placeholders with your actual Cloudflare
R2 credentials.
```

</details>

# Cloudflare R2 Credentials
AWS_ACCESS_KEY_ID=your_r2_access_key_id
AWS_SECRET_ACCESS_KEY=your_r2_secret_access_key
AWS_DEFAULT_REGION=auto
AWS_BUCKET=your_bucket_name
AWS_ENDPOINT=https://<account_id>.r2.cloudflarestorage.com
AWS_USE_PATH_STYLE_ENDPOINT=true
```

    > Note: AWS_USE_PATH_STYLE_ENDPOINT must be set to true for R2 compatibility.  

### 3. Automated Migration & Switching

I have created two new Artisan commands to handle the transition:

- A. Migrate Files (storage:migrate)
This command copies all files from your local storage to R2 (or vice versa).
```
# Migrate from local 'public' disk to 's3' (R2)
php artisan storage:migrate public s3
```

- B. Switch Storage (storage:switch)
This command updates your .env file to toggle between storage disks.
```
# Switch to Cloudflare R2
php artisan storage:switch s3
```
```
# Switch back to local storage
php artisan storage:switch public
```

### 4. How the Code Handles It  
The application is already configured to use the REPORT_DISK environment variable for report images
(as seen in config/filesystems.php). The storage:switch command automatically updates this variable,
ensuring that:

- ReportController.php stores new images on the active disk.
- Report.php generates the correct URLs and handles deletions on the active disk.

Summary of Commands

| Task | Command |
| :--- | :--- |
| Install | `composer require league/flysystem-aws-s3-v3` |
| Migrate Files | `php artisan storage:migrate public s3` |
| Switch to R2 | `php artisan storage:switch s3` |
| Switch to Local | `php artisan storage:switch public` |
