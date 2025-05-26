<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $jadwal = $_POST['jadwal'];
    $pembimbing_id = $_POST['pembimbing_id'];
    $kapasitas = $_POST['kapasitas'];

    $sql = "INSERT INTO ekskul (nama, deskripsi, jadwal, pembimbing_id, kapasitas) 
            VALUES ('$nama', '$deskripsi', '$jadwal', " . ($pembimbing_id ? "'$pembimbing_id'" : "NULL") . ", '$kapasitas')";
    
    if ($conn->query($sql)) {
        header("Location: dashboard_admin.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Ekskul</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Tambah Ekskul</h2>
        <form method="POST">
            <div class="mb-3">
                <label>Nama Ekskul:</label>
                <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Deskripsi:</label>
                <textarea name="deskripsi" class="form-control" required></textarea>
            </div>
            <div class="mb-3">
                <label>Jadwal:</label>
                <input type="text" name="jadwal" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Pembimbing:</label>
                <select name="pembimbing_id" class="form-control">
                    <option value="">Tanpa Pembimbing</option>
                    <?php
                    $pembimbing_query = "SELECT * FROM pembimbing";
                    $pembimbing_result = $conn->query($pembimbing_query);
                    while ($row = $pembimbing_result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . $row['nama'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label>Kapasitas:</label>
                <input type="number" name="kapasitas" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="admin_dashboard.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>
</html>
