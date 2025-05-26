<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Ambil data ekskul berdasarkan ID
$id = intval($_GET['id']);
$ekskul_query = "SELECT * FROM ekskul WHERE id = $id";
$ekskul_result = $conn->query($ekskul_query);
$ekskul = $ekskul_result->fetch_assoc();

if (!$ekskul) {
    echo "<script>alert('Ekskul tidak ditemukan!'); window.location.href='admin_dashboard.php';</script>";
    exit();
}

// Ambil daftar pembimbing
$pembimbing_query = "SELECT * FROM pembimbing";
$pembimbing_result = $conn->query($pembimbing_query);

// Ambil pembimbing yang sudah dipilih
$selected_pembimbing = [];
$pembimbing_ekskul_query = "SELECT pembimbing_id FROM pembimbing_ekskul WHERE ekskul_id = $id";
$pembimbing_ekskul_result = $conn->query($pembimbing_ekskul_query);
while ($row = $pembimbing_ekskul_result->fetch_assoc()) {
    $selected_pembimbing[] = $row['pembimbing_id'];
}

// Proses form jika disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $conn->real_escape_string($_POST['nama']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $jadwal = $conn->real_escape_string($_POST['jadwal']);
    $kapasitas = intval($_POST['kapasitas']);
    $ruangan = $conn->real_escape_string($_POST['ruangan']);
    $waktu = $conn->real_escape_string($_POST['waktu']);
    $pembimbing_ids = $_POST['pembimbing_id'] ?? [];

    // Pastikan folder uploads ada
    if (!file_exists('uploads')) {
        mkdir('uploads', 0777, true);
    }

    // Proses upload logo jika ada file baru
    $logo_query = "";
    if (!empty($_FILES['logo']['name'])) {
        $target_dir = "uploads/";
        $logo_name = basename($_FILES['logo']['name']);
        $target_file = $target_dir . $logo_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validasi file gambar
        $check = getimagesize($_FILES["logo"]["tmp_name"]);
        if ($check === false) {
            echo "<script>alert('File bukan gambar.');</script>";
        } elseif ($_FILES["logo"]["size"] > 5000000) { // Batas ukuran 5MB
            echo "<script>alert('Ukuran file terlalu besar. Maksimal 5MB.');</script>";
        } elseif (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            echo "<script>alert('Hanya file JPG, JPEG, PNG, & GIF yang diperbolehkan.');</script>";
        } else {
            if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
                $logo_query = ", logo = '$target_file'";
            } else {
                echo "<script>alert('Gagal mengupload gambar.');</script>";
            }
        }
    }

    // Update data ekskul di database
    $sql = "UPDATE ekskul SET 
            nama = '$nama', deskripsi = '$deskripsi', jadwal = '$jadwal', kapasitas = '$kapasitas',
            ruangan = '$ruangan', waktu = '$waktu' 
            $logo_query WHERE id = $id";

    if ($conn->query($sql)) {
        // Hapus data pembimbing lama
        $conn->query("DELETE FROM pembimbing_ekskul WHERE ekskul_id = $id");

        // Tambahkan pembimbing baru jika ada
        if (!empty($pembimbing_ids)) {
            foreach ($pembimbing_ids as $pembimbing_id) {
                $query = "INSERT INTO pembimbing_ekskul (pembimbing_id, ekskul_id) VALUES ('$pembimbing_id', '$id')";
                $conn->query($query);
            }
        }

        echo "<script>window.location.href='admin_dashboard.php';</script>";
        exit();
    } else {
        echo "<script>alert('Terjadi kesalahan: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Ekskul</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Edit Ekskul</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label>Nama Ekskul:</label>
                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($ekskul['nama']) ?>" required>
            </div>
            <div class="mb-3">
                <label>Deskripsi:</label>
                <textarea name="deskripsi" class="form-control" required><?= htmlspecialchars($ekskul['deskripsi']) ?></textarea>
            </div>
            <div class="mb-3">
                <label>Jadwal:</label>
                <input type="text" name="jadwal" class="form-control" value="<?= htmlspecialchars($ekskul['jadwal']) ?>" required>
            </div>
            <div class="mb-3">
                <label>Kapasitas:</label>
                <input type="number" name="kapasitas" class="form-control" value="<?= htmlspecialchars($ekskul['kapasitas']) ?>" required>
            </div>
            <div class="mb-3">
                <label>Ruangan:</label>
                <input type="text" name="ruangan" class="form-control" value="<?= htmlspecialchars($ekskul['ruangan']) ?>" required>
            </div>
            <div class="mb-3">
                <label>Waktu:</label>
                <input type="text" name="waktu" class="form-control" value="<?= htmlspecialchars($ekskul['waktu']) ?>" required>
            </div>
            <div class="mb-3">
                <label>Logo Ekskul:</label>
                <input type="file" name="logo" class="form-control">
                <?php if (!empty($ekskul['logo'])): ?>
                    <p>Logo saat ini: <br><img src="<?= htmlspecialchars($ekskul['logo']) ?>" width="100"></p>
                <?php else: ?>
                    <p>Belum ada logo.</p>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label>Pembimbing:</label>
                <select name="pembimbing_id[]" class="form-control" multiple required>
                    <?php while ($pembimbing = $pembimbing_result->fetch_assoc()) { ?>
                        <option value="<?= $pembimbing['id'] ?>" 
                            <?= in_array($pembimbing['id'], $selected_pembimbing) ? 'selected' : ''; ?> >
                            <?= htmlspecialchars($pembimbing['nama']) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
            <a href="admin_dashboard.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</body>
</html>
