<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

// Ambil data pendaftaran ekskul
$filter_ekskul = isset($_GET['filter_ekskul']) ? $_GET['filter_ekskul'] : '';

$sql = "SELECT pendaftaran.id, siswa.nama, siswa.kelas, ekskul.nama AS ekskul_nama, ekskul.ruangan, ekskul.waktu, pendaftaran.status 
        FROM pendaftaran 
        JOIN siswa ON pendaftaran.siswa_id = siswa.id 
        JOIN ekskul ON pendaftaran.ekskul_id = ekskul.id";

if (!empty($filter_ekskul)) {
    $sql .= " WHERE ekskul.id = " . intval($filter_ekskul);
}

$result = $conn->query($sql) or die("Query Error: " . $conn->error);

// Ambil data pembimbing ekskul
$pembimbing_query = "SELECT pembimbing.id, pembimbing.nama, pembimbing.email, 
                            GROUP_CONCAT(ekskul.nama SEPARATOR ', ') AS ekskul_dibina,
                            GROUP_CONCAT(ekskul.ruangan SEPARATOR ', ') AS ruangan_eksul,
                            GROUP_CONCAT(ekskul.waktu SEPARATOR ', ') AS waktu_eksul
                     FROM pembimbing
                     LEFT JOIN pembimbing_ekskul ON pembimbing.id = pembimbing_ekskul.pembimbing_id
                     LEFT JOIN ekskul ON pembimbing_ekskul.ekskul_id = ekskul.id
                     GROUP BY pembimbing.id";
$pembimbing_result = $conn->query($pembimbing_query) or die("Query Error: " . $conn->error);


// Ambil data ekstrakurikuler dengan pembimbingnya
$ekskul_query = "SELECT ekskul.id, ekskul.nama, ekskul.deskripsi, ekskul.jadwal, ekskul.kapasitas, ekskul.ruangan, ekskul.waktu,
                        GROUP_CONCAT(pembimbing.nama SEPARATOR ', ') AS pembimbing_nama
                 FROM ekskul
                 LEFT JOIN pembimbing_ekskul ON ekskul.id = pembimbing_ekskul.ekskul_id
                 LEFT JOIN pembimbing ON pembimbing_ekskul.pembimbing_id = pembimbing.id
                 GROUP BY ekskul.id";
$ekskul_result = $conn->query($ekskul_query) or die("Query Error: " . $conn->error);

// Ambil data siswa
$siswa_query = "SELECT * FROM siswa";
$siswa_result = $conn->query($siswa_query) or die("Query Error: " . $conn->error);

// Ambil filter galeri dari GET request
$filter_galeri = isset($_GET['filter_galeri']) ? $_GET['filter_galeri'] : '';

// Query untuk mengambil data galeri dengan pembimbing dan ekskul
$sql = "SELECT galeri.*, 
            ekskul.nama AS ekskul_nama, 
            pembimbing.nama AS pembimbing_nama
        FROM galeri
        JOIN ekskul ON galeri.ekskul_id = ekskul.id
        LEFT JOIN pembimbing ON galeri.pembimbing_id = pembimbing.id";

// Menambahkan filter jika ada
if (!empty($filter_galeri)) {
    $sql .= " WHERE galeri.ekskul_id = " . intval($filter_galeri);
}

// Eksekusi query dan simpan hasilnya
$galeri_result = $conn->query($sql);

// Cek apakah query berhasil dijalankan
if (!$galeri_result) {
    die("Query gagal: " . $conn->error); // Menampilkan error jika query gagal
}

?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Admin Dashboard</a>
            <a class="btn btn-danger" href="admin_logout.php">Logout</a>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Pendaftaran Ekskul -->
        <h2>Daftar Pendaftaran Ekskul</h2>
        <a href="tambah_pendaftaran.php" class="btn btn-success mb-3">Tambah Pendaftaran</a>
        <form method="GET" class="mb-3">
    <div class="row g-2 align-items-center">
        <div class="col-auto">
            <label for="filter_ekskul" class="col-form-label">Filter Ekskul:</label>
        </div>
        <div class="col-auto">
            <select name="filter_ekskul" id="filter_ekskul" class="form-select" onchange="this.form.submit()">
                <option value="">Semua Ekskul</option>
                <?php
                // Ambil semua ekskul untuk isi dropdown
                $ekskul_dropdown_query = "SELECT id, nama FROM ekskul";
                $ekskul_dropdown_result = $conn->query($ekskul_dropdown_query);
                while ($ekskul = $ekskul_dropdown_result->fetch_assoc()) {
                    $selected = (isset($_GET['filter_ekskul']) && $_GET['filter_ekskul'] == $ekskul['id']) ? 'selected' : '';
                    echo "<option value='{$ekskul['id']}' $selected>{$ekskul['nama']}</option>";
                }
                ?>
            </select>
        </div>
    </div>
