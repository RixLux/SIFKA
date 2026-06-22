# Custom Artisan Commands Guide

This document lists the custom Artisan commands created for the SIFKA project to simplify development, testing, and environment maintenance.

## Available Commands

### 1. `app:reset`
Orchestrates a complete reset of the local development environment.
- **Purpose**: Wipes the database, re-runs all migrations, re-seeds default data, and clears all uploaded report images from storage.
- **Usage**:
  ```bash
  php artisan app:reset
  ```
- **Note**: This runs the entire cleanup process automatically without prompting for confirmation.

### 2. `app:create-super-admin`
Creates a new Super Admin account using values defined in the environment variables.
- **Purpose**: Automatically provisions a Super Admin using credentials defined by `SUPER_ADMIN_NAME`, `SUPER_ADMIN_EMAIL`, and `SUPER_ADMIN_PASSWORD` in your `.env`.
- **Usage**:
  ```bash
  php artisan app:create-super-admin
  ```

### 3. `seed:data`
Provides customized database seeding options.
- **Purpose**: Allows seeding specific parts of the database.
- **Usage**:
  - Seed all data:
    ```bash
    php artisan seed:data -a
    ```
  - Seed only users:
    ```bash
    php artisan seed:data --user
    ```
  - Seed all data *except* users:
    ```bash
    php artisan seed:data --no-user
    ```

### 4. `storage:clear-reports`
Provides a targeted way to clear report images from storage.
- **Purpose**: Deletes all files within the `reports` directory on the configured `REPORT_DISK` (local `public` or cloud `s3`).
- **Usage**:
  ```bash
  php artisan storage:clear-reports
  ```
- **Note**: This will prompt for confirmation before deleting. To skip the prompt (e.g., in CI or scripts), use:
  ```bash
  php artisan storage:clear-reports --no-interaction
  ```

### 5. `storage:migrate`
Copies files between different storage disks.
- **Purpose**: Transfers all files from one storage disk (e.g. `public`) to another (e.g. `s3` / Cloudflare R2).
- **Usage**:
  ```bash
  php artisan storage:migrate public s3
  ```

### 6. `storage:switch`
Changes the active storage disk for reports.
- **Purpose**: Programmatically updates the `REPORT_DISK` key in your active `.env` file and clears the config cache.
- **Usage**:
  ```bash
  # Switch to Cloudflare R2 / S3
  php artisan storage:switch s3

  # Switch to local public disk
  php artisan storage:switch public
  ```

### 7. `token:set-system-expiry`
Sets the system-wide API token expiration duration.
- **Purpose**: Calculates total minutes and writes `SANCTUM_EXPIRATION` to your `.env` file.
- **Usage**:
  ```bash
  # Set token expiration to 1 day, 2 hours, and 30 seconds
  php artisan token:set-system-expiry --day=1 --minute=120 --second=30
  ```
- **Note**: Run `php artisan config:clear` afterward to apply changes.

### 8. `token:set-expiry`
Forces an expiration time on a specific user's latest token.
- **Purpose**: Sets a direct expiration datetime on the latest PersonalAccessToken of a target user.
- **Usage**:
  ```bash
  # Set user 1's latest token to expire in 3 hours
  php artisan token:set-expiry 1 --minute=180
  ```

### 9. `token:set-remember`
Configures a user's latest token for "Remember Me" functionality.
- **Purpose**: Extends the latest token's life to exactly 3 days (if true) or resets it to never expire (if false).
- **Usage**:
  ```bash
  # Set token to expire in 3 days
  php artisan token:set-remember 1 true

  # Set token to never expire
  php artisan token:set-remember 1 false
  ```
