// assets/js/map.js - Inisialisasi dan Kontrol Peta

// Koordinat pusat Bandar Lampung
const BANDAR_LAMPUNG_CENTER = [-5.3971, 105.2668];
const DEFAULT_ZOOM = 12;

// Inisialisasi peta
let map = L.map('map').setView(BANDAR_LAMPUNG_CENTER, DEFAULT_ZOOM);

// Ambil nama file halaman sekarang
const currentPage = window.location.pathname.split('/').pop();

// Layer groups untuk kontrol
let kecamatanLayer = L.layerGroup();
let pendidikanLayer = L.layerGroup();
let kesehatanLayer = L.layerGroup();
let ibadahLayer = L.layerGroup();

// Mapping halaman ke layer yang ingin aktif
const pageLayerMap = {
    "": kecamatanLayer,
    "index.php": kecamatanLayer,
    "kepadatan.php": kecamatanLayer,
    "pendidikan.php": pendidikanLayer,
    "kesehatan.php": kesehatanLayer,
    "ibadah.php": ibadahLayer
};

// Aktifkan layer sesuai halaman
const activeLayer = pageLayerMap[currentPage];
if (activeLayer) {
    activeLayer.addTo(map);
}

// Tambahkan tile layer (basemap)
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    maxZoom: 19
}).addTo(map);

// Fungsi untuk mendapatkan warna berdasarkan kepadatan
function getColor(density) {
    return density > 15000 ? '#8B0000' :
           density > 10000 ? '#FF4500' :
           density > 7000  ? '#FFA500' :
           density > 5000  ? '#FFD700' :
           density > 3000  ? '#90EE90' :
                            '#006400';
}

// Fungsi untuk style polygon kecamatan
function style(feature) {
    return {
        fillColor: getColor(feature.properties.Kepadatan),
        weight: 2,
        opacity: 1,
        color: 'white',
        dashArray: '3',
        fillOpacity: 0.6
    };
}

// Fungsi highlight saat hover
function highlightFeature(e) {
    let layer = e.target;
    
    layer.setStyle({
        weight: 3,
        color: '#000000',
        dashArray: '',
        fillOpacity: 0.8
    });
    
    if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
        layer.bringToFront();
    }
    
    updateInfoPanel(layer.feature.properties);
}

// Fungsi reset highlight
function resetHighlight(e) {
    let layer = e.target;
    kecamatanLayer.resetStyle(layer);
    
    const infoPanel = document.getElementById('info-panel');
    if (infoPanel) {
        infoPanel.innerHTML = 
            '<h4 class="text-xl font-bold text-gray-800 mb-2">Informasi Kecamatan</h4><p class="text-gray-600">Klik pada peta untuk melihat detail</p>';
    }
}

// Fungsi zoom to feature
function zoomToFeature(e) {
    map.fitBounds(e.target.getBounds());
}

// Fungsi update info panel
function updateInfoPanel(props) {
    const infoPanel = document.getElementById('info-panel');
    if (!infoPanel) return;
    
    let density = props.Kepadatan || 0;
    let kategori = '';
    let badgeClass = '';
    
    if (density > 10000) {
        kategori = 'Sangat Padat';
        badgeClass = 'bg-red-600';
    } else if (density > 7000) {
        kategori = 'Padat Sedang';
        badgeClass = 'bg-yellow-500';
    } else {
        kategori = 'Berpotensi';
        badgeClass = 'bg-green-500';
    }
    
    infoPanel.innerHTML = `
        <h4 class="text-xl font-bold text-gray-800 mb-3">${props.NAMOBJ || props.Kecamatan || 'Tidak diketahui'}</h4>
        <div class="space-y-2">
            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                <span class="text-gray-600 font-medium">Kepadatan:</span>
                <span class="font-bold text-gray-800">${density.toLocaleString('id-ID')} jiwa/km²</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                <span class="text-gray-600 font-medium">Kategori:</span>
                <span class="px-3 py-1 rounded-full text-white text-sm font-semibold ${badgeClass}">${kategori}</span>
            </div>
            ${props.luas_km2 ? `
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <span class="text-gray-600 font-medium">Luas:</span>
                    <span class="font-bold text-gray-800">${props.luas_km2} km²</span>
                </div>
            ` : ''}
            ${props.jumlah_penduduk ? `
                <div class="flex justify-between items-center py-2">
                    <span class="text-gray-600 font-medium">Penduduk:</span>
                    <span class="font-bold text-gray-800">${props.jumlah_penduduk.toLocaleString('id-ID')} jiwa</span>
                </div>
            ` : ''}
        </div>
    `;
}

