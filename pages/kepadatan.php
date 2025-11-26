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

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <style>
        #map {
            height: 600px;
            width: 100%;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="bg-gray-50">

    <!-- Header -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <nav class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-blue-800 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-xl">S</span>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800">SIG UAS</h1>
                </div>
                <ul class="hidden md:flex space-x-8">
                    <li><a href="/index.php" class="text-gray-600 hover:text-blue-600 transition">Beranda</a></li>
                    <li><a href="/pages/kepadatan.php" class="text-blue-600 font-semibold border-b-2 border-blue-600 pb-1">Kepadatan Penduduk</a></li>
                    <li><a href="/pages/pendidikan.php" class="text-gray-600 hover:text-blue-600 transition">Pendidikan</a></li>
                    <li><a href="/pages/kesehatan.php" class="text-gray-600 hover:text-blue-600 transition">Kesehatan</a></li>
                    <li><a href="/pages/ibadah.php" class="text-gray-600 hover:text-blue-600 transition">Sarana Ibadah</a></li>
                </ul>
                <button class="md:hidden text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main>

        <!-- Hero Section -->
        <section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-4xl md:text-5xl font-bold mb-4">Analisis Kepadatan Penduduk</h2>
                <p class="text-xl md:text-2xl text-blue-100">Distribusi dan Kategorisasi Kepadatan di 20 Kecamatan</p>
            </div>
        </section>

        <!-- Statistik -->
        <section class="py-12 bg-white">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 text-center hover:shadow-lg transition">
                        <div class="w-16 h-16 bg-blue-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-700 mb-2">Total Kecamatan</h4>
                        <p class="text-4xl font-bold text-blue-600"><?= $total_kecamatan ?></p>
                        <span class="text-gray-600">kecamatan</span>
                    </div>

                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 text-center hover:shadow-lg transition">
                        <div class="w-16 h-16 bg-green-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-700 mb-2">Kepadatan Rata-rata</h4>
                        <p class="text-4xl font-bold text-green-600"><?= number_format($rata_rata_kepadatan, 0, ',', '.') ?></p>
                        <span class="text-gray-600">jiwa/km²</span>
                    </div>

                    <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg p-6 text-center hover:shadow-lg transition">
                        <div class="w-16 h-16 bg-red-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-700 mb-2">Wilayah Sangat Padat</h4>
                        <p class="text-4xl font-bold text-red-600">4</p>
                        <span class="text-gray-600">kecamatan</span>
                    </div>

                    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg p-6 text-center hover:shadow-lg transition">
                        <div class="w-16 h-16 bg-yellow-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-700 mb-2">Wilayah Berpotensi</h4>
                        <p class="text-4xl font-bold text-yellow-600">11</p>
                        <span class="text-gray-600">kecamatan</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Grafik -->
        <section class="py-12 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="bg-white rounded-lg shadow-md p-6 md:p-8">
                    <h3 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">Grafik Kepadatan Penduduk per Kecamatan</h3>
                    <p class="text-gray-600 mb-8">Top 10 Kecamatan Terpadat</p>

                    <div class="space-y-4">
                        <?php
                        $max_density = 18619;
                        $top_10 = array_slice($kecamatan_data, 0, 10);
                        foreach ($top_10 as $index => $kec):
                            $width_percent = ($kec['kepadatan'] / $max_density) * 100;
                            $colors = ['from-red-500 to-red-600', 'from-orange-500 to-orange-600', 'from-yellow-500 to-yellow-600'];
                            $color_class = $colors[min($index, 2)];
                        ?>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm md:text-base font-medium text-gray-700"><?= $kec['nama'] ?></span>
                                    <span class="text-sm font-semibold text-gray-800"><?= number_format($kec['kepadatan'], 0, ',', '.') ?> jiwa/km²</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-8 overflow-hidden">
                                    <div class="bg-gradient-to-r <?= $color_class ?> h-8 rounded-full flex items-center justify-end px-3 text-white text-sm font-semibold transition-all duration-500" style="width: <?= $width_percent ?>%">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Tabel -->
        <section class="py-12 bg-white">
            <div class="container mx-auto px-4">
                <h3 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6">Tabel Data Kepadatan Penduduk</h3>

                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-blue-600 text-white">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">No</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Kecamatan</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Kepadatan</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Kategori</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php
                                $no = 1;
                                foreach ($kecamatan_data as $kec):
                                    if ($kec['kepadatan'] > 12000) {
                                        $kategori = 'Sangat Padat';
                                        $badge_class = 'bg-red-900 text-white';
                                    } elseif ($kec['kepadatan'] > 10000) {
                                        $kategori = 'Sangat Padat';
                                        $badge_class = 'bg-red-600 text-white';
                                    } elseif ($kec['kepadatan'] > 7000) {
                                        $kategori = 'Padat Sedang';
                                        $badge_class = 'bg-orange-500 text-white';
                                    } else {
                                        $kategori = 'Berpotensi';
                                        $badge_class = 'bg-green-400 text-gray-800';
                                    }
                                ?>
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 text-sm text-gray-700"><?= $no++ ?></td>
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-800"><?= $kec['nama'] ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-700"><?= number_format($kec['kepadatan'], 0, ',', '.') ?></td>
                                        <td class="px-6 py-4">
                                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold <?= $badge_class ?>">
                                                <?= $kategori ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <!-- Analisis -->
        <section class="py-12 bg-gray-50">
            <div class="container mx-auto px-4">
                <h3 class="text-2xl md:text-3xl font-bold text-gray-800 text-center mb-12">Insight dan Rekomendasi</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-red-600">
                        <h4 class="text-xl font-bold text-gray-800 mb-4">Zona Merah — Sangat Padat</h4>
                        <ul class="space-y-3 mb-4">
                            <li class="flex justify-between items-center pb-2 border-b border-gray-200">
                                <span class="text-gray-700 font-medium">Tanjung Karang Timur</span>
                                <span class="font-semibold text-red-600">18.619</span>
                            </li>
                            <li class="flex justify-between items-center pb-2 border-b border-gray-200">
                                <span class="text-gray-700 font-medium">Tanjung Karang Pusat</span>
                                <span class="font-semibold text-red-600">14.379</span>
                            </li>
                            <li class="flex justify-between items-center pb-2 border-b border-gray-200">
                                <span class="text-gray-700 font-medium">Kedaton</span>
                                <span class="font-semibold text-red-600">13.896</span>
                            </li>
                            <li class="flex justify-between items-center">
                                <span class="text-gray-700 font-medium">Bumi Waras</span>
                                <span class="font-semibold text-red-600">12.869</span>
                            </li>
                        </ul>
                        <div class="bg-red-50 border-l-4 border-red-600 p-4 rounded">
                            <p class="text-sm text-gray-700"><span class="font-semibold">Rekomendasi:</span> Perlu peningkatan infrastruktur dan pengaturan tata ruang ketat.</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-yellow-500">
                        <h4 class="text-xl font-bold text-gray-800 mb-4">Zona Kuning — Padat Sedang</h4>
                        <ul class="space-y-3 mb-4">
                            <li class="flex justify-between items-center pb-2 border-b border-gray-200">
                                <span class="text-gray-700 font-medium">Way Halim</span>
                                <span class="font-semibold text-yellow-600">10.955</span>
                            </li>
                            <li class="flex justify-between items-center pb-2 border-b border-gray-200">
                                <span class="text-gray-700 font-medium">Enggal</span>
                                <span class="font-semibold text-yellow-600">9.263</span>
                            </li>
                            <li class="flex justify-between items-center pb-2 border-b border-gray-200">
                                <span class="text-gray-700 font-medium">Langkapura</span>
                                <span class="font-semibold text-yellow-600">8.183</span>
                            </li>
                            <li class="flex justify-between items-center">
                                <span class="text-gray-700 font-medium">Labuhan Ratu</span>
                                <span class="font-semibold text-yellow-600">7.903</span>
                            </li>
                        </ul>
                        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                            <p class="text-sm text-gray-700"><span class="font-semibold">Rekomendasi:</span> Penguatan transportasi publik dan fasilitas umum.</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-green-500">
                        <h4 class="text-xl font-bold text-gray-800 mb-4">Zona Hijau — Berpotensi</h4>
                        <ul class="space-y-3 mb-4">
                            <li class="flex justify-between items-center pb-2 border-b border-gray-200">
                                <span class="text-gray-700 font-medium">Teluk Betung Barat</span>
                                <span class="font-semibold text-green-600">2.110</span>
                            </li>
                            <li class="flex justify-between items-center pb-2 border-b border-gray-200">
                                <span class="text-gray-700 font-medium">Sukabumi</span>
                                <span class="font-semibold text-green-600">2.922</span>
                            </li>
                            <li class="flex justify-between items-center pb-2 border-b border-gray-200">
                                <span class="text-gray-700 font-medium">Kemiling</span>
                                <span class="font-semibold text-green-600">4.046</span>
                            </li>
                            <li class="flex justify-between items-center">
                                <span class="text-gray-700 font-medium">Rajabasa</span>
                                <span class="font-semibold text-green-600">4.328</span>
                            </li>
                        </ul>
                        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded">
                            <p class="text-sm text-gray-700"><span class="font-semibold">Rekomendasi:</span> Area ini cocok untuk pengembangan permukiman baru.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Map Section -->
        <section class="py-12 bg-white">
            <div class="container mx-auto px-4">
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Peta Interaktif Bandar Lampung</h3>
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" id="layer-kecamatan" checked class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                            <span class="text-gray-700">Batas Kecamatan</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" id="layer-pendidikan" class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                            <span class="text-gray-700">Fasilitas Pendidikan (362)</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" id="layer-kesehatan" class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                            <span class="text-gray-700">Rumah Sakit (24)</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" id="layer-ibadah" class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                            <span class="text-gray-700">Sarana Ibadah (1340)</span>
                        </label>
                    </div>
                </div>

                <div id="map" class="mb-6"></div>

                <div id="info-panel" class="bg-white rounded-lg shadow-md p-6">
                    <h4 class="text-xl font-bold text-gray-800 mb-2">Informasi Kecamatan</h4>
                    <p class="text-gray-600">Klik pada peta untuk melihat detail</p>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto px-4 text-center">
            <p class="text-lg mb-2">2025 Sistem Informasi Geografis Bandar Lampung</p>
            <p class="text-gray-400">Jurusan Ilmu Komputer - Universitas Lampung</p>
        </div>
    </footer>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- Custom JS -->
    <script src="/assets/js/map.js"></script>

</body>
</html>