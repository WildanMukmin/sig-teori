// assets/js/map.js - Inisialisasi dan Kontrol Peta

// Koordinat pusat Bandar Lampung
const BANDAR_LAMPUNG_CENTER = [-5.3971, 105.2668];
const DEFAULT_ZOOM = 12;

// Inisialisasi peta
let map = L.map('map').setView(BANDAR_LAMPUNG_CENTER, DEFAULT_ZOOM);

// Layer groups untuk kontrol
let kecamatanLayer = L.layerGroup().addTo(map);
let pendidikanLayer = L.layerGroup();
let kesehatanLayer = L.layerGroup();
let ibadahLayer = L.layerGroup();

// Tambahkan tile layer (basemap)
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    maxZoom: 19
}).addTo(map);

// Fungsi untuk mendapatkan warna berdasarkan kepadatan
function getColor(density) {
    return density > 15000 ? '#8B0000' :   // Merah tua - sangat padat
           density > 10000 ? '#FF4500' :   // Merah - padat
           density > 7000  ? '#FFA500' :   // Orange - sedang
           density > 5000  ? '#FFD700' :   // Kuning - rendah sedang
           density > 3000  ? '#90EE90' :   // Hijau muda
                            '#006400';     // Hijau tua - rendah
}

// Fungsi untuk style polygon kecamatan
function style(feature) {
    return {
        fillColor: getColor(feature.properties.Kepadatan),
        weight: 2,
        opacity: 1,
        color: 'white',
        dashArray: '3',
        fillOpacity: 0.7
    };
}

// Fungsi highlight saat hover
function highlightFeature(e) {
    let layer = e.target;
    console.log(layer)
    
    layer.setStyle({
        weight: 5,
        color: '#666',
        dashArray: '',
        fillOpacity: 0.9
    });
    
    layer.bringToFront();
    
    // Update info panel
    updateInfoPanel(layer.feature.properties);
}

// Fungsi reset highlight
function resetHighlight(e) {
    kecamatanLayer.resetStyle(e.target);
    document.getElementById('info-panel').innerHTML = 
        '<h4>Informasi Kecamatan</h4><p>Klik pada peta untuk melihat detail</p>';
}

// Fungsi zoom to feature
function zoomToFeature(e) {
    map.fitBounds(e.target.getBounds());
}

// Fungsi update info panel
function updateInfoPanel(props) {
    let density = props.Kepadatan || 'N/A';
    let kategori = '';
    
    if (density > 10000) kategori = '🔴 Sangat Padat';
    else if (density > 7000) kategori = '🟡 Padat Sedang';
    else kategori = '🟢 Berpotensi';
    
    document.getElementById('info-panel').innerHTML = `
        <h4>${props.NAMOBJ}</h4>
        <div class="popup-info">
            <p><strong>Kepadatan:</strong> ${density.toLocaleString('id-ID')} jiwa/km²</p>
            <p><strong>Kategori:</strong> ${kategori}</p>
            ${props.luas_km2 ? `<p><strong>Luas:</strong> ${props.luas_km2} km²</p>` : ''}
            ${props.jumlah_penduduk ? `<p><strong>Penduduk:</strong> ${props.jumlah_penduduk.toLocaleString('id-ID')} jiwa</p>` : ''}
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
    
    // Popup
    let density = feature.properties.Kepadatan || 'N/A';
    layer.bindPopup(`
        <h4>${feature.properties.NAMOBJ}</h4>
        <div class="popup-info">
            <p><strong>Kepadatan:</strong> ${density.toLocaleString('id-ID')} jiwa/km²</p>
        </div>
    `);
}

// Load data GeoJSON Kecamatan
fetch('data/geojson/kecamatan.geojson')
    .then(response => response.json())
    .then(data => {
        L.geoJSON(data, {
            style: style,
            onEachFeature: onEachFeature
        }).addTo(kecamatanLayer);
    })
    .catch(error => {
        console.error('Error loading kecamatan data:', error);
        // Fallback: buat dummy data jika file tidak ada
        createDummyKecamatan();
    });


// Load Fasilitas Pendidikan
fetch('data/geojson/pendidikan.geojson')
    .then(response => response.json())
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
                layer.bindPopup(`
                    <h4>🏫 ${feature.properties.NAMOBJ}</h4>
                    <p><strong>Tipe:</strong> ${feature.properties.tipe || 'N/A'}</p>
                    <p><strong>Alamat:</strong> ${feature.properties.alamat || 'N/A'}</p>
                `);
            }
        }).addTo(pendidikanLayer);
    })
    .catch(error => console.log('Pendidikan data not loaded'));

// Load Rumah Sakit
fetch('data/geojson/rumahsakit.geojson')
    .then(response => response.json())
    .then(data => {
        let hospitalIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
            iconSize: [30, 30],
            iconAnchor: [15, 15],
            popupAnchor: [0, -15]
        });
        
        L.geoJSON(data, {
            pointToLayer: function(feature, latlng) {
                return L.marker(latlng, {icon: hospitalIcon});
            },
            onEachFeature: function(feature, layer) {
                layer.bindPopup(`
                    <h4>🏥 ${feature.properties.NAMOBJ}</h4>
                    <p><strong>Alamat:</strong> ${feature.properties.alamat || 'N/A'}</p>
                `);
            }
        }).addTo(kesehatanLayer);
    })
    .catch(error => console.log('Hospital data not loaded'));

// Load Sarana Ibadah
fetch('data/geojson/ibadah.geojson')
    .then(response => response.json())
    .then(data => {
        let mosqueIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/2679/2679683.png',
            iconSize: [20, 20],
            iconAnchor: [10, 10],
            popupAnchor: [0, -10]
        });
        
        L.geoJSON(data, {
            pointToLayer: function(feature, latlng) {
                return L.marker(latlng, {icon: mosqueIcon});
            },
            onEachFeature: function(feature, layer) {
                layer.bindPopup(`
                    <h4>🕌 ${feature.properties.NAMOBJ}</h4>
                    <p><strong>Kategori:</strong> ${feature.properties.kategori || 'N/A'}</p>
                `);
            }
        }).addTo(ibadahLayer);
    })
    .catch(error => console.log('Ibadah data not loaded'));

// Layer Controls - Toggle layers
document.getElementById('layer-kecamatan').addEventListener('change', function(e) {
    if (e.target.checked) {
        map.addLayer(kecamatanLayer);
    } else {
        map.removeLayer(kecamatanLayer);
    }
});

document.getElementById('layer-pendidikan').addEventListener('change', function(e) {
    if (e.target.checked) {
        map.addLayer(pendidikanLayer);
    } else {
        map.removeLayer(pendidikanLayer);
    }
});

    document.getElementById('layer-kesehatan').addEventListener('change', function(e) {
    if (e.target.checked) {
        map.addLayer(kesehatanLayer);
    } else {
        map.removeLayer(kesehatanLayer);
    }
});

document.getElementById('layer-ibadah').addEventListener('change', function(e) {
    if (e.target.checked) {
        map.addLayer(ibadahLayer);
    } else {
        map.removeLayer(ibadahLayer);
    }
});

// Add legend to map
let legend = L.control({position: 'bottomright'});

legend.onAdd = function(map) {
    let div = L.DomUtil.create('div', 'density-legend');
    let grades = [0, 3000, 5000, 7000, 10000, 15000];
    let labels = ['<h4>Kepadatan (jiwa/km²)</h4>'];
    
    for (let i = 0; i < grades.length; i++) {
        labels.push(
            '<div class="legend-item">' +
            '<div class="legend-color" style="background:' + getColor(grades[i] + 1) + '"></div> ' +
            grades[i] + (grades[i + 1] ? '&ndash;' + grades[i + 1] : '+') +
            '</div>'
        );
    }
    
    div.innerHTML = labels.join('');
    return div;
};

legend.addTo(map);

// Add scale control
L.control.scale({imperial: false, metric: true}).addTo(map);

console.log('Map initialized successfully');