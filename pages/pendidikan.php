<?php
// ===============================================
// LOAD DATA GEOJSON FASILITAS PENDIDIKAN
// ===============================================

$geojson_path = __DIR__ . '/../data/geojson/pendidikan.geojson';

if (!file_exists($geojson_path)) {
    die("GeoJSON fasilitas pendidikan tidak ditemukan: $geojson_path");
}

$geojson_data = json_decode(file_get_contents($geojson_path), true);
$features = $geojson_data['features'] ?? [];

// Parsing ke array agar mudah dipakai
$pendidikan_data = [];

$kategori_counter = [];

foreach ($features as $f) {
    $props = $f['properties'];

    $nama = $props['NAMOBJ'] ?? "Tidak diketahui";
    $kategori = $props['REMARK'] ?? "Lainnya";
    $alamat = "-"; // GeoJSON pendidikan kamu tidak memiliki alamat
    $kecamatan = "-"; // Tidak ada field kecamatan, jadi dikosongkan

    // Hitung statistik kategori
    if (!isset($kategori_counter[$kategori])) {
        $kategori_counter[$kategori] = 0;
    }
    $kategori_counter[$kategori]++;

    // Tambah data ke array final
    $pendidikan_data[] = [
        'nama'     => $nama,
        'kategori' => $kategori,
        'alamat'   => $alamat,
        'kecamatan_nama' => $kecamatan,
        'geometry' => $f['geometry'] ?? null
    ];
}
$jenjang_counter = [
    'TK/PAUD' => 0,
    'SD/MI' => 0,
    'SMP/MTs' => 0,
    'SMA/SMK/MA' => 0,
    'Perguruan Tinggi' => 0,
];

// Tentukan jenjang berdasar kategori
function classifyJenjang($kategori) {
    $kategori = strtoupper($kategori);

    if (str_contains($kategori, 'TK') || str_contains($kategori, 'PAUD'))
        return 'TK/PAUD';

    if (str_contains($kategori, 'SD') || str_contains($kategori, 'MI'))
        return 'SD/MI';

    if (str_contains($kategori, 'SMP') || str_contains($kategori, 'MTS'))
        return 'SMP/MTs';

    if (str_contains($kategori, 'SMA') || str_contains($kategori, 'SMK') || str_contains($kategori, 'MA'))
        return 'SMA/SMK/MA';

    if (
        str_contains($kategori, 'UNIV') || 
        str_contains($kategori, 'INSTITUT') || 
        str_contains($kategori, 'POLI') || 
        str_contains($kategori, 'SEKOLAH TINGGI')
    )
        return 'Perguruan Tinggi';

    return 'Lainnya';
}

$jenjang = classifyJenjang($kategori);
if (!isset($jenjang_counter[$jenjang])) {
    $jenjang_counter[$jenjang] = 0;
}
$jenjang_counter[$jenjang]++;

$total_fasilitas = count($pendidikan_data);

$distribusi_jenjang = [];
foreach ($jenjang_counter as $jenjang => $jumlah) {
    if ($jumlah > 0) {
        $persentase = round(($jumlah / $total_fasilitas) * 100, 2);
        $distribusi_jenjang[] = [
            'jenjang' => $jenjang,
            'jumlah' => $jumlah,
            'persen' => $persentase
        ];
    }
}


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
            color: black;
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

        <!-- Map Container -->
        <section class="map-section">
            <div class="container">
                <div class="map-controls">
                    <h3>Peta Interaktif Bandar Lampung</h3>
                    <div class="layer-controls">
                        <label>
                            <input type="checkbox" id="layer-kecamatan"> 
                            Batas Kecamatan
                        </label>
                        <label>
                            <input type="checkbox" id="layer-pendidikan" checked> 
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

        <?php 
        // Hitung total
        $total = array_sum($kategori_counter);

        // Loop setiap kategori secara dinamis
        foreach ($kategori_counter as $kategori => $jumlah):
            // Hitung persentase
            $persen = ($jumlah / $total) * 100;
            $rounded = round($persen, 1) + 10;
        ?>
        
        <div class="chart-item">
            <div class="chart-label"><?= htmlspecialchars($kategori) ?></div>
            <div class="chart-bar-container">
                <div class="chart-bar" style="width: <?= $rounded ?>%;">
                    <?= $jumlah ?> unit (<?= $rounded ?>%)
                </div>
            </div>
        </div>

        <?php endforeach; ?>
        
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
         <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- Custom JS -->
    <script src="/assets/js/map.js"></script>
    <script>
        // Filter functionality
        const facilityCards = document.querySelectorAll('.facility-card');
        const searchBox = document.getElementById('searchBox');
        
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
        