<?php
session_start();
require 'koneksi.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_id = $_SESSION['admin_id'];
    $deskripsi = trim($_POST['deskripsi']);
    $ekskul_id = $_POST['ekskul_id'];

    // Validasi ekskul_id
    $query_check_ekskul = "SELECT id FROM ekskul WHERE id = ?";
    $stmt_check_ekskul = $conn->prepare($query_check_ekskul);
    $stmt_check_ekskul->bind_param("i", $ekskul_id);
    $stmt_check_ekskul->execute();
    $stmt_check_ekskul->store_result();
    if ($stmt_check_ekskul->num_rows === 0) {
        $_SESSION['error'] = "Ekskul tidak ditemukan!";
        header("Location: tambah_galeri.php");
        exit();
    }

    // Cek dan proses gambar
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['gambar']['tmp_name'];
        $file_name = time() . '_' . basename($_FILES['gambar']['name']);
        $target_dir = "uploads/galeri/";
        $target_file = $target_dir . $file_name;

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['gambar']['type'];
        $file_size = $_FILES['gambar']['size'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($file_type, $allowed_types)) {
            $_SESSION['error'] = "Hanya file gambar yang diperbolehkan!";
            header("Location: tambah_galeri.php");
            exit();
        }

        if ($file_size > $max_size) {
            $_SESSION['error'] = "Ukuran file terlalu besar, maksimal 5MB!";
            header("Location: tambah_galeri.php");
            exit();
        }

        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (move_uploaded_file($file_tmp, $target_file)) {
            // Simpan ke tabel galeri, pembimbing_id diset NULL karena ini admin
            $query = "INSERT INTO galeri (pembimbing_id, foto, deskripsi, ekskul_id) VALUES (NULL, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssi", $file_name, $deskripsi, $ekskul_id);
            if ($stmt->execute()) {
                $_SESSION['success'] = "Foto berhasil ditambahkan!";
                header("Location: galeri.php");
                exit();
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

// Ambil data ekskul untuk dropdown
$query_ekskul = "SELECT id, nama FROM ekskul";
$result_ekskul = $conn->query($query_ekskul);
$ekskul_options = [];
while ($row = $result_ekskul->fetch_assoc()) {
    $ekskul_options[] = $row;
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
                <label for="gambar" class="form-label">Pilih Gambar</label>
                <input type="file" class="form-control" name="gambar" id="gambar" accept="image/*" required>
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control" name="deskripsi" id="deskripsi" rows="4" required></textarea>
            </div>
            <div class="mb-3">
                <label for="ekskul_id" class="form-label">Pilih Ekskul</label>
                <select class="form-select" name="ekskul_id" id="ekskul_id" required>
                    <?php foreach ($ekskul_options as $ekskul): ?>
                        <option value="<?php echo $ekskul['id']; ?>"><?php echo $ekskul['nama']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Tambah Galeri</button>
        </form>
    </div>
</body>
</html>
