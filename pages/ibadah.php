<?php
// ===============================================
// LOAD DATA GEOJSON SARANA IBADAH
// ===============================================

$geojson_path = __DIR__ . '/../data/geojson/ibadah.geojson';

if (!file_exists($geojson_path)) {
    die("GeoJSON sarana ibadah tidak ditemukan: $geojson_path");
}

$geojson_data = json_decode(file_get_contents($geojson_path), true);
$features = $geojson_data['features'] ?? [];

$ibadah_data = [];
$kategori_counter = [];

foreach ($features as $f) {
    $props = $f['properties'];
    $nama = $props['NAMOBJ'] ?? "Tidak diketahui";
    $kategori = $props['REMARK'] ?? "Lainnya";

    if (!isset($kategori_counter[$kategori])) {
        $kategori_counter[$kategori] = 0;
    }
    $kategori_counter[$kategori]++;

    $ibadah_data[] = [
        'nama' => $nama,
        'kategori' => $kategori,
        'geometry' => $f['geometry'] ?? null
    ];
}

$total_sarana = count($ibadah_data);
arsort($kategori_counter);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sarana Ibadah - SIG Bandar Lampung</title>
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
    <header class="bg-white shadow-md sticky top-0 z-[1000]">
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
                    <li><a href="/index.php" class="text-gray-600 hover:text-blue-600 transition">Beranda</a></li>
                    <li><a href="/pages/kepadatan.php" class="text-gray-600 hover:text-blue-600 transition">Kepadatan
                            Penduduk</a></li>
                    <li><a href="/pages/pendidikan.php"
                            class="text-gray-600 hover:text-blue-600 transition">Pendidikan</a></li>
                    <li><a href="/pages/kesehatan.php"
                            class="text-gray-600 hover:text-blue-600 transition">Kesehatan</a></li>

                    <!-- Highlight aktif -->
                    <li><a href="/pages/ibadah.php"
                            class="text-blue-600 font-semibold border-b-2 border-blue-600 pb-1">Sarana Ibadah</a></li>
                </ul>

                <!-- Mobile Button -->
                <button id="menu-btn" class="md:hidden text-gray-700">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
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
                    <li><a href="/pages/kesehatan.php" class="block py-2 hover:text-blue-600 transition">Kesehatan</a>
                    </li>
                    <li><a href="/pages/ibadah.php"
                            class="block py-2 text-blue-600 font-semibold border-l-4 border-blue-600 pl-2">Sarana
                            Ibadah</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <section class="bg-gradient-to-r from-purple-600 to-purple-800 text-white py-20">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl md:text-5xl font-bold mb-4">Sarana Ibadah Bandar Lampung</h2>
            <p class="text-xl md:text-2xl text-purple-100">Pemetaan dan Analisis <?= number_format($total_sarana) ?>
                Tempat Ibadah</p>
        </div>
    </section>

    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                    class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-6 text-center hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-purple-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-700 mb-2">Total Sarana Ibadah</h4>
                    <p class="text-4xl font-bold text-purple-600"><?= number_format($total_sarana) ?></p>
                    <span class="text-gray-600">tempat ibadah</span>
                </div>

                <?php
                $kategori_items = array_slice($kategori_counter, 0, 3, true);
                $colors = [
                    ['from-blue-50', 'to-blue-100', 'bg-blue-600', 'text-blue-600'],
                    ['from-green-50', 'to-green-100', 'bg-green-600', 'text-green-600'],
                    ['from-yellow-50', 'to-yellow-100', 'bg-yellow-600', 'text-yellow-600']
                ];
                $index = 0;
                foreach ($kategori_items as $kategori => $jumlah):
                    $color = $colors[$index];
                    ?>
                    <div
                        class="bg-gradient-to-br <?= $color[0] ?> <?= $color[1] ?> rounded-lg p-6 text-center hover:shadow-lg transition">
                        <div class="w-16 h-16 <?= $color[2] ?> rounded-full mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                </path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-700 mb-2"><?= htmlspecialchars($kategori) ?></h4>
                        <p class="text-4xl font-bold <?= $color[3] ?>"><?= number_format($jumlah) ?></p>
                        <span class="text-gray-600">unit</span>
                    </div>
                    <?php
                    $index++;
                endforeach;
                ?>
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
                    <label class="flex items-center space-x-2"><input type="checkbox" id="layer-kesehatan"
                            class="w-4 h-4"><span class="text-gray-700">Rumah Sakit (24)</span></label>
                    <label class="flex items-center space-x-2"><input type="checkbox" id="layer-ibadah" checked
                            class="w-4 h-4"><span class="text-gray-700">Sarana Ibadah
                            (<?= $total_sarana ?>)</span></label>
                </div>
            </div>
            <div id="map" class="mb-6"></div>
            <div id="info-panel" class="bg-white rounded-lg shadow-md p-6">
                <h4 class="text-xl font-bold text-gray-800 mb-2">Informasi Kecamatan</h4>
                <p class="text-gray-600">Klik pada peta untuk melihat detail</p>
            </div>
        </div>
    </section>

    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <h3 class="text-2xl md:text-3xl font-bold text-gray-800 mb-8 text-center">Analisis Distribusi Sarana Ibadah
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-purple-600">
                    <h4 class="text-xl font-bold text-gray-800 mb-4">Konsentrasi Tinggi</h4>
                    <p class="text-sm text-purple-600 font-semibold mb-3">Wilayah Pusat Kota</p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-start"><span class="text-purple-600 mr-2">•</span>Tanjung Karang Pusat
                        </li>
                        <li class="flex items-start"><span class="text-purple-600 mr-2">•</span>Tanjung Karang Timur
                        </li>
                        <li class="flex items-start"><span class="text-purple-600 mr-2">•</span>Teluk Betung Selatan
                        </li>
                        <li class="flex items-start"><span class="text-purple-600 mr-2">•</span>Enggal</li>
                    </ul>
                    <div class="bg-purple-50 border-l-4 border-purple-600 p-4 rounded mt-4">
                        <p class="text-sm text-gray-700">Distribusi merata di kawasan padat penduduk dengan
                            aksesibilitas tinggi</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-blue-500">
                    <h4 class="text-xl font-bold text-gray-800 mb-4">Konsentrasi Sedang</h4>
                    <p class="text-sm text-blue-600 font-semibold mb-3">Wilayah Berkembang</p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-start"><span class="text-blue-600 mr-2">•</span>Kedaton</li>
                        <li class="flex items-start"><span class="text-blue-600 mr-2">•</span>Rajabasa</li>
                        <li class="flex items-start"><span class="text-blue-600 mr-2">•</span>Sukarame</li>
                        <li class="flex items-start"><span class="text-blue-600 mr-2">•</span>Way Halim</li>
                    </ul>
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded mt-4">
                        <p class="text-sm text-gray-700">Pertumbuhan seiring perkembangan permukiman baru</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-green-500">
                    <h4 class="text-xl font-bold text-gray-800 mb-4">Potensi Pengembangan</h4>
                    <p class="text-sm text-green-600 font-semibold mb-3">Wilayah Pinggiran</p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-start"><span class="text-green-600 mr-2">•</span>Bumi Waras</li>
                        <li class="flex items-start"><span class="text-green-600 mr-2">•</span>Kemiling</li>
                        <li class="flex items-start"><span class="text-green-600 mr-2">•</span>Langkapura</li>
                        <li class="flex items-start"><span class="text-green-600 mr-2">•</span>Labuhan Ratu</li>
                    </ul>
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded mt-4">
                        <p class="text-sm text-gray-700">Memerlukan penambahan sarana seiring pertumbuhan populasi</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 bg-gray-50">
        <div class="container mx-auto px-4">
            <h3 class="text-2xl md:text-3xl font-bold text-gray-800 mb-8">Distribusi Berdasarkan Kategori</h3>
            <div class="bg-white rounded-lg shadow-md p-8 space-y-6">
                <?php
                $total = array_sum($kategori_counter);
                foreach ($kategori_counter as $kategori => $jumlah):
                    $persen = ($jumlah / $total) * 100;
                    $rounded = round($persen, 1);
                    ?>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-base font-semibold text-gray-700"><?= htmlspecialchars($kategori) ?></span>
                            <span class="text-sm font-bold text-gray-800"><?= number_format($jumlah) ?> unit
                                (<?= $rounded ?>%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-10 overflow-hidden">
                            <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-10 rounded-full flex items-center justify-end px-3 text-white text-sm font-semibold transition-all duration-500 chart-bar"
                                style="width: <?= $rounded ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <div class="bg-white rounded-lg shadow-md p-8">
                <h3 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6">Analisis Jangkauan Pelayanan</h3>
                <div class="space-y-4">
                    <div class="flex items-center p-6 bg-purple-50 rounded-lg border-l-4 border-purple-600">
                        <div class="text-5xl font-bold text-purple-600 min-w-[120px]">98%</div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-1">Coverage Area</h4>
                            <p class="text-gray-600">Hampir seluruh wilayah Bandar Lampung memiliki akses mudah ke
                                tempat ibadah dalam radius 1 km.</p>
                        </div>
                    </div>

                    <div class="flex items-center p-6 bg-blue-50 rounded-lg border-l-4 border-blue-600">
                        <div class="text-5xl font-bold text-blue-600 min-w-[120px]">67</div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-1">Rata-rata per Kecamatan</h4>
                            <p class="text-gray-600">Setiap kecamatan memiliki rata-rata 67 sarana ibadah untuk melayani
                                masyarakat.</p>
                        </div>
                    </div>

                    <div class="flex items-center p-6 bg-green-50 rounded-lg border-l-4 border-green-600">
                        <div class="text-5xl font-bold text-green-600 min-w-[120px]">1:750</div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-1">Rasio Sarana : Penduduk</h4>
                            <p class="text-gray-600">Rata-rata 1 sarana ibadah melayani sekitar 750 penduduk,
                                menunjukkan distribusi yang baik.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 bg-gradient-to-r from-purple-600 to-purple-800">
        <div class="container mx-auto px-4">
            <h3 class="text-2xl md:text-3xl font-bold text-white mb-8 text-center">Rekomendasi Pengembangan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                    class="bg-white bg-opacity-10 backdrop-blur-lg rounded-lg p-6 border-2 border-white border-opacity-20">
                    <h4 class="text-xl font-bold text-white mb-3">1. Pemerataan Distribusi</h4>
                    <ul class="space-y-2 text-purple-100">
                        <li>• Identifikasi blank spot di wilayah pinggiran</li>
                        <li>• Prioritas pembangunan di area berkembang</li>
                        <li>• Koordinasi dengan pemerintah daerah</li>
                    </ul>
                </div>
                <div
                    class="bg-white bg-opacity-10 backdrop-blur-lg rounded-lg p-6 border-2 border-white border-opacity-20">
                    <h4 class="text-xl font-bold text-white mb-3">2. Aksesibilitas</h4>
                    <ul class="space-y-2 text-purple-100">
                        <li>• Integrasi dengan transportasi publik</li>
                        <li>• Fasilitas parkir memadai</li>
                        <li>• Akses jalan yang baik</li>
                    </ul>
                </div>
                <div
                    class="bg-white bg-opacity-10 backdrop-blur-lg rounded-lg p-6 border-2 border-white border-opacity-20">
                    <h4 class="text-xl font-bold text-white mb-3">3. Peningkatan Fasilitas</h4>
                    <ul class="space-y-2 text-purple-100">
                        <li>• Renovasi sarana yang sudah tua</li>
                        <li>• Penambahan fasilitas pendukung</li>
                        <li>• Ruang parkir yang luas</li>
                    </ul>
                </div>
                <div
                    class="bg-white bg-opacity-10 backdrop-blur-lg rounded-lg p-6 border-2 border-white border-opacity-20">
                    <h4 class="text-xl font-bold text-white mb-3">4. Manajemen Data</h4>
                    <ul class="space-y-2 text-purple-100">
                        <li>• Database terintegrasi</li>
                        <li>• Monitoring kondisi bangunan</li>
                        <li>• Sistem informasi digital</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <h3 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6">Daftar Sarana Ibadah</h3>
            <div class="bg-white rounded-lg shadow-md p-4 mb-6">
                <div class="flex flex-col md:flex-row gap-4">
                    <input type="text" id="searchBox"
                        class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-600 transition"
                        placeholder="Cari nama tempat ibadah...">
                    <select id="filterCategory"
                        class="px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-600 transition">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($kategori_counter as $kat => $jml): ?>
                            <option value="<?= htmlspecialchars($kat) ?>"><?= htmlspecialchars($kat) ?> (<?= $jml ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="facilityGrid">
                <?php foreach ($ibadah_data as $tempat): ?>
                    <div class="facility-card bg-white rounded-lg p-5 shadow-md hover:shadow-lg transition border-l-4 border-purple-600"
                        data-category="<?= htmlspecialchars($tempat['kategori']) ?>">
                        <span
                            class="inline-block px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-semibold mb-2"><?= htmlspecialchars($tempat['kategori']) ?></span>
                        <h4 class="text-base font-bold text-gray-800 mb-2"><?= htmlspecialchars($tempat['nama']) ?></h4>
                    </div>
                <?php endforeach; ?>
            </div>
            <div id="noResults" class="hidden text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-gray-600 text-lg">Tidak ada hasil yang ditemukan</p>
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
    <script>
        const menuBtn = document.getElementById("menu-btn");
        const mobileMenu = document.getElementById("mobile-menu");

        menuBtn.addEventListener("click", () => {
            mobileMenu.classList.toggle("hidden");
        });
    </script>


    <script src="/assets/js/map.js"></script>
    <script>
        const searchBox = document.getElementById('searchBox');
        const filterCategory = document.getElementById('filterCategory');
        const facilityCards = document.querySelectorAll('.facility-card');
        const noResults = document.getElementById('noResults');

        function filterCards() {
            const searchTerm = searchBox.value.toLowerCase();
            const categoryFilter = filterCategory.value;
            let visibleCount = 0;

            facilityCards.forEach(card => {
                const name = card.querySelector('h4').textContent.toLowerCase();
                const category = card.getAttribute('data-category');

                const matchSearch = name.includes(searchTerm);
                const matchCategory = !categoryFilter || category === categoryFilter;

                if (matchSearch && matchCategory) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            noResults.style.display = visibleCount === 0 ? 'block' : 'none';
        }

        if (searchBox) searchBox.addEventListener('input', filterCards);
        if (filterCategory) filterCategory.addEventListener('change', filterCards);

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const width = entry.target.getAttribute('style').match(/width:\s*([0-9.]+%)/)[1];
                    entry.target.style.width = width;
                }
            });
        });

        document.querySelectorAll('.chart-bar').forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0';
            observer.observe(bar);
            setTimeout(() => bar.style.width = width, 100);
        });
    </script>
</body>

</html>