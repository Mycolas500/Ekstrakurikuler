<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }
        .contact-section {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }
        .contact-section h1, .contact-section h2 {
            font-size: 2rem;
            color: #333;
            font-weight: 600;
            text-align: center;
        }
        .contact-section p {
            font-size: 1.2rem;
            color: #555;
            text-align: center;
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
        <a class="navbar-brand" href="#">Kontak</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="Homepage.php">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="tentang_ekskul.php">Tentang Ekskul</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="tentang_pengembang.php">Tim Pengembang</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="kontak.php">Kontak</a>
                </li>
                <li class="nav-item d-flex align-items-center ms-4">
                    <?php
                    session_start(); // Pastikan session dimulai
                    // // Pastikan session foto ada sebelum digunakan
                     if (isset($_SESSION['foto']) && !empty($_SESSION['foto'])) {
                        $foto = 'uploads/' . $_SESSION['foto'];
                    } else {
                        $foto = 'uploads/default.png'; // Default foto jika tidak ada
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

<!-- Hubungi Kami -->
<div class="container mt-5">
    <div class="contact-section text-center">
        <h1>Hubungi Kami</h1>
        <div class="divider"></div>
        <div class="row">
            <div class="col-md-4">
                <div class="card p-3">
                    <h5><i class="bi bi-phone"></i> Telepon</h5>
                    <p>(+62)895700382000</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3">
                    <h5><i class="bi bi-geo-alt"></i> Alamat</h5>
                    <p>SMK Negeri 5 Surakarta<br>Jl. Adi Sucipto No.42, Kerten, Laweyan, Surakarta, Jawa Tengah 57143</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3">
                    <h5><i class="bi bi-envelope"></i> Email</h5>
                    <p>info@smkn5solo.net</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Google Maps -->
    <div class="contact-section">
        <h2>Lokasi Kami</h2>
        <div class="divider"></div>
        <div class="text-center">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3955.4657126207753!2d110.78585837408163!3d-7.528716274915274!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a1440b46b5c6b%3A0xd42d711be0a99df1!2sSMK%20Negeri%205%20Surakarta!5e0!3m2!1sid!2sid!4v1711539202021" 
                width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy">
            </iframe>
            <br>
            <a href="https://maps.app.goo.gl/2YQnDrxCT5CYjrPc8" target="_blank" class="btn btn-primary mt-3">
                Lihat di Google Maps
            </a>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-3 mt-5">
    <p>&copy; 2025 SMKN 5 Surakarta. All Rights Reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
