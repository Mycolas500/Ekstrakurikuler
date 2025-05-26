<?php
require 'koneksi.php';

// Cek apakah ID ada di URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // Ambil ID dari URL dan sanitasi
    $id = (int) $_GET['id']; // Konversi ID menjadi integer untuk menghindari injection

    // Persiapkan query DELETE
    $sql = "DELETE FROM pembimbing WHERE id = ?";

    // Siapkan statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameter (tipe 'i' untuk integer)
        $stmt->bind_param("i", $id);

        // Eksekusi query
        if ($stmt->execute()) {
            // Redirect ke halaman dashboard setelah sukses
            header("Location: admin_dashboard.php");
            exit(); // Pastikan eksekusi berhenti setelah redirect
        } else {
            // Jika gagal, tampilkan error
            echo "Error: " . $stmt->error;
        }

        // Tutup prepared statement
        $stmt->close();
    } else {
        // Jika prepare statement gagal
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    // Jika ID tidak ada atau tidak valid
    echo "Invalid ID.";
}

// Tutup koneksi
$conn->close();
?>
