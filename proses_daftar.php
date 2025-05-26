<?php
session_start();
require 'koneksi.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Pastikan request menggunakan metode POST dan data dikirim dengan benar
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['ekskul_id']) || !is_numeric($_POST['ekskul_id'])) {
    echo "Ekskul tidak ditemukan!";
    exit();
}

$siswa_id = $_SESSION['user_id'];
$ekskul_id = intval($_POST['ekskul_id']); // Pastikan ID ekskul berupa angka
$tanggal_daftar = date("Y-m-d");
$status = "Pending";

// Cek apakah siswa sudah mendaftar ekskul ini
$cek = $conn->prepare("SELECT id FROM pendaftaran WHERE siswa_id = ? AND ekskul_id = ?");
$cek->bind_param("ii", $siswa_id, $ekskul_id);
$cek->execute();
$result = $cek->get_result();

if ($result->num_rows > 0) {
    echo "Kamu sudah mendaftar di ekstrakurikuler ini!";
    exit();
}

// Simpan ke database
$sql = "INSERT INTO pendaftaran (siswa_id, ekskul_id, tanggal_daftar, status) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiss", $siswa_id, $ekskul_id, $tanggal_daftar, $status);

if ($stmt->execute()) {
    header("Location: ekskul_saya.php?success=1");
    exit(); // Pastikan script berhenti setelah redirect
} else {
    echo "Gagal mendaftar! Kesalahan: " . $stmt->error;
}

// Tutup koneksi
$stmt->close();
$conn->close();
?>
