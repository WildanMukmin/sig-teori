-- data/sql/sig_bandarlampung.sql
-- Database untuk Sistem Informasi Geografis Bandar Lampung

-- Buat database
CREATE DATABASE IF NOT EXISTS sig_bandarlampung 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE sig_bandarlampung;

-- Tabel Kecamatan
CREATE TABLE IF NOT EXISTS kecamatan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    luas_km2 DECIMAL(10,2),
    jumlah_penduduk INT,
    kepadatan DECIMAL(10,2),
    laki_laki INT,
    perempuan INT,
    rasio_jk DECIMAL(5,2),
    islam INT,
    kristen INT,
    katolik INT,
    hindu INT,
    buddha INT,
    geojson TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert data kepadatan kecamatan
INSERT INTO kecamatan (nama, kepadatan, laki_laki, perempuan, rasio_jk) VALUES
('Tanjung Karang Timur', 18619, NULL, NULL, NULL),
('Tanjung Karang Pusat', 14379, NULL, NULL, NULL),
('Kedaton', 13896, 26381, 26094, 101.1),
('Bumi Waras', 12869, NULL, NULL, NULL),
('Teluk Betung Selatan', 11278, NULL, NULL, NULL),
('Teluk Betung Utara', 11550, NULL, NULL, NULL),
('Way Halim', 10955, NULL, NULL, NULL),
('Enggal', 9263, NULL, NULL, NULL),
('Langkapura', 8183, NULL, NULL, NULL),
('Labuhan Ratu', 7903, NULL, NULL, NULL),
('Tanjung Senang', 6753, NULL, NULL, NULL),
('Kedamaian', 6410, NULL, NULL, NULL),
('Sukarame', 6148, 33785, 32918, 102.6),
('Bandar Lampung', 5986, NULL, NULL, NULL),
('Panjang', 5488, 38119, 36568, 104.2),
('Tanjung Karang Barat', 5476, NULL, NULL, NULL),
('Teluk Betung Timur', 4805, NULL, NULL, NULL),
('Rajabasa', 4328, NULL, NULL, NULL),
('Kemiling', 4046, NULL, NULL, NULL),
('Sukabumi', 2922, NULL, NULL, NULL),
('Teluk Betung Barat', 2110, NULL, NULL, NULL);

