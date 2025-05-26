<?php
session_start();
include 'koneksi.php'; // File koneksi database

// Ambil data ekstrakurikuler dari database
$query = "SELECT id, nama, deskripsi, logo FROM ekskul";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Ekstrakurikuler</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }
        .about-section {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }
        .divider {
            width: 50px;
            height: 4px;
            background: #007bff;
            margin: 10px auto;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="#">Ekstrakurikuler</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="Homepage.php">Beranda</a></li>
                <li class="nav-item"><a class="nav-link active" href="tentang_ekskul.php">Tentang Ekskul</a></li>
                <li class="nav-item"><a class="nav-link" href="tentang_pengembang.php">Tim Pengembang</a></li>
                <li class="nav-item"><a class="nav-link" href="kontak.php">Kontak</a></li>
                <li class="nav-item d-flex align-items-center ms-4">
                    <?php
                    if (isset($_SESSION['foto']) && !empty($_SESSION['foto'])) {
                        $foto = 'uploads/' . $_SESSION['foto'];
                    } else {
                        $foto = 'uploads/default.png';
                    }
                    ?>
                    <a href="edit_profil.php">
                        <img src="<?php echo htmlspecialchars($foto, ENT_QUOTES, 'UTF-8'); ?>" alt="Profil" 
                             class="rounded-circle border border-light" 
                             style="width: 40px; height: 40px; object-fit: cover;">
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Tentang Ekskul -->
<div class="container mt-5">
    <div class="about-section">
        <h1>Tentang Ekstrakurikuler</h1>
        <div class="divider"></div>
        <p>
            Ekstrakurikuler adalah kegiatan di luar jam pelajaran yang bertujuan untuk mengembangkan minat, bakat, dan keterampilan siswa. 
            Ekskul memberikan kesempatan bagi siswa untuk belajar hal baru, mengasah kemampuan, serta membangun kerjasama dalam tim.
        </p>
    </div>

    <div class="about-section">
        <h2>Jenis Ekstrakurikuler</h2>
        <div class="divider"></div>
        <div class="row">
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <div class="col-md-4 mb-4">
                    <div class="card p-3">
                        <img src="uploads/<?php echo htmlspecialchars($row['logo']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['nama']); ?>">
                        <div class="card-body text-center">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['nama']); ?></h5>
                            <p class="card-text"><?php echo substr(htmlspecialchars($row['deskripsi']), 0, 80) . '...'; ?></p>
                            <a href="detail_tentang_ekskul.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-3 mt-5">
    <p>&copy; 2025 Ekstrakurikuler Sekolah. All Rights Reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
