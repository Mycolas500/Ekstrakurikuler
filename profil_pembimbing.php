<?php
session_start();
require 'koneksi.php';

// Pastikan user sudah login sebagai pembimbing
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pembimbing') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data pembimbing dari database
$query = "SELECT nama, email, no_hp, foto FROM pembimbing WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$pembimbing = $result->fetch_assoc();
$stmt->close();

// Proses update profil
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_baru = trim($_POST["nama"]);
    $no_hp_baru = trim($_POST["no_hp"]);

    // Validasi nomor HP harus tepat 12 angka
    if (!preg_match('/^[0-9]{12}$/', $no_hp_baru)) {
        $error = "Nomor HP harus terdiri dari tepat 12 digit angka.";
    }

    // Cek apakah pengguna mengunggah foto baru
    if (!empty($_FILES["foto"]["name"]) && !isset($error)) {
        $target_dir = "uploads/";
        $file_ext = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
        $file_name = uniqid() . "." . $file_ext;
        $target_file = $target_dir . $file_name;
        
        // Validasi format gambar
        $allowed_ext = ["jpg", "jpeg", "png", "gif"];
        if (!in_array(strtolower($file_ext), $allowed_ext)) {
            $error = "Format gambar tidak valid! Gunakan JPG, JPEG, PNG, atau GIF.";
        } elseif (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
            // Hapus foto lama jika ada
            if (!empty($pembimbing['foto']) && file_exists("uploads/" . $pembimbing['foto'])) {
                unlink("uploads/" . $pembimbing['foto']);
            }
            $update_query = "UPDATE pembimbing SET nama = ?, no_hp = ?, foto = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("sssi", $nama_baru, $no_hp_baru, $file_name, $user_id);
        } else {
            $error = "Gagal mengunggah gambar.";
        }
    } elseif (!isset($error)) {
        // Jika tidak mengganti foto
        $update_query = "UPDATE pembimbing SET nama = ?, no_hp = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssi", $nama_baru, $no_hp_baru, $user_id);
    }

    if (!isset($error) && $stmt->execute()) {
        $_SESSION['nama'] = $nama_baru;
        if (!empty($file_name)) {
            $_SESSION['foto'] = $file_name;
        }
        header("Location: profil_pembimbing.php?success=1");
        exit();
    } else {
        $error = isset($error) ? $error : "Terjadi kesalahan saat memperbarui profil.";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pembimbing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard_pembimbing.php">Dashboard Pembimbing</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a href="logout.php" class="btn btn-danger ms-3">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h3>Profil Pembimbing</h3>
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Profil berhasil diperbarui!</div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <img src="uploads/<?php echo htmlspecialchars($pembimbing['foto'] ?: 'default.jpg'); ?>" class="rounded-circle" width="150" height="150" alt="Foto Profil">
                </div>
                <form action="profil_pembimbing.php" method="post" enctype="multipart/form-data" class="mt-3">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" id="nama" name="nama" class="form-control" value="<?php echo htmlspecialchars($pembimbing['nama']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email (Tidak dapat diubah)</label>
                        <input type="email" id="email" class="form-control" value="<?php echo htmlspecialchars($pembimbing['email']); ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="no_hp" class="form-label">Nomor HP</label>
                        <input type="text" id="no_hp" name="no_hp" class="form-control" value="<?php echo htmlspecialchars($pembimbing['no_hp']); ?>" required minlength="12" maxlength="12" pattern="[0-9]{12}">
                        <small class="text-muted">Nomor HP harus terdiri dari 12 angka.</small>
                    </div>
                    <div class="mb-3">
                        <label for="foto" class="form-label">Foto Profil (Opsional)</label>
                        <input type="file" id="foto" name="foto" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="dashboard_pembimbing.php" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
