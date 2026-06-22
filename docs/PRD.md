# Product Requirement Document (PRD)

## Spatial Location Optimization & Map Integration

### 1. Overview & Objectives

Currently, the facility reporting application stores spatial locations (Buildings, Facilities, and Reports) using separate `latitude` and `longitude` numeric columns. To support interactive map rendering via `mapcn` and ensure performant spatial querying (e.g., pinpointing issues, bounding-box map filtering, and proximity searches), we are migrating the database architecture to use native MariaDB spatial data types (`POINT`).

#### Key Objectives:

* Optimize database query performance for interactive maps.
* Standardize the API layer to output native GeoJSON for seamless frontend integration with `mapcn`.
* Ensure accurate, centimeter-level pinpoint accuracy for reporting issues on a map.

---

### 2. User Stories & Features

#### 2.1 Facility Manager / Admin View (Map Dashboard)

* **As a** Facility Manager,
* **I want to** open a map view and instantly see clustered pins of all open maintenance reports,
* **So that** I can visually track which buildings or areas have the highest concentration of issues.

#### 2.2 Reporter View (Pinpoint Issue)

* **As a** field technician or reporter,
* **I want to** click/tap directly on a map to drop a pin exactly where an asset or issue is located,
* **So that** I don't have to guess or manually type coordinates.

---

### 3. Technical Architecture & Database Schema

The database layout will consolidate individual numeric coordinates into a single indexed spatial geometry field.

#### 3.1 Schema Definition

All tables tracking physical locations (`buildings`, `facilities`, `reports`) will implement the following column signature:

| Column Name | Data Type | Modifiers | Index Type | Purpose |
| --- | --- | --- | --- | --- |
| `location` | `POINT` | `NOT NULL` | `SPATIAL` | Stores standard geographic coordinates using `POINT(longitude latitude)` format. |

#### 3.2 Key Architectural Rules:

1. **Coordinate Ordering (Spatial Rule):** MariaDB and GeoJSON conform to the $X, Y$ coordinate structure. Therefore, all data transformations must handle coordinates in the explicit order of **`[Longitude, Latitude]`**.
2. **Spatial Reference System:** The coordinates must utilize SRID `4326` (WGS 84), matching the default output of standard web browser GPS and map tile providers.

---

### 4. System Implementation Plan

#### Phase 1: Database Setup (Laravel Migrations)

Because the app is in development, we will drop the old `latitude`/`longitude` columns entirely and replace them with the `geometry` field types.

```php
Schema::table('reports', function (Blueprint $table) {
    $table->dropColumn(['latitude', 'longitude']);
    // Create native point field with spatial indexing for rapid bounding-box scans
    $table->geometry('location', 'point')->spatialIndex(); 
});

```

#### Phase 2: Backend Backend Serialization (Eloquent Models)

Laravel models must automatically handle parsing MariaDB spatial types into readable geometry properties:

```php
use Illuminate\Database\Eloquent\Casts\AsGeometry;

class Report extends Model {
    protected $casts = [
        'location' => AsGeometry::class,
    ];
}

```

#### Phase 3: API Specification (GeoJSON Pipeline)

The Laravel API must return standard `FeatureCollection` formats directly to the frontend. This avoids making the React application parse coordinates manually inside loops.

```json
{
  "type": "FeatureCollection",
  "features": [
    {
      "type": "Feature",
      "geometry": {
        "type": "Point",
        "coordinates": [100.3506686, -0.8979667] 
      },
      "properties": {
        "id": 42,
        "description": "Water pipe leak in Hallway B",
        "status": "Open"
      }
    }
  ]
}

```

#### Phase 4: Frontend Map Engine Integration (`mapcn`)

* Implement `<Map>` wrapper using local styling configurations.
* Feed the GeoJSON payloads directly into the `<MapClusterLayer>` component provided by `mapcn`.
* Render detailed modal popups when an individual marker point is clicked.

---

### 5. Non-Functional Requirements & Performance

* **Query Latency:** Bounding box map queries utilizing `ST_Within` along with the `SPATIAL INDEX` must return data in less than 100ms when executing against a mock dataset of 10,000 markers.
* **Precision Guard:** Map pinpoint accuracy must maintain 6 decimal places of resolution, tracking item positions down to a $\sim 10\text{ cm}$ threshold.

---

### 6. Acceptance Criteria

| Criteria ID | Scenario | Given/When/Then | Status |
| --- | --- | --- | --- |
| **AC-01** | Successful Map Load | **Given** there are active facility reports,<br>

<br>**When** the user opens the map dashboard,<br>

<br>**Then** the system fetches the GeoJSON endpoint and `mapcn` correctly renders clustered pins. | ⬜ Pending |
| **AC-02** | Accurate Pin Placement | **Given** a user is creating a report,<br>

<br>**When** they click a specific spot on the map interface,<br>

<br>**Then** the backend correctly persists the coordinates into the database `POINT` column. | ⬜ Pending |
| **AC-03** | Database Integrity | **Given** a location entry is saved without coordinates,<br>

<br>**When** validation executes,<br>

<br>**Then** the database rejects the row with a `NOT NULL` constraint exception. | ⬜ Pending |
