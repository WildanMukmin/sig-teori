<?php
$geojson_path = __DIR__ . '/../data/geojson/rumahsakit.geojson';
if (!file_exists($geojson_path)) {
    die("GeoJSON fasilitas kesehatan tidak ditemukan: $geojson_path");
}
$geojson_data = json_decode(file_get_contents($geojson_path), true);
$features = $geojson_data['features'] ?? [];
$kesehatan_data = [];
foreach ($features as $f) {
    $props = $f['properties'];
    $kesehatan_data[] = [
        'nama' => $props['NAMOBJ'] ?? 'Tidak diketahui',
        'kategori' => $props['REMARK'] ?? 'Tidak diketahui',
        'alamat' => $props['ALAMAT'] ?? 'Alamat Belum Ditemukan',
        'geometry' => $f['geometry'] ?? "Belum Ada Koordinat",
    ];
}
$total_rs = count($kesehatan_data);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Potensi Kesehatan - SIG Bandar Lampung</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map {
            height: 500px;
            border-radius: 0.75rem;
        }
    </style>
</head>

<body class="bg-gray-50">
    <header class="bg-white shadow-md sticky top-0 z-50">
        <nav class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">

                <!-- Logo -->
                <div class="flex items-center space-x-2">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-blue-600 to-blue-800 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-xl">S</span>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800">SIG UAS</h1>
                </div>

                <!-- Desktop Menu -->
                <ul class="hidden md:flex space-x-8 font-medium">
                    <li>
                        <a href="/index.php" class="text-gray-600 hover:text-blue-600 transition">
                            Beranda
                        </a>
                    </li>

                    <li>
                        <a href="/pages/kepadatan.php" class="text-gray-600 hover:text-blue-600 transition">
                            Kepadatan Penduduk
                        </a>
                    </li>

                    <li>
                        <a href="/pages/pendidikan.php" class="text-gray-600 hover:text-blue-600 transition">
                            Pendidikan
                        </a>
                    </li>

                    <!-- HIGHLIGHT AKTIF -->
                    <li>
                        <a href="/pages/kesehatan.php"
                            class="text-blue-600 font-semibold border-b-2 border-blue-600 pb-1">
                            Kesehatan
                        </a>
                    </li>

                    <li>
                        <a href="/pages/ibadah.php" class="text-gray-600 hover:text-blue-600 transition">
                            Sarana Ibadah
                        </a>
                    </li>
                </ul>

                <!-- Mobile Button -->
                <button id="menu-btn" class="md:hidden text-gray-600">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden mt-4">
                <ul class="flex flex-col space-y-4 font-medium text-gray-700">
                    <li><a href="/index.php" class="block py-2 hover:text-blue-600 transition">Beranda</a></li>
                    <li><a href="/pages/kepadatan.php" class="block py-2 hover:text-blue-600 transition">Kepadatan
                            Penduduk</a></li>
                    <li><a href="/pages/pendidikan.php" class="block py-2 hover:text-blue-600 transition">Pendidikan</a>
                    </li>

                    <!-- MOBILE ACTIVE -->
                    <li>
                        <a href="/pages/kesehatan.php"
                            class="block py-2 text-blue-600 font-semibold border-l-4 border-blue-600 pl-3">
                            Kesehatan
                        </a>
                    </li>

                    <li><a href="/pages/ibadah.php" class="block py-2 hover:text-blue-600 transition">Sarana Ibadah</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>


    <section class="bg-gradient-to-r from-red-600 to-red-800 text-white py-20">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl md:text-5xl font-bold mb-4">Potensi Layanan Kesehatan</h2>
            <p class="text-xl md:text-2xl text-red-100">Ekosistem 24 Rumah Sakit & Fasilitas Kesehatan</p>
        </div>
    </section>

    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                    class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg p-6 text-center hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-red-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-700 mb-2">Total Rumah Sakit</h4>
                    <p class="text-4xl font-bold text-red-600"><?= $total_rs ?></p>
                    <span class="text-gray-600">fasilitas kesehatan</span>
                </div>
                <div
                    class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 text-center hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-blue-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-700 mb-2">RS Tipe A & B</h4>
                    <p class="text-4xl font-bold text-blue-600">8</p>
                    <span class="text-gray-600">rumah sakit rujukan</span>
                </div>
                <div
                    class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 text-center hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-green-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-700 mb-2">Tenaga Medis</h4>
                    <p class="text-4xl font-bold text-green-600">2,500+</p>
                    <span class="text-gray-600">dokter & perawat</span>
                </div>
                <div
                    class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-6 text-center hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-purple-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-700 mb-2">Kapasitas Tempat Tidur</h4>
                    <p class="text-4xl font-bold text-purple-600">3,000+</p>
                    <span class="text-gray-600">bed capacity</span>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Peta Interaktif Bandar Lampung</h3>
                <div class="flex flex-wrap gap-4">
                    <label class="flex items-center space-x-2"><input type="checkbox" id="layer-kecamatan"
                            class="w-4 h-4"><span class="text-gray-700">Batas Kecamatan</span></label>
                    <label class="flex items-center space-x-2"><input type="checkbox" id="layer-pendidikan"
                            class="w-4 h-4"><span class="text-gray-700">Fasilitas Pendidikan (362)</span></label>
                    <label class="flex items-center space-x-2"><input type="checkbox" id="layer-kesehatan" checked
                            class="w-4 h-4"><span class="text-gray-700">Rumah Sakit (24)</span></label>
                    <label class="flex items-center space-x-2"><input type="checkbox" id="layer-ibadah"
                            class="w-4 h-4"><span class="text-gray-700">Sarana Ibadah (1340)</span></label>
                </div>
            </div>
            <div id="map" class="mb-6"></div>
        </div>
    </section>

    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <div class="bg-white rounded-lg shadow-md p-8">
                <h3 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6">Analisis Jangkauan Pelayanan</h3>
                <div class="space-y-4">
                    <div class="flex items-center p-6 bg-blue-50 rounded-lg border-l-4 border-blue-600">
                        <div class="text-5xl font-bold text-blue-600 min-w-[120px]">95%</div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-1">Coverage Area</h4>
                            <p class="text-gray-600">Hampir seluruh wilayah Bandar Lampung tercakup dalam radius 5 km
                                dari rumah sakit terdekat.</p>
                        </div>
                    </div>
                    <div class="flex items-center p-6 bg-green-50 rounded-lg border-l-4 border-green-600">
                        <div class="text-5xl font-bold text-green-600 min-w-[120px]">3-5</div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-1">Radius Pelayanan (km)</h4>
                            <p class="text-gray-600">Rata-rata jarak tempuh dari permukiman ke RS terdekat adalah 3–5
                                km.</p>
                        </div>
                    </div>
                    <div class="flex items-center p-6 bg-purple-50 rounded-lg border-l-4 border-purple-600">
                        <div class="text-5xl font-bold text-purple-600 min-w-[120px]">8:1</div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-1">Rasio RS : Kecamatan</h4>
                            <p class="text-gray-600">24 RS untuk 20 kecamatan — rata-rata 1–2 RS per kecamatan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 bg-gradient-to-r from-blue-600 to-blue-800">
        <div class="container mx-auto px-4">
            <div class="text-center text-white mb-8">
                <h3 class="text-2xl md:text-3xl font-bold mb-2">Pengembangan Center of Excellence (COE)</h3>
                <p class="text-xl text-blue-100">Strategi spesialisasi layanan kesehatan</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    class="bg-white bg-opacity-10 backdrop-blur-lg rounded-lg p-6 border-2 border-white border-opacity-20 hover:bg-opacity-20 transition">
                    <h4 class="text-xl font-bold text-white mb-2 text-center">Heart Center</h4>
                    <p class="text-blue-100 text-center">RS Abdul Moeloek sebagai pusat jantung.</p>
                </div>
                <div
                    class="bg-white bg-opacity-10 backdrop-blur-lg rounded-lg p-6 border-2 border-white border-opacity-20 hover:bg-opacity-20 transition">
                    <h4 class="text-xl font-bold text-white mb-2 text-center">Oncology Center</h4>
                    <p class="text-blue-100 text-center">Unit Kanker - RS Advent & RS Urip.</p>
                </div>
                <div
                    class="bg-white bg-opacity-10 backdrop-blur-lg rounded-lg p-6 border-2 border-white border-opacity-20 hover:bg-opacity-20 transition">
                    <h4 class="text-xl font-bold text-white mb-2 text-center">Orthopaedic Center</h4>
                    <p class="text-blue-100 text-center">Bedah tulang dan rehabilitasi.</p>
                </div>
                <div
                    class="bg-white bg-opacity-10 backdrop-blur-lg rounded-lg p-6 border-2 border-white border-opacity-20 hover:bg-opacity-20 transition">
                    <h4 class="text-xl font-bold text-white mb-2 text-center">Neuroscience Center</h4>
                    <p class="text-blue-100 text-center">Layanan stroke 24/7.</p>
                </div>
                <div
                    class="bg-white bg-opacity-10 backdrop-blur-lg rounded-lg p-6 border-2 border-white border-opacity-20 hover:bg-opacity-20 transition">
                    <h4 class="text-xl font-bold text-white mb-2 text-center">Women & Children Center</h4>
                    <p class="text-blue-100 text-center">NICU, PICU, dan layanan obstetri.</p>
                </div>
                <div
                    class="bg-white bg-opacity-10 backdrop-blur-lg rounded-lg p-6 border-2 border-white border-opacity-20 hover:bg-opacity-20 transition">
                    <h4 class="text-xl font-bold text-white mb-2 text-center">Pulmonary Center</h4>
                    <p class="text-blue-100 text-center">Pusat penyakit paru & pernafasan.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 bg-gray-50">
        <div class="container mx-auto px-4">
            <h3 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6">Daftar Rumah Sakit</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($kesehatan_data as $rs): ?>
                    <div class="bg-white rounded-lg p-6 shadow-md hover:shadow-lg transition border-t-4 border-blue-600">
                        <h4 class="text-lg font-bold text-gray-800 mb-2"><?= htmlspecialchars($rs['nama']) ?></h4>
                        <span
                            class="inline-block px-3 py-1 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-full text-sm font-semibold mb-3"><?= htmlspecialchars($rs['kategori']) ?></span>
                        <p class="text-gray-600 text-sm"><?= htmlspecialchars($rs['alamat']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto px-4 text-center">
            <p class="text-lg mb-2">2025 Sistem Informasi Geografis Bandar Lampung</p>
            <p class="text-gray-400">Jurusan Ilmu Komputer - Universitas Lampung</p>
        </div>
    </footer>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Custom JS -->
    <script>
        const btn = document.getElementById("menu-btn");
        const menu = document.getElementById("mobile-menu");

        btn.addEventListener("click", () => {
            menu.classList.toggle("hidden");
        });
    </script>
    <script src="/assets/js/map.js"></script>
</body>

</html>