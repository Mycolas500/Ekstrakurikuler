<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pembimbing') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['ekskul_id'])) {
    header("Location: dashboard_pembimbing.php");
    exit();
}

$ekskul_id = $_GET['ekskul_id'];
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jadwal = $_POST['jadwal'];
    $waktu = $_POST['waktu'];
    $ruangan = $_POST['ruangan'];

    $query = "UPDATE ekskul SET jadwal = ?, waktu = ?, ruangan = ? WHERE id = ? AND pembimbing_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssii", $jadwal, $waktu, $ruangan, $ekskul_id, $user_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    header("Location: dashboard_pembimbing.php");
    exit();
}

$query = "SELECT nama, jadwal, waktu, ruangan FROM ekskul WHERE id = ? AND pembimbing_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $ekskul_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$ekskul = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Jadwal Ekskul</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <h3>Edit Jadwal: <?php echo htmlspecialchars($ekskul['nama']); ?></h3>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Hari Ekskul</label>
                <input type="text" class="form-control" name="jadwal" value="<?php echo htmlspecialchars($ekskul['jadwal']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Waktu</label>
                <input type="text" class="form-control" name="waktu" value="<?php echo htmlspecialchars($ekskul['waktu']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Ruangan</label>
                <input type="text" class="form-control" name="ruangan" value="<?php echo htmlspecialchars($ekskul['ruangan']); ?>">
            </div>
            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="dashboard_pembimbing.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>
</html>
