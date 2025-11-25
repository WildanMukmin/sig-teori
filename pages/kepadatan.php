<?php
// =============================================================
// LOAD DATA GEOJSON
// =============================================================
$geojson_path = __DIR__ . '/../data/geojson/kecamatan.geojson';

if (!file_exists($geojson_path)) {
    die("GeoJSON tidak ditemukan: $geojson_path");
}

$geojson_data = json_decode(file_get_contents($geojson_path), true);

// Ambil fitur
$features = $geojson_data['features'] ?? [];

// Buat array kecamatan seperti format lama
$kecamatan_data = [];

foreach ($features as $f) {
    $props = $f['properties'];

    $kecamatan_data[] = [
        'nama'      => $props['Kecamatan'] ?? $props['NAMOBJ'] ?? 'Tidak diketahui',
        'kepadatan' => $props['Kepadatan'] ?? 0,
    ];
}

// =============================================================
// SORT kecamatan dari kepadatan tertinggi → terendah
// =============================================================
usort($kecamatan_data, function ($a, $b) {
    return $b['kepadatan'] <=> $a['kepadatan'];
});

// =============================================================
// Perhitungan statistik
// =============================================================
$total_kecamatan = count($kecamatan_data);

$total_kepadatan = array_sum(array_column($kecamatan_data, 'kepadatan'));
$rata_rata_kepadatan = ($total_kecamatan > 0) ? $total_kepadatan / $total_kecamatan : 0;

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis Kepadatan Penduduk - SIG Bandar Lampung</title>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">

    <style>
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .data-table th,
        .data-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        .data-table th {
            background: #667eea;
            color: white;
            font-weight: 600;
        }

        .data-table tr:hover {
            background: #f8f9fa;
        }

        .density-badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .badge-very-high {
            background: #8B0000;
            color: white;
        }

        .badge-high {
            background: #FF4500;
            color: white;
        }

        .badge-medium {
            background: #FFA500;
            color: white;
        }

        .badge-low {
            background: #90EE90;
            color: #333;
        }

        .chart-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            margin-top: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .chart-bar {
            display: flex;
            align-items: center;
            margin: 1rem 0;
        }

        .chart-label {
            width: 200px;
            font-size: 0.9rem;
        }

        .chart-bar-fill {
            height: 30px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 5px;
            transition: width 0.5s ease;
            display: flex;
            align-items: center;
            padding: 0 10px;
            color: white;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .stat-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: inline-block;
        }

        .icon-blue {
            background: #3b82f6;
        }

        .icon-green {
            background: #10b981;
        }

        .icon-red {
            background: #ef4444;
        }

        .icon-yellow {
            background: #f59e0b;
        }
    </style>

</head>

