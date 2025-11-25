<?php
// pages/kesehatan.php - Halaman Analisis Potensi Kesehatan
require_once '../config/database.php';

// Ambil data fasilitas kesehatan dari database
$sql = "SELECT f.*, k.nama as kecamatan_nama 
        FROM fasilitas f 
        LEFT JOIN kecamatan k ON f.kecamatan_id = k.id 
        WHERE f.tipe = 'kesehatan' 
        ORDER BY f.nama";
$kesehatan_data = fetchAll($sql);

// Hitung total rumah sakit
$total_rs = count($kesehatan_data) > 0 ? count($kesehatan_data) : 24;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Potensi Kesehatan - SIG Bandar Lampung</title>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        .hospital-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .hospital-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s;
            border-top: 5px solid #667eea;
        }
        
        .hospital-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .hospital-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .hospital-icon {
            font-size: 3rem;
        }
        
        .hospital-card h4 {
            color: #333;
            font-size: 1.2rem;
            margin: 0;
        }
        
        .hospital-type {
            display: inline-block;
            padding: 0.4rem 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin: 0.5rem 0;
        }
        
        .hospital-info {
            margin-top: 1rem;
            font-size: 0.95rem;
            color: #666;
            line-height: 1.8;
        }
        
        .hospital-info p {
            margin: 0.5rem 0;
            display: flex;
            align-items: start;
            gap: 0.5rem;
        }
        
        .info-icon {
            color: #667eea;
            font-weight: bold;
            min-width: 20px;
        }

        /* COE */
        .coe-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem;
            border-radius: 15px;
            margin-top: 3rem;
        }
        
        .coe-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .coe-card {
            background: rgba(255,255,255,0.15);
            padding: 2rem;
            border-radius: 12px;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255,255,255,0.2);
            transition: all 0.3s;
        }
        
        .coe-card:hover {
            background: rgba(255,255,255,0.25);
            transform: scale(1.05);
        }
        
        .coe-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        /* Coverage */
        .coverage-analysis {
            background: white;
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-top: 2rem;
        }
        
        .coverage-item {
            display: flex;
            align-items: center;
            padding: 1.5rem;
            margin: 1rem 0;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 5px solid #667eea;
        }
        
        .coverage-number {
            font-size: 3rem;
            font-weight: bold;
            color: #667eea;
            min-width: 100px;
        }
        
        .coverage-text {
            flex: 1;
        }

        /* Network */
        .network-diagram {
            background: white;
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-top: 2rem;
        }
        
        .network-connections {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .network-node {
            text-align: center;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 15px;
            min-width: 200px;
            position: relative;
        }

        /* Map */
        #map {
            width: 100%;
            height: 500px;
            border-radius: 15px;
            margin-top: 1rem;
        }

        .buffer-legend {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 1000;
        }

        .buffer-item {
            display: flex;
            align-items: center;
            margin: 0.5rem 0;
            font-size: 0.9rem;
        }

        .buffer-color {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            margin-right: 0.8rem;
            border: 2px solid #333;
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
                    <li><a href="kepadatan.php">Kepadatan Penduduk</a></li>
                    <li><a href="pendidikan.php">Pendidikan</a></li>
                    <li><a href="kesehatan.php" class="active">Kesehatan</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Hero -->
    <section class="hero">
        <div class="container">
            <h2>Potensi Layanan Kesehatan</h2>
            <p>Ekosistem 24 Rumah Sakit & Fasilitas Kesehatan</p>
        </div>
    </section>

    <!-- Statistics -->
    <section class="statistics">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"></div>
                    <h4>Total Rumah Sakit</h4>
                    <p class="stat-number"><?= $total_rs ?></p>
                    <span>fasilitas kesehatan</span>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"></div>
                    <h4>RS Tipe A & B</h4>
                    <p class="stat-number">8</p>
                    <span>rumah sakit rujukan</span>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"></div>
                    <h4>Tenaga Medis</h4>
                    <p class="stat-number">2,500+</p>
                    <span>dokter & perawat</span>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"></div>
                    <h4>Kapasitas Tempat Tidur</h4>
                    <p class="stat-number">3,000+</p>
                    <span>bed capacity</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Map -->
    <section class="map-section">
        <div class="container">
            <h3>Peta Sebaran Rumah Sakit & Buffer Zone</h3>
            <p style="color:#666;margin-bottom:1rem;">
                Lingkaran menunjukkan radius pelayanan 3–5 km per rumah sakit
            </p>

            <div id="map" class="accessibility-map"></div>

            <div class="buffer-legend">
                <h4 style="margin-bottom: 0.8rem; font-size: 0.95rem;">Radius Pelayanan</h4>
                <div class="buffer-item">
                    <div class="buffer-color" style="background: rgba(102, 126, 234, 0.3);"></div>
                    <span>3 km - Akses Mudah</span>
                </div>
                <div class="buffer-item">
                    <div class="buffer-color" style="background: rgba(102, 126, 234, 0.15);"></div>
                    <span>5 km - Jangkauan Normal</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Coverage -->
    <section class="statistics">
        <div class="container">
            <div class="coverage-analysis">
                <h3>Analisis Jangkauan Pelayanan</h3>
                
                <div class="coverage-item">
                    <div class="coverage-number">95%</div>
                    <div class="coverage-text">
                        <h4>Coverage Area</h4>
                        <p>Hampir seluruh wilayah Bandar Lampung tercakup dalam radius 5 km dari rumah sakit terdekat.</p>
                    </div>
                </div>
                
                <div class="coverage-item">
                    <div class="coverage-number">3-5</div>
                    <div class="coverage-text">
                        <h4>Radius Pelayanan (km)</h4>
                        <p>Rata-rata jarak tempuh dari permukiman ke RS terdekat adalah 3–5 km.</p>
                    </div>
                </div>
                
                <div class="coverage-item">
                    <div class="coverage-number">8:1</div>
                    <div class="coverage-text">
                        <h4>Rasio RS : Kecamatan</h4>
                        <p>24 RS untuk 20 kecamatan — rata-rata 1–2 RS per kecamatan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- COE -->
    <section class="statistics">
        <div class="container">
            <div class="coe-section">
                <h3>Pengembangan Center of Excellence (COE)</h3>
                <p style="margin-bottom:1rem;">
                    Strategi spesialisasi layanan kesehatan
                </p>

                <div class="coe-grid">
                    <div class="coe-card">
                        <div class="coe-icon"></div>
                        <h4>Heart Center</h4>
                        <p>RS Abdul Moeloek sebagai pusat jantung.</p>
                    </div>

                    <div class="coe-card">
                        <div class="coe-icon"></div>
                        <h4>Oncology Center</h4>
                        <p>Unit Kanker - RS Advent & RS Urip.</p>
                    </div>

                    <div class="coe-card">
                        <div class="coe-icon"></div>
                        <h4>Orthopaedic Center</h4>
                        <p>Bedah tulang dan rehabilitasi.</p>
                    </div>

                    <div class="coe-card">
                        <div class="coe-icon"></div>
                        <h4>Neuroscience Center</h4>
                        <p>Layanan stroke 24/7.</p>
                    </div>

                    <div class="coe-card">
                        <div class="coe-icon"></div>
                        <h4>Women & Children Center</h4>
                        <p>NICU, PICU, dan layanan obstetri.</p>
                    </div>

                    <div class="coe-card">
                        <div class="coe-icon"></div>
                        <h4>Pulmonary Center</h4>
                        <p>Pusat penyakit paru & pernafasan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Network -->
    <section class="statistics">
        <div class="container">
            <div class="network-diagram">
                <h3>Jaringan Rujukan Kesehatan</h3>

                <div class="network-connections">
                    <div class="network-node">
                        <div class="network-node-icon"></div>
                        <h4>Puskesmas</h4>
                        <p>Layanan Primer<br>30+ Puskesmas</p>
                    </div>

                    <div class="network-node">
                        <div class="network-node-icon"></div>
                        <h4>RS Tipe C/D</h4>
                        <p>Layanan Sekunder<br>16 RS</p>
                    </div>

                    <div class="network-node">
                        <div class="network-node-icon"></div>
                        <h4>RS Tipe A/B</h4>
                        <p>Layanan Tersier<br>8 RS</p>
                    </div>

                    <div class="network-node">
                        <div class="network-node-icon"></div>
                        <h4>RS Nasional</h4>
                        <p>Super Spesialistik<br>Jakarta</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Daftar RS -->
    <section class="statistics">
        <div class="container">
            <h3>Daftar Rumah Sakit</h3>
            
            <div class="hospital-grid">
                <?php if (count($kesehatan_data) > 0): ?>
                    <?php foreach ($kesehatan_data as $rs): ?>
                    <div class="hospital-card">
                        <div class="hospital-header">
                            <div class="hospital-icon"></div>
                            <div>
                                <h4><?= htmlspecialchars($rs['nama']) ?></h4>
                                <span class="hospital-type"><?= htmlspecialchars($rs['kategori']) ?></span>
                            </div>
                        </div>
                        <div class="hospital-info">
                            <?php if ($rs['alamat']): ?>
                            <p><span class="info-icon"></span> <?= htmlspecialchars($rs['alamat']) ?></p>
                            <?php endif; ?>
                            <?php if ($rs['kecamatan_nama']): ?>
                            <p><span class="info-icon"></span> Kec. <?= htmlspecialchars($rs['kecamatan_nama']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Dummy Data Jika DB Kosong -->
                    <div class="hospital-card">
                        <div class="hospital-header">
                            <div class="hospital-icon"></div>
                            <div>
                                <h4>RSUD Abdul Moeloek</h4>
                                <span class="hospital-type">RS Tipe A</span>
                            </div>
                        </div>
                        <div class="hospital-info">
                            <p><span class="info-icon"></span> Jl. Dr. Rivai No.6, Teluk Betung</p>
                            <p><span class="info-icon"></span> (0721) 703312</p>
                            <p><span class="info-icon"></span> 500+ Bed | 24 Jam</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        // Initialize map
        const map = L.map('map').setView([-5.3971, 105.2668], 12);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        // Hospital icon
        const hospitalIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
            iconSize: [35, 35],
            iconAnchor: [17, 17],
            popupAnchor: [0, -17]
        });
        
        // Load hospital data
        fetch('../data/geojson/rumahsakit.geojson')
            .then(response => response.json())
            .then(data => {
                L.geoJSON(data, {
                    pointToLayer: function(feature, latlng) {
                        // Add buffer circles
                        L.circle(latlng, {
                            radius: 3000,
                            color: '#667eea',
                            fillColor: '#667eea',
                            fillOpacity: 0.1,
                            weight: 1
                        }).addTo(map);
                        
                        L.circle(latlng, {
                            radius: 5000,
                            color: '#667eea',
                            fillColor: '#667eea',
                            fillOpacity: 0.05,
                            weight: 1,
                            dashArray: '5, 5'
                        }).addTo(map);
                        
                        return L.marker(latlng, {icon: hospitalIcon});
                    },
                    onEachFeature: function(feature, layer) {
                        const props = feature.properties;
                        layer.bindPopup(`
                            <div style="min-width: 250px;">
                                <h4 style="color: #667eea; margin-bottom: 0.8rem;">
                                    🏥 ${props.nama || 'Rumah Sakit'}
                                </h4>
                                <p style="margin: 0.5rem 0;"><strong>Tipe:</strong> ${props.kategori || 'N/A'}</p>
                                <p style="margin: 0.5rem 0;"><strong>Alamat:</strong> ${props.alamat || 'N/A'}</p>
                                <p style="margin: 0.5rem 0; color: #667eea;">
                                    <strong>Radius Pelayanan:</strong> 3-5 km
                                </p>
                            </div>
                        `);
                    }
                }).addTo(map);
            })
            .catch(error => {
                console.log('Hospital data not loaded, using dummy data');
                createDummyHospitals();
            });
        
        // Dummy hospital markers
        function createDummyHospitals() {
            const hospitals = [
                {name: 'RSUD Abdul Moeloek', lat: -5.4278, lng: 105.2608, type: 'RS Tipe A'},
                {name: 'RS Advent', lat: -5.4356, lng: 105.2751, type: 'RS Tipe B'},
                {name: 'RS Urip Sumoharjo', lat: -5.4467, lng: 105.2759, type: 'RS TNI'},
                {name: 'RS Immanuel', lat: -5.3971, lng: 105.2891, type: 'RS Tipe C'}
            ];
            
            hospitals.forEach(h => {
                // Buffer zones
                L.circle([h.lat, h.lng], {
                    radius: 3000,
                    color: '#667eea',
                    fillColor: '#667eea',
                    fillOpacity: 0.1,
                    weight: 1
                }).addTo(map);
                
                L.circle([h.lat, h.lng], {
                    radius: 5000,
                    color: '#667eea',
                    fillColor: '#667eea',
                    fillOpacity: 0.05,
                    weight: 1,
                    dashArray: '5, 5'
                }).addTo(map);
                
                // Marker
                L.marker([h.lat, h.lng], {icon: hospitalIcon})
                    .bindPopup(`
                        <h4 style="color: #667eea;">🏥 ${h.name}</h4>
                        <p><strong>Tipe:</strong> ${h.type}</p>
                        <p style="color: #667eea;"><strong>Radius:</strong> 3-5 km</p>
                    `)
                    .addTo(map);
            });
        }
        
        // Add scale control
        L.control.scale({imperial: false, metric: true}).addTo(map);
    </script>

</body>
</html>
