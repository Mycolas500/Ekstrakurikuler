<?php
session_start();
require '../koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';

if ($aksi == 'terima') {
    $query = "UPDATE pendaftaran SET status = 'Diterima' WHERE id = ?";
} elseif ($aksi == 'tolak') {
    $query = "UPDATE pendaftaran SET status = 'Ditolak' WHERE id = ?";
} else {
    header("Location: admin_dashboard.php");
    exit();
}

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    echo "<script>alert('Status pendaftaran diperbarui!'); window.location='admin_dashboard.php';</script>";
}
?>
