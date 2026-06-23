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

#### 2.1 Admin Epic: Facility & Asset Management

##### 2.1.1 Dashboard & Map View

* **As an** Admin,
* **I want to** open a map view and instantly see clustered pins of all open maintenance reports,
* **So that** I can visually track which buildings or areas have the highest concentration of issues.

##### 2.1.2 Building Management

* **As an** Admin,
* **I want to** create, read, update, and delete (CRUD) building profiles (including name, address, and GPS coordinates),
* **So that** I can accurately map where facilities and maintenance issues are located.

##### 2.1.3 Facility & Category Management

* **As an** Admin,
* **I want to** define facility categories (e.g., Listrik/Electricity, Water, Field/Sports Area) and assign specific facilities to them,
* **So that** maintenance reports can be accurately categorized and routed to the right repair teams.

#### 2.2 Admin Epic: User Management

##### 2.2.1 User Administration

* **As an** Admin,
* **I want to** manage user accounts (create, view, edit status, or deactivate) and assign roles (e.g., Tenant, Staff, Maintenance Worker),
* **So that** I can control system access and ensure accountability across the platform.

#### 2.3 Student / Reporter Epic: Report & Issue Management

##### 2.3.1 Report Location Selection

* **As a** Student or Reporter,
* **I want to** click/tap directly on a map to drop a pin, fetch my current GPS location, or select an existing facility marker,
* **So that** I can accurately pinpoint exactly where an asset or maintenance issue is located without guessing coordinates.

##### 2.3.2 Report Management (My Reports)

* **As a** Student or Reporter,
* **I want to** view a list of my submitted reports and have the ability to edit the details or delete them if they are no longer relevant,
* **So that** I can keep my active submissions accurate and up to date.

##### 2.3.3 Tracking & Notifications

* **As a** Student or Reporter,
* **I want to** track the real-time status of my reports and receive instant notifications whenever there is an update (e.g., status changed to "In Progress" or "Resolved"),
* **So that** I know my concerns are being addressed without needing to manually check the app.

#### 2.4 Admin & Staff Epic: Report Operations & Triage

##### 2.4.1 Real-Time Monitoring & Alerts

* **As an** Admin or Staff member,
* **I want to** receive instant notifications and see a visual indicator whenever a new maintenance issue is submitted,
* **So that** I can immediately triage urgent problems and maintain a fast response time.

##### 2.4.2 Proximity & Radius Filtering

* **As an** Admin or Staff member,
* **I want to** place a pin on an interactive map and set a custom radius (e.g., within 50 meters) to filter and view all relevant reports in that specific zone,
* **So that** I can group nearby issues together and assign them to a single maintenance team efficiently.

##### 2.4.3 Advanced Report Filtering

* **As an** Admin or Staff member,
* **I want to** filter the list of reports by specific date ranges (e.g., all reports created in July) and current workflow status (Pending, In Progress, Resolved, Rejected),
* **So that** I can isolate backlog items, track monthly trends, or focus only on active issues.

##### 2.4.4 Global Search

* **As an** Admin or Staff member,
* **I want to** search for reports using keywords from the report title or the reporter's name,
* **So that** I can instantly find a specific submission when looking up a user's inquiry or a known issue.

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
| **AC-01** | Successful Map Load | - **Given** there are active facility reports,<br>  <br> - **When** the user opens the map dashboard,<br> <br>- **Then** the system fetches the GeoJSON endpoint and `mapcn` correctly renders clustered pins. |  Pending |
| **AC-02** | Accurate Pin Placement | - **Given** a user is creating a report,<br>  <br>- **When** they click a specific spot on the map interface,<br> <br>- **Then** the backend correctly persists the coordinates into the database `POINT` column. |  Pending |
| **AC-03** | Database Integrity |- **Given** a location entry is saved without coordinates,<br>  <br>- **When** validation executes,<br> <br>- **Then** the database rejects the row with a `NOT NULL` constraint exception. |  Pending |