// Fungsi attach event ke setiap feature
function onEachFeature(feature, layer) {
    layer.on({
        mouseover: highlightFeature,
        mouseout: resetHighlight,
        click: zoomToFeature
    });
    
    let density = feature.properties.Kepadatan || 0;
    let namaKec = feature.properties.NAMOBJ || feature.properties.Kecamatan || 'Tidak diketahui';
    
    layer.bindPopup(`
        <div class="p-2">
            <h4 class="text-lg font-bold text-gray-800 mb-2">${namaKec}</h4>
            <div class="text-sm">
                <p><strong>Kepadatan:</strong> ${density.toLocaleString('id-ID')} jiwa/km²</p>
            </div>
        </div>
    `);
}

// Load data GeoJSON Kecamatan
fetch('/data/geojson/kecamatan.geojson')
    .then(response => {
        if (!response.ok) throw new Error('Failed to load kecamatan data');
        return response.json();
    })
    .then(data => {
        L.geoJSON(data, {
            style: style,
            onEachFeature: onEachFeature
        }).addTo(kecamatanLayer);
        console.log('Kecamatan layer loaded successfully');
    })
    .catch(error => {
        console.warn('Error loading kecamatan data:', error);
    });

// Load Fasilitas Pendidikan
fetch('/data/geojson/pendidikan.geojson')
    .then(response => {
        if (!response.ok) throw new Error('Failed to load pendidikan data');
        return response.json();
    })
    .then(data => {
        let schoolIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/3976/3976625.png',
            iconSize: [25, 25],
            iconAnchor: [12, 12],
            popupAnchor: [0, -12]
        });
        
        L.geoJSON(data, {
            pointToLayer: function(feature, latlng) {
                return L.marker(latlng, {icon: schoolIcon});
            },
            onEachFeature: function(feature, layer) {
                let nama = feature.properties.NAMOBJ || 'Tidak diketahui';
                let tipe = feature.properties.REMARK || 'N/A';
                
                layer.bindPopup(`
                    <div class="p-2">
                        <h4 class="text-base font-bold text-gray-800 mb-2">${nama}</h4>
                        <p class="text-sm"><strong>Tipe:</strong> ${tipe}</p>
                    </div>
                `);
            }
        }).addTo(pendidikanLayer);
        console.log('Pendidikan layer loaded successfully');
    })
    .catch(error => {
        console.warn('Error loading pendidikan data:', error);
    });

// Load Rumah Sakit
fetch('/data/geojson/rumahsakit.geojson')
    .then(response => {
        if (!response.ok) throw new Error('Failed to load rumahsakit data');
        return response.json();
    })
    .then(data => {
        let hospitalIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
            iconSize: [30, 30],
            iconAnchor: [15, 15],
            popupAnchor: [0, -15]
        });
        
        L.geoJSON(data, {
            pointToLayer: function(feature, latlng) {
                // Add buffer circles untuk radius layanan
                L.circle(latlng, {
                    radius: 3000,
                    color: '#3B82F6',
                    fillColor: '#3B82F6',
                    fillOpacity: 0.1,
                    weight: 1
                }).addTo(kesehatanLayer);
                
                L.circle(latlng, {
                    radius: 5000,
                    color: '#3B82F6',
                    fillColor: '#3B82F6',
                    fillOpacity: 0.05,
                    weight: 1,
                    dashArray: '5, 5'
                }).addTo(kesehatanLayer);
                
                return L.marker(latlng, {icon: hospitalIcon});
            },
            onEachFeature: function(feature, layer) {
                let nama = feature.properties.NAMOBJ || 'Tidak diketahui';
                let kategori = feature.properties.REMARK || 'N/A';
                let alamat = feature.properties.ALAMAT || 'Alamat tidak tersedia';
                
                layer.bindPopup(`
                    <div class="p-2">
                        <h4 class="text-base font-bold text-red-600 mb-2">🏥 ${nama}</h4>
                        <div class="text-sm space-y-1">
                            <p><strong>Kategori:</strong> ${kategori}</p>
                            <p><strong>Alamat:</strong> ${alamat}</p>
                        </div>
                    </div>
                `);
            }
        }).addTo(kesehatanLayer);
        console.log('Kesehatan layer loaded successfully');
    })
    .catch(error => {
        console.warn('Error loading rumahsakit data:', error);
    });

