<?php
// koneksi.php

 $host = 'localhost';
 $dbname = 'db_wisata_bandung'; // Ganti dengan nama database Anda
 $user = 'root'; // Ganti dengan username database Anda
 $pass = ''; // Ganti dengan password database Anda

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    // Set mode error PDO ke exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Hentikan skrip dan tampilkan pesan error jika koneksi gagal
    die("Could not connect to the database $dbname :" . $e->getMessage());
}
?>