# Progress Report Group 1

## **Project Description**

| No | Aspect | Description |
| --- | --- | --- |
| 1 | Background | Campus facility maintenance is often hindered by slow reporting processes and difficulties in determining accurate damage locations. Students and staff are often confused about where to report, and maintenance teams struggle to find coordinate locations from descriptive-only reports. |
| 2 | Project Goals | - Provide a fast, integrated facility damage reporting platform.<br>- Utilize Geo-Spatial (GPS) technology for location accuracy.<br>- Simplify tracking of repair statuses for both reporters and administrators (Staff/Admin). |
| 3 | System Overview | SIFKA consists of a Laravel-based Backend API and a React-based Frontend Dashboard. Users can select facilities on the map, submit reports with descriptions and photos, and include their GPS location. Staff receive notifications and update the status in real-time. Admins have full control over facility category master data. |
| 4 | Key Features | - Geo-Tagged Reporting: Automated GPS coordinate attachment.<br>- Role-based Access Control: Separated access for Students, Staff (Technician), and Admins.<br>- Category Management: CRUD controls for facility categories.<br>- Map Visualization: Interactive map view of facilities and reports.<br>- Image Upload: Attaching photo proofs of damage.<br>- Status Tracking: Step-by-step repair progress tracking. |
| 5 | Technologies Used | - Backend: Laravel 13, Sanctum (Auth), MariaDB (POINT Spatial).<br>- Frontend: React, Vite, Tailwind CSS, Zustand (State Management).<br>- Maps: mapcn component wrapper.<br>- Documentation: MkDocs. |


## **Project Progress**

| No | Module / Task | Status | Notes |
| --- | --- | --- | --- |
| 1 | **Initialization & Authentication** | Completed | Project setup and API Token Authentication |
| 2 | **Geo-Spatial Database Schema** | Completed | Migrated legacy coordinates to native POINT spatial types |
| 3 | **Core Logic & Security** | Completed | Robust API, FormRequests validation, and Rate Limiting |
| 4 | **Custom Artisan Commands** | Completed | Automation for app resetting, storage switches, token control, and migrations |

## **Documentation**

| No | Documentation Type | Description |
| --- | --- | --- |
| 1 | Flowchart / Diagram | Available in the docs directory |
| 2 | Use Case / Wireframe | Available in the docs directory |
| 3 | App Screenshots | Integrated in the frontend dashboard |
| 4 | Other | Static MkDocs website: <https://rixlux.github.io/SIFKA/> |

## **Challenges & Solutions**

| No | Challenge | Solution |
| --- | --- | --- |
| 1 | Endpoint Security | Explicitly restrict registration routes and assign default student role |
| 2 | Lack of facility addition endpoint | Created new building and facility endpoints for better master data control |
| 3 | Cloud Storage Integration | Built custom `storage:switch` and `storage:migrate` commands for Cloudflare R2 |

## **Overall Progress**

| Indicator | Value |
| --- | --- |
| Progress Percentage | 100 % |
| Project Status | On Track |
