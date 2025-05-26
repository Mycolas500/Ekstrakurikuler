<?php
require 'koneksi.php';

// Ambil data dari database
$query = "SELECT * FROM galeri";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Ekskul</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
            text-align: center;
            padding: 8px;
        }
        img {
            max-width: 100px;
            height: auto;
        }
    </style>
</head>
<body>
    <h2>Galeri Ekskul</h2>
    <a href="upload_galeri.php">Tambah Foto</a>
    <table>
        <tr>
            <th>ID</th>
            <th>ID Ekskul</th>
            <th>Foto</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['ekskul_id'] ?></td>
            <td><img src="galeri/<?= htmlspecialchars($row['foto']) ?>" alt="Foto Ekskul"></td>
            <td>
                <a href="hapus_galeri.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus foto ini?')">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