<body>

    <!-- Header -->
    <header>
        <nav class="navbar">
            <div class="container">
                <div class="logo">
                    <h1>SIG UAS</h1>
                </div>
                <ul class="nav-menu">
                    <li><a href="../index.php">Beranda</a></li>
                    <li><a href="kepadatan.php" class="active">Kepadatan Penduduk</a></li>
                    <li><a href="pendidikan.php">Pendidikan</a></li>
                    <li><a href="kesehatan.php">Kesehatan</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main>

        <section class="hero">
            <div class="container">
                <h2>Analisis Kepadatan Penduduk</h2>
                <p>Distribusi dan Kategorisasi Kepadatan di 20 Kecamatan</p>
            </div>
        </section>

        <!-- Statistik -->
        <section class="statistics">
            <div class="container">
                <div class="stats-grid">

                    <div class="stat-card">
                        <span class="stat-icon icon-blue"></span>
                        <h4>Total Kecamatan</h4>
                        <p class="stat-number"><?= $total_kecamatan ?></p>
                        <span>kecamatan</span>
                    </div>

                    <div class="stat-card">
                        <span class="stat-icon icon-green"></span>
                        <h4>Kepadatan Rata-rata</h4>
                        <p class="stat-number"><?= number_format($rata_rata_kepadatan, 0, ',', '.') ?></p>
                        <span>jiwa/km²</span>
                    </div>

                    <div class="stat-card">
                        <span class="stat-icon icon-red"></span>
                        <h4>Wilayah Sangat Padat</h4>
                        <p class="stat-number">4</p>
                        <span>kecamatan</span>
                    </div>

                    <div class="stat-card">
                        <span class="stat-icon icon-yellow"></span>
                        <h4>Wilayah Berpotensi</h4>
                        <p class="stat-number">11</p>
                        <span>kecamatan</span>
                    </div>

                </div>
            </div>
        </section>

        <!-- Grafik -->
        <section class="statistics">
            <div class="container">
                <div class="chart-container">
                    <h3>Grafik Kepadatan Penduduk per Kecamatan</h3>
                    <p style="color:#666;margin-bottom:1rem;">Top 10 Kecamatan Terpadat</p>

                    <?php
                    $max_density = 18619;
                    $top_10 = array_slice($kecamatan_data, 0, 10);
                    foreach ($top_10 as $kec):
                        $width_percent = ($kec['kepadatan'] / $max_density) * 100;
                    ?>
                        <div class="chart-bar">
                            <div class="chart-label"><?= $kec['nama'] ?></div>
                            <div class="chart-bar-fill" style="width: <?= $width_percent ?>%">
                                <?= number_format($kec['kepadatan'], 0, ',', '.') ?> jiwa/km²
                            </div>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>
        </section>

        <!-- Tabel -->
        <section class="statistics">
            <div class="container">
                <h3>Tabel Data Kepadatan Penduduk</h3>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kecamatan</th>
                            <th>Kepadatan</th>
                            <th>Kategori</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $no = 1;
                        foreach ($kecamatan_data as $kec):
                            if ($kec['kepadatan'] > 12000) {
                                $kategori = 'Sangat Padat';
                                $badge_class = 'badge-very-high';
                            } elseif ($kec['kepadatan'] > 10000) {
                                $kategori = 'Sangat Padat';
                                $badge_class = 'badge-high';
                            } elseif ($kec['kepadatan'] > 7000) {
                                $kategori = 'Padat Sedang';
                                $badge_class = 'badge-medium';
                            } else {
                                $kategori = 'Berpotensi';
                                $badge_class = 'badge-low';
                            }
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= $kec['nama'] ?></strong></td>
                                <td><?= number_format($kec['kepadatan'], 0, ',', '.') ?></td>
                                <td><span class="density-badge <?= $badge_class ?>"><?= $kategori ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </section>

        <!-- Analisis -->
        <section class="analysis">
            <div class="container">
                <h3>Insight dan Rekomendasi</h3>

                <div class="analysis-grid">

                    <div class="analysis-card">
                        <h4>Zona Merah — Sangat Padat</h4>
                        <ul>
                            <li><strong>Tanjung Karang Timur</strong> – 18.619 jiwa/km²</li>
                            <li><strong>Tanjung Karang Pusat</strong> – 14.379 jiwa/km²</li>
                            <li><strong>Kedaton</strong> – 13.896 jiwa/km²</li>
                            <li><strong>Bumi Waras</strong> – 12.869 jiwa/km²</li>
                        </ul>
                        <p><strong>Rekomendasi:</strong> Perlu peningkatan infrastruktur dan pengaturan tata ruang ketat.</p>
                    </div>

                    <div class="analysis-card">
                        <h4>Zona Kuning — Padat Sedang</h4>
                        <ul>
                            <li>Way Halim – 10.955 jiwa/km²</li>
                            <li>Enggal – 9.263 jiwa/km²</li>
                            <li>Langkapura – 8.183 jiwa/km²</li>
                            <li>Labuhan Ratu – 7.903 jiwa/km²</li>
                        </ul>
                        <p><strong>Rekomendasi:</strong> Penguatan transportasi publik dan fasilitas umum.</p>
                    </div>

                    <div class="analysis-card">
                        <h4>Zona Hijau — Berpotensi</h4>
                        <ul>
                            <li>Teluk Betung Barat – 2.110 jiwa/km²</li>
                            <li>Sukabumi – 2.922 jiwa/km²</li>
                            <li>Kemiling – 4.046 jiwa/km²</li>
                            <li>Rajabasa – 4.328 jiwa/km²</li>
                        </ul>
                        <p><strong>Rekomendasi:</strong> Area ini cocok untuk pengembangan permukiman baru.</p>
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
    <script src="/assets/js/map.js"></script>

</body>

</html>
