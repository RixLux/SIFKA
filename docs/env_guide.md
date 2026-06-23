# Environment Variables Setup Guide

This guide describes each configuration variable in `.env.example` and provides instructions on how to fill them for your local development and production environments in SIFKA.

## Configuration Categories

<details markdown="1">
<summary>Click to see details</summary>

### 1. Application Settings
Configure basic details of the Laravel application.

*   **`APP_NAME`**: The name of the application (e.g., `SIFKA`).
*   **`APP_ENV`**: The application environment. Use `local` for local development, `testing` for running tests, and `production` for production deployments.
*   **`APP_KEY`**: The application key used for encryption. Generate it using:
    ```bash
    php artisan key:generate
    ```
*   **`APP_DEBUG`**: Set to `true` to enable detailed error pages during development. Always set to `false` in production.
*   **`APP_URL`**: The primary base URL of your API (e.g., `http://localhost:8000`).
*   **`VITE_SERVICES_URL`**: The base URL of the API endpoints used by the Vite frontend (especially useful when accessing via tunnels like Tailscale).

### 2. Localization Settings
*   **`APP_LOCALE`**: The default language for translation. (e.g., `en` or `id`).
*   **`APP_FALLBACK_LOCALE`**: The fallback language if a translation is missing (e.g., `en`).
*   **`APP_FAKER_LOCALE`**: Locale used by Faker to generate dummy data (e.g., `en_US` or `id_ID`).

### 3. Security Settings
*   **`BCRYPT_ROUNDS`**: The cost factor for hashing passwords. The default is `12` for security.

### 4. Logging Configuration
*   **`LOG_CHANNEL`**: Where application logs should be sent. The default for development is `stack`.
*   **`LOG_STACK`**: Channels to include in the stack log (e.g., `single`).
*   **`LOG_LEVEL`**: The minimum severity level to log (e.g., `debug`, `info`, `warning`, `error`).

### 5. Database Connection
*   **`DB_CONNECTION`**: Database driver. The SIFKA project uses spatial features, which requires `mysql` or `mariadb`.
*   **`DB_HOST`**: Database server hostname (usually `127.0.0.1` or `localhost`).
*   **`DB_PORT`**: Port number (default for MySQL is `3306`).
*   **`DB_DATABASE`**: Name of the database (e.g., `SIFKA`).
*   **`DB_USERNAME`**: Database username.
*   **`DB_PASSWORD`**: Database password.

### 6. Session & Cache
*   **`SESSION_DRIVER`**: Driver used to persist sessions (e.g., `database`, `cookie`, `file`).
*   **`SESSION_LIFETIME`**: Session duration in minutes (e.g., `120`).
*   **`SESSION_ENCRYPT`**: Encrypt session data (`true` or `false`).
*   **`SESSION_PATH`**: Session cookie path (default: `/`).
*   **`SESSION_DOMAIN`**: Session cookie domain (default: `null`).
*   **`CACHE_STORE`**: Storage driver for caching. Default is `database` or `file`.
*   **`QUEUE_CONNECTION`**: Connection used for asynchronous jobs. Default is `database` (or `sync` for synchronous).

### 7. Redis & Memcached (Optional)
Used for high-performance session, cache, or queue storage.

*   **`REDIS_CLIENT`**: Redis client library (e.g., `phpredis`).
*   **`REDIS_HOST`**: Redis server hostname (default: `127.0.0.1`).
*   **`REDIS_PASSWORD`**: Redis server password.
*   **`REDIS_PORT`**: Redis port (default: `6379`).

### 8. Mail Setup
Configuration to send system emails.

*   **`MAIL_MAILER`**: Mail driver. Set to `log` during development to write emails to local log files, or `smtp` in production.
*   **`MAIL_HOST`**, **`MAIL_PORT`**, **`MAIL_USERNAME`**, **`MAIL_PASSWORD`**: SMTP server credentials.
*   **`MAIL_FROM_ADDRESS`**: Sender email address.
*   **`MAIL_FROM_NAME`**: Sender name.

### 9. File Storage & Report Images
*   **`REPORT_DISK`**: The disk configuration used to store report images.
    *   Set to `public` to store images locally inside `storage/app/public/reports/` (requires running `php artisan storage:link`).
    *   Set to `s3` to store images in S3-compatible cloud storage (AWS S3 or Cloudflare R2).
*   **`AWS_ACCESS_KEY_ID`**: Cloud access key ID.
*   **`AWS_SECRET_ACCESS_KEY`**: Cloud secret access key.
*   **`AWS_DEFAULT_REGION`**: Region (e.g., `auto` for Cloudflare R2).
*   **`AWS_BUCKET`**: Bucket name.
*   **`AWS_ENDPOINT`**: Endpoint URL (mandatory for Cloudflare R2, e.g., `https://<account_id>.r2.cloudflarestorage.com`).
*   **`AWS_USE_PATH_STYLE_ENDPOINT`**: Set to `true` for Cloudflare R2 compatibility.
*   **`AWS_URL`**: Public bucket URL for image access (e.g., `https://pub-<id>.r2.dev`).

### 10. Super Admin Credentials
These are used by the custom command `php artisan app:create-super-admin` to seed a super admin user.

*   **`SUPER_ADMIN_NAME`**: Full name of the initial admin user.
*   **`SUPER_ADMIN_EMAIL`**: Email address used to log in.
*   **`SUPER_ADMIN_PASSWORD`**: Strong password for authentication.

### 11. Laravel Scout & Meilisearch
Configures full-text search capability.

*   **`SCOUT_DRIVER`**: Search driver. Set to `meilisearch` to enable full-text indexing, or `database` for fallback.
*   **`MEILISEARCH_KEY`**: API key to authenticate with the Meilisearch server.

### 12. WebSocket Broadcasting (Laravel Reverb)
Enables real-time client updates.

*   **`REVERB_APP_ID`**, **`REVERB_APP_KEY`**, **`REVERB_APP_SECRET`**: App credentials generated or set for Reverb.
*   **`REVERB_HOST`**, **`REVERB_PORT`**, **`REVERB_SCHEME`**: WebSocket server connection details.
*   **`VITE_REVERB_APP_KEY`**, **`VITE_REVERB_HOST`**, **`VITE_REVERB_PORT`**, **`VITE_REVERB_SCHEME`**: Mirror variables used by the Vite frontend.

### 13. API Authentication (Laravel Sanctum)
*   **`SANCTUM_EXPIRATION`**: Token expiration time in minutes. (e.g. `1440` for 24 hours, or `null` for never).
*   **`SANCTUM_STATEFUL_DOMAINS`**: List of domains allowed to use stateful session authentication (comma-separated, e.g., `localhost:5173,127.0.0.1:5173`).

</details>

