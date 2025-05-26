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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deskripsi = trim($_POST['deskripsi']);
    
    if (!empty($_FILES['foto']['name'])) { // Sesuaikan dengan form
        $file_tmp = $_FILES['foto']['tmp_name'];
        $file_name = time() . '_' . basename($_FILES['foto']['name']);
        $target_dir = "uploads/galeri/";
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($file_tmp, $target_file)) {
            // Hapus foto lama jika ada
            if (!empty($row['foto']) && file_exists($target_dir . $row['foto'])) {
                unlink($target_dir . $row['foto']);
            }

            $query = "UPDATE galeri SET foto = ?, deskripsi = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssi", $file_name, $deskripsi, $id);
        }
    } else {
        $query = "UPDATE galeri SET deskripsi = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $deskripsi, $id);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "Foto berhasil diperbarui!";
        header("Location: dashboard_pembimbing.php");
        exit();
    } else {
        $_SESSION['error'] = "Terjadi kesalahan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Galeri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h3>Edit Foto</h3>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="foto" class="form-label">Ganti Gambar (Opsional)</label>
                <input type="file" class="form-control" name="foto" id="foto" accept="image/*">
                <img src="uploads/galeri/<?php echo $row['foto']; ?>" width="100" class="mt-2">
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control" name="deskripsi" id="deskripsi" rows="3"><?php echo $row['deskripsi']; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="dashboard_pembimbing.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</body>
</html>
