<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM pendaftaran WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Pendaftaran berhasil dihapus.'); window.location='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus pendaftaran.'); window.location='admin_dashboard.php';</script>";
    }
    
    $stmt->close();
    $conn->close();
} else {
    header("Location: admin_dashboard.php");
    exit();
}
?>
