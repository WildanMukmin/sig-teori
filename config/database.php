<?php
// config/database.php - Database Configuration

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Kosongkan jika menggunakan XAMPP default
define('DB_NAME', 'sig_bandarlampung');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");

// Function to get database connection
function getConnection() {
    global $conn;
    return $conn;
}

// Function to execute query safely
function query($sql) {
    global $conn;
    $result = $conn->query($sql);
    
    if (!$result) {
        error_log("Query Error: " . $conn->error);
        return false;
    }
    
    return $result;
}

// Function to fetch all results
function fetchAll($sql) {
    $result = query($sql);
    if (!$result) return [];
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    return $data;
}

// Function to fetch single row
function fetchOne($sql) {
    $result = query($sql);
    if (!$result) return null;
    
    return $result->fetch_assoc();
}

// Function to escape string
function escape($string) {
    global $conn;
    return $conn->real_escape_string($string);
}

// Close connection function
function closeConnection() {
    global $conn;
    if ($conn) {
        $conn->close();
    }
}
?>