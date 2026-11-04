<?php

// Set waktu PHP ke Waktu Indonesia Barat
date_default_timezone_set('Asia/Jakarta');

$conn = mysqli_connect("localhost", "root", "", "crm_bengkel");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

mysqli_query($conn, "SET time_zone = '+07:00'");
?>