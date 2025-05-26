<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT nama, kelas, foto FROM siswa WHERE id = '$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $kelas = mysqli_real_escape_string($conn, $_POST['kelas']);

    if (!empty($_FILES['foto']['name'])) {
        $foto = $_FILES['foto'];
        $foto_name = time() . "_" . basename($foto['name']);
        $target_dir = "uploads/";
        $target_file = $target_dir . $foto_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ["jpg", "jpeg", "png", "gif"];

        if (in_array($imageFileType, $allowed_types)) {
            move_uploaded_file($foto['tmp_name'], $target_file);
            $update_sql = "UPDATE siswa SET nama='$nama', kelas='$kelas', foto='$foto_name' WHERE id='$user_id'";
        } else {
            $error = "Format gambar harus JPG, JPEG, PNG, atau GIF!";
        }
    } else {
        $update_sql = "UPDATE siswa SET nama='$nama', kelas='$kelas' WHERE id='$user_id'";
    }

    if (isset($update_sql) && $conn->query($update_sql) === TRUE) {
        $_SESSION['nama'] = $nama;
        header("Location: profil.php?status=success");
        exit();
    } else {
        $error = "Gagal memperbarui profil!";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 400px;">
        <div class="card-body">
            <h2 class="card-title text-center">Edit Profil</h2>
            <?php if (isset($error)) { echo "<p class='text-danger'>$error</p>"; } ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3 text-center">
                    <img src="uploads/<?php echo !empty($user['foto']) ? $user['foto'] : 'default.png'; ?>" 
                         class="rounded-circle" width="100" height="100" alt="Foto Profil">
                </div>
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($user['nama']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="kelas" class="form-label">Kelas</label>
                     <input type="text" class="form-control" id="kelas" name="kelas" value="<?php echo htmlspecialchars($user['kelas']); ?>" readonly></div>
                <div class="mb-3">
                    <label for="foto" class="form-label">Foto Profil</label>
                    <input type="file" class="form-control" id="foto" name="foto">
                </div>
                <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
            </form>
            <a href="Homepage.php" class="btn btn-secondary w-100 mt-2">Batal</a>
        </div>
    </div>
</div>

</body>
</html
