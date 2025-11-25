<?php
// pages/pendidikan.php - Halaman Analisis Potensi Pendidikan
require_once '../config/database.php';

// Ambil data fasilitas pendidikan dari database
$sql = "SELECT f.*, k.nama as kecamatan_nama 
        FROM fasilitas f 
        LEFT JOIN kecamatan k ON f.kecamatan_id = k.id 
        WHERE f.tipe = 'pendidikan' 
        ORDER BY f.nama";
$pendidikan_data = fetchAll($sql);

// Hitung statistik per kategori
$sql_stats = "SELECT kategori, COUNT(*) as jumlah 
              FROM fasilitas 
              WHERE tipe = 'pendidikan' 
              GROUP BY kategori";
$stats_kategori = fetchAll($sql_stats);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Potensi Pendidikan - SIG Bandar Lampung</title>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        .facility-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        .facility-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            border-left: 4px solid #667eea;
        }
        .facility-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        .facility-card h4 {
            color: #667eea;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }
        .facility-card .category {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            background: #f0f4ff;
            color: #667eea;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .facility-info {
            font-size: 0.9rem;
            color: #666;
            line-height: 1.8;
        }
        .filter-section {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .filter-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .filter-btn {
            padding: 0.7rem 1.5rem;
            border: 2px solid #667eea;
            background: white;
            color: #667eea;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
        }
        .filter-btn:hover,
        .filter-btn.active {
            background: #667eea;
            color: white;
        }
        .search-box {
            padding: 0.8rem 1.2rem;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            font-size: 1rem;
            width: 100%;
            max-width: 400px;
            transition: border-color 0.3s;
        }
        .search-box:focus {
            outline: none;
            border-color: #667eea;
        }
        .distribution-chart {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-top: 2rem;
        }
        .chart-item {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
        }
        .chart-label {
            width: 150px;
            font-weight: 600;
            color: #333;
        }
        .chart-bar-container {
            flex: 1;
            background: #f0f4ff;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
            height: 40px;
        }
        .chart-bar {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            padding: 0 15px;
            color: white;
            font-weight: 600;
            transition: width 0.8s ease;
        }
        .recommendations {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem;
            border-radius: 15px;
            margin-top: 3rem;
        }
        .recommendation-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        .recommendation-item {
            background: rgba(255,255,255,0.1);
            padding: 1.5rem;
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }
        .recommendation-item h4 {
            color: white;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
<header>
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <h1>SIG UAS</h1>
            </div>
            <ul class="nav-menu">
                <li><a href="../index.php">Beranda</a></li>
                <li><a href="kepadatan.php">Kepadatan Penduduk</a></li>
                <li><a href="pendidikan.php" class="active">Pendidikan</a></li>
                <li><a href="kesehatan.php">Kesehatan</a></li>
            </ul>
        </div>
    </nav>
</header>

<main>

<section class="hero">
    <div class="container">
        <h2>Potensi Pendidikan Bandar Lampung</h2>
        <p>Pemetaan dan Analisis 362 Fasilitas Pendidikan</p>
    </div>
</section>

<section class="statistics">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <h4>Total Fasilitas</h4>
                <p class="stat-number">362</p>
                <span>unit pendidikan</span>
            </div>
            <div class="stat-card">
                <h4>Perguruan Tinggi</h4>
                <p class="stat-number">12+</p>
                <span>universitas & institut</span>
            </div>
            <div class="stat-card">
                <h4>Sekolah Menengah</h4>
                <p class="stat-number">150+</p>
                <span>SMP & SMA/SMK</span>
            </div>
            <div class="stat-card">
                <h4>Sekolah Dasar</h4>
                <p class="stat-number">200+</p>
                <span>SD & TK</span>
            </div>
        </div>
    </div>
</section>

<section class="map-section">
    <div class="container">
        <h3>Peta Persebaran Fasilitas Pendidikan</h3>
        <div id="map"></div>
    </div>
</section>

<section class="statistics">
    <div class="container">
        <h3>Analisis Distribusi Geografis</h3>
        <div class="analysis-grid">
            <div class="analysis-card">
                <h4>Wilayah Pusat & Utara (Konsentrasi Tinggi)</h4>
                <ul>
                    <li>Kepadatan fasilitas sangat tinggi</li>
                    <li>Pusat magnet pelajar</li>
                    <li>TK hingga Perguruan Tinggi lengkap</li>
                </ul>
            </div>

            <div class="analysis-card">
                <h4>Wilayah Berkembang (Konsentrasi Sedang)</h4>
                <ul>
                    <li>Distribusi fasilitas mulai merata</li>
                </ul>
            </div>

            <div class="analysis-card">
                <h4>Wilayah Pinggiran (Blank Spot)</h4>
                <ul>
                    <li>Kepadatan fasilitas sangat rendah</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="statistics">
    <div class="container">
        <h3>Distribusi Jenjang Pendidikan</h3>

        <div class="chart-item">
            <div class="chart-label">TK/PAUD</div>
            <div class="chart-bar-container">
                <div class="chart-bar" style="width:25%;">90 unit (25%)</div>
            </div>
        </div>

        <div class="chart-item">
            <div class="chart-label">SD/MI</div>
            <div class="chart-bar-container">
                <div class="chart-bar" style="width:30%;">110 unit (30%)</div>
            </div>
        </div>

        <div class="chart-item">
            <div class="chart-label">SMP/MTs</div>
            <div class="chart-bar-container">
                <div class="chart-bar" style="width:20%;">72 unit (20%)</div>
            </div>
        </div>

        <div class="chart-item">
            <div class="chart-label">SMA/SMK/MA</div>
            <div class="chart-bar-container">
                <div class="chart-bar" style="width:22%;">78 unit (22%)</div>
            </div>
        </div>

        <div class="chart-item">
            <div class="chart-label">Perguruan Tinggi</div>
            <div class="chart-bar-container">
                <div class="chart-bar" style="width:3%;">12 unit (3%)</div>
            </div>
        </div>

    </div>
</section>

<section class="statistics">
    <div class="container">
        <div class="recommendations">
            <h3>Rekomendasi Pengembangan</h3>

            <div class="recommendation-list">
                <div class="recommendation-item">
                    <h4>1. Perencanaan Zonasi</h4>
                    <ul>
                        <li>Prioritas USB</li>
                        <li>Analisis spasial lokasi optimal</li>
                    </ul>
                </div>

                <div class="recommendation-item">
                    <h4>2. Integrasi Transportasi</h4>
                    <ul>
                        <li>School zoning untuk wilayah padat</li>
                        <li>Rute aman sekolah</li>
                    </ul>
                </div>

                <div class="recommendation-item">
                    <h4>3. Klasterisasi</h4>
                    <ul>
                        <li>Education Hub di Gedong Meneng</li>
                    </ul>
                </div>

                <div class="recommendation-item">
                    <h4>4. Peningkatan Kualitas</h4>
                    <ul>
                        <li>Digitalisasi & smart classroom</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</section>

<?php if (count($pendidikan_data) > 0): ?>
<section class="statistics">
    <div class="container">
        <h3>Daftar Fasilitas Pendidikan</h3>
        
        <div class="filter-section">
            <input type="text" class="search-box" id="searchBox" placeholder="Cari nama sekolah...">
            <div class="filter-buttons" style="margin-top:1rem;">
                <button class="filter-btn active" data-filter="all">Semua</button>
                <button class="filter-btn" data-filter="SD">SD/MI</button>
                <button class="filter-btn" data-filter="SMP">SMP/MTs</button>
                <button class="filter-btn" data-filter="SMA">SMA/MA</button>
                <button class="filter-btn" data-filter="SMK">SMK</button>
                <button class="filter-btn" data-filter="Perguruan Tinggi">Perguruan Tinggi</button>
            </div>
        </div>

        <div class="facility-grid" id="facilityGrid">
            <?php foreach ($pendidikan_data as $sekolah): ?>
            <div class="facility-card" data-category="<?= htmlspecialchars($sekolah['kategori']) ?>">
                <span class="category"><?= htmlspecialchars($sekolah['kategori']) ?></span>
                <h4><?= htmlspecialchars($sekolah['nama']) ?></h4>
                <div class="facility-info">
                    <?php if ($sekolah['alamat']): ?>
                    <p><?= htmlspecialchars($sekolah['alamat']) ?></p>
                    <?php endif; ?>

                    <?php if ($sekolah['kecamatan_nama']): ?>
                    <p>Kec. <?= htmlspecialchars($sekolah['kecamatan_nama']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>
<?php endif; ?>

</main>

<footer>
    <div class="container">
        <p>&copy; 2025 Sistem Informasi Geografis Bandar Lampung</p>
        <p>Jurusan Ilmu Komputer - Universitas Lampung</p>
    </div>
</footer>

  <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Initialize map
        const map = L.map('map').setView([-5.3971, 105.2668], 12);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        // Marker cluster group
        const markers = L.markerClusterGroup({
            maxClusterRadius: 50,
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: true
        });
        
        // Custom school icon
        const schoolIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/3976/3976625.png',
            iconSize: [30, 30],
            iconAnchor: [15, 15],
            popupAnchor: [0, -15]
        });
        
        // Load education facilities
        fetch('../data/geojson/pendidikan.geojson')
            .then(response => response.json())
            .then(data => {
                L.geoJSON(data, {
                    pointToLayer: function(feature, latlng) {
                        return L.marker(latlng, {icon: schoolIcon});
                    },
                    onEachFeature: function(feature, layer) {
                        const props = feature.properties;
                        layer.bindPopup(`
                            <div style="min-width: 200px;">
                                <h4 style="color: #667eea; margin-bottom: 0.5rem;">
                                    🏫 ${props.nama || 'N/A'}
                                </h4>
                                <p style="margin: 0.3rem 0;"><strong>Jenis:</strong> ${props.kategori || 'N/A'}</p>
                                <p style="margin: 0.3rem 0;"><strong>Alamat:</strong> ${props.alamat || 'N/A'}</p>
                            </div>
                        `);
                    }
                }).addTo(markers);
                
                map.addLayer(markers);
            })
            .catch(error => {
                console.log('Education data not loaded, using dummy data');
                createDummyEducationMarkers();
            });
        
        // Dummy data if GeoJSON not available
        function createDummyEducationMarkers() {
            const dummySchools = [
                {name: 'SD Negeri 1 Tanjung Karang', lat: -5.4205, lng: 105.2668, type: 'SD'},
                {name: 'SMP Negeri 2 Bandar Lampung', lat: -5.4189, lng: 105.2701, type: 'SMP'},
                {name: 'SMA Negeri 1 Bandar Lampung', lat: -5.3893, lng: 105.2542, type: 'SMA'},
                {name: 'SMK Negeri 3 Bandar Lampung', lat: -5.4123, lng: 105.2789, type: 'SMK'},
                {name: 'Universitas Lampung', lat: -5.3586, lng: 105.2412, type: 'PT'}
            ];
            
            dummySchools.forEach(school => {
                L.marker([school.lat, school.lng], {icon: schoolIcon})
                    .bindPopup(`<h4>🏫 ${school.name}</h4><p><strong>Jenis:</strong> ${school.type}</p>`)
                    .addTo(markers);
            });
            
            map.addLayer(markers);
        }
        
        // Filter functionality
        const filterBtns = document.querySelectorAll('.filter-btn');
        const facilityCards = document.querySelectorAll('.facility-card');
        const searchBox = document.getElementById('searchBox');
        
        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                const filter = this.dataset.filter;
                
                facilityCards.forEach(card => {
                    if (filter === 'all' || card.dataset.category.includes(filter)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
        
        // Search functionality
        if (searchBox) {
            searchBox.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                facilityCards.forEach(card => {
                    const name = card.querySelector('h4').textContent.toLowerCase();
                    if (name.includes(searchTerm)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        }
        
        // Animate chart bars on scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.width = entry.target.dataset.width;
                }
            });
        });
        
        document.querySelectorAll('.chart-bar').forEach(bar => {
            bar.dataset.width = bar.style.width;
            bar.style.width = '0';
            observer.observe(bar);
        });
    </script>


</body>
</html>
        