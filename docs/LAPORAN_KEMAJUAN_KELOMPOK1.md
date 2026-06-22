**Deskripsi Project**

+----+--------------------------+--------------------------------------+
| No | Aspek                    | Uraian                               |
+====+==========================+======================================+
| 1  | Latar Belakang           | Pemeliharaan fasilitas kampus        |
|    |                          | seringkali terhambat oleh lambatnya  |
|    |                          | proses pelaporan dan sulitnya        |
|    |                          | menentukan lokasi kerusakan yang     |
|    |                          | akurat. Mahasiswa dan staf           |
|    |                          | seringkali bingung harus melapor ke  |
|    |                          | mana, dan pihak pemeliharaan sulit   |
|    |                          | menemukan titik koordinat kerusakan  |
|    |                          | yang dilaporkan secara deskriptif    |
|    |                          | saja.                                |
+----+--------------------------+--------------------------------------+
| 2  | Tujuan Project           | -   Menyediakan platform pelaporan   |
|    |                          |     kerusakan fasilitas yang cepat   |
|    |                          |     dan terintegrasi.                |
|    |                          |                                      |
|    |                          | -   Menggunakan teknologi            |
|    |                          |     Geo-Spatial (GPS) untuk akurasi  |
|    |                          |     lokasi kerusakan.                |
|    |                          |                                      |
|    |                          | -   Mempermudah pemantauan status    |
|    |                          |     perbaikan bagi pelapor maupun    |
|    |                          |     pihak pengelola (Staff/Admin).   |
+----+--------------------------+--------------------------------------+
| 3  | Gambaran Sistem          | SIFKA terdiri dari Backend API       |
|    |                          | berbasis Laravel dan Frontend        |
|    |                          | Dashboard berbasis React. Pengguna   |
|    |                          | dapat memilih fasilitas yang         |
|    |                          | tersedia di peta, mengirimkan        |
|    |                          | laporan berupa deskripsi dan foto,   |
|    |                          | serta menyertakan lokasi GPS mereka  |
|    |                          | saat itu. Staff akan menerima        |
|    |                          | notifikasi dan dapat memperbarui     |
|    |                          | status perbaikan secara real-time.   |
|    |                          | Admin memiliki kendali penuh untuk   |
|    |                          | mengelola master data kategori       |
|    |                          | fasilitas.                           |
+----+--------------------------+--------------------------------------+
| 4  | Fitur Utama              | -   Geo-Tagged Reporting: Pelaporan  |
|    |                          |     dengan koordinat GPS otomatis.   |
|    |                          |                                      |
|    |                          | -   Role-based Access Control:       |
|    |                          |     Pemisahan hak akses antara       |
|    |                          |     Mahasiswa (Pelapor), Staff       |
|    |                          |     (Teknisi), dan Admin.            |
|    |                          |                                      |
|    |                          | -   Category Management: Admin dapat |
|    |                          |     menambah, mengubah, atau         |
|    |                          |     menghapus kategori fasilitas.    |
|    |                          |                                      |
|    |                          | -   Map Visualization: Visualisasi   |
|    |                          |     sebaran fasilitas dan titik      |
|    |                          |     kerusakan di Google Maps.        |
|    |                          |                                      |
|    |                          | -   Image Upload: Lampiran foto      |
|    |                          |     bukti kerusakan.                 |
|    |                          |                                      |
|    |                          | -   Status Tracking: Pemantauan      |
|    |                          |     tahapan perbaikan dari Pending   |
|    |                          |     hingga Resolved.                 |
+----+--------------------------+--------------------------------------+
| 5  | Teknologi yang Digunakan | -   Backend: Laravel 13, Sanctum     |
|    |                          |     (Auth), SQLite/MySQL.            |
|    |                          |                                      |
|    |                          | -   Frontend: React, Vite, Tailwind  |
|    |                          |     CSS, Zustand (State Management). |
|    |                          |                                      |
|    |                          | -   Maps: Google Maps JavaScript     |
|    |                          |     SDK.                             |
|    |                          |                                      |
|    |                          | -   Documentation: MkDocs.           |
+----+--------------------------+--------------------------------------+

**Kemajuan Project**

  ----------------------------------------------------------------------------
  No             Bagian yang        Status                   Keterangan
                 Dikerjakan         (Belum/Proses/Selesai)   
  -------------- ------------------ ------------------------ -----------------
  1              **Inisialisasi &   Selesai                  Persiapan Projek
                 Authentication**                            

  2              **Geo-Spatial      Selesai                  Menangani data
                 Database Schema**                           relasional dengan
                                                             presisi koordinat
                                                             tinggi.

  3              **Core Logic &     Selesai                  Membangun API
                 Security**                                  yang aman dan
                                                             efisien.

  4                                                          
  ----------------------------------------------------------------------------

**Dokumentasi**

  -------------------------------------------------------------------------------
  No     Jenis Dokumentasi               Keterangan
  ------ ------------------------------- ----------------------------------------
  1      Flowchart / Diagram             

  2      Use Case / Wireframe            

  3      Tampilan Aplikasi               

  4      Lainnya                         Situs Statis
                                         Link:<https://rixlux.github.io/SIFKA/>
  -------------------------------------------------------------------------------

**Kendala dan Solusi**

  ------------------------------------------------------------------------
  No    Kendala                                    Solusi
  ----- ------------------------------------------ -----------------------
  1     Keamanan Endpoint                          Secara eksplisit
                                                   menempatkan user saat
                                                   registrasi dengan role
                                                   'student'

  2     Kurangnya cara untuk menambah daftar       Update endpoint
        fasilitas                                  facilities agar
                                                   memiliki sifat yang
                                                   sama seperti categories

  3     Optimisasi redudancy pada database         Menambah endpoint
                                                   building untuk manage
                                                   lokasi bangunan
  ------------------------------------------------------------------------

**Rencana Selanjutnya**

  -----------------------------------------------------------------------
  No     Rencana Kegiatan                     Target Waktu
  ------ ------------------------------------ ---------------------------
  1\.    Optimisasi API                       Minggu depan

  2      Optimisasi query database            Minggu depan

  3      Merancang Tampilan UI                Minggu depan
  -----------------------------------------------------------------------

**Progress Keseluruhan**

  -----------------------------------------------------------------------
  Keterangan                          Nilai
  ----------------------------------- -----------------------------------
  Persentase Kemajuan                 \_\_ %

  Status Project                      On Track / Terlambat / Perlu
                                      Perbaikan
  -----------------------------------------------------------------------

### Screenshot

[]{.mark}

-   []{.mark}

[]{.mark}

-   []{.mark}

[]{.mark}

-   

[]{.mark}

-   

[]{.mark}

-   login-gagal

[]{.mark}

-   login-gagal[]{.mark}

-   []{.mark}

-   []{.mark}

-   

-   post-categories-user-gagal

[]{.mark}

[]{.mark}

[]{.mark}

[]{.mark}
