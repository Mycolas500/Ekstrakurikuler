<?php
require 'koneksi.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Hapus siswa dari database
    $sql = "DELETE FROM siswa WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: admin_dashboard.php"); // Redirect setelah berhasil
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "Invalid ID.";
}
?>
