# SIFKA

SIFKA is a robust, high-performance API built with Laravel 13, designed to manage and report on campus facilities. It features deep architectural alignment, advanced security hardening, and global search capabilities.

## Technical Stack
- **Framework:** Laravel 13 (PHP 8.3)
- **Security:** Laravel Sanctum (Auth), Rate Limiting (Throttling), Policy-based Authorization.
- **Search Engine:** Laravel Scout with Meilisearch.
- **Documentation:** Scramble (OpenAPI) and MKDocs.
- **Testing:** PHPUnit (hardened feature & unit tests).

## Key Architectural Features

### 1. Global Search (Meilisearch)
High-speed, fuzzy search implemented across all major resources

### 2. Payload Alignment & Strict Validation
The API maintains a predictable request/response contract. All resource inputs are standardized via **FormRequests**, ensuring that authorization and validation happen before any database execution.

- **Asymmetric Mapping:** Automatically converts flat frontend coordinates into structured backend geometry.
- **Security:** Unauthorized requests are rejected (403) prior to validation (422) to prevent metadata leaks.

### 3. Security Hardening
- **Throttling:** All authentication routes (`login`, `register`, `logout`) are protected by a `5 requests / minute` rate limiter.
- **Admin Controls:** Granular role management allowing Admins to register Staff accounts via the API.

## Project Documentation
For deep dives into the project's design and roadmap, refer to the `docs/` directory

## Getting Started

### Prerequisites
- PHP 8.3+
- Composer
- Meilisearch Server (Local or Cloud)

### Installation
1. **Clone & Install Dependencies:**
   ```bash
   composer install
   npm install && npm run build
   ```
2. **Environment Setup:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. **Database & Indexing:**
   ```bash
   php artisan migrate --seed
   php artisan scout:import "App\Models\Building"
   php artisan scout:import "App\Models\Category"
   php artisan scout:import "App\Models\Facility"
   php artisan scout:import "App\Models\Report"
   php artisan scout:import "App\Models\User"
   ```
4. **Start Project**  
    Example if u use Feesh and tmux.
    ```
    cat ~/.local/bin/SIFKA-dev 
    #!/bin/bash

    SESSION="sifka"

    # Meilisearch

    tmux new-session -d -s $SESSION -c "/var/home/awchan/Project/SIFKA" "fish -c './meilisearch --master-key mkVHdcZOS7CcBGgUAc6shTNDM8KAi9aVxU3oNTsgWTs'"

    # Reverb
    tmux split-window -h -t $SESSION -c "/var/home/awchan/Project/SIFKA" "fish -c 'php artisan reverb:start'"


    # Backend
    tmux select-pane -t 0
    tmux split-window -v -t $SESSION -c "/var/home/awchan/Project/SIFKA" "fish -c 'php artisan serve --host=0.0.0.0 --port=8000'"


    # Frontend Vite
    tmux select-pane -t 2
    tmux split-window -v -t $SESSION -c "/var/home/awchan/Project/SIFKA/FE_SIFKA" "fish -c 'npm run dev -- --host'"


    tmux attach-session -t $SESSION
    ```

    > Create script with that exact name or any name your choice and put it it into this location `~/.local/bin/` and set it as executable `chmod +x ~/.local/bin/SIFKA-dev`

    Then you can just simply start it with `SIFKA-dev`
   
   
## Testing
Run the comprehensive test suite to ensure stability:
```bash
php artisan test --compact
```

## Laravel Brain
This project uses **LaraMint Brain** for architectural analysis. To explore the codebase graph or export context snapshots:
```bash
php artisan brain:scan
```
