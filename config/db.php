<?php
// Konfigurasi koneksi database
define('DB_HOST', 'localhost');
define('DB_USER', 'tugaspabw_2414101032');
define('DB_PASS', '241410103224141010322414101032');
define('DB_NAME', 'tugaspabw_2414101032');

// Membuat koneksi ke database
 $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set charset ke utf8mb4
 $conn->set_charset("utf8mb4");

// Fungsi untuk membersihkan input
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Fungsi untuk membuat kode booking unik
function generate_booking_code() {
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $booking_code = "";
    for ($i = 0; $i < 8; $i++) {
        $booking_code .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $booking_code;
}

// Fungsi untuk memeriksa apakah user sudah login
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Fungsi untuk memeriksa apakah user adalah admin
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}