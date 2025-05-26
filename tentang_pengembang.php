<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }

        /* About Section */
        .about-section {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
            text-align: center;
        }

        .about-section h1 {
            font-size: 2.5rem;
            color: #333;
            font-weight: 600;
        }

        .about-section p {
            font-size: 1.2rem;
            color: #555;
            line-height: 1.6;
        }

        /* Team Section */
        .team-member {
            text-align: center;
            margin-top: 40px;
        }

        .divider {
            width: 50px;
            height: 4px;
            background: #007bff;
            margin: 10px auto;
            border-radius: 5px;
        }

        .team-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .team-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        /* Efek elegan untuk profil tim pengembang */
        .team-card img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #007bff;
            transition: box-shadow 0.3s, transform 0.3s;
        }

        .team-card img:hover {
            box-shadow: 0px 0px 20px rgba(0, 123, 255, 0.8);
            transform: scale(1.05);
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
                <li class="nav-item">
                    <a class="nav-link" href="Homepage.php">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="tentang_ekskul.php">Tentang Ekskul</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="tentang_pengembang.php">Tim Pengembang</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="kontak_pengembang.php">Kontak</a>
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

<!-- Tentang Kami -->
<div class="container mt-5">
    <div class="about-section">
        <h1>Tentang Kami</h1>
        <div class="divider"></div>
        <p>
            Kami adalah tim yang berdedikasi untuk menyediakan platform terbaik bagi siswa untuk mendaftar dan mengikuti kegiatan ekstrakurikuler di sekolah. Dengan visi untuk membantu siswa mengembangkan potensi mereka, kami berkomitmen memberikan pengalaman yang mudah dan menyenangkan.
        </p>
        <p>
            Website ini dirancang untuk memudahkan siswa melihat ekskul yang tersedia, mendaftar, dan melihat status pendaftaran mereka. Kami juga bekerja sama dengan para pembimbing yang berkompeten untuk memastikan setiap kegiatan berjalan dengan baik.
        </p>
    </div>

    <!-- Tim Pengembang -->
    <div class="team-member">
        <h2>Tim Kami</h2>
        <div class="divider"></div>
        <div class="row justify-content-center">
            <div class="col-md-4 mb-4">
                <div class="team-card text-center p-3">
                    <img src="uploads/Myco.jpg" alt="Mycolas">
                    <h4 class="mt-3">Mycolas</h4>
                    <p>Pengembang utama dan backend developer.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="team-card text-center p-3">
                    <img src="uploads/Praja1.jpg" alt="Praja">
                    <h4 class="mt-3">Praja</h4>
                    <p>Frontend developer dan UI/UX designer.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="team-card text-center p-3">
                    <img src="uploads/Arfa.jpg" alt="Arfa">
                    <h4 class="mt-3">Arfa</h4>
                    <p>Tester dan dokumentasi proyek.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Assistant -->
    <div class="team-member">
        <h2>AI Assistant</h2>
        <div class="divider"></div>
        <div class="row justify-content-center">
            <div class="col-md-4 mb-4">
                <div class="team-card text-center p-3">
                    <img src="uploads/Clara.jpg" alt="Clara AI">
                    <h4 class="mt-3">Clara (AI Assistant)</h4>
                    <p>Membantu dalam perancangan dan pengembangan.</p>
                </div>
            </div>
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
