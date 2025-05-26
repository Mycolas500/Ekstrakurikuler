<?php
session_start();
require 'koneksi.php';

// Cek apakah pengguna sudah login
$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ekstrakurikuler Sekolah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css"> <!-- External CSS -->
    <style>
/* Hero Section */
.hero-section {
    position: relative;
    overflow: hidden;
    height: 350px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-align: center;
}

.video-container {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
}

.video-container video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.hero-section .content {
    position: relative;
    z-index: 2;
    text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.7);
}

/* Profil */
.profile-img {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid white;
}

/* Kartu Ekskul */
.ekskul-card {
    height: 280px; /* Tambah tinggi kartu */
    border-radius: 10px;
    overflow: hidden;
    position: relative;
    transition: transform 0.3s ease;
    background-color: black;
}

.ekskul-card:hover {
    transform: scale(1.05);
}

/* Gambar ekskul */
.ekskul-card img {
    width: 100%;
    height: 70%;
    object-fit: cover; /* Agar gambar tetap proporsional */
}

/* Overlay Ekskul */
.ekskul-card .card-overlay {
    position: absolute;
    bottom: 0;
    width: 100%;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    text-align: center;
    padding: 10px;
}

/* Tombol Daftar */
.ekskul-card .btn {
    width: 100%;
    margin-top: 5px;
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
                    <li class="nav-item"><a class="nav-link" href="#">Beranda</a></li>
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

    <!-- Hero Section -->
    <header class="hero-section">
        <div class="video-container">
            <video autoplay loop muted playsinline>
                <source src="uploads/Kayoko.mp4" type="video/mp4">
            </video>
        </div>
        <div class="container content">
            <h1>Selamat Datang di Ekstrakurikuler Sekolah</h1>
            <p class="lead">Temukan dan kembangkan bakatmu bersama ekstrakurikuler terbaik di sekolah!</p>
            <?php if (!$isLoggedIn) { ?>
                <a href="register.php" class="btn btn-light btn-lg mt-3">Gabung Sekarang</a>
            <?php } ?>
        </div>
    </header>

    <!-- Daftar Ekskul -->
    <?php if ($isLoggedIn) { ?>
    <section class="container mt-5">
        <h2 class="text-center">Daftar Ekstrakurikuler</h2>
        <p class="text-center">Silakan pilih ekstrakurikuler yang kamu minati.</p>

        <div class="row">
            <?php
            $sql = "SELECT id, nama, logo FROM ekskul";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) { 
            ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card ekskul-card shadow-sm">
                        <img src="uploads/<?php echo $row['logo']; ?>" alt="<?php echo $row['nama']; ?>">
                        <div class="card-overlay">
                            <h5 class="card-title"><?php echo $row['nama']; ?></h5>
                            <a href="daftar_ekskul.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Daftar</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>
    <?php } ?>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>&copy; 2025 Ekstrakurikuler Sekolah. All Rights Reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
