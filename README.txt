DASHBOARD MONITORING PERIZINAN MBLB
Web GIS Internal — PHP + SQLite + Leaflet + Chart.js + Turf.js

============================================================

DESKRIPSI SISTEM

Dashboard Monitoring Perizinan MBLB merupakan aplikasi berbasis Web GIS
yang digunakan untuk mendukung monitoring dan evaluasi perizinan
Mineral Bukan Logam dan Batuan (MBLB) pada Cabang Dinas ESDM Wilayah II.

Sistem ini memungkinkan:
- visualisasi data izin dalam bentuk peta interaktif
- analisis spasial usulan WIUP berbasis Excel
- deteksi indikasi overlap dengan izin existing
- penyajian data dalam bentuk statistik, grafik, dan tabel

Aplikasi ini dikembangkan sebagai bagian dari Proyek Aktualisasi
Latsar CPNS Tahun 2026.

============================================================

TUJUAN PENGEMBANGAN

- Meningkatkan efektivitas evaluasi perizinan berbasis spasial
- Mempermudah analisis WIUP, IUP, dan rekomendasi teknis
- Menyediakan sistem monitoring internal yang terintegrasi

============================================================

ARSITEKTUR SISTEM

Backend        : PHP
Database       : SQLite
Web Map        : Leaflet.js
Visualisasi    : Chart.js
Analisis GIS   : Turf.js
Input Data     : Excel (.xlsx)
Data Spasial   : GeoJSON

============================================================

STRUKTUR PROYEK

minerba_dashboard/

- index.php                 → Dashboard utama
- login.php                 → Halaman login
- proses_login.php          → Proses login
- logout.php                → Logout
- ubah_password.php         → Ubah password
- log_aktivitas.php         → Log aktivitas user

- includes/
  - db.php                  → Koneksi database
  - auth.php                → Proteksi halaman
  - activity_log.php        → Sistem logging

- data/
  - minerba.sqlite          → Database
  - iup.geojson             → Data izin existing
  - template_wiup.xlsx      → Template WIUP

- assets/
  - north-arrow.png         → Simbol arah utara

============================================================

CARA MENJALANKAN

1. Salin proyek ke komputer
   Contoh:
   D:\projek\minerba_dashboard

2. Buka Command Prompt
   cd /d D:\projek\minerba_dashboard

3. Jalankan server PHP
   php -S localhost:8000

4. Buka browser
   http://localhost:8000

Catatan:
- Tidak memerlukan MySQL
- Menggunakan SQLite
- Pastikan file GeoJSON tersedia

============================================================

SISTEM LOGIN

- Menggunakan autentikasi berbasis session
- Password disimpan dalam bentuk hash
- Halaman dilindungi menggunakan auth.php

============================================================

MANAJEMEN PASSWORD

Ubah Password:
- melalui halaman login → klik "Ubah Password"

Reset Password:
- dilakukan manual oleh admin (script sementara)
- tidak disimpan permanen demi keamanan

============================================================

SUMBER DATA

Data Existing:
- file: data/iup.geojson
- digunakan untuk peta, statistik, tabel, dan overlap

Data Usulan WIUP:
- format: Excel (.xlsx)
- digunakan untuk analisis spasial sementara

============================================================

FITUR UTAMA

FILTER DATA
- perusahaan / nomor SK
- kabupaten
- komoditas
- status izin

PETA INTERAKTIF
- polygon izin existing
- popup informasi
- basemap (OSM, Google, ESRI, Topografi)
- legenda, skala, north arrow

UKUR JARAK
- klik dua titik → tampil jarak
- mode interaksi polygon dinonaktifkan

FULLSCREEN MAP
- tampilan peta penuh

STATISTIK & GRAFIK
- total izin
- distribusi jenis izin
- komoditas

TABEL DATA
- sorting
- pagination
- highlight baris
- klik → fokus peta

UPLOAD WIUP
- upload Excel
- otomatis membentuk polygon

ANALISIS OVERLAP
- deteksi tumpang tindih
- ringkasan dan detail overlap

LOG AKTIVITAS
- login / logout
- lihat SK
- upload WIUP
- ubah password
- hapus log

============================================================

CARA PENGGUNAAN

1. Login ke sistem
2. Gunakan filter data
3. Analisis peta dan statistik
4. Upload WIUP (opsional)
5. Lihat hasil overlap
6. Gunakan fitur ukur jika diperlukan

============================================================

FORMAT EXCEL WIUP

Kolom wajib:
- urut
- longitude
- latitude

Fungsi:
- membentuk polygon usulan WIUP

============================================================

CATATAN PENTING

- Data existing tidak diubah melalui dashboard
- Upload WIUP hanya bersifat sementara
- Sistem digunakan secara internal
- Semua akses dilindungi login

============================================================

MANAJEMEN LOG

- Log disimpan di database
- Dapat dihapus melalui dashboard
- Disarankan menggunakan retensi (misalnya 90 hari)

============================================================

PENGEMBANGAN LANJUT

- multi-user system
- integrasi database spasial
- export laporan
- monitoring masa berlaku izin
- dashboard analitik lanjutan
- integrasi WebGIS

============================================================

PENUTUP

Aplikasi ini dikembangkan untuk mendukung pengelolaan data
perizinan berbasis peta agar proses evaluasi menjadi:

- lebih cepat
- lebih akurat
- lebih terintegrasi

============================================================