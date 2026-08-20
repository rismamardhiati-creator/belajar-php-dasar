<?php
session_start();
if (!isset($_SESSION['login'])) header("Location: login.php");
include 'koneksi.php';

$id = $_GET['id'];
$data = mysqli_query($conn, "SELECT * FROM barang WHERE id=$id");
$row = mysqli_fetch_assoc($data);

if (isset($_POST['update'])) {
    $nama = $_POST['nama_barang'];
    $kategori = $_POST['kategori'];
    $stok = $_POST['stok'];
    $harga = $_POST['harga'];

    mysqli_query($conn, "UPDATE barang SET nama_barang='$nama', kategori='$kategori', stok='$stok', harga='$harga' WHERE id=$id");
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Barang</title>
    <!-- Menghubungkan ke file CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Edit Barang</h2>
        <form method="POST">
            <label>Nama Barang:</label>
            <input type="text" name="nama_barang" value="<?= $row['nama_barang'] ?>" required>

            <label>Kategori:</label>
            <input type="text" name="kategori" value="<?= $row['kategori'] ?>" required>

            <label>Stok:</label>
            <input type="number" name="stok" value="<?= $row['stok'] ?>" required>

            <label>Harga:</label>
            <input type="number" name="harga" value="<?= $row['harga'] ?>" required>

            <button type="submit" name="update">Update</button>
            <a href="index.php" class="btn" style="background-color: #dc3545;">Batal</a>
        </form>
    </div>
</body>
</html>