// Load Sarana Ibadah
fetch('/data/geojson/ibadah.geojson')
    .then(response => {
        if (!response.ok) throw new Error('Failed to load ibadah data');
        return response.json();
    })
    .then(data => {
        let mosqueIcon = L.icon({
            iconUrl: "https://cdn-icons-png.flaticon.com/128/3053/3053758.png",
            iconSize: [20, 20],
            iconAnchor: [10, 10],
            popupAnchor: [0, -10]
        });
        
        L.geoJSON(data, {
            pointToLayer: function(feature, latlng) {
                return L.marker(latlng, {icon: mosqueIcon});
            },
            onEachFeature: function(feature, layer) {
                let nama = feature.properties.NAMOBJ || 'Tidak diketahui';
                let kategori = feature.properties.REMARK || 'N/A';
                
                layer.bindPopup(`
                    <div class="p-2">
                        <h4 class="text-base font-bold text-purple-600 mb-2">🕌 ${nama}</h4>
                        <p class="text-sm"><strong>Kategori:</strong> ${kategori}</p>
                    </div>
                `);
            }
        }).addTo(ibadahLayer);
        console.log('Ibadah layer loaded successfully');
    })
    .catch(error => {
        console.warn('Error loading ibadah data:', error);
    });

// Layer Controls - Toggle layers
const layerControls = [
    { id: 'layer-kecamatan', layer: kecamatanLayer },
    { id: 'layer-pendidikan', layer: pendidikanLayer },
    { id: 'layer-kesehatan', layer: kesehatanLayer },
    { id: 'layer-ibadah', layer: ibadahLayer }
];

layerControls.forEach(control => {
    const element = document.getElementById(control.id);
    if (element) {
        element.addEventListener('change', function(e) {
            if (e.target.checked) {
                map.addLayer(control.layer);
            } else {
                map.removeLayer(control.layer);
            }
        });
    }
});

// Add legend to map
let legend = L.control({position: 'bottomright'});

legend.onAdd = function(map) {
    let div = L.DomUtil.create('div', 'bg-white rounded-lg shadow-lg p-4');
    let grades = [0, 3000, 5000, 7000, 10000, 15000];
    let labels = ['<h4 class="text-sm font-bold text-gray-800 mb-3">Kepadatan (jiwa/km²)</h4>'];
    
    for (let i = 0; i < grades.length; i++) {
        labels.push(
            '<div class="flex items-center mb-2">' +
            '<div class="w-6 h-6 rounded mr-2" style="background:' + getColor(grades[i] + 1) + '"></div> ' +
            '<span class="text-xs text-gray-700">' + grades[i] + (grades[i + 1] ? '–' + grades[i + 1] : '+') + '</span>' +
            '</div>'
        );
    }
    
    div.innerHTML = labels.join('');
    return div;
};

legend.addTo(map);

// Add scale control
L.control.scale({
    imperial: false,
    metric: true,
    position: 'bottomleft'
}).addTo(map);

// Add zoom control dengan posisi custom
map.zoomControl.setPosition('topright');

console.log('Map initialized successfully for page:', currentPage);