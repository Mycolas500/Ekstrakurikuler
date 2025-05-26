<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pembimbing') {
    header("Location: dashboard_pembimbing.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$nama = $_SESSION['nama'];
$foto = $_SESSION['foto'];

// Mengambil data siswa yang dibimbing
$query = "
    SELECT s.id, s.nama, s.kelas, 
       e.nama AS ekskul, 
       e.jadwal, 
       e.ruangan, 
       e.waktu, 
       e.id AS ekskul_id
FROM siswa s
JOIN pendaftaran p ON s.id = p.siswa_id AND p.status = 'Diterima'
JOIN ekskul e ON p.ekskul_id = e.id
JOIN pembimbing_ekskul pe ON e.id = pe.ekskul_id
WHERE pe.pembimbing_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$siswa_result = $stmt->get_result();
$siswa = $siswa_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Mengambil data galeri
$galeri_query = "SELECT * FROM galeri WHERE pembimbing_id = ?";
$stmt = $conn->prepare($galeri_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$galeri_result = $stmt->get_result();
$galeri = $galeri_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pembimbing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function confirmHapus(event, nama) {
            if (!confirm("Apakah Anda yakin ingin menghapus siswa '" + nama + "' dari ekskul?")) {
                event.preventDefault();
            }
        }

        function editJadwal(siswaId, ekskulId) {
            var newJadwal = prompt("Masukkan jadwal baru:");
            var newRuangan = prompt("Masukkan ruangan baru:");
            var newWaktu = prompt("Masukkan waktu baru:");

            if (newJadwal !== null && newRuangan !== null && newWaktu !== null) {
                $.post("update_jadwal.php", 
                    { siswa_id: siswaId, ekskul_id: ekskulId, jadwal: newJadwal, ruangan: newRuangan, waktu: newWaktu }, 
                    function(response) {
                        alert(response);
                        location.reload();
                    }
                );
            }
        }
    </script>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="dashboard_pembimbing.php">Dashboard Pembimbing</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a href="profil_pembimbing.php" class="nav-link text-white d-flex align-items-center">
                            <img src="uploads/<?php echo htmlspecialchars($foto); ?>" class="rounded-circle me-2" width="40" height="40">
                            <?php echo htmlspecialchars($nama); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="logout.php" class="btn btn-danger ms-3">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h3>Daftar Siswa yang Dibimbing</h3>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Ekskul</th>
                        <th>Jadwal</th>
                        <th>Ruangan</th>
                        <th>Waktu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($siswa)): ?>
                        <?php foreach ($siswa as $index => $s): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($s['nama']); ?></td>
                                <td><?php echo htmlspecialchars($s['kelas']); ?></td>
                                <td><?php echo htmlspecialchars($s['ekskul']); ?></td>
                                <td><?php echo htmlspecialchars($s['jadwal']); ?></td>
                                <td><?php echo htmlspecialchars($s['ruangan']); ?></td>
                                <td><?php echo htmlspecialchars($s['waktu']); ?></td>
                                <td>
                                    <a href="profil_siswa.php?siswa_id=<?php echo $s['id']; ?>" class="btn btn-sm btn-info">Lihat</a>
                                    <button class="btn btn-sm btn-primary" onclick="editJadwal(<?php echo $s['id']; ?>, <?php echo $s['ekskul_id']; ?>)">Edit</button>
                                    <a href="edit_ekskul_pembimbing.php?siswa_id=<?php echo $s['id']; ?>" class="btn btn-sm btn-warning">Pindah</a>
                                    <a href="hapus_siswa_pembimbing.php?siswa_id=<?php echo $s['id']; ?>" class="btn btn-sm btn-danger" onclick="confirmHapus(event, '<?php echo htmlspecialchars($s['nama']); ?>')">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">Tidak ada siswa yang terdaftar.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <h3>Galeri</h3>
        <a href="tambah_galeri.php" class="btn btn-primary mb-3">Tambah Foto</a>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Gambar</th>
                        <th>Deskripsi</th>
                        <th>Tanggal Upload</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require 'koneksi.php';
                    $pembimbing_id = $_SESSION['user_id'];
                    $query = "SELECT * FROM galeri WHERE pembimbing_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $pembimbing_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $no = 1;

                    while ($row = $result->fetch_assoc()):
                    ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><img src="uploads/galeri/<?php echo $row['foto']; ?>" width="100" alt="Foto"></td>
                            <td><?php echo $row['deskripsi']; ?></td>
                            <td><?php echo date('d M Y', strtotime($row['tanggal_upload'])); ?></td>
                            <td>
                                <a href="lihat_galeri.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm">Lihat</a>
                                <a href="edit_galeri.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="hapus_galeri.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus foto ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
