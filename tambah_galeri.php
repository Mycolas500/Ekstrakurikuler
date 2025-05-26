<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pembimbing') {
    header("Location: login.php");
    exit();
}

$pembimbing_id = $_SESSION['user_id'];
$ekskul_ids = [];

// Ambil semua ekskul yang diampu oleh pembimbing
$query_ekskul = "SELECT ekskul_id FROM pembimbing_ekskul WHERE pembimbing_id = ?";
$stmt = $conn->prepare($query_ekskul);
$stmt->bind_param("i", $pembimbing_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $ekskul_ids[] = $row['ekskul_id'];
}
$stmt->close();

// Jika tidak punya ekskul sama sekali
if (empty($ekskul_ids)) {
    $_SESSION['error'] = "Anda belum terdaftar sebagai pembimbing di ekskul manapun.";
    header("Location: tambah_galeri.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deskripsi = trim($_POST['deskripsi']);
    $ekskul_id = $_POST['ekskul_id'];

    // Validasi ekskul_id yang dikirim dari form apakah milik pembimbing
    if (!in_array($ekskul_id, $ekskul_ids)) {
        $_SESSION['error'] = "Ekskul tidak valid untuk pembimbing ini.";
        header("Location: tambah_galeri.php");
        exit();
    }

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['gambar']['tmp_name'];
        $file_name = time() . '_' . basename($_FILES['gambar']['name']);
        $target_dir = "uploads/galeri/";
        $target_file = $target_dir . $file_name;

        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (move_uploaded_file($file_tmp, $target_file)) {
            $query = "INSERT INTO galeri (ekskul_id, pembimbing_id, foto, deskripsi) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iiss", $ekskul_id, $pembimbing_id, $file_name, $deskripsi);

            if ($stmt->execute()) {
                $_SESSION['success'] = "Foto berhasil ditambahkan!";
            } else {
                $_SESSION['error'] = "Terjadi kesalahan saat menyimpan ke database.";
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = "Gagal mengunggah gambar.";
        }
    } else {
        $_SESSION['error'] = "Harap pilih gambar untuk diunggah.";
    }

    header("Location: tambah_galeri.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Foto Galeri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h3 class="mb-4">Tambah Foto ke Galeri</h3>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form action="tambah_galeri.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="ekskul" class="form-label">Pilih Ekskul</label>
                <select class="form-control" name="ekskul_id" id="ekskul" required>
                    <?php
                    require 'koneksi.php';
                    foreach ($ekskul_ids as $id) {
                        $q = $conn->prepare("SELECT nama FROM ekskul WHERE id = ?");
                        $q->bind_param("i", $id);
                        $q->execute();
                        $res = $q->get_result()->fetch_assoc();
                        echo "<option value='{$id}'>{$res['nama']}</option>";
                        $q->close();
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="gambar" class="form-label">Pilih Gambar</label>
                <input type="file" class="form-control" name="gambar" id="gambar" accept="image/*" required>
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control" name="deskripsi" id="deskripsi" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Unggah</button>
            <a href="dashboard_pembimbing.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>
</html>
