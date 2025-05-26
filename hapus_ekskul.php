<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Hapus data dari database
    $sql = "DELETE FROM ekskul WHERE id = $id";
    if ($conn->query($sql)) {
        header("Location: admin_dashboard.php?msg=deleted");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    header("Location: admin_dashboard.php");
    exit();
}
?>
