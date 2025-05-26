<?php
session_start();
require 'koneksi.php';

// Cek apakah ID ekskul diberikan
if (!isset($_GET['id'])) {
    echo "<p class='text-center text-danger'>Ekskul tidak ditemukan.</p>";
    exit;
}

$id_ekskul = $_GET['id'];

// Ambil daftar anggota berdasarkan pendaftaran yang diterima
$sql = "SELECT s.nama, s.kelas, 
               COALESCE(s.foto, 'default-profile.png') AS foto 
        FROM pendaftaran p
        JOIN siswa s ON p.siswa_id = s.id
        WHERE p.ekskul_id = ? AND p.status = 'Diterima'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_ekskul);
$stmt->execute();
$result = $stmt->get_result();

// Ambil nama ekskul
$sql_ekskul = "SELECT nama FROM ekskul WHERE id = ?";
$stmt_ekskul = $conn->prepare($sql_ekskul);
$stmt_ekskul->bind_param("i", $id_ekskul);
$stmt_ekskul->execute();
$result_ekskul = $stmt_ekskul->get_result();
$ekskul = $result_ekskul->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Anggota - <?php echo htmlspecialchars($ekskul['nama']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="Homepage.php">Ekstrakurikuler</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="Homepage.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="tentang.php">Tentang Kami</a></li>
                    <li class="nav-item"><a class="nav-link" href="kontak.php">Kontak</a></li>
                    <?php if (isset($_SESSION['user_id'])) { ?>
                        <li class="nav-item"><a class="nav-link" href="profil.php">Profil</a></li>
                        <li class="nav-item"><a class="nav-link" href="ekskul_saya.php">Ekskul Saya</a></li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                Logout (<?php echo htmlspecialchars($_SESSION['nama']); ?>)
                            </a>
                        </li>
                    <?php } else { ?>
                        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Konten Utama -->
    <div class="container mt-5">
        <h2 class="fw-bold text-center">Daftar Anggota <?php echo htmlspecialchars($ekskul['nama']); ?></h2>

        <?php if ($result->num_rows > 0) { ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped mt-4">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center">Foto</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td class="text-center">
                                    <img src="uploads/<?php echo htmlspecialchars($row['foto']); ?>" 
                                         alt="Foto Profil" class="rounded-circle"
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                </td>
                                <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                <td><?php echo htmlspecialchars($row['kelas']); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <p class="text-center text-muted mt-4">Belum ada anggota yang bergabung dalam ekskul ini.</p>
        <?php } ?>

        <div class="text-center mt-4">
            <a href="daftar_ekskul.php?id=<?php echo $id_ekskul; ?>" class="btn btn-secondary">Kembali</a>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>&copy; 2025 Ekstrakurikuler Sekolah. All Rights Reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php 
$stmt->close();
$stmt_ekskul->close();
$conn->close();
?>
