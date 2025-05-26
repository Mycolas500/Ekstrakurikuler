<?php
session_start();
require 'koneksi.php'; // Panggil koneksi database
$conn = new mysqli("localhost", "root", "", "project");

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$siswa_id = $_SESSION['user_id'];

// Gunakan prepared statement untuk menghindari SQL Injection
$stmt = $conn->prepare("SELECT ekskul.id, ekskul.nama, ekskul.jadwal, ekskul.ruangan, ekskul.waktu,
                               COALESCE(pembimbing.nama, 'Admin Pembimbing') AS pembimbing, 
                               pendaftaran.status
                        FROM pendaftaran
                        JOIN ekskul ON pendaftaran.ekskul_id = ekskul.id
                        LEFT JOIN pembimbing_ekskul ON ekskul.id = pembimbing_ekskul.ekskul_id
                        LEFT JOIN pembimbing ON pembimbing_ekskul.pembimbing_id = pembimbing.id
                        WHERE pendaftaran.siswa_id = ?");
$stmt->bind_param("i", $siswa_id); // Bind parameter siswa_id sebagai integer
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ekskul Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .status {
            font-weight: bold;
        }
        .status-diterima {
            color: green;
        }
        .status-ditolak {
            color: red;
        }
        .status-menunggu {
            color: orange;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4">Ekskul yang Saya Ikuti</h1>

    <?php if ($result->num_rows > 0) { ?>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Nama Ekskul</th>
                    <th>Jadwal</th>
                    <th>Ruangan</th>
                    <th>Waktu</th>
                    <th>Pembimbing</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nama']); ?></td>
                        <td><?php echo htmlspecialchars($row['jadwal']); ?></td>
                        <td><?php echo htmlspecialchars($row['ruangan']); ?></td>
                        <td><?php echo htmlspecialchars($row['waktu']); ?></td>
                        <td><?php echo htmlspecialchars($row['pembimbing']); ?></td>
                        <td class="status 
                            <?php 
                            echo ($row['status'] == 'Diterima') ? 'status-diterima' : 
                                 (($row['status'] == 'Ditolak') ? 'status-ditolak' : 'status-menunggu');
                            ?>">
                            <?php echo htmlspecialchars($row['status']); ?>
                        </td>
                        <td>
                            <a href="detail_ekskul.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm">Lihat Ekstrakurikuler</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p class="text-center">Tidak ada ekskul yang Anda ikuti.</p>
    <?php } ?>

    <!-- Tombol kembali ke beranda dipindahkan ke bawah -->
    <div class="text-center mt-4">
        <a href="Homepage.php" class="btn btn-secondary">Kembali ke Beranda</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
