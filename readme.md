# 🗺️ Sistem Informasi Geografis (SIG) Bandar Lampung

Web-based Geographic Information System untuk analisis kepadatan penduduk dan potensi wilayah Kota Bandar Lampung.

## 📋 Daftar Isi

- [Tentang Project](#tentang-project)
- [Fitur Utama](#fitur-utama)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Struktur Project](#struktur-project)
- [Panduan Instalasi](#panduan-instalasi)
- [Cara Export Data dari QGIS](#cara-export-data-dari-qgis)
- [Setup Database](#setup-database)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Troubleshooting](#troubleshooting)
- [Tim Pengembang](#tim-pengembang)

## 📖 Tentang Project

Project ini merupakan implementasi Sistem Informasi Geografis berbasis web untuk menganalisis:

- **Kepadatan Penduduk** di 20 kecamatan Bandar Lampung
- **Potensi Pendidikan** (362 fasilitas)
- **Potensi Kesehatan** (24 rumah sakit)
- **Potensi Modal Sosial** (1.340 sarana ibadah)

## ✨ Fitur Utama

### 1. Peta Interaktif

- Visualisasi batas kecamatan dengan color coding berdasarkan kepadatan
- Toggle layer untuk fasilitas pendidikan, kesehatan, dan ibadah
- Popup informasi detail saat klik pada peta
- Legend dan scale control

### 2. Analisis Kepadatan

- Kategorisasi wilayah: Sangat Padat, Padat Sedang, Berpotensi
- Grafik dan tabel data kepadatan
- Rekomendasi pengembangan wilayah

### 3. Dashboard Statistik

- Statistik real-time dari database
- Visualisasi data dengan chart
- Export data (future enhancement)

## 🛠️ Teknologi yang Digunakan

### Backend

- **PHP 7.4+** - Server-side scripting
- **MySQL 5.7+** - Database management

### Frontend

- **HTML5/CSS3** - Structure & styling
- **JavaScript (ES6)** - Interactive features
- **Leaflet.js 1.9.4** - Web mapping library

### Tools

- **QGIS 3.x** - GIS data preparation
- **XAMPP/LAMP** - Local development server

## 📁 Struktur Project

```
sig-bandarlampung/
│
├── index.php                 # Halaman utama
├── README.md                 # Dokumentasi
│
├── config/
│   └── database.php         # Konfigurasi database
│
├── assets/
│   ├── css/
│   │   └── style.css       # Main stylesheet
│   ├── js/
│   │   ├── map.js          # Map initialization
│   │   └── analysis.js     # Analysis functions
│   └── images/
│       └── logo.png        # Logo aplikasi
│
├── data/
│   ├── geojson/            # GeoJSON files dari QGIS
│   │   ├── kecamatan.geojson
│   │   ├── pendidikan.geojson
│   │   ├── rumahsakit.geojson
│   │   └── ibadah.geojson
│   └── sql/
│       └── sig_bandarlampung.sql
│
├── pages/
│   ├── kepadatan.php       # Analisis kepadatan
│   ├── pendidikan.php      # Potensi pendidikan
│   ├── kesehatan.php       # Potensi kesehatan
│   └── sosial.php          # Potensi modal sosial
│
├── api/                     # REST API endpoints
│   ├── get_kecamatan.php
│   ├── get_facilities.php
│   └── analysis.php
│
└── libs/
    └── functions.php        # Helper functions
```

## 🚀 Panduan Instalasi

### Prasyarat

- XAMPP/LAMP/WAMP terinstall
- QGIS 3.x terinstall
- Web browser modern (Chrome, Firefox, Edge)
- Text editor (VS Code, Sublime, Notepad++)

### Langkah 1: Clone/Download Project

```bash
# Clone repository (jika menggunakan Git)
git clone https://github.com/username/sig-bandarlampung.git

# Atau download ZIP dan extract
```

### Langkah 2: Pindahkan ke Web Root

**Untuk XAMPP (Windows):**

```
Copy folder ke: C:/xampp/htdocs/sig-bandarlampung/
```

**Untuk LAMP (Linux):**

```bash
sudo cp -r sig-bandarlampung /var/www/html/
sudo chown -R www-data:www-data /var/www/html/sig-bandarlampung
```

### Langkah 3: Start Services

**XAMPP:**

1. Buka XAMPP Control Panel
2. Start Apache
3. Start MySQL

**LAMP:**

```bash
sudo systemctl start apache2
sudo systemctl start mysql
```

## 🗺️ Cara Export Data dari QGIS

### A. Export Batas Kecamatan

1. Buka QGIS dan load layer Kecamatan Bandar Lampung
2. Klik kanan layer → **Export** → **Save Features As...**
3. Pengaturan:
   - **Format**: GeoJSON
   - **File name**: `kecamatan.geojson`
   - **CRS**: EPSG:4326 - WGS 84
   - **Encoding**: UTF-8
4. Klik **OK**
5. Simpan ke folder: `data/geojson/`

### B. Tambahkan Atribut Kepadatan

Sebelum export, pastikan layer kecamatan memiliki field kepadatan:

1. Buka **Attribute Table** layer Kecamatan
2. Toggle **Editing Mode** (ikon pensil)
3. Klik **Open Field Calculator**
4. Buat field baru:
   - Output field name: `kepadatan`
   - Output field type: Decimal number (real)
   - Expression: `"jumlah_penduduk" / "luas_km2"`
5. Klik **OK** dan **Save Edits**

### C. Export Fasilitas (Pendidikan, Kesehatan, Ibadah)

Ulangi langkah export untuk setiap layer:

```
pendidikan.geojson  → 362 titik fasilitas pendidikan
rumahsakit.geojson  → 24 titik rumah sakit
ibadah.geojson      → 1340 titik sarana ibadah
```

**Tips:** Pastikan atribut mencakup minimal: nama, tipe, alamat, latitude, longitude

### D. Validasi GeoJSON

Gunakan https://geojson.io untuk validasi:

1. Upload file GeoJSON
2. Cek apakah peta muncul dengan benar
3. Periksa atribut di properties

## 💾 Setup Database

### Langkah 1: Buat Database

1. Akses phpMyAdmin: `http://localhost/phpmyadmin`
2. Klik tab **SQL**
3. Jalankan script berikut:

```sql
CREATE DATABASE sig_bandarlampung
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Langkah 2: Import SQL File

**Cara 1: Via phpMyAdmin**

1. Pilih database `sig_bandarlampung`
2. Klik tab **Import**
3. Choose file: `data/sql/sig_bandarlampung.sql`
4. Klik **Go**

**Cara 2: Via Command Line**

```bash
mysql -u root -p sig_bandarlampung < data/sql/sig_bandarlampung.sql
```

### Langkah 3: Konfigurasi Koneksi

Edit file `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Kosongkan untuk XAMPP default
define('DB_NAME', 'sig_bandarlampung');
```

### Langkah 4: Verifikasi

Test koneksi dengan mengakses: `http://localhost/sig-bandarlampung/`

## ▶️ Menjalankan Aplikasi

### 1. Start Web Server

Pastikan Apache dan MySQL sudah running.

### 2. Akses Aplikasi

Buka browser dan akses:

```
http://localhost/sig-bandarlampung/
```

### 3. Navigasi Menu

- **Beranda**: Dashboard utama dengan peta interaktif
- **Kepadatan Penduduk**: Analisis detail kepadatan
- **Pendidikan**: Pemetaan 362 fasilitas pendidikan
- **Kesehatan**: Lokasi 24 rumah sakit
- **Modal Sosial**: Distribusi 1.340 sarana ibadah

## 🐛 Troubleshooting

### Problem: Peta tidak muncul

**Solusi:**

1. Cek console browser (F12) untuk error JavaScript
2. Pastikan file GeoJSON ada di `data/geojson/`
3. Cek koneksi internet (untuk CDN Leaflet)

**Alternatif:** Download Leaflet secara lokal

```bash
# Download dari https://leafletjs.com/download.html
# Extract ke assets/libs/leaflet/
```

### Problem: Database connection error

**Solusi:**

1. Cek MySQL service: `sudo systemctl status mysql`
2. Verifikasi credentials di `config/database.php`
3. Test koneksi:

```bash
mysql -u root -p
USE sig_bandarlampung;
SHOW TABLES;
```

### Problem: GeoJSON tidak ter-load

**Solusi:**

1. Validasi format GeoJSON di https://geojson.io
2. Cek path file:

```javascript
// Di assets/js/map.js
fetch("data/geojson/kecamatan.geojson"); // Path relatif dari index.php
```

3. Cek permissions:

```bash
chmod 644 data/geojson/*.geojson
```

### Problem: 404 Not Found

**Solusi:**

1. Pastikan `.htaccess` ada (jika diperlukan)
2. Enable mod_rewrite:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Problem: Popup tidak muncul

**Solusi:**

1. Cek struktur properties di GeoJSON
2. Pastikan field `nama` dan `kepadatan` ada
3. Debug dengan console.log:

```javascript
console.log(feature.properties);
```

## 📊 Data yang Digunakan

Sumber data:

- **BPS Kota Bandar Lampung Dalam Angka 2024**
- **Data Penduduk Semester 2 Tahun 2023**
- **Disdukcapil Kota Bandar Lampung 2022/2023**

Data statistik:

- Total Kecamatan: **20**
- Total Penduduk: **~1.000.000 jiwa**
- Kepadatan Rata-rata: **5.986 jiwa/km²**
- Kecamatan Terpadat: **Tanjung Karang Timur** (18.619 jiwa/km²)

## 🔮 Future Enhancements

- [ ] User authentication (admin/user)
- [ ] CRUD untuk data fasilitas
- [ ] Analisis buffer (radius pelayanan)
- [ ] Routing (shortest path)
- [ ] Heatmap visualization
- [ ] Export laporan PDF
- [ ] Mobile responsive optimization
- [ ] RESTful API documentation
- [ ] Integration dengan Google Maps

## 👥 Tim Pengembang

1. Alia Rahayu (2317051079)
2. Bungaran Natanael S (2317051048)
3. Febrina Aulia Azahra (2317051010)
4. Wildan Mukmin (2317051080)
5. Oryza Surya Hapsari (2317051107)
6. Carissa Oktavia Sanjaya (2317051005)

**Jurusan Ilmu Komputer**  
**Fakultas Matematika dan Ilmu Pengetahuan Alam**  
**Universitas Lampung**  
**2025**

## 📝 License

Project ini dibuat untuk keperluan akademik.

## 📞 Kontak & Support

Untuk pertanyaan atau issue, silakan hubungi:

- Email: sig@ilkom.unila.ac.id
- GitHub Issues: [Link to issues]

---

**Happy Mapping! 🗺️**

# Panduan Setup Web SIG Bandar Lampung

## 1. Struktur Project

```
sig-bandarlampung/
│
├── index.php                 # Halaman utama
├── config/
│   └── database.php         # Konfigurasi database
│
├── assets/
│   ├── css/
│   │   └── style.css       # Styling
│   ├── js/
│   │   ├── map.js          # Inisialisasi peta
│   │   └── analysis.js     # Fungsi analisis
│   └── images/             # Logo, icons
│
├── data/
│   ├── geojson/            # File GeoJSON dari QGIS
│   │   ├── kecamatan.geojson
│   │   ├── pendidikan.geojson
│   │   ├── rumahsakit.geojson
│   │   └── ibadah.geojson
│   └── sql/
│       └── sig_bandarlampung.sql
│
├── pages/
│   ├── kepadatan.php       # Analisis kepadatan
│   ├── pendidikan.php      # Potensi pendidikan
│   ├── kesehatan.php       # Potensi kesehatan
│   └── sosial.php          # Potensi modal sosial
│
├── api/
│   ├── get_kecamatan.php   # API get data kecamatan
│   ├── get_facilities.php  # API get fasilitas
│   └── analysis.php        # API analisis spasial
│
└── libs/
    └── functions.php        # Helper functions
```

## 2. Persiapan Data dari QGIS

### Langkah 1: Export Data dari QGIS ke GeoJSON

**A. Export Batas Kecamatan:**

1. Buka QGIS project Anda
2. Klik kanan layer "Kecamatan Bandar Lampung"
3. Export → Save Features As...
4. Format: **GeoJSON**
5. CRS: **EPSG:4326 - WGS 84**
6. File name: `kecamatan.geojson`
7. Centang "Add saved file to map" (untuk verifikasi)

**B. Export Fasilitas Pendidikan:**

1. Klik kanan layer "Fasilitas Pendidikan"
2. Export → Save Features As...
3. Format: **GeoJSON**
4. CRS: **EPSG:4326**
5. File name: `pendidikan.geojson`
6. Pastikan atribut mencakup: nama, tipe, alamat

**C. Export Rumah Sakit:**

1. Sama seperti di atas
2. File name: `rumahsakit.geojson`

**D. Export Sarana Ibadah:**

1. Sama seperti di atas
2. File name: `ibadah.geojson`

### Langkah 2: Tambahkan Atribut Kepadatan ke Layer Kecamatan

Sebelum export, tambahkan data kepadatan:

1. Buka **Attribute Table** layer Kecamatan
2. Toggle Editing mode (pensil icon)
3. Open Field Calculator
4. Tambahkan field baru:
   - Field name: `kepadatan`
   - Field type: Decimal number (real)
   - Width: 10
   - Precision: 2
5. Input manual atau gunakan formula jika ada field luas dan jumlah penduduk:
   ```
   "jumlah_penduduk" / "luas_km2"
   ```
6. Save edits

## 3. Setup Database MySQL

### A. Buat Database

```sql
CREATE DATABASE sig_bandarlampung CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sig_bandarlampung;
```

### B. Tabel Kecamatan

```sql
CREATE TABLE kecamatan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    luas_km2 DECIMAL(10,2),
    jumlah_penduduk INT,
    kepadatan DECIMAL(10,2),
    rasio_jk DECIMAL(5,2),
    geojson TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### C. Tabel Fasilitas

```sql
CREATE TABLE fasilitas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(200),
    tipe ENUM('pendidikan','kesehatan','ibadah'),
    kategori VARCHAR(100),
    alamat TEXT,
    kecamatan_id INT,
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    geojson TEXT,
    FOREIGN KEY (kecamatan_id) REFERENCES kecamatan(id)
);
```

### D. Insert Data Kepadatan

```sql
INSERT INTO kecamatan (nama, luas_km2, jumlah_penduduk, kepadatan) VALUES
('Tanjung Karang Timur', NULL, NULL, 18619),
('Tanjung Karang Pusat', NULL, NULL, 14379),
('Kedaton', NULL, NULL, 13896),
('Bumi Waras', NULL, NULL, 12869),
('Teluk Betung Selatan', NULL, NULL, 11278),
('Teluk Betung Utara', NULL, NULL, 11550),
('Way Halim', NULL, NULL, 10955),
('Enggal', NULL, NULL, 9263),
('Langkapura', NULL, NULL, 8183),
('Labuhan Ratu', NULL, NULL, 7903),
('Tanjung Senang', NULL, NULL, 6753),
('Kedamaian', NULL, NULL, 6410),
('Sukarame', NULL, NULL, 6148),
('Bandar Lampung', NULL, NULL, 5986),
('Panjang', NULL, NULL, 5488),
('Tanjung Karang Barat', NULL, NULL, 5476),
('Teluk Betung Timur', NULL, NULL, 4805),
('Rajabasa', NULL, NULL, 4328),
('Kemiling', NULL, NULL, 4046),
('Sukabumi', NULL, NULL, 2922),
('Teluk Betung Barat', NULL, NULL, 2110);
```

## 4. Install Dependencies

### A. Requirements

- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx
- Composer (optional)

### B. Install Leaflet (Web Mapping Library)

Tidak perlu install, gunakan CDN di HTML:

```html
<!-- Leaflet CSS -->
<link
  rel="stylesheet"
  href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
/>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
```

## 5. Cara Import GeoJSON ke Database (Optional)

Anda bisa menyimpan GeoJSON langsung di folder atau di database:

### Method 1: File-based (Recommended untuk prototype)

Simpan file `.geojson` di folder `data/geojson/` dan load via AJAX

### Method 2: Database-based

```php
<?php
// Script untuk import GeoJSON ke database
$geojson_file = file_get_contents('data/geojson/kecamatan.geojson');
$geojson = json_decode($geojson_file, true);

foreach ($geojson['features'] as $feature) {
    $nama = $feature['properties']['nama'];
    $geojson_str = json_encode($feature['geometry']);

    $sql = "UPDATE kecamatan SET geojson = ? WHERE nama = ?";
    // Execute prepared statement
}
?>
```

## 6. Setup Web Server

### Menggunakan XAMPP (Windows):

1. Install XAMPP dari https://www.apachefriends.org
2. Copy folder project ke `C:/xampp/htdocs/sig-bandarlampung/`
3. Start Apache dan MySQL dari XAMPP Control Panel
4. Buka browser: `http://localhost/sig-bandarlampung/`

### Menggunakan LAMP (Linux):

```bash
sudo apt update
sudo apt install apache2 php mysql-server php-mysql
sudo systemctl start apache2
sudo cp -r sig-bandarlampung /var/www/html/
```

## 7. Testing

1. Akses: `http://localhost/sig-bandarlampung/`
2. Cek apakah peta muncul
3. Test klik pada kecamatan untuk melihat popup
4. Test menu navigasi ke halaman analisis

## 8. Troubleshooting

**Peta tidak muncul:**

- Cek console browser (F12) untuk error JavaScript
- Pastikan file GeoJSON path benar
- Cek koneksi internet untuk CDN Leaflet

**Database connection error:**

- Cek kredensial di `config/database.php`
- Pastikan MySQL service running
- Cek nama database sudah dibuat

**GeoJSON tidak ter-load:**

- Cek file exists di `data/geojson/`
- Cek format GeoJSON valid (gunakan geojson.io untuk validasi)
- Cek CORS jika menggunakan domain berbeda

## 9. Next Steps

Setelah prototype jalan:

1. Tambahkan fitur analisis spasial (buffer, intersect)
2. Implementasi pencarian fasilitas terdekat
3. Tambahkan layer control untuk toggle layer
4. Implementasi heatmap kepadatan
5. Export laporan PDF
6. Tambahkan user authentication untuk admin

## 10. Resources

- Leaflet Documentation: https://leafletjs.com/
- GeoJSON Spec: https://geojson.org/
- QGIS Tutorials: https://www.qgistutorials.com/
- PHP MySQL: https://www.php.net/manual/en/book.mysqli.php
