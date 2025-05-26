<?php
session_start();
require 'koneksi.php'; // Panggil koneksi database
$conn = new mysqli("localhost", "root", "", "project");

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Cek apakah ada parameter ID ekskul
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Ekskul tidak ditemukan.";
    exit();
}

$ekskul_id = $_GET['id'];

// Query untuk mendapatkan detail ekskul beserta pembimbingnya
$stmt = $conn->prepare("SELECT ekskul.nama, ekskul.jadwal, ekskul.ruangan, ekskul.waktu,
                               COALESCE(pembimbing.nama, 'Admin Pembimbing') AS pembimbing, 
                               COALESCE(pembimbing.foto, 'default.jpg') AS pembimbing_foto,
                               COALESCE(pembimbing.no_hp, '-') AS no_hp
                        FROM ekskul
                        LEFT JOIN pembimbing_ekskul ON ekskul.id = pembimbing_ekskul.ekskul_id
                        LEFT JOIN pembimbing ON pembimbing_ekskul.pembimbing_id = pembimbing.id
                        WHERE ekskul.id = ?");
$stmt->bind_param("i", $ekskul_id);
$stmt->execute();
$result = $stmt->get_result();

// Cek apakah data ekskul ditemukan
if ($result->num_rows === 0) {
    echo "Ekskul tidak ditemukan.";
    exit();
}

$ekskul = $result->fetch_assoc();

// Query untuk mendapatkan daftar anggota ekskul
$stmt = $conn->prepare("SELECT siswa.nama, COALESCE(siswa.foto, 'default.jpg') AS foto
                        FROM pendaftaran
                        JOIN siswa ON pendaftaran.siswa_id = siswa.id
                        WHERE pendaftaran.ekskul_id = ? AND pendaftaran.status = 'Diterima'");
$stmt->bind_param("i", $ekskul_id);
$stmt->execute();
$anggota_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Ekstrakurikuler</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }
        .profile-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4">Detail Ekstrakurikuler</h1>
    <table class="table table-bordered">
        <tr>
            <th>Nama Ekstrakurikuler</th>
            <td><?php echo htmlspecialchars($ekskul['nama']); ?></td>
        </tr>
        <tr>
            <th>Pembimbing</th>
            <td>
                <img src="uploads/<?php echo htmlspecialchars($ekskul['pembimbing_foto']); ?>" class="profile-img" alt="Foto Pembimbing">
                <br>
                <strong><?php echo htmlspecialchars($ekskul['pembimbing']); ?></strong>
                <br>
                📞 <strong>No HP:</strong> <?php echo htmlspecialchars($ekskul['no_hp']); ?>
            </td>
        </tr>
        <tr>
            <th>Jadwal</th>
            <td><?php echo htmlspecialchars($ekskul['jadwal']); ?></td>
        </tr>
        <tr>
            <th>Ruangan</th>
            <td><?php echo htmlspecialchars($ekskul['ruangan']); ?></td>
        </tr>
        <tr>
            <th>Waktu</th>
            <td><?php echo htmlspecialchars($ekskul['waktu']); ?></td>
        </tr>
    </table>

    <h3 class="mt-4">Anggota Ekstrakurikuler</h3>
    <?php if ($anggota_result->num_rows > 0) { ?>
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Foto</th>
                    <th>Nama</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $anggota_result->fetch_assoc()) { ?>
                    <tr>
                        <td>
                            <img src="uploads/<?php echo htmlspecialchars($row['foto']); ?>" class="profile-img" alt="Foto Anggota">
                        </td>
                        <td><?php echo htmlspecialchars($row['nama']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p>Tidak ada anggota dalam ekskul ini.</p>
    <?php } ?>

    <!-- Tombol kembali ke halaman sebelumnya -->
    <div class="text-center mt-4">
        <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
