<?php
require 'koneksi.php';

if (isset($_GET['siswa_id'])) {
    $siswa_id = $_GET['siswa_id'];

    $query = "SELECT id, nama FROM ekskul";
    $result = $conn->query($query);
    $ekskul = $result->fetch_all(MYSQLI_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $ekskul_id = $_POST['ekskul_id'];

        $query = "UPDATE siswa SET ekstrakurikuler = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $ekskul_id, $siswa_id);
        $stmt->execute();

        header("Location: dashboard_pembimbing.php");
        exit();
    }
} else {
    header("Location: dashboard_pembimbing.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Ekskul</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <h3>Edit Ekskul Siswa</h3>
        <form method="POST">
            <label for="ekskul_id">Pilih Ekskul:</label>
            <select name="ekskul_id" id="ekskul_id" class="form-select">
                <?php foreach ($ekskul as $e): ?>
                    <option value="<?php echo $e['id']; ?>"><?php echo htmlspecialchars($e['nama']); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary mt-3">Simpan</button>
        </form>
    </div>
</body>
</html>
