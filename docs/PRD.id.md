# Dokumen Persyaratan Produk (PRD)

## Optimisasi Lokasi Spasial & Integrasi Peta

### 1. Deskripsi Umum & Tujuan

Saat ini, aplikasi pelaporan fasilitas menyimpan lokasi spasial (Gedung, Fasilitas, dan Laporan) menggunakan kolom numerik `latitude` dan `longitude` yang terpisah. Untuk mendukung perenderan peta interaktif melalui `mapcn` dan memastikan kueri spasial yang berkinerja tinggi (misalnya, menentukan lokasi masalah secara presisi, penyaringan peta berdasarkan kotak pembatas/bounding-box, dan pencarian radius terdekat), kami memigrasikan arsitektur database untuk menggunakan tipe data spasial bawaan MariaDB (`POINT`).

#### Tujuan Utama:

* Mengoptimalkan kinerja kueri database untuk peta interaktif.
* Menstandarisasi lapisan API untuk menghasilkan GeoJSON asli agar integrasi dengan `mapcn` pada sisi frontend menjadi mulus.
* Memastikan akurasi titik koordinat hingga tingkat sentimeter saat melaporkan masalah di peta.

---

### 2. User Stories & Features (Kisah Pengguna & Fitur)

#### 2.1 Admin Epic: Facility & Asset Management (Manajemen Fasilitas & Aset)

##### 2.1.1 Dashboard & Map View (Dasbor & Tampilan Peta)

* **Sebagai** Admin,
* **Saya ingin** membuka tampilan peta dan langsung melihat pin yang terkelompok (*clustered*) dari semua laporan pemeliharaan yang terbuka,
* **Agar** saya dapat melacak secara visual gedung atau area mana yang memiliki konsentrasi masalah tertinggi.

##### 2.1.2 Building Management (Manajemen Gedung)

* **Sebagai** Admin,
* **Saya ingin** membuat, melihat, mengubah, dan menghapus (CRUD) profil gedung (termasuk nama, alamat, dan koordinat GPS),
* **Agar** saya dapat memetakan dengan akurat di mana lokasi fasilitas dan masalah pemeliharaan berada.

##### 2.1.3 Facility & Category Management (Manajemen Fasilitas & Kategori)

* **Sebagai** Admin,
* **Saya ingin** menentukan kategori fasilitas (contoh: Listrik, Air, Lapangan/Area Olahraga) dan menetapkan fasilitas tertentu ke dalam kategori tersebut,
* **Agar** laporan pemeliharaan dapat dikategorikan dengan akurat dan diteruskan ke tim perbaikan yang tepat.

#### 2.2 Admin Epic: User Management (Manajemen Pengguna)

##### 2.2.1 User Administration (Administrasi Pengguna)

* **Sebagai** Admin,
* **Saya ingin** mengelola akun pengguna (membuat, melihat, mengubah status, atau menonaktifkan) dan menetapkan peran (contoh: Penyewa/Tenant, Staf, Petugas Pemeliharaan),
* **Agar** saya dapat mengontrol akses sistem dan memastikan akuntabilitas di seluruh platform.

#### 2.3 Student / Reporter Epic: Report & Issue Management (Manajemen Laporan & Masalah oleh Mahasiswa/Pelapor)

##### 2.3.1 Report Location Selection (Pemilihan Lokasi Laporan)

* **Sebagai** Mahasiswa atau Pelapor,
* **Saya ingin** mengklik/mengetuk langsung pada peta untuk menaruh pin, mengambil lokasi GPS saya saat ini, atau memilih penanda fasilitas yang sudah ada,
* **Agar** saya dapat menunjukkan dengan akurat di mana tepatnya posisi aset atau masalah pemeliharaan tanpa harus menebak-nebak koordinat.

##### 2.3.2 Report Management (My Reports) [Manajemen Laporan - Laporan Saya]

* **Sebagai** Mahasiswa atau Pelapor,
* **Saya ingin** melihat daftar laporan yang telah saya kirimkan dan memiliki kemampuan untuk mengubah rinciannya atau menghapusnya jika sudah tidak relevan,
* **Agar** saya dapat menjaga data pengajuan aktif saya tetap akurat dan mutakhir.

##### 2.3.3 Tracking & Notifications (Pelacakan & Notifikasi)

* **Sebagai** Mahasiswa atau Pelapor,
* **Saya ingin** melacak status laporan saya secara *real-time* dan menerima notifikasi instan setiap kali ada pembaruan (contoh: status berubah menjadi "Dalam Proses" atau "Selesai"),
* **Agar** saya tahu bahwa keluhan saya sedang ditangani tanpa harus memeriksa aplikasi secara manual.

#### 2.4 Admin & Staff Epic: Report Operations & Triage (Operasi Laporan & Triase oleh Admin & Staf)

##### 2.4.1 Real-Time Monitoring & Alerts (Pemantauan & Peringatan Real-Time)

* **Sebagai** Admin atau Staf,
* **Saya ingin** menerima notifikasi instan dan melihat indikator visual setiap kali ada laporan masalah pemeliharaan yang baru masuk,
* **Agar** saya dapat segera melakukan triase (pemilahan) pada masalah yang mendesak dan menjaga waktu respons yang cepat.

##### 2.4.2 Proximity & Radius Filtering (Penyaringan Berdasarkan Jarak & Radius)

* **Sebagai** Admin atau Staf,
* **Saya ingin** menaruh pin pada peta interaktif dan menetapkan radius khusus (contoh: dalam radius 50 meter) untuk menyaring dan melihat semua laporan yang relevan di zona tersebut,
* **Agar** saya dapat mengelompokkan masalah-masalah yang berdekatan dan menugaskannya ke satu tim pemeliharaan secara efisien.

