<?php
// Memanggil file koneksi
include 'koneksi.php';

// Ambil data dari tabel mahasiswa
$query  = "SELECT * FROM mahasiswa";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Mahasiswa</title>
</head>
<body>

    <h3>Data Mahasiswa:</h3>

    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
        <p>
            ID: <?= $row["id"]; ?><br>
            Nama: <?= $row["nama"]; ?><br>
            Umur: <?= $row["umur"]; ?> tahun<br>
            Alamat: <?= $row["alamat"]; ?>
        </p>
        <hr>
    <?php endwhile; ?>

    <p>
        <a href="koneksi.php">Cek Koneksi</a> | 
        <a href="tampilan.php">Refresh Tampilan</a>
    </p>

</body>
</html>