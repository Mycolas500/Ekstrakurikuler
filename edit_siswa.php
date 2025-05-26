<?php
require 'koneksi.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Ambil data siswa berdasarkan ID
    $sql = "SELECT * FROM siswa WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $siswa = $result->fetch_assoc();
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
        exit();
    }

    // Ambil semua data ekstrakurikuler
    $sql_ekstrakurikuler = "SELECT * FROM ekskul";
    $result_ekstrakurikuler = $conn->query($sql_ekstrakurikuler);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Ambil data dari form
        $nama = $_POST['nama'];
        $kelas = $_POST['kelas'];
        $email = $_POST['email'];
        $ekstrakurikuler = $_POST['ekstrakurikuler'];

        if ($_FILES['foto']['name']) {
            // Jika foto baru diupload
            $foto = $_FILES['foto']['name'];
            $target = 'uploads/' . basename($foto);
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
                // Update foto dan data siswa
                $sql_update = "UPDATE siswa SET nama = ?, kelas = ?, email = ?, foto = ?, ekstrakurikuler = ? WHERE id = ?";
                if ($stmt_update = $conn->prepare($sql_update)) {
                    $stmt_update->bind_param("sssssi", $nama, $kelas, $email, $foto, $ekstrakurikuler, $id);
                    $stmt_update->execute();
                    $stmt_update->close();
                    header("Location: admin_dashboard.php"); // Redirect setelah berhasil
                    exit();
                }
            } else {
                echo "Error uploading file.";
            }
        } else {
            // Jika foto tidak diubah
            $sql_update = "UPDATE siswa SET nama = ?, kelas = ?, email = ?, ekstrakurikuler = ? WHERE id = ?";
            if ($stmt_update = $conn->prepare($sql_update)) {
                $stmt_update->bind_param("ssssi", $nama, $kelas, $email, $ekstrakurikuler, $id);
                $stmt_update->execute();
                $stmt_update->close();
                header("Location: admin_dashboard.php");
                exit();
            }
        }
    }
} else {
    echo "Invalid ID.";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Edit Siswa</a>
            <a class="btn btn-danger" href="admin_logout.php">Logout</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Edit Data Siswa</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" value="<?php echo $siswa['nama']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="kelas" class="form-label">Kelas</label>
                <input type="text" class="form-control" id="kelas" name="kelas" value="<?php echo $siswa['kelas']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $siswa['email']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="ekstrakurikuler" class="form-label">Ekstrakurikuler</label>
                <select class="form-control" id="ekstrakurikuler" name="ekstrakurikuler" required>
                    <?php while ($ekskul = $result_ekstrakurikuler->fetch_assoc()) { ?>
                        <option value="<?php echo $ekskul['id']; ?>" <?php echo ($siswa['ekstrakurikuler'] == $ekskul['id']) ? 'selected' : ''; ?>>
                            <?php echo $ekskul['nama']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="foto" class="form-label">Foto (Opsional)</label>
                <input type="file" class="form-control" id="foto" name="foto">
                <img src="uploads/<?php echo $siswa['foto']; ?>" width="100" class="mt-2">
            </div>
            <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
            <a href="admin_dashboard.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</body>
</html>