-- Tabel Fasilitas (Pendidikan, Kesehatan, Ibadah)
CREATE TABLE IF NOT EXISTS fasilitas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(200) NOT NULL,
    tipe ENUM('pendidikan','kesehatan','ibadah') NOT NULL,
    kategori VARCHAR(100),
    alamat TEXT,
    kecamatan_id INT,
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    telepon VARCHAR(20),
    keterangan TEXT,
    geojson TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kecamatan_id) REFERENCES kecamatan(id) ON DELETE SET NULL,
    INDEX idx_tipe (tipe),
    INDEX idx_kecamatan (kecamatan_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data fasilitas pendidikan
INSERT INTO fasilitas (nama, tipe, kategori, alamat, kecamatan_id, latitude, longitude) VALUES
('SD Negeri 1 Tanjung Karang Pusat', 'pendidikan', 'SD', 'Jl. Kartini No.1', 2, -5.4205, 105.2668),
('SMP Negeri 2 Bandar Lampung', 'pendidikan', 'SMP', 'Jl. Ki Maja', 2, -5.4189, 105.2701),
('SMA Negeri 1 Bandar Lampung', 'pendidikan', 'SMA', 'Jl. Laksamana Malahayati', 3, -5.3893, 105.2542),
('Universitas Lampung', 'pendidikan', 'Perguruan Tinggi', 'Jl. Prof. Dr. Ir. Sumantri Brojonegoro No.1', 9, -5.3586, 105.2412);

-- Insert sample data rumah sakit
INSERT INTO fasilitas (nama, tipe, kategori, alamat, kecamatan_id, latitude, longitude) VALUES
('RSUD Abdul Moeloek', 'kesehatan', 'Rumah Sakit Umum', 'Jl. Dr. Rivai No.6', 2, -5.4278, 105.2608),
('RS Advent Bandar Lampung', 'kesehatan', 'Rumah Sakit Umum', 'Jl. Teuku Umar No.48', 2, -5.4356, 105.2751),
('RS Urip Sumoharjo', 'kesehatan', 'Rumah Sakit TNI', 'Jl. Urip Sumoharjo', 4, -5.4467, 105.2759),
('RS Immanuel', 'kesehatan', 'Rumah Sakit Umum', 'Jl. Soekarno Hatta', 11, -5.3971, 105.2891);

-- Insert sample data sarana ibadah
INSERT INTO fasilitas (nama, tipe, kategori, alamat, kecamatan_id, latitude, longitude) VALUES
('Masjid Agung Al-Furqon', 'ibadah', 'Masjid', 'Jl. Jend. Sudirman', 2, -5.4251, 105.2654),
('Masjid Al-Anwar', 'ibadah', 'Masjid', 'Jl. Raden Intan', 1, -5.4289, 105.2897),
('Gereja Katedral Santo Yoseph', 'ibadah', 'Gereja', 'Jl. Hasanudin', 2, -5.4312, 105.2589),
('Vihara Tian Ti', 'ibadah', 'Vihara', 'Jl. Diponegoro', 2, -5.4198, 105.2723);

-- Tabel Analisis (untuk menyimpan hasil analisis)
CREATE TABLE IF NOT EXISTS analisis (
    id INT PRIMARY KEY AUTO_INCREMENT,
    judul VARCHAR(200) NOT NULL,
    jenis_analisis VARCHAR(100),
    deskripsi TEXT,
    hasil_analisis TEXT,
    tanggal_analisis DATE,
    created_by VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample analisis
INSERT INTO analisis (judul, jenis_analisis, deskripsi, hasil_analisis, tanggal_analisis) VALUES
('Analisis Kepadatan Penduduk 2024', 'Kepadatan', 
 'Analisis distribusi kepadatan penduduk di 20 kecamatan', 
 'Terdapat 4 kecamatan dengan kepadatan sangat tinggi (>12.000 jiwa/km²)', 
 '2024-01-15'),
('Analisis Sebaran Fasilitas Pendidikan', 'Pendidikan', 
 'Pemetaan 362 fasilitas pendidikan', 
 'Fasilitas terkonsentrasi di pusat kota, kurang di wilayah pesisir', 
 '2024-01-20'),
('Analisis Aksesibilitas Rumah Sakit', 'Kesehatan', 
 'Analisis jangkauan 24 rumah sakit', 
 'Radius pelayanan rata-rata 3-5 km per RS', 
 '2024-02-01');

-- Tabel User (untuk autentikasi - optional)
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role ENUM('admin','user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@sig.lampung.go.id', 'admin');

-- View untuk statistik kecamatan
CREATE OR REPLACE VIEW v_statistik_kecamatan AS
SELECT 
    k.id,
    k.nama,
    k.kepadatan,
    COUNT(CASE WHEN f.tipe = 'pendidikan' THEN 1 END) as jumlah_pendidikan,
    COUNT(CASE WHEN f.tipe = 'kesehatan' THEN 1 END) as jumlah_kesehatan,
    COUNT(CASE WHEN f.tipe = 'ibadah' THEN 1 END) as jumlah_ibadah,
    CASE 
        WHEN k.kepadatan > 10000 THEN 'Sangat Padat'
        WHEN k.kepadatan > 7000 THEN 'Padat Sedang'
        ELSE 'Berpotensi'
    END as kategori_kepadatan
FROM kecamatan k
LEFT JOIN fasilitas f ON k.id = f.kecamatan_id
GROUP BY k.id, k.nama, k.kepadatan;

-- Stored Procedure untuk mendapatkan fasilitas terdekat
DELIMITER //
CREATE PROCEDURE sp_get_nearest_facilities(
    IN p_latitude DECIMAL(10,8),
    IN p_longitude DECIMAL(11,8),
    IN p_tipe VARCHAR(20),
    IN p_limit INT
)
BEGIN
    SELECT 
        id,
        nama,
        tipe,
        kategori,
        alamat,
        latitude,
        longitude,
        (6371 * acos(
            cos(radians(p_latitude)) * 
            cos(radians(latitude)) * 
            cos(radians(longitude) - radians(p_longitude)) + 
            sin(radians(p_latitude)) * 
            sin(radians(latitude))
        )) AS jarak_km
    FROM fasilitas
    WHERE tipe = p_tipe
    ORDER BY jarak_km
    LIMIT p_limit;
END //
DELIMITER ;

-- Function untuk menghitung total fasilitas per kecamatan
DELIMITER //
CREATE FUNCTION fn_total_fasilitas(kec_id INT) 
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE total INT;
    SELECT COUNT(*) INTO total 
    FROM fasilitas 
    WHERE kecamatan_id = kec_id;
    RETURN total;
END //
DELIMITER ;

-- Indexes untuk optimasi query
CREATE INDEX idx_fasilitas_location ON fasilitas(latitude, longitude);
CREATE INDEX idx_kecamatan_kepadatan ON kecamatan(kepadatan);

-- Grant privileges (adjust as needed)
-- GRANT ALL PRIVILEGES ON sig_bandarlampung.* TO 'sig_user'@'localhost' IDENTIFIED BY 'sig_password';
-- FLUSH PRIVILEGES;

SELECT 'Database sig_bandarlampung berhasil dibuat!' as Status;