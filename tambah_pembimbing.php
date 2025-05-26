<?php
require 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form dan pastikan aman
    $nama = htmlspecialchars($_POST['nama']);
    $email = htmlspecialchars($_POST['email']);
    $no_hp = htmlspecialchars($_POST['no_hp']);

    // Validasi file upload
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $foto = $_FILES['foto']['name'];
    $file_extension = strtolower(pathinfo($foto, PATHINFO_EXTENSION));
    $target = "uploads/" . basename($foto);

    // Cek apakah file memiliki ekstensi yang diizinkan
    if (!in_array($file_extension, $allowed_extensions)) {
        echo "Ekstensi file tidak diizinkan. Hanya file gambar yang diperbolehkan.";
        exit();
    }

    // Cek apakah file sudah ada
    if (file_exists($target)) {
        echo "File sudah ada, ganti nama file dan coba lagi.";
        exit();
    }

    // Upload file
    if (!move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
        echo "Gagal meng-upload file.";
        exit();
    }

    // Masukkan data pembimbing ke tabel pembimbing
    $stmt = $conn->prepare("INSERT INTO pembimbing (nama, email, no_hp, foto) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama, $email, $no_hp, $foto);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Ambil ID pembimbing yang baru saja dimasukkan
        $pembimbing_id = $stmt->insert_id;

        // Menambahkan relasi pembimbing dengan ekskul
        $sql_relasi = "INSERT INTO pembimbing_ekskul (pembimbing_id, ekskul_id) VALUES (?, ?)";
        $stmt_relasi = $conn->prepare($sql_relasi);
        $ekskul_id = 6;  // Misalnya, ID ekskul untuk futsal adalah 6 (ganti sesuai dengan ID ekskul yang sesuai)
        $stmt_relasi->bind_param("ii", $pembimbing_id, $ekskul_id);
        $stmt_relasi->execute();

        if ($stmt_relasi->affected_rows > 0) {
            header("Location: admin_dashboard.php"); // Arahkan ke halaman dashboard setelah sukses
            exit(); // Pastikan eksekusi berhenti setelah redirect
        } else {
            echo "Error adding relation: " . $conn->error; // Jika relasi gagal
        }
    } else {
        echo "Error inserting pembimbing: " . $conn->error; // Jika pembimbing gagal dimasukkan
    }

    // Tutup prepared statement
    $stmt->close();
    $stmt_relasi->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pembimbing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2>Tambah Pembimbing Ekstrakurikuler</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" name="nama" class="form-control" id="nama" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" class="form-control" id="email" required>
            </div>
            <div class="mb-3">
                <label for="no_hp" class="form-label">No HP</label>
                <input type="text" name="no_hp" class="form-control" id="no_hp" required>
            </div>
            <div class="mb-3">
                <label for="foto" class="form-label">Foto</label>
                <input type="file" name="foto" class="form-control" id="foto" required>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
