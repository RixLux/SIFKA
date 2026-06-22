# Custom Artisan Commands Guide

This document lists the custom Artisan commands created for the SIFKA project to simplify development and environment maintenance.

## Available Commands

### 1. `app:reset`
This command orchestrates the full reset of the development environment.

- **Purpose**: Wipes the database, re-runs all migrations, re-seeds the data, and clears all uploaded report images.
- **Usage**:
  ```bash
  php artisan app:reset
  ```
- **Note**: This runs the cleanup process automatically without prompting for confirmation.

### 2. `storage:clear-reports`
This command provides a targeted way to clear report images from storage.

- **Purpose**: Deletes all files within the `reports` directory on the configured `REPORT_DISK` (local or S3).
- **Usage**:
  ```bash
  php artisan storage:clear-reports
  ```
- **Note**: This will prompt for confirmation before deleting files. To skip the prompt (e.g., in scripts), use:
  ```bash
  php artisan storage:clear-reports --no-interaction
  ```

### 3. `app:create-super-admin`
- **Purpose**: Create a super admin user from environment variables.
- **Usage**:
  ```bash
  php artisan app:create-super-admin
  ```

---
*If you add more custom commands in the future, please update this file.*
