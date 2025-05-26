<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siswa_id = $_SESSION['user_id'];
    $ekskul_id = $_POST['ekskul_id'];

    // Cek apakah siswa terdaftar
    $stmt = $conn->prepare("SELECT * FROM pendaftaran WHERE siswa_id = ? AND ekskul_id = ? AND status = 'Diterima'");
    $stmt->bind_param("ii", $siswa_id, $ekskul_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt_update = $conn->prepare("UPDATE pendaftaran SET status_keluar = 'Menunggu Persetujuan' WHERE siswa_id = ? AND ekskul_id = ?");
        $stmt_update->bind_param("ii", $siswa_id, $ekskul_id);
        if ($stmt_update->execute()) {
            $_SESSION['success'] = "Pengajuan keluar berhasil dikirim. Tunggu persetujuan pembimbing.";
        } else {
            $_SESSION['error'] = "Gagal mengajukan keluar.";
        }
        $stmt_update->close();
    } else {
        $_SESSION['error'] = "Data pendaftaran tidak ditemukan.";
    }
}

header("Location: detail_ekskul.php?id=$ekskul_id");
exit();
