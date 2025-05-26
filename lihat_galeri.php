<?php
require 'koneksi.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: dashboard_pembimbing.php");
    exit();
}

$id = $_GET['id'];
$query = "SELECT * FROM galeri WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: dashboard_pembimbing.php");
    exit();
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lihat Galeri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h3>Detail Foto</h3>
        <img src="uploads/galeri/<?php echo $row['foto']; ?>" class="img-fluid" alt="Foto">
        <p class="mt-3"><strong>Deskripsi:</strong> <?php echo $row['deskripsi']; ?></p>
        <a href="dashboard_pembimbing.php" class="btn btn-secondary">Kembali</a>
    </div>
</body>
</html>
