<?php
session_start();
require 'koneksi.php';

$isLoggedIn = isset($_SESSION['user_id']);
$ekskul_id = $_GET['id'] ?? null;

if (!$ekskul_id) {
    echo "Ekstrakurikuler tidak ditemukan.";
    exit;
}

$sql = "SELECT * FROM ekskul WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ekskul_id);
$stmt->execute();
$ekskul = $stmt->get_result()->fetch_assoc();

if (!$ekskul) {
    echo "Ekstrakurikuler tidak ditemukan.";
    exit;
}

$sqlPembimbing = "SELECT DISTINCT pembimbing.nama 
                  FROM pembimbing_ekskul 
                  JOIN pembimbing ON pembimbing_ekskul.pembimbing_id = pembimbing.id 
                  WHERE pembimbing_ekskul.ekskul_id = ?";
$stmtPembimbing = $conn->prepare($sqlPembimbing);
$stmtPembimbing->bind_param("i", $ekskul_id);
$stmtPembimbing->execute();
$resultPembimbing = $stmtPembimbing->get_result();

$nama_pembimbing = [];
while ($row = $resultPembimbing->fetch_assoc()) {
    $nama_pembimbing[] = $row['nama'];
}
$pembimbing_list = implode(", ", $nama_pembimbing);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($ekskul['nama']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            margin-left: 10px;
        }
    </style>
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="#">Ekstrakurikuler</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="Homepage.php">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="tentang_ekskul.php">Tentang Ekskul</a></li>
                <li class="nav-item"><a class="nav-link" href="kontak.php">Kontak</a></li>

                <?php if ($isLoggedIn) { ?>
                    <li class="nav-item"><a class="nav-link" href="ekskul_saya.php">Ekskul Saya</a></li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout (<?php echo $_SESSION['nama']; ?>)</a>
                    </li>
                    <li class="nav-item">
                        <a href="edit_profil.php">
                            <?php 
                            $foto = !empty($_SESSION['foto']) ? 'uploads/' . $_SESSION['foto'] : 'uploads/default.png';
                            ?>
                            <img src="<?php echo $foto; ?>" alt="Profil" class="profile-img">
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

<header class="bg-primary text-white text-center py-5">
    <div class="container">
        <h1><?= htmlspecialchars($ekskul['nama']) ?></h1>
        <p class="lead"><?= htmlspecialchars($ekskul['deskripsi']) ?></p>
    </div>
</header>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-6">
            <h4>Detail Ekskul</h4>
            <table class="table table-bordered">
                <tr><th>Jadwal</th><td><?= htmlspecialchars($ekskul['jadwal']) ?></td></tr>
                <tr><th>Ruangan</th><td><?= htmlspecialchars($ekskul['ruangan']) ?></td></tr>
                <tr><th>Waktu</th><td><?= htmlspecialchars($ekskul['waktu']) ?></td></tr>
                <tr><th>Pembimbing</th><td><?= htmlspecialchars($pembimbing_list) ?></td></tr>
            </table>

            <?php if ($isLoggedIn): ?>
                <?php
                $stmtCek = $conn->prepare("SELECT id, status, tanggal_daftar, TIMESTAMPDIFF(MINUTE, tanggal_daftar, NOW()) AS menit_berlalu 
                                           FROM pendaftaran 
                                           WHERE siswa_id = ? AND ekskul_id = ?");
                $stmtCek->bind_param("ii", $_SESSION['user_id'], $ekskul_id);
                $stmtCek->execute();
                $result = $stmtCek->get_result();
                $pendaftaran = $result->fetch_assoc();
                ?>

                <?php if (!$pendaftaran): ?>
                    <a href="daftar.php?ekskul_id=<?= $ekskul_id ?>" class="btn btn-primary w-100">Daftar Sekarang</a>

                <?php elseif ($pendaftaran['status'] === 'Pending'): ?>
                    <?php
                    $menit = (int) $pendaftaran['menit_berlalu'];
                    $sisaDetik = max(0, (60 - $menit) * 60);
                    ?>

                    <?php if ($menit <= 60): ?>
                        <form action="batal_daftar.php" method="POST" class="mb-2" id="formBatal">
                            <input type="hidden" name="pendaftaran_id" value="<?= $pendaftaran['id'] ?>">
                            <button type="submit" class="btn btn-danger w-100">Batal Daftar</button>
                        </form>
                        <div class="alert alert-info text-center" id="countdownBox">
                            Anda sudah mendaftar. Waktu pembatalan tersisa:
                            <div id="countdown" class="fw-bold text-danger"></div>
                        </div>
                        <script>
                            let detik = <?= $sisaDetik ?>;
                            const countdownEl = document.getElementById('countdown');
                            const formBatal = document.getElementById('formBatal');
                            const countdownBox = document.getElementById('countdownBox');

                            function updateCountdown() {
                                if (detik <= 0) {
                                    countdownEl.textContent = 'Waktu habis';
                                    if (formBatal) formBatal.remove();
                                    countdownBox.innerHTML = '<strong>Anda sudah mendaftar. Silakan tunggu konfirmasi pembimbing.</strong>';
                                    return;
                                }
                                let m = Math.floor(detik / 60);
                                let s = detik % 60;
                                countdownEl.textContent = `${m} menit ${s < 10 ? '0' + s : s} detik`;
                                detik--;
                                setTimeout(updateCountdown, 1000);
                            }
                            updateCountdown();
                        </script>
                    <?php else: ?>
                        <div class="alert alert-info text-center">Anda sudah mendaftar. Silakan tunggu konfirmasi pembimbing.</div>
                    <?php endif; ?>

                <?php elseif ($pendaftaran['status'] === 'Diterima'): ?>
                    <div class="alert alert-success text-center">Selamat! Anda sudah <strong>terdaftar</strong>.</div>
                    <button class="btn btn-success w-100" disabled>Sudah Terdaftar</button>

                <?php elseif ($pendaftaran['status'] === 'Ditolak'): ?>
                    <div class="alert alert-danger text-center">Maaf, pendaftaran Anda <strong>ditolak</strong>.</div>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-warning">Silakan <a href="login.php">login</a> untuk mendaftar ekskul ini.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<footer class="bg-dark text-white text-center py-3 mt-5">
    <p>&copy; 2025 Ekstrakurikuler Sekolah</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
