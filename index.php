<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIG Bandar Lampung - Sistem Informasi Geografis</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

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

                <!-- Menu Desktop -->
                <ul class="hidden md:flex space-x-8 font-medium">
                    <li><a href="/index.php" class="text-blue-600 border-b-2 border-blue-600 pb-1">Beranda</a></li>
                    <li><a href="/pages/kepadatan.php" class="text-gray-600 hover:text-blue-600 transition">Kepadatan
                            Penduduk</a></li>
                    <li><a href="/pages/pendidikan.php"
                            class="text-gray-600 hover:text-blue-600 transition">Pendidikan</a></li>
                    <li><a href="/pages/kesehatan.php"
                            class="text-gray-600 hover:text-blue-600 transition">Kesehatan</a></li>
                    <li><a href="/pages/ibadah.php" class="text-gray-600 hover:text-blue-600 transition">Sarana
                            Ibadah</a></li>
                </ul>

                <!-- Mobile Button -->
                <button id="menu-btn" class="md:hidden text-gray-700 focus:outline-none">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden mt-4">
                <ul class="flex flex-col space-y-4 text-gray-700 font-medium">
                    <li><a href="/index.php" class="block py-2 text-blue-600 font-semibold border-l-4 border-blue-600 pl-3">Beranda</a></li>
                    <li><a href="/pages/kepadatan.php" class="block py-2 hover:text-blue-600 transition">Kepadatan
                            Penduduk</a></li>
                    <li><a href="/pages/pendidikan.php" class="block py-2 hover:text-blue-600 transition">Pendidikan</a>
                    </li>
                    <li><a href="/pages/kesehatan.php" class="block py-2 hover:text-blue-600 transition">Kesehatan</a>
                    </li>
                    <li><a href="/pages/ibadah.php" class="block py-2 hover:text-blue-600 transition">Sarana Ibadah</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        <!-- Hero Section -->
        <section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-4xl md:text-5xl font-bold mb-4">Sistem Informasi Geografis Kota Bandar Lampung</h2>
                <p class="text-xl md:text-2xl text-blue-100">Analisis Kepadatan Penduduk dan Potensi Wilayah</p>
            </div>
        </section>

        <!-- Map Section -->
        <section class="py-12">
            <div class="container mx-auto px-4">
                <!-- Map Controls -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Peta Interaktif Bandar Lampung</h3>
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" id="layer-kecamatan" checked
                                class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                            <span class="text-gray-700">Batas Kecamatan</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" id="layer-pendidikan"
                                class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                            <span class="text-gray-700">Fasilitas Pendidikan (362)</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" id="layer-kesehatan"
                                class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                            <span class="text-gray-700">Rumah Sakit (24)</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" id="layer-ibadah"
                                class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                            <span class="text-gray-700">Sarana Ibadah (1340)</span>
                        </label>
                    </div>
                </div>

                <!-- Map Container -->
                <div id="map" class="mb-6"></div>

                <!-- Info Panel -->
                <div id="info-panel" class="bg-white rounded-lg shadow-md p-6">
                    <h4 class="text-xl font-bold text-gray-800 mb-2">Informasi Kecamatan</h4>
                    <p class="text-gray-600">Klik pada peta untuk melihat detail</p>
                </div>
            </div>
        </section>

        <!-- Statistics Section -->
        <section class="py-12 bg-white">
            <div class="container mx-auto px-4">
                <h3 class="text-3xl font-bold text-gray-800 text-center mb-12">Statistik Kota Bandar Lampung</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div
                        class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 text-center hover:shadow-lg transition">
                        <div class="w-16 h-16 bg-blue-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-700 mb-2">Kepadatan Rata-rata</h4>
                        <p class="text-4xl font-bold text-blue-600">5.986</p>
                        <span class="text-gray-600">jiwa/km²</span>
                    </div>

                    <div
                        class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 text-center hover:shadow-lg transition">
                        <div class="w-16 h-16 bg-green-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                </path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-700 mb-2">Fasilitas Pendidikan</h4>
                        <p class="text-4xl font-bold text-green-600">362</p>
                        <span class="text-gray-600">unit</span>
                    </div>

                    <div
                        class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg p-6 text-center hover:shadow-lg transition">
                        <div class="w-16 h-16 bg-red-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-700 mb-2">Rumah Sakit</h4>
                        <p class="text-4xl font-bold text-red-600">24</p>
                        <span class="text-gray-600">unit</span>
                    </div>

                    <div
                        class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-6 text-center hover:shadow-lg transition">
                        <div class="w-16 h-16 bg-purple-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-700 mb-2">Sarana Ibadah</h4>
                        <p class="text-4xl font-bold text-purple-600">1.340</p>
                        <span class="text-gray-600">unit</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Analysis Section -->
        <section class="py-12 bg-gray-50">
            <div class="container mx-auto px-4">
                <h3 class="text-3xl font-bold text-gray-800 text-center mb-12">Analisis Wilayah</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-red-500">
                        <h4 class="text-xl font-bold text-gray-800 mb-4">Wilayah Sangat Padat</h4>
                        <ul class="space-y-3">
                            <li class="flex justify-between items-center pb-2 border-b border-gray-200">
                                <span class="text-gray-700">Tanjung Karang Timur</span>
                                <span class="font-semibold text-red-600">18.619</span>
                            </li>
                            <li class="flex justify-between items-center pb-2 border-b border-gray-200">
                                <span class="text-gray-700">Tanjung Karang Pusat</span>
                                <span class="font-semibold text-red-600">14.379</span>
                            </li>
                            <li class="flex justify-between items-center pb-2 border-b border-gray-200">
                                <span class="text-gray-700">Kedaton</span>
                                <span class="font-semibold text-red-600">13.896</span>
                            </li>
                            <li class="flex justify-between items-center">
                                <span class="text-gray-700">Bumi Waras</span>
                                <span class="font-semibold text-red-600">12.869</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-yellow-500">
                        <h4 class="text-xl font-bold text-gray-800 mb-4">Wilayah Padat Sedang</h4>
                        <ul class="space-y-3">
                            <li class="flex justify-between items-center pb-2 border-b border-gray-200">
                                <span class="text-gray-700">Way Halim</span>
                                <span class="font-semibold text-yellow-600">10.955</span>
                            </li>
                            <li class="flex justify-between items-center pb-2 border-b border-gray-200">
                                <span class="text-gray-700">Enggal</span>
                                <span class="font-semibold text-yellow-600">9.263</span>
                            </li>
                            <li class="flex justify-between items-center pb-2 border-b border-gray-200">
                                <span class="text-gray-700">Langkapura</span>
                                <span class="font-semibold text-yellow-600">8.183</span>
                            </li>
                            <li class="flex justify-between items-center">
                                <span class="text-gray-700">Labuhan Ratu</span>
                                <span class="font-semibold text-yellow-600">7.903</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-green-500">
                        <h4 class="text-xl font-bold text-gray-800 mb-4">Wilayah Berpotensi</h4>
                        <ul class="space-y-3 mb-4">
                            <li class="flex justify-between items-center pb-2 border-b border-gray-200">
                                <span class="text-gray-700">Teluk Betung Barat</span>
                                <span class="font-semibold text-green-600">2.110</span>
                            </li>
                            <li class="flex justify-between items-center pb-2 border-b border-gray-200">
                                <span class="text-gray-700">Sukabumi</span>
                                <span class="font-semibold text-green-600">2.922</span>
                            </li>
                            <li class="flex justify-between items-center pb-2 border-b border-gray-200">
                                <span class="text-gray-700">Kemiling</span>
                                <span class="font-semibold text-green-600">4.046</span>
                            </li>
                            <li class="flex justify-between items-center">
                                <span class="text-gray-700">Rajabasa</span>
                                <span class="font-semibold text-green-600">4.328</span>
                            </li>
                        </ul>
                        <p class="text-sm text-gray-500 bg-green-50 p-3 rounded">Potensi pengembangan permukiman baru
                        </p>
                    </div>
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
    <script>
        const menuBtn = document.getElementById("menu-btn");
        const mobileMenu = document.getElementById("mobile-menu");

        menuBtn.addEventListener("click", () => {
            mobileMenu.classList.toggle("hidden");
        });
    </script>
    <script src="/assets/js/map.js"></script>
</body>

</html>