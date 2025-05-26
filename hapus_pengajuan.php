<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pembimbing') {
    header("Location: dashboard_pembimbing.php");
    exit();
}

if (isset($_GET['id'])) {
    $pendaftaran_id = intval($_GET['id']);

    // Hapus data dari tabel pengajuan_keluar
    $stmt = $conn->prepare("DELETE FROM pengajuan_keluar WHERE pendaftaran_id = ?");
    $stmt->bind_param("i", $pendaftaran_id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
header("Location: dashboard_pembimbing.php");
exit();
?>
