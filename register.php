<?php
session_start();
require 'koneksi.php';
$conn = new mysqli("localhost", "root", "", "project");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debugging: Lihat apa yang diterima dari form
    var_dump($_POST); // Cek data yang dikirim via form POST
    var_dump($_FILES); // Cek data file yang diupload

    $nama = isset($_POST['nama']) ? trim($_POST['nama']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $kelas_no_hp = isset($_POST['kelas_nohp']) ? trim($_POST['kelas_nohp']) : ''; // Pastikan nama input sesuai
    $foto = $_FILES['foto'];

    // Set default ekstrakurikuler ID jika tidak dipilih
    $ekstrakurikuler_default = 1; // Misalnya ID pertama pada tabel ekstrakurikuler

    if (empty($nama) || empty($email) || empty($password) || empty($kelas_no_hp)) {
        $error = "Semua kolom harus diisi!";
    } else {
        // Cek apakah email sudah terdaftar di siswa atau pembimbing
        $cek_email_siswa = $conn->prepare("SELECT email FROM siswa WHERE email = ?");
        $cek_email_pembimbing = $conn->prepare("SELECT email FROM pembimbing WHERE email = ?");

        $cek_email_siswa->bind_param("s", $email);
        $cek_email_siswa->execute();
        $cek_email_siswa->store_result();

        $cek_email_pembimbing->bind_param("s", $email);
        $cek_email_pembimbing->execute();
        $cek_email_pembimbing->store_result();

        if ($cek_email_siswa->num_rows > 0 || $cek_email_pembimbing->num_rows > 0) {
            $error = "Email sudah terdaftar!";
        } else {
            // Validasi upload foto
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($foto["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed_types = ["jpg", "png", "jpeg", "gif"];
            $upload_ok = 1;

            // Cek apakah file gambar sudah ada
            if (file_exists($target_file)) {
                $error = "Maaf, file sudah ada.";
                $upload_ok = 0;
            }

            // Cek ukuran file
            if ($foto["size"] > 500000) {
                $error = "Maaf, file terlalu besar.";
                $upload_ok = 0;
            }

            // Cek jenis file gambar
            if (!in_array($imageFileType, $allowed_types)) {
                $error = "Maaf, hanya gambar JPG, JPEG, PNG, dan GIF yang diperbolehkan.";
                $upload_ok = 0;
            }

            // Jika semua kondisi valid
            if ($upload_ok == 0) {
                $error = "File tidak dapat diupload.";
            } else {
                // Coba upload file
                if (move_uploaded_file($foto["tmp_name"], $target_file)) {
                    // Hash password
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);

                    if ($password === "Miku123") {
                        // Jika pembimbing, simpan ke tabel pembimbing
                        $stmt = $conn->prepare("INSERT INTO pembimbing (nama, email, no_hp, foto) VALUES (?, ?, ?, ?)");
                        $stmt->bind_param("ssss", $nama, $email, $kelas_no_hp, $target_file);
                    } else {
                        // Jika siswa, simpan ke tabel siswa
                        // Pastikan ekstrakurikuler diisi dengan nilai default atau ID yang valid
                        $stmt = $conn->prepare("INSERT INTO siswa (nama, kelas, email, password, foto, ekstrakurikuler) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param("sssssi", $nama, $kelas_no_hp, $email, $password_hash, $target_file, $ekstrakurikuler_default); // Menggunakan ID ekstrakurikuler default
                    }

                    if ($stmt->execute()) {
                        // Set pesan sukses
                        $_SESSION['registrasi_berhasil'] = "Registrasi berhasil! Silakan login.";
                        header("Location: login.php");
                        exit();
                    } else {
                        $error = "Gagal mendaftar: " . $conn->error;
                    }
                } else {
                    $error = "Terjadi kesalahan saat mengupload file.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function cekPassword() {
            let password = document.getElementById("password").value;
            let label = document.getElementById("kelasLabel");
            let input = document.getElementById("kelas_nohp");

            if (password === "Miku123") {
                label.innerText = "Nomor HP";
                input.placeholder = "Masukkan Nomor HP";
                input.type = "tel";
            } else {
                label.innerText = "Kelas";
                input.placeholder = "Masukkan Kelas";
                input.type = "text";
            }
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>Registrasi</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)) { ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php } ?>
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Nama:</label>
                                <input type="text" name="nama" class="form-control" value="<?php echo isset($nama) ? $nama : ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" id="kelasLabel">Kelas:</label>
                                <input type="text" name="kelas_nohp" id="kelas_nohp" class="form-control" value="<?php echo isset($kelas_no_hp) ? $kelas_no_hp : ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email:</label>
                                <input type="email" name="email" class="form-control" value="<?php echo isset($email) ? $email : ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password:</label>
                                <input type="password" name="password" id="password" class="form-control" required oninput="cekPassword()">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Foto Profil:</label>
                                <input type="file" name="foto" class="form-control" accept="image/*" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Daftar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
