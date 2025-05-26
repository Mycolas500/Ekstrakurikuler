<?php
session_start();
require 'koneksi.php'; // Pastikan koneksi database tersedia

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = intval($_GET['id']);
    $status = $_GET['status'];

    // Pastikan status yang diberikan valid
    if (in_array($status, ['Diterima', 'Ditolak'])) {
        $sql = "UPDATE pendaftaran SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $id);
        
        if ($stmt->execute()) {
            echo "<script>alert('Status berhasil diperbarui!'); window.location='admin_dashboard.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui status!'); window.location='admin_dashboard.php';</script>";
        }
    } else {
        echo "<script>alert('Status tidak valid!'); window.location='admin_dashboard.php';</script>";
    }
} else {
    echo "<script>alert('Parameter tidak lengkap!'); window.location='admin_dashboard.php';</script>";
}
?>
