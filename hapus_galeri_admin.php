<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $pembimbing_id = $_SESSION['user_id'];

    $query = "SELECT foto FROM galeri WHERE id = ? AND pembimbing_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $id, $pembimbing_id);
    $stmt->execute();
    $stmt->bind_result($foto);
    $stmt->fetch();
    $stmt->close();

    if ($foto && file_exists($foto)) {
        unlink($foto);
    }

    $query = "DELETE FROM galeri WHERE id = ? AND pembimbing_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $id, $pembimbing_id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Foto berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Gagal menghapus foto.";
    }
    $stmt->close();
}

header("Location: admin_dashboard.php");
exit();
?>
