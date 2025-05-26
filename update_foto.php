<?php
require 'koneksi.php'; // Pastikan koneksi ke database benar

header('Content-Type: application/json'); // Set header untuk JSON response

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';

    if (!empty($email)) {
        // Cek di tabel siswa dulu
        $stmt = $conn->prepare("SELECT foto FROM siswa WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($foto);
        $stmt->fetch();
        $stmt->close();

        // Jika tidak ditemukan di siswa, cek di pembimbing
        if (!$foto) {
            $stmt = $conn->prepare("SELECT foto FROM pembimbing WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->bind_result($foto);
            $stmt->fetch();
            $stmt->close();
        }

        // Jika ditemukan foto, kirim respons JSON
        if ($foto) {
            echo json_encode(["success" => true, "foto" => $foto]);
        } else {
            echo json_encode(["success" => false, "foto" => "default-profile.png"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Email kosong"]);
    }

    $conn->close();
}
?>
