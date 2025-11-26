<?php
$geojson_path = __DIR__ . '/../data/geojson/pendidikan.geojson';
if (!file_exists($geojson_path)) {
    die("GeoJSON fasilitas pendidikan tidak ditemukan: $geojson_path");
}
$geojson_data = json_decode(file_get_contents($geojson_path), true);
$features = $geojson_data['features'] ?? [];
$pendidikan_data = [];
$kategori_counter = [];

foreach ($features as $f) {
    $props = $f['properties'];
    $nama = $props['NAMOBJ'] ?? "Tidak diketahui";
    $kategori = $props['REMARK'] ?? "Lainnya";

    if (!isset($kategori_counter[$kategori])) {
        $kategori_counter[$kategori] = 0;
    }
    $kategori_counter[$kategori]++;

    $pendidikan_data[] = [
        'nama' => $nama,
        'kategori' => $kategori,
        'alamat' => "-",
        'kecamatan_nama' => "-",
        'geometry' => $f['geometry'] ?? null
    ];
}

$total_fasilitas = count($pendidikan_data);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Potensi Pendidikan - SIG Bandar Lampung</title>
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

                    <!-- ACTIVE -->
                    <li>
                        <a href="/pages/pendidikan.php"
                            class="text-blue-600 font-semibold border-b-2 border-blue-600 pb-1">
                            Pendidikan
                        </a>
                    </li>

                    <li>
                        <a href="/pages/kesehatan.php" class="text-gray-600 hover:text-blue-600 transition">
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

                    <!-- MOBILE ACTIVE -->
                    <li>
                        <a href="/pages/pendidikan.php"
                            class="block py-2 text-blue-600 font-semibold border-l-4 border-blue-600 pl-3">
                            Pendidikan
                        </a>
                    </li>

                    <li><a href="/pages/kesehatan.php" class="block py-2 hover:text-blue-600 transition">Kesehatan</a>
                    </li>
                    <li><a href="/pages/ibadah.php" class="block py-2 hover:text-blue-600 transition">Sarana Ibadah</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <section class="bg-gradient-to-r from-green-600 to-green-800 text-white py-20">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl md:text-5xl font-bold mb-4">Potensi Pendidikan Bandar Lampung</h2>
            <p class="text-xl md:text-2xl text-green-100">Pemetaan dan Analisis 362 Fasilitas Pendidikan</p>
        </div>
    </section>

    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                    class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 text-center hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-green-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-700 mb-2">Total Fasilitas</h4>
                    <p class="text-4xl font-bold text-green-600">362</p>
                    <span class="text-gray-600">unit pendidikan</span>
                </div>
                <div
                    class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 text-center hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-blue-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-700 mb-2">Perguruan Tinggi</h4>
                    <p class="text-4xl font-bold text-blue-600">12+</p>
                    <span class="text-gray-600">universitas & institut</span>
                </div>
                <div
                    class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-6 text-center hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-purple-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M12 14l9-5-9-5-9 5 9 5z"></path>
                            <path
                                d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z">
                            </path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-700 mb-2">Sekolah Menengah</h4>
                    <p class="text-4xl font-bold text-purple-600">150+</p>
                    <span class="text-gray-600">SMP & SMA/SMK</span>
                </div>
                <div
                    class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg p-6 text-center hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-yellow-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-700 mb-2">Sekolah Dasar</h4>
                    <p class="text-4xl font-bold text-yellow-600">200+</p>
                    <span class="text-gray-600">SD & TK</span>
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
                    <label class="flex items-center space-x-2"><input type="checkbox" id="layer-pendidikan" checked
                            class="w-4 h-4"><span class="text-gray-700">Fasilitas Pendidikan (362)</span></label>
                    <label class="flex items-center space-x-2"><input type="checkbox" id="layer-kesehatan"
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
            <h3 class="text-2xl md:text-3xl font-bold text-gray-800 mb-8 text-center">Analisis Distribusi Geografis</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-green-600">
                    <h4 class="text-xl font-bold text-gray-800 mb-4">Wilayah Pusat & Utara</h4>
                    <p class="text-sm text-green-600 font-semibold mb-3">Konsentrasi Tinggi</p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-start"><span class="text-green-600 mr-2">•</span>Kepadatan fasilitas
                            sangat tinggi</li>
                        <li class="flex items-start"><span class="text-green-600 mr-2">•</span>Pusat magnet pelajar</li>
                        <li class="flex items-start"><span class="text-green-600 mr-2">•</span>TK hingga Perguruan
                            Tinggi lengkap</li>
                    </ul>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-yellow-500">
                    <h4 class="text-xl font-bold text-gray-800 mb-4">Wilayah Berkembang</h4>
                    <p class="text-sm text-yellow-600 font-semibold mb-3">Konsentrasi Sedang</p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-start"><span class="text-yellow-600 mr-2">•</span>Distribusi fasilitas
                            mulai merata</li>
                        <li class="flex items-start"><span class="text-yellow-600 mr-2">•</span>Potensi pengembangan
                            tinggi</li>
                    </ul>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-red-500">
                    <h4 class="text-xl font-bold text-gray-800 mb-4">Wilayah Pinggiran</h4>
                    <p class="text-sm text-red-600 font-semibold mb-3">Blank Spot</p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-start"><span class="text-red-600 mr-2">•</span>Kepadatan fasilitas sangat
                            rendah</li>
                        <li class="flex items-start"><span class="text-red-600 mr-2">•</span>Butuh penambahan sekolah
                            baru</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 bg-gray-50">
        <div class="container mx-auto px-4">
            <h3 class="text-2xl md:text-3xl font-bold text-gray-800 mb-8">Distribusi Jenjang Pendidikan</h3>
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
                            <span class="text-sm font-bold text-gray-800"><?= $jumlah ?> unit (<?= $rounded ?>%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-10 overflow-hidden">
                            <div class="bg-gradient-to-r from-green-500 to-green-600 h-10 rounded-full flex items-center justify-end px-3 text-white text-sm font-semibold transition-all duration-500 chart-bar"
                                style="width: <?= $rounded ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="py-12 bg-gradient-to-r from-green-600 to-green-800">
        <div class="container mx-auto px-4">
            <h3 class="text-2xl md:text-3xl font-bold text-white mb-8 text-center">Rekomendasi Pengembangan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                    class="bg-white bg-opacity-10 backdrop-blur-lg rounded-lg p-6 border-2 border-white border-opacity-20">
                    <h4 class="text-xl font-bold text-white mb-3">1. Perencanaan Zonasi</h4>
                    <ul class="space-y-2 text-green-100">
                        <li>• Prioritas USB</li>
                        <li>• Analisis spasial lokasi optimal</li>
                    </ul>
                </div>
                <div
                    class="bg-white bg-opacity-10 backdrop-blur-lg rounded-lg p-6 border-2 border-white border-opacity-20">
                    <h4 class="text-xl font-bold text-white mb-3">2. Integrasi Transportasi</h4>
                    <ul class="space-y-2 text-green-100">
                        <li>• School zoning untuk wilayah padat</li>
                        <li>• Rute aman sekolah</li>
                    </ul>
                </div>
                <div
                    class="bg-white bg-opacity-10 backdrop-blur-lg rounded-lg p-6 border-2 border-white border-opacity-20">
                    <h4 class="text-xl font-bold text-white mb-3">3. Klasterisasi</h4>
                    <ul class="space-y-2 text-green-100">
                        <li>• Education Hub di Gedong Meneng</li>
                        <li>• Sinergi antar institusi</li>
                    </ul>
                </div>
                <div
                    class="bg-white bg-opacity-10 backdrop-blur-lg rounded-lg p-6 border-2 border-white border-opacity-20">
                    <h4 class="text-xl font-bold text-white mb-3">4. Peningkatan Kualitas</h4>
                    <ul class="space-y-2 text-green-100">
                        <li>• Digitalisasi & smart classroom</li>
                        <li>• Pelatihan guru berkala</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <h3 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6">Daftar Fasilitas Pendidikan</h3>
            <div class="bg-white rounded-lg shadow-md p-4 mb-6">
                <input type="text" id="searchBox"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-600 transition"
                    placeholder="Cari nama sekolah...">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="facilityGrid">
                <?php foreach ($pendidikan_data as $sekolah): ?>
                    <div
                        class="facility-card bg-white rounded-lg p-5 shadow-md hover:shadow-lg transition border-l-4 border-green-600">
                        <span
                            class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold mb-2"><?= htmlspecialchars($sekolah['kategori']) ?></span>
                        <h4 class="text-base font-bold text-gray-800 mb-2"><?= htmlspecialchars($sekolah['nama']) ?></h4>
                        <div class="text-sm text-gray-600">
                            <?php if ($sekolah['alamat'] && $sekolah['alamat'] != '-'): ?>
                                <p><?= htmlspecialchars($sekolah['alamat']) ?></p>
                            <?php endif; ?>
                            <?php if ($sekolah['kecamatan_nama'] && $sekolah['kecamatan_nama'] != '-'): ?>
                                <p>Kec. <?= htmlspecialchars($sekolah['kecamatan_nama']) ?></p>
                            <?php endif; ?>
                        </div>
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
    <script src="/assets/js/map.js"></script>
    <script>
        const btn = document.getElementById("menu-btn");
        const menu = document.getElementById("mobile-menu");

        btn.addEventListener("click", () => {
            menu.classList.toggle("hidden");
        });
    </script>
    <script>
        const searchBox = document.getElementById('searchBox');
        const facilityCards = document.querySelectorAll('.facility-card');

        if (searchBox) {
            searchBox.addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase();
                facilityCards.forEach(card => {
                    const name = card.querySelector('h4').textContent.toLowerCase();
                    card.style.display = name.includes(searchTerm) ? 'block' : 'none';
                });
            });
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.width = entry.target.getAttribute('style').match(/width:\s*([0-9.]+%)/)[1];
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