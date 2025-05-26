<?php
require 'koneksi.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Ambil data ekskul
    $query = "SELECT nama, deskripsi, logo FROM ekskul WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $ekskul = $result->fetch_assoc();

    // Ambil data galeri ekskul (termasuk deskripsi dan tanggal)
    $query_gallery = "SELECT foto, deskripsi, tanggal_upload FROM galeri WHERE ekskul_id = ?";
    $stmt_gallery = $conn->prepare($query_gallery);
    $stmt_gallery->bind_param("i", $id);
    $stmt_gallery->execute();
    $result_gallery = $stmt_gallery->get_result();

    $gallery_items = [];
    while ($row = $result_gallery->fetch_assoc()) {
        if (!empty($row['foto'])) {
            $gallery_items[] = [
                'foto' => "uploads/galeri/" . $row['foto'],
                'deskripsi' => $row['deskripsi'],
                'tanggal' => date("d M Y", strtotime($row['tanggal_upload']))
            ];
        }
    }
} else {
    echo "ID tidak ditemukan!";
    exit;
}
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
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
            margin: 40px auto;
        }
        .rounded-img {
            width: 100%;
            max-height: 300px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        .carousel-caption {
            background: rgba(0, 0, 0, 0.6);
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center"><?= htmlspecialchars($ekskul['nama']); ?></h2>

    <!-- Foto Ekskul -->
    <div class="text-center my-4">
        <img src="uploads/<?= htmlspecialchars($ekskul['logo']); ?>" class="rounded-img" alt="Logo Ekskul">
    </div>

    <!-- Deskripsi Ekskul -->
    <p class="text-center"><?= nl2br(htmlspecialchars($ekskul['deskripsi'])); ?></p>

    <!-- Galeri Kegiatan -->
    <h3 class="text-center mt-5">Galeri Kegiatan</h3>
    <div id="galeriCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php if (!empty($gallery_items)) : ?>
                <?php foreach ($gallery_items as $index => $item) : ?>
                    <div class="carousel-item <?= $index === 0 ? 'active' : ''; ?>">
                        <div class="text-center">
                            <h5 class="mt-3 text-dark"><?= htmlspecialchars($item['deskripsi']); ?></h5>
                        </div>
                        <img src="<?= htmlspecialchars($item['foto']); ?>" class="d-block w-100 rounded" alt="Kegiatan">
                        <div class="carousel-caption">
                            <p class="text-light"><?= htmlspecialchars($item['tanggal']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="carousel-item active">
                    <div class="text-center">
                        <h5 class="mt-3 text-dark">Belum ada kegiatan</h5>
                    </div>
                    <img src="uploads/galeri/default.jpg" class="d-block w-100 rounded" alt="Tidak ada foto">
                    <div class="carousel-caption">
                        <p class="text-light">-</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#galeriCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#galeriCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
