<?php
require 'koneksi.php';

if (isset($_GET['siswa_id'])) {
    $siswa_id = $_GET['siswa_id'];

    // Hapus siswa dari ekskul (set NULL)
    $query = "UPDATE siswa SET ekstrakurikuler = NULL WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $siswa_id);
    $stmt->execute();

    header("Location: dashboard_pembimbing.php");
    exit();
}
?>
