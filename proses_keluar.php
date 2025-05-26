<?php
session_start();
require 'koneksi.php';

$pendaftaran_id = $_POST['pendaftaran_id'];
$alasan = $_POST['alasan'];
$user_id = $_SESSION['user_id'];

// ✅ Cek apakah ID pendaftaran milik user ini dan masih aktif
$cek = $conn->prepare("SELECT * FROM pendaftaran WHERE id = ? AND siswa_id = ?");
$cek->bind_param("ii", $pendaftaran_id, $user_id);
$cek->execute();
$cekResult = $cek->get_result();

if ($cekResult->num_rows === 0) {
    echo "<script>alert('Data pendaftaran tidak ditemukan atau bukan milik Anda.'); window.location.href='Homepage.php';</script>";
    exit;
}

// 🔍 Cek pengajuan keluar terakhir
$query = "SELECT * FROM pengajuan_keluar WHERE pendaftaran_id = ? ORDER BY tanggal_ajukan DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $pendaftaran_id);
$stmt->execute();
$result = $stmt->get_result();
$pengajuan_terakhir = $result->fetch_assoc();

$bolehAjukan = true;
$pesan = "";

if ($pengajuan_terakhir) {
    if ($pengajuan_terakhir['status'] === 'Ditolak') {
        $jumlah = $pengajuan_terakhir['jumlah_pengajuan'];
        $tanggalTolak = strtotime($pengajuan_terakhir['tanggal_tolak']);
        $selisihHari = (time() - $tanggalTolak) / (60 * 60 * 24);

        if ($jumlah >= 2) {
            $bolehAjukan = false;
            $pesan = "Pengajuan keluar sudah ditolak 2x. Tidak bisa mengajukan lagi.";
        } elseif ($selisihHari < 7) {
            $bolehAjukan = false;
            $pesan = "Silakan ajukan ulang setelah " . ceil(7 - $selisihHari) . " hari lagi.";
        }
    } elseif ($pengajuan_terakhir['status'] === 'Menunggu') {
        $bolehAjukan = false;
        $pesan = "Pengajuan keluar masih menunggu persetujuan.";
    } elseif ($pengajuan_terakhir['status'] === 'Disetujui') {
        $bolehAjukan = false;
        $pesan = "Kamu sudah keluar dari ekskul.";
    }
}

if ($bolehAjukan) {
    $jumlah = ($pengajuan_terakhir) ? $pengajuan_terakhir['jumlah_pengajuan'] + 1 : 1;

    $insert = "INSERT INTO pengajuan_keluar (pendaftaran_id, alasan, status, jumlah_pengajuan) 
               VALUES (?, ?, 'Menunggu', ?)";
    $stmt = $conn->prepare($insert);
    $stmt->bind_param("isi", $pendaftaran_id, $alasan, $jumlah);

    if ($stmt->execute()) {
        echo "<script>alert('Pengajuan berhasil diajukan.'); window.location.href='Homepage.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal menyimpan pengajuan.'); window.location.href='Homepage.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('$pesan'); window.location.href='Homepage.php';</script>";
    exit;
}
?>
