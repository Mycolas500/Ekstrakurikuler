<?php
$host = "localhost";  // Server database
$user = "root";       // Username MySQL (default: root)
$pass = "";           // Password MySQL (kosong jika pakai XAMPP)
$db   = "project"; // Nama database

// Membuat koneksi
$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
