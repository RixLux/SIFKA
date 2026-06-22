# Migration & Deployment Guide: Spatial Data Overhaul

This document provides instructions for deploying the spatial data features (mapcn integration) and ensuring a smooth transition from legacy coordinate storage.

## 1. Database Migration

The application has migrated from separate `latitude` and `longitude` numeric columns to the native MariaDB/MySQL `POINT` spatial type.

### Running Migrations
To update your production/staging database, run:
```bash
php artisan migrate
```

### What happens during migration:
1.  A new `location` column of type `GEOMETRY (POINT, 4326)` is added to `buildings`, `facilities`, and `reports`.
2.  Existing numeric data is automatically converted to spatial points.
3.  Spatial indexes are created for high-performance proximity and bounding-box queries.
4.  Legacy `latitude` and `longitude` columns are dropped.

## 2. Meilisearch Indexing

The search engine now supports distance-based sorting and filtering using the `_geo` attribute.

### Re-indexing Data
After running the database migration, you **must** update the search indexes:
```bash
php artisan scout:import "App\Models\Building"
php artisan scout:import "App\Models\Facility"
php artisan scout:import "App\Models\Report"
```

## 3. Frontend & API Synchronization

The API now defaults to standard JSON, but supports optimized GeoJSON for map rendering.

### GeoJSON Endpoints
All resource index and search endpoints support the `format=geojson` query parameter:
*   `GET /api/buildings?format=geojson`
*   `GET /api/facilities/search?q=Library&format=geojson`

The frontend API client (`FE_SIFKA/src/api/client.js`) has been updated to use relative paths (`/api`) to work seamlessly with Vite proxies and tunnels like Tailscale Funnel.

## 4. Tailscale & External Access

To access the development environment via Tailscale Funnel (e.g., from a mobile device):

1.  **Frontend (Vite)**: Ensure `vite.config.js` has `host: '0.0.0.0'` and the tunnel domain in `allowedHosts`.
2.  **CORS**: `config/cors.php` must include the tunnel URL (e.g., `https://your-node.tailscale.net:5173`) in `allowed_origins`.
3.  **GPS/Geolocation**: Browser GPS features **require HTTPS**. Use Tailscale Funnel or a local SSL certificate to enable this feature on mobile devices.

## 5. Deployment Checklist
*   [ ] Run `php artisan migrate`
*   [ ] Re-import Scout models (Meilisearch)
*   [ ] Update `cors.php` with production/tunnel domains
*   [ ] Run `npm install` and `npm run build` for the frontend
