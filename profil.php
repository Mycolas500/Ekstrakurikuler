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

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    $user = null; // Jika user tidak ditemukan
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 400px;">
        <div class="card-body text-center">
            <?php if ($user): ?>
                <img src="<?php echo !empty($user['foto']) ? 'uploads/' . $user['foto'] : 'uploads/default.png'; ?>" 
                     class="rounded-circle" width="120" height="120" alt="Foto Profil">
                <h3 class="mt-3"><?php echo htmlspecialchars($user['nama']); ?></h3>
                <p class="text-muted"><?php echo htmlspecialchars($user['kelas']); ?></p>
                <a href="edit_profil.php" class="btn btn-primary w-100">Edit Profil</a>
            <?php else: ?>
                <p class="text-danger">Data tidak ditemukan. Silakan login ulang.</p>
                <a href="login.php" class="btn btn-secondary w-100">Login</a>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
