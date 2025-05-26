<?php
session_start();
require 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Pastikan ada parameter siswa_id
if (!isset($_GET['siswa_id'])) {
    header("Location: dashboard_pembimbing.php");
    exit();
}

$siswa_id = $_GET['siswa_id'];

// Mengambil data siswa
$query = "
    SELECT s.id, s.nama, s.kelas, s.foto,
           e.nama AS ekskul_nama, 
           e.deskripsi, 
           e.jadwal, 
           e.waktu, 
           e.ruangan, 
           e.logo
    FROM siswa s
    JOIN pendaftaran p ON s.id = p.siswa_id AND p.status = 'Diterima'
    JOIN ekskul e ON p.ekskul_id = e.id
    JOIN pembimbing_ekskul pe ON e.id = pe.ekskul_id
    WHERE pe.pembimbing_id = ? AND s.id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $siswa_id);
$stmt->execute();
$result = $stmt->get_result();
$siswa = $result->fetch_assoc();
$stmt->close();
$conn->close();

// Jika siswa tidak ditemukan, kembali ke dashboard
if (!$siswa) {
    header("Location: dashboard_pembimbing.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card mx-auto shadow" style="max-width: 450px;">
        <div class="card-body text-center">
            <h2 class="card-title text-primary"><?php echo htmlspecialchars($siswa['nama']); ?></h2>
            <p class="text-muted">Kelas: <?php echo htmlspecialchars($siswa['kelas']); ?></p>
            
            <!-- Foto Profil -->
            <img src="uploads/<?php echo htmlspecialchars($siswa['foto']); ?>" 
                 class="rounded-circle border" 
                 width="150" height="150" 
                 alt="Foto Profil">

            <!-- Ekstrakurikuler -->
            <h5 class="mt-3 text-secondary">Ekstrakurikuler:</h5>
            <div class="card mt-3">
                <div class="card-body">
                    <!-- Logo ekskul -->
                    <?php if (!empty($siswa['logo'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($siswa['logo']); ?>" 
                             class="img-fluid mb-2" 
                             width="80" alt="Logo Ekskul">
                    <?php endif; ?>

                    <h5 class="card-title"><?php echo htmlspecialchars($siswa['ekskul_nama']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($siswa['deskripsi']); ?></p>
                    <p><strong>Jadwal:</strong> <?php echo htmlspecialchars($siswa['jadwal']); ?></p>
                    <p><strong>Waktu:</strong> <?php echo htmlspecialchars($siswa['waktu']); ?></p>
                    <p><strong>Ruangan:</strong> <?php echo htmlspecialchars($siswa['ruangan']); ?></p>
                </div>
            </div>

            <!-- Tombol Kembali -->
            <a href="dashboard_pembimbing.php" class="btn btn-primary mt-3">Kembali</a>
        </div>
    </div>
</div>

</body>
</html>
