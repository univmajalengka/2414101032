<?php
require_once 'config/db.php';

// Hapus semua session
session_unset();
session_destroy();

// Redirect ke halaman utama
header("Location: index.php");
exit();
?>