##### 2.4.3 Advanced Report Filtering (Penyaringan Laporan Lanjutan)

* **Sebagai** Admin atau Staf,
* **Saya ingin** menyaring daftar laporan berdasarkan rentang tanggal tertentu (contoh: semua laporan yang dibuat pada bulan Juli) dan status alur kerja saat ini (Menunggu/Pending, Dalam Proses, Selesai, Ditolak),
* **Agar** saya dapat memisahkan item yang tertunda (*backlog*), melacak tren bulanan, atau hanya fokus pada masalah yang sedang aktif.

##### 2.4.4 Global Search (Pencarian Global)

* **Sebagai** Admin atau Staf,
* **Saya ingin** mencari laporan menggunakan kata kunci dari judul laporan atau nama pelapor,
* **Agar** saya dapat menemukan pengajuan tertentu secara instan saat mencari info keluhan pengguna atau masalah yang sudah diketahui.

---

### 3. Arsitektur Teknis & Skema Database

Struktur database akan mengonsolidasikan koordinat numerik individu menjadi satu bidang geometri spasial yang diindeks.

#### 3.1 Definisi Skema

Semua tabel yang melacak lokasi fisik (`buildings`, `facilities`, `reports`) akan menerapkan tanda tangan kolom berikut:

| Nama Kolom | Tipe Data | Modifikator | Tipe Indeks | Tujuan |
| --- | --- | --- | --- | --- |
| `location` | `POINT` | `NOT NULL` | `SPATIAL` | Menyimpan koordinat geografis standar menggunakan format `POINT(longitude latitude)`. |

#### 3.2 Aturan Arsitektur Utama:

1. **Urutan Koordinat (Aturan Spasial):** MariaDB dan GeoJSON menyesuaikan dengan struktur koordinat $X, Y$. Oleh karena itu, semua transformasi data harus menangani koordinat dalam urutan eksplisit **`[Longitude, Latitude]`**.
2. **Sistem Referensi Spasial:** Koordinat harus menggunakan SRID `4326` (WGS 84), yang cocok dengan output default dari GPS peramban web standar dan penyedia peta.

---

### 4. Rencana Implementasi Sistem

#### Fase 1: Konfigurasi Database (Migrasi Laravel)

Karena aplikasi sedang dalam masa pengembangan, kami akan menghapus kolom `latitude`/`longitude` yang lama dan menggantinya dengan tipe bidang `geometry`.

```php
Schema::table('reports', function (Blueprint $table) {
    $table->dropColumn(['latitude', 'longitude']);
    // Membuat bidang titik asli dengan indeks spasial untuk pemindaian kotak pembatas yang cepat
    $table->geometry('location', 'point')->spatialIndex(); 
});
```

#### Fase 2: Serialisasi Model Eloquent (Backend)

Model Laravel harus secara otomatis menangani penguraian tipe spasial MariaDB ke properti geometri yang mudah dibaca:

```php
use Illuminate\Database\Eloquent\Casts\AsGeometry;

class Report extends Model {
    protected $casts = [
        'location' => AsGeometry::class,
    ];
}
```

#### Fase 3: Spesifikasi API (GeoJSON Pipeline)

Laravel API harus mengembalikan format `FeatureCollection` standar langsung ke frontend. Ini menghindari aplikasi React mengurai koordinat secara manual di dalam perulangan.

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
        "description": "Kebocoran pipa air di Koridor B",
        "status": "Terbuka"
      }
    }
  ]
}
```

#### Fase 4: Integrasi Peta Frontend (`mapcn`)

* Terapkan pembungkus `<Map>` menggunakan konfigurasi gaya lokal.
* Kirim payload GeoJSON langsung ke komponen `<MapClusterLayer>` yang disediakan oleh `mapcn`.
* Tampilkan popup modal detail saat penanda (marker) diklik.

---

### 5. Persyaratan Non-Fungsional & Kinerja

* **Latensi Kueri:** Kueri peta kotak pembatas yang menggunakan `ST_Within` bersama dengan `SPATIAL INDEX` harus mengembalikan data dalam waktu kurang dari 100ms saat dieksekusi pada dataset simulasi sebanyak 10.000 penanda.
* **Akurasi Presisi:** Akurasi titik peta harus mempertahankan resolusi hingga 6 angka di belakang koma, melacak posisi item hingga batas $\sim 10\text{ cm}$.

---

### 6. Kriteria Penerimaan (Acceptance Criteria)

| ID Kriteria | Skenario | Given/When/Then | Status |
| --- | --- | --- | --- |
| **AC-01** | Pemuatan Peta Berhasil | **Given** terdapat laporan fasilitas yang aktif,<br><br>**When** pengguna membuka dashboard peta,<br><br>**Then** sistem mengambil data dari endpoint GeoJSON dan `mapcn` merender pin terklaster dengan benar. |  Tertunda |
| **AC-02** | Penempatan Pin Akurat | **Given** pengguna sedang membuat laporan,<br><br>**When** mereka mengeklik titik tertentu pada antarmuka peta,<br><br>**Then** backend berhasil menyimpan koordinat ke kolom `POINT` di database. |  Tertunda |
| **AC-03** | Integritas Database | **Given** entri lokasi disimpan tanpa koordinat,<br><br>**When** validasi dijalankan,<br><br>**Then** database menolak baris tersebut dengan pengecualian batasan `NOT NULL`. |  Tertunda |
