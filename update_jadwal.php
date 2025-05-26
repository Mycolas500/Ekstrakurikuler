<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $siswa_id = $_POST['siswa_id'];
    $ekskul_id = $_POST['ekskul_id'];
    $jadwal = $_POST['jadwal'];
    $ruangan = $_POST['ruangan'];
    $waktu = $_POST['waktu'];

    $query = "UPDATE ekskul SET jadwal = ?, ruangan = ?, waktu = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $jadwal, $ruangan, $waktu, $ekskul_id);

    if ($stmt->execute()) {
        echo "Jadwal berhasil diperbarui!";
    } else {
        echo "Gagal memperbarui jadwal!";
    }

    $stmt->close();
    $conn->close();
}
?>
