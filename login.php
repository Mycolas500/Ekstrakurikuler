<?php
session_start();
require 'koneksi.php';

$conn = new mysqli("localhost", "root", "", "project");

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Cek di tabel siswa dulu
    $stmt = $conn->prepare("SELECT id, nama, password, foto, 'siswa' AS role FROM siswa WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    // Jika tidak ditemukan di siswa, cek di tabel pembimbing
    if (!$user) {
        $stmt = $conn->prepare("SELECT id, nama, password, foto, 'pembimbing' AS role FROM pembimbing WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
    }

    // Jika pengguna ditemukan
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['foto'] = $user['foto'];
        $_SESSION['role'] = $user['role']; // Simpan peran pengguna

        // Arahkan sesuai peran
        if ($user['role'] == 'pembimbing') {
            header("Location: dashboard_pembimbing.php");
        } else {
            header("Location: homepage.php");
        }
        exit();
    } else {
        $error = "Email atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ekstrakurikuler</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow" style="width: 400px;">
            <h3 class="text-center mb-3">Login</h3>

            <?php if ($error) { ?>
                <div class="alert alert-danger text-center">
                    <?php echo $error; ?>
                </div>
            <?php } ?>

            <form action="" method="post">
                <div class="mb-3 text-center">
                    <img id="foto-profil" src="default-profile.png" class="rounded-circle" width="100" height="100" alt="Foto Profil">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" id="email" placeholder="Masukkan email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="Masukkan password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>

            <p class="text-center mt-3">Belum punya akun? <a href="register.php">Daftar</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let emailInput = document.getElementById("email");
            let fotoProfil = document.getElementById("foto-profil");

            emailInput.addEventListener("input", function () {
                let email = this.value.trim();
                if (email.length > 3) {
                    fetch("update_foto.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: "email=" + encodeURIComponent(email)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            fotoProfil.src = "uploads/" + data.foto;
                        } else {
                            fotoProfil.src = "default-profile.png";
                        }
                    })
                    .catch(error => console.error("Error:", error));
                } else {
                    fotoProfil.src = "default-profile.png";
                }
            });
        });
    </script>
</body>
</html> 