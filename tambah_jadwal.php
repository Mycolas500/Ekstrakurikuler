<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pembimbing') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak!']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jadwal = trim($_POST['jadwal']);
    $user_id = $_SESSION['user_id'];

    if (empty($jadwal)) {
        echo json_encode(['status' => 'error', 'message' => 'Jadwal tidak boleh kosong!']);
        exit();
    }

    // Cek apakah pembimbing memiliki ekskul yang bisa diperbarui
    $query = "SELECT ekskul_id FROM pembimbing_ekskul WHERE pembimbing_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Anda tidak memiliki ekskul yang bisa diperbarui!']);
        exit();
    }

    $ekskul = $result->fetch_assoc();
    $ekskul_id = $ekskul['ekskul_id'];
    $stmt->close();

    // Update jadwal ekskul
    $updateQuery = "UPDATE ekskul SET jadwal = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $jadwal, $ekskul_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil ditambahkan!', 'jadwal' => $jadwal]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan jadwal!']);
    }

    $stmt->close();
    $conn->close();
}
?>
