<?php
session_start();
require 'koneksi.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    die("Anda harus login terlebih dahulu.");
}

$siswa_id = intval($_SESSION['user_id']);
$ekskul_id = isset($_GET['ekskul_id']) ? intval($_GET['ekskul_id']) : 0;

if ($ekskul_id <= 0) {
    die("ID ekskul tidak valid.");
}

// Cek apakah siswa dan ekskul valid
function dataExists($conn, $table, $id) {
    $allowed_tables = ['siswa', 'ekskul'];
    if (!in_array($table, $allowed_tables)) return false;

    $stmt = $conn->prepare("SELECT id FROM `$table` WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $exists = $stmt->get_result()->num_rows > 0;
    $stmt->close();
    return $exists;
}

if (!dataExists($conn, 'siswa', $siswa_id)) die("Siswa tidak ditemukan.");
if (!dataExists($conn, 'ekskul', $ekskul_id)) die("Ekskul tidak ditemukan.");

// Cek apakah sudah mendaftar
$stmt = $conn->prepare("SELECT id FROM pendaftaran WHERE siswa_id = ? AND ekskul_id = ?");
$stmt->bind_param("ii", $siswa_id, $ekskul_id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo "<script>alert('Anda sudah mendaftar ke ekskul ini!'); window.location='daftar_ekskul.php';</script>";
    exit;
}
$stmt->close();

// Simpan pendaftaran baru
$stmt = $conn->prepare("INSERT INTO pendaftaran (siswa_id, ekskul_id, tanggal_daftar, status) VALUES (?, ?, CURRENT_TIMESTAMP, 'Pending')");
$stmt->bind_param("ii", $siswa_id, $ekskul_id);

if ($stmt->execute()) {
    echo "<script>
    alert('Pendaftaran berhasil! Tunggu konfirmasi dari admin.');
    window.location='daftar_ekskul.php?id=$ekskul_id';
</script>";

} else {
    echo "<script>alert('Terjadi kesalahan saat mendaftar.'); window.location='daftar_ekskul.php';</script>";
}

$stmt->close();
$conn->close();
?>
