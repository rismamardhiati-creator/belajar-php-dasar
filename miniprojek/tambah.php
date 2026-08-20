<?php
session_start();
if (!isset($_SESSION['login'])) header("Location: login.php");
include 'koneksi.php';

if (isset($_POST['submit'])) {
    $nama = $_POST['nama_barang'];
    $kategori = $_POST['kategori'];
    $stok = $_POST['stok'];
    $harga = $_POST['harga'];

    mysqli_query($conn, "INSERT INTO barang VALUES('', '$nama', '$kategori', '$stok', '$harga')");
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Barang</title>
    <!-- Menghubungkan ke file CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Tambah Barang</h2>
        <form method="POST">
            <label>Nama Barang:</label>
            <input type="text" name="nama_barang" required>

            <label>Kategori:</label>
            <input type="text" name="kategori" required>

            <label>Stok:</label>
            <input type="number" name="stok" required>

            <label>Harga:</label>
            <input type="number" name="harga" required>

            <button type="submit" name="submit">Simpan</button>
            <a href="index.php" class="btn" style="background-color: #dc3545;">Batal</a>
        </form>
    </div>
</body>
</html>