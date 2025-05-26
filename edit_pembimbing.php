<?php
session_start();
require 'koneksi.php';

$id = $_GET['id'];

// Cek apakah user sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil data pembimbing
$query = "SELECT * FROM pembimbing WHERE id=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

// Ambil daftar ekskul
$ekskulQuery = "SELECT * FROM ekskul";
$ekskulResult = $conn->query($ekskulQuery);

// Ambil ekskul yang diampu pembimbing beserta ruangan dan waktu dari tabel ekskul
$pembimbingEkskulQuery = "
    SELECT e.id AS ekskul_id, e.nama, e.ruangan, e.waktu 
    FROM pembimbing_ekskul pe
    JOIN ekskul e ON pe.ekskul_id = e.id
    WHERE pe.pembimbing_id=?";
$stmt2 = $conn->prepare($pembimbingEkskulQuery);
$stmt2->bind_param("i", $id);
$stmt2->execute();
$resultEkskul = $stmt2->get_result();
$ekskulPembimbing = [];

while ($row = $resultEkskul->fetch_assoc()) {
    $ekskulPembimbing[$row['ekskul_id']] = $row;
}

// Proses Update Data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $password = $_POST['password'];
    $ekskul_id = $_POST['ekskul'];

    // Jika password diisi, update dengan password baru yang di-hash
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $sql = "UPDATE pembimbing SET nama=?, email=?, no_hp=?, password=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $nama, $email, $no_hp, $hashedPassword, $id);
    } else {
        $sql = "UPDATE pembimbing SET nama=?, email=?, no_hp=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nama, $email, $no_hp, $id);
    }

    if ($stmt->execute()) {
        // Update ekskul pembimbing
        $deleteQuery = "DELETE FROM pembimbing_ekskul WHERE pembimbing_id=?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $insertQuery = "INSERT INTO pembimbing_ekskul (pembimbing_id, ekskul_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("ii", $id, $ekskul_id);
        $stmt->execute();

        echo "<script>alert('Data pembimbing berhasil diperbarui!'); window.location.href='admin_dashboard.php';</script>";
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pembimbing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function updateRuanganWaktu() {
            var ekskulDropdown = document.getElementById("ekskul");
            var ruanganInput = document.getElementById("ruangan");
            var waktuInput = document.getElementById("waktu");

            var ekskulData = <?php
                $ekskulArray = [];
                $ekskulResult->data_seek(0);
                while ($row = $ekskulResult->fetch_assoc()) {
                    $ekskulArray[$row['id']] = ['ruangan' => $row['ruangan'], 'waktu' => $row['waktu']];
                }
                echo json_encode($ekskulArray);
            ?>;

            var selectedEkskul = ekskulDropdown.value;
            if (selectedEkskul in ekskulData) {
                ruanganInput.value = ekskulData[selectedEkskul].ruangan;
                waktuInput.value = ekskulData[selectedEkskul].waktu;
            } else {
                ruanganInput.value = "";
                waktuInput.value = "";
            }
        }
    </script>
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Edit Pembimbing</h2>
    <form action="" method="POST">
        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="nama" value="<?php echo htmlspecialchars($data['nama']); ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($data['email']); ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">No HP</label>
            <input type="text" name="no_hp" value="<?php echo htmlspecialchars($data['no_hp']); ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password Baru (Opsional)</label>
            <input type="password" name="password" class="form-control" placeholder="Isi jika ingin mengganti password">
        </div>

        <div class="mb-3">
            <label class="form-label">Ekskul yang Diampu</label>
            <select name="ekskul" id="ekskul" class="form-control" onchange="updateRuanganWaktu()" required>
                <option value="">Pilih Ekskul</option>
                <?php
                $ekskulResult->data_seek(0);
                while ($ekskul = $ekskulResult->fetch_assoc()) {
                    $selected = isset($ekskulPembimbing[$ekskul['id']]) ? 'selected' : '';
                    echo "<option value='{$ekskul['id']}' $selected>{$ekskul['nama']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Ruangan</label>
            <input type="text" id="ruangan" class="form-control" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Waktu</label>
            <input type="text" id="waktu" class="form-control" readonly>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="admin_dashboard.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
</body>
</html>
