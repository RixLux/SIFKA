# Dokumen Persyaratan Produk (PRD)

## Optimisasi Lokasi Spasial & Integrasi Peta

### 1. Deskripsi Umum & Tujuan

Saat ini, aplikasi pelaporan fasilitas menyimpan lokasi spasial (Gedung, Fasilitas, dan Laporan) menggunakan kolom numerik `latitude` dan `longitude` yang terpisah. Untuk mendukung perenderan peta interaktif melalui `mapcn` dan memastikan kueri spasial yang berkinerja tinggi (misalnya, menentukan lokasi masalah secara presisi, penyaringan peta berdasarkan kotak pembatas/bounding-box, dan pencarian radius terdekat), kami memigrasikan arsitektur database untuk menggunakan tipe data spasial bawaan MariaDB (`POINT`).

#### Tujuan Utama:

* Mengoptimalkan kinerja kueri database untuk peta interaktif.
* Menstandarisasi lapisan API untuk menghasilkan GeoJSON asli agar integrasi dengan `mapcn` pada sisi frontend menjadi mulus.
* Memastikan akurasi titik koordinat hingga tingkat sentimeter saat melaporkan masalah di peta.

---

### 2. User Stories & Fitur

#### 2.1 Tampilan Pengelola Fasilitas / Admin (Dashboard Peta)

* **Sebagai seorang** Pengelola Fasilitas,
* **Saya ingin** membuka tampilan peta dan langsung melihat pin terklaster dari semua laporan pemeliharaan yang terbuka,
* **Sehingga** saya dapat memantau secara visual gedung atau area mana yang memiliki konsentrasi masalah tertinggi.

#### 2.2 Tampilan Pelapor (Menentukan Titik Masalah)

* **Sebagai seorang** teknisi lapangan atau pelapor,
* **Saya ingin** mengeklik/mengetuk langsung pada peta untuk meletakkan pin tepat di lokasi aset atau masalah berada,
* **Sehingga** saya tidak perlu menebak-nebak atau mengetik koordinat secara manual.

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
| **AC-01** | Pemuatan Peta Berhasil | **Given** terdapat laporan fasilitas yang aktif,<br><br>**When** pengguna membuka dashboard peta,<br><br>**Then** sistem mengambil data dari endpoint GeoJSON dan `mapcn` merender pin terklaster dengan benar. | ⬜ Tertunda |
| **AC-02** | Penempatan Pin Akurat | **Given** pengguna sedang membuat laporan,<br><br>**When** mereka mengeklik titik tertentu pada antarmuka peta,<br><br>**Then** backend berhasil menyimpan koordinat ke kolom `POINT` di database. | ⬜ Tertunda |
| **AC-03** | Integritas Database | **Given** entri lokasi disimpan tanpa koordinat,<br><br>**When** validasi dijalankan,<br><br>**Then** database menolak baris tersebut dengan pengecualian batasan `NOT NULL`. | ⬜ Tertunda |
