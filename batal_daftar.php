<?php
session_start();
require 'koneksi.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    die("Anda harus login terlebih dahulu.");
}

$siswa_id = $_SESSION['user_id'];
$pendaftaran_id = isset($_POST['pendaftaran_id']) ? intval($_POST['pendaftaran_id']) : 0;

// Validasi ID
if ($pendaftaran_id <= 0) {
    die("ID pendaftaran tidak valid.");
}

// Ambil data pendaftaran sesuai user
$stmt = $conn->prepare("SELECT ekskul_id, status, TIMESTAMPDIFF(MINUTE, tanggal_daftar, NOW()) AS menit_berlalu 
                        FROM pendaftaran 
                        WHERE id = ? AND siswa_id = ?");
$stmt->bind_param("ii", $pendaftaran_id, $siswa_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Pendaftaran tidak ditemukan atau bukan milik Anda.");
}

$data = $result->fetch_assoc();
$menit_berlalu = (int)$data['menit_berlalu'];

// Cek status
if ($data['status'] !== 'Pending') {
    echo "<script>alert('Pendaftaran sudah diproses. Tidak bisa dibatalkan.'); window.location='daftar_ekskul.php';</script>";
    exit;
}

// Cek waktu pembatalan
if ($menit_berlalu > 60) {
    echo "<script>alert('Waktu pembatalan sudah habis (lebih dari 60 menit).'); window.location='daftar_ekskul.php';</script>";
    exit;
}

// Hapus pendaftaran
$stmt = $conn->prepare("DELETE FROM pendaftaran WHERE id = ? AND siswa_id = ?");
$stmt->bind_param("ii", $pendaftaran_id, $siswa_id);
if ($stmt->execute()) {
    echo "<script>alert('Pendaftaran berhasil dibatalkan.'); window.location='daftar_ekskul.php';</script>";
} else {
    echo "<script>alert('Terjadi kesalahan saat membatalkan.'); window.location='daftar_ekskul.php';</script>";
}

$stmt->close();
$conn->close();
?>
