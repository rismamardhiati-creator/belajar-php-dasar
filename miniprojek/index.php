<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

// Fitur Search
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $query = "SELECT * FROM barang WHERE nama_barang LIKE '%$search%' OR kategori LIKE '%$search%'";
} else {
    $query = "SELECT * FROM barang";
}
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Stok Barang</title>
    <!-- Menghubungkan ke file CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Stok Barang (Halo, <?= $_SESSION['username']; ?>)</h2>
        <a href="logout.php" class="btn" style="background-color: #dc3545;">Logout</a>
        <hr>

        <a href="tambah.php" class="btn">+ Tambah Barang</a>
        <br><br>

        <!-- Fitur Search -->
        <form method="GET">
            <input type="text" name="search" placeholder="Cari barang..." value="<?= $search ?>">
            <button type="submit">Cari</button>
        </form>
        <br>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $row['nama_barang']; ?></td>
                    <td><?= $row['kategori']; ?></td>
                    <td><?= $row['stok']; ?></td>
                    <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                    <td>
                        <a href="edit.php?id=<?= $row['id']; ?>" class="btn" style="padding: 4px 8px; font-size: 12px; background-color: #ffc107; color: black;">Edit</a>
                        <a href="hapus.php?id=<?= $row['id']; ?>" class="btn" style="padding: 4px 8px; font-size: 12px; background-color: #dc3545;" onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>