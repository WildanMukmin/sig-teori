CREATE DATABASE sig_bandarlampung;
USE sig_bandarlampung;

CREATE TABLE kecamatan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    luas_km2 DECIMAL(10,2),
    jumlah_penduduk INT,
    kepadatan DECIMAL(10,2),
    rasio_jk DECIMAL(5,2),
    geojson TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE fasilitas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(200),
    tipe ENUM('pendidikan','kesehatan','ibadah'),
    kategori VARCHAR(100),
    alamat TEXT,
    kecamatan_id INT,
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    geojson TEXT,
    FOREIGN KEY (kecamatan_id) REFERENCES kecamatan(id)
);

INSERT INTO kecamatan (nama, luas_km2, jumlah_penduduk, kepadatan) VALUES
('Tanjung Karang Timur', NULL, NULL, 18619),
('Tanjung Karang Pusat', NULL, NULL, 14379),
('Kedaton', NULL, NULL, 13896),
('Bumi Waras', NULL, NULL, 12869),
('Teluk Betung Selatan', NULL, NULL, 11278),
('Teluk Betung Utara', NULL, NULL, 11550),
('Way Halim', NULL, NULL, 10955),
('Enggal', NULL, NULL, 9263),
('Langkapura', NULL, NULL, 8183),
('Labuhan Ratu', NULL, NULL, 7903),
('Tanjung Senang', NULL, NULL, 6753),
('Kedamaian', NULL, NULL, 6410),
('Sukarame', NULL, NULL, 6148),
('Bandar Lampung', NULL, NULL, 5986),
('Panjang', NULL, NULL, 5488),
('Tanjung Karang Barat', NULL, NULL, 5476),
('Teluk Betung Timur', NULL, NULL, 4805),
('Rajabasa', NULL, NULL, 4328),
('Kemiling', NULL, NULL, 4046),
('Sukabumi', NULL, NULL, 2922),
('Teluk Betung Barat', NULL, NULL, 2110);

