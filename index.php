<?php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIG Bandar Lampung - Sistem Informasi Geografis</title>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header>
        <nav class="navbar">
            <div class="container">
                <div class="logo">
                    <h1>SIG Bandar Lampung</h1>
                </div>
                <ul class="nav-menu">
                    <li><a href="index.php" class="active">Beranda</a></li>
                    <li><a href="pages/kepadatan.php">Kepadatan Penduduk</a></li>
                    <li><a href="pages/pendidikan.php">Pendidikan</a></li>
                    <li><a href="pages/kesehatan.php">Kesehatan</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        <!-- Hero Section -->
        <section class="hero">
            <div class="container">
                <h2>Sistem Informasi Geografis Kota Bandar Lampung</h2>
                <p>Analisis Kepadatan Penduduk dan Potensi Wilayah</p>
            </div>
        </section>

        <!-- Map Container -->
        <section class="map-section">
            <div class="container">
                <div class="map-controls">
                    <h3>Peta Interaktif Bandar Lampung</h3>
                    <div class="layer-controls">
                        <label>
                            <input type="checkbox" id="layer-kecamatan" checked> 
                            Batas Kecamatan
                        </label>
                        <label>
                            <input type="checkbox" id="layer-pendidikan"> 
                            Fasilitas Pendidikan (362)
                        </label>
                        <label>
                            <input type="checkbox" id="layer-kesehatan"> 
                            Rumah Sakit (24)
                        </label>
                        <label>
                            <input type="checkbox" id="layer-ibadah"> 
                            Sarana Ibadah (1340)
                        </label>
                    </div>
                </div>

                <div id="map"></div>

                <div id="info-panel" class="info-panel">
                    <h4>Informasi Kecamatan</h4>
                    <p>Klik pada peta untuk melihat detail</p>
                </div>
            </div>
        </section>

        <!-- Statistics Section -->
        <section class="statistics">
            <div class="container">
                <h3>Statistik Kota Bandar Lampung</h3>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon"></div>
                        <h4>Kepadatan Rata-rata</h4>
                        <p class="stat-number">5.986</p>
                        <span>jiwa/km²</span>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"></div>
                        <h4>Fasilitas Pendidikan</h4>
                        <p class="stat-number">362</p>
                        <span>unit</span>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"></div>
                        <h4>Rumah Sakit</h4>
                        <p class="stat-number">24</p>
                        <span>unit</span>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"></div>
                        <h4>Sarana Ibadah</h4>
                        <p class="stat-number">1.340</p>
                        <span>unit</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Analysis Section -->
        <section class="analysis">
            <div class="container">
                <h3>Analisis Wilayah</h3>

                <div class="analysis-grid">
                    <div class="analysis-card">
                        <h4>Wilayah Sangat Padat</h4>
                        <ul>
                            <li>Tanjung Karang Timur (18.619 jiwa/km²)</li>
                            <li>Tanjung Karang Pusat (14.379 jiwa/km²)</li>
                            <li>Kedaton (13.896 jiwa/km²)</li>
                            <li>Bumi Waras (12.869 jiwa/km²)</li>
                        </ul>
                    </div>

                    <div class="analysis-card">
                        <h4>Wilayah Padat Sedang</h4>
                        <ul>
                            <li>Way Halim (10.955 jiwa/km²)</li>
                            <li>Enggal (9.263 jiwa/km²)</li>
                            <li>Langkapura (8.183 jiwa/km²)</li>
                            <li>Labuhan Ratu (7.903 jiwa/km²)</li>
                        </ul>
                    </div>

                    <div class="analysis-card">
                        <h4>Wilayah Berpotensi</h4>
                        <ul>
                            <li>Teluk Betung Barat (2.110 jiwa/km²)</li>
                            <li>Sukabumi (2.922 jiwa/km²)</li>
                            <li>Kemiling (4.046 jiwa/km²)</li>
                            <li>Rajabasa (4.328 jiwa/km²)</li>
                        </ul>
                        <p class="note">* Potensi pengembangan permukiman baru</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2025 Sistem Informasi Geografis Bandar Lampung</p>
            <p>Jurusan Ilmu Komputer - Universitas Lampung</p>
        </div>
    </footer>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/map.js"></script>
</body>
</html>
