# Project Prerequisite & Setup Guide

This guide outlines the requirements and steps to get the SIFKA project running on your local machine.

## System Requirements

Before you begin, ensure you have the following installed:

- **PHP 8.5+** (Required for the latest Laravel features)
- **Composer** (PHP dependency manager)
- **Node.js & npm** (For frontend bundling)
- **MySQL 8.0+** (Or compatible database with Spatial support)
- **Git**

## Setup Steps

Follow these steps to clone and set up the project:

### 1. Clone the Repository
```bash
git clone <repository-url>
cd SIFKA
```

### 2. Install Dependencies
Install both backend and frontend dependencies:

```bash
# Install PHP dependencies
composer install

# Install JS dependencies
npm install
```

### 3. Environment Configuration
Copy the example environment file and generate an application key:

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Setup
1. Create a new database (e.g., `sifka_db`) in your MySQL server.
2. Update the `DB_*` variables in your `.env` file with your credentials.
3. Run the migrations and seeders:

```bash
php artisan migrate --seed
```

### 5. Storage Link
Create a symbolic link from `public/storage` to `storage/app/public` to make uploaded images accessible:

```bash
php artisan storage:link
```

### 6. Meilisearch Setup (Search Engine)
This project uses Meilisearch for search functionality.
1. Ensure Meilisearch is running (locally or via Docker).  
For Local System use:  
```
curl -L https://install.meilisearch.com | sh
```
And after meilisearch installed run it with:  
```
./meilisearch 
```
2. Update `MEILISEARCH_HOST` and `MEILISEARCH_KEY` in your `.env`.
3. Import the data to Meilisearch:

```bash
php artisan scout:import "App\Models\Report"
php artisan scout:import "App\Models\Facility"
```

### 7. Run the Application
Start the server:

```bash
# Start Laravel server
php artisan serve
```

## Image Management
By default, images are stored locally. If you wish to use a different disk, refer to `docs/imageManagement.md` and update the `REPORT_DISK` variable in your `.env`.

## Common Commands

- **Run Tests:** `php artisan test`
- **Fix Code Style:** `vendor/bin/pint`
- **Clear Cache:** `php artisan optimize:clear`
