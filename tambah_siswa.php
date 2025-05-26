<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $nama = $_POST['nama'];
    $kelas = $_POST['kelas'];
    $email = $_POST['email'];
    $ekstrakurikuler = $_POST['ekstrakurikuler'];

    // Upload foto
    $foto = $_FILES['foto']['name'];
    $target = 'uploads/' . basename($foto);
    
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
        // Insert data ke database
        $sql = "INSERT INTO siswa (nama, kelas, email, foto, ekstrakurikuler) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssss", $nama, $kelas, $email, $foto, $ekstrakurikuler);
            $stmt->execute();
            $stmt->close();
            header("Location: admin_dashboard.php"); // Redirect ke dashboard setelah berhasil
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Error uploading file.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Tambah Siswa</a>
            <a class="btn btn-danger" href="admin_logout.php">Logout</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Form Tambah Siswa</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" required>
            </div>
            <div class="mb-3">
                <label for="kelas" class="form-label">Kelas</label>
                <input type="text" class="form-control" id="kelas" name="kelas" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="ekstrakurikuler" class="form-label">Ekstrakurikuler</label>
                <input type="text" class="form-control" id="ekstrakurikuler" name="ekstrakurikuler" required>
            </div>
            <div class="mb-3">
                <label for="foto" class="form-label">Foto</label>
                <input type="file" class="form-control" id="foto" name="foto" required>
            </div>
            <button type="submit" class="btn btn-primary">Tambah</button>
            <a href="admin_dashboard.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>
</html>