</form>

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Ekskul</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nama']); ?></td>
                        <td><?php echo htmlspecialchars($row['kelas']); ?></td>
                        <td><?php echo htmlspecialchars($row['ekskul_nama']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td>
                            <a href="update_status.php?id=<?php echo $row['id']; ?>&status=Diterima" class="btn btn-success btn-sm">Terima</a>
                            <a href="update_status.php?id=<?php echo $row['id']; ?>&status=Ditolak" class="btn btn-danger btn-sm">Tolak</a>
                            <a href="hapus_pendaftaran.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm" onclick="return confirm('Yakin ingin menghapus?');">Hapus</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <hr>

<!-- Daftar Pembimbing -->
<h2>Daftar Pembimbing</h2>
<a href="tambah_pembimbing.php" class="btn btn-success mb-3">Tambah Pembimbing</a>
<table class="table table-bordered">
    <thead class="table-dark">
        <tr>
            <th>Nama Pembimbing</th>
            <th>Email</th>
            <th>Ekskul Dibimbing</th>
            <th>Ruangan</th>
            <th>Waktu</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $pembimbing_result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['nama']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo $row['ekskul_dibina'] ? htmlspecialchars($row['ekskul_dibina']) : 'Belum ada'; ?></td>
                <td><?php echo $row['ruangan_eksul'] ? htmlspecialchars($row['ruangan_eksul']) : ''; ?></td>
                <td><?php echo $row['waktu_eksul'] ? htmlspecialchars($row['waktu_eksul']) : ''; ?></td>
                <td>
                    <a href="edit_pembimbing.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                    <a href="hapus_pembimbing.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?');">Hapus</a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

        <hr>

        <!-- Kelola Ekstrakurikuler -->
        <h2>Kelola Ekstrakurikuler</h2>
        <a href="tambah_ekskul.php" class="btn btn-success mb-3">Tambah Ekstrakurikuler</a>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Nama</th>
                    <th>Deskripsi</th>
                    <th>Jadwal</th>
                    <th>Ruangan</th>
                    <th>Waktu</th>
                    <th>Pembimbing</th>
                    <th>Kapasitas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $ekskul_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nama']); ?></td>
                        <td><?php echo htmlspecialchars($row['deskripsi']); ?></td>
                        <td><?php echo htmlspecialchars($row['jadwal']); ?></td>
                        <td><?php echo htmlspecialchars($row['ruangan']); ?></td>
                        <td><?php echo htmlspecialchars($row['waktu']); ?></td>
                        <td><?php echo $row['pembimbing_nama'] ? htmlspecialchars($row['pembimbing_nama']) : 'Belum ada'; ?></td>
                        <td><?php echo htmlspecialchars($row['kapasitas']); ?></td>
                        <td>
                            <a href="edit_ekskul.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="hapus_ekskul.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?');">Hapus</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <hr>

        <!-- Daftar Siswa -->
        <h2>Daftar Siswa</h2>
        <a href="tambah_siswa.php" class="btn btn-success mb-3">Tambah Siswa</a>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Email</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $siswa_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nama']); ?></td>
                        <td><?php echo htmlspecialchars($row['kelas']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td>
                            <a href="edit_siswa.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="hapus_siswa.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?');">Hapus</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>

            <hr>
    </table>
    <!-- Daftar Galeri -->
<h2>Daftar Galeri</h2>
<a href="tambah_galeri_admin.php" class="btn btn-primary mb-3">Tambah Foto</a>
<form method="GET" class="mb-4">
    <div class="row g-2 align-items-center">
        <div class="col-auto">
            <label for="filter_galeri" class="col-form-label">Filter Galeri Ekskul:</label>
        </div>
        <div class="col-auto">
            <select name="filter_galeri" id="filter_galeri" class="form-select" onchange="this.form.submit()">
                <option value="">Semua Ekskul</option>
                <?php
                // Ambil semua ekskul untuk filter
                $ekskul_query = "SELECT id, nama FROM ekskul";
                $ekskul_result = $conn->query($ekskul_query);
                while ($ekskul = $ekskul_result->fetch_assoc()) {
                    $selected = (isset($_GET['filter_galeri']) && $_GET['filter_galeri'] == $ekskul['id']) ? 'selected' : '';
                    echo "<option value='{$ekskul['id']}' $selected>{$ekskul['nama']}</option>";
                }
                ?>
            </select>
        </div>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Gambar</th>
                <th>Deskripsi</th>
                <th>Ekskul</th>
                <th>Pembimbing</th>
                <th>Tanggal Upload</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while ($row = $galeri_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><img src="uploads/galeri/<?php echo htmlspecialchars($row['foto']); ?>" width="100" alt="Foto"></td>
                    <td><?php echo htmlspecialchars($row['deskripsi']); ?></td>
                    <td><?php echo $row['ekskul_nama'] ? htmlspecialchars($row['ekskul_nama']) : 'Tanpa Ekskul'; ?></td>
                    <td><?php echo $row['pembimbing_nama'] ? htmlspecialchars($row['pembimbing_nama']) : 'Tidak Diketahui'; ?></td>
                    <td><?php echo date('d M Y', strtotime($row['tanggal_upload'])); ?></td>
                    <td>
                        <a href="lihat_galeri_admin.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm">Lihat</a>
                        <a href="edit_galeri_admin.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="hapus_galeri_admin.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus foto ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
