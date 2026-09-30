<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}

require_once "config/Database.php";
require_once "classes/Order.php";

$database = new Database();
$db = $database->getConnection();
$orderObj = new Order($db);

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];
$nama = $_SESSION['nama'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_order'])) {
        $nama_pelanggan = !empty($_POST['nama_pelanggan']) ? $_POST['nama_pelanggan'] : $nama;
        $jenis_pilihan = $_POST['jenis_barang'];
        if ($jenis_pilihan === 'Lainnya') {
            $jenis_barang = !empty($_POST['jenis_barang_custom']) ? $_POST['jenis_barang_custom'] : 'Lainnya';
        } else {
            $jenis_barang = $jenis_pilihan;
        }

        $layanan = $_POST['layanan'];
        $berat_kg = !empty($_POST['berat_kg']) ? (float)$_POST['berat_kg'] : 0;
        $panjang_m = !empty($_POST['panjang_m']) ? (float)$_POST['panjang_m'] : null;
        $lebar_m = !empty($_POST['lebar_m']) ? (float)$_POST['lebar_m'] : null;
        $metode_pembayaran = $_POST['metode_pembayaran'];
        $jumlah_bayar = !empty($_POST['jumlah_bayar']) ? (float)$_POST['jumlah_bayar'] : 0;
        $alamat = $_POST['alamat_pickup'];
        $catatan = $_POST['catatan'];

        $biaya_tambahan = 0;
        if ($layanan === 'Cuci Komplit (Cuci + Kering + Setrika)') {
            $biaya_tambahan = 3000;
        } elseif ($layanan === 'Cuci Kering Saja') {
            $biaya_tambahan = 1000;
        } elseif ($layanan === 'Setrika Saja') {
            $biaya_tambahan = 0;
        }

        if ($jenis_pilihan === 'Karpet') {
            $tarif_karpet = 12000 + $biaya_tambahan;
            $total_harga = ($panjang_m * $lebar_m) * $tarif_karpet;
        } else {
            $tarif_kg = 7000 + $biaya_tambahan;
            $total_harga = $berat_kg * $tarif_kg;
        }

        if ($jumlah_bayar <= 0) {
            $status_pembayaran = 'Belum Bayar';
        } elseif ($jumlah_bayar < $total_harga) {
            $status_pembayaran = 'DP (Sebagian)';
        } else {
            $status_pembayaran = 'Lunas';
        }

        $kode_resi = "RL-" . date("Ymd") . rand(100, 999);
        
        $query = "INSERT INTO orders (kode_resi, pelanggan_id, nama_pelanggan, layanan, jenis_barang, berat_kg, panjang_m, lebar_m, total_harga, jumlah_bayar, status_pembayaran, metode_pembayaran, alamat_pickup, catatan, status_pakaian) 
                  VALUES (:kode_resi, :pelanggan_id, :nama_pelanggan, :layanan, :jenis_barang, :berat_kg, :panjang_m, :lebar_m, :total_harga, :jumlah_bayar, :status_pembayaran, :metode_pembayaran, :alamat_pickup, :catatan, 'Menunggu Penjemputan')";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':kode_resi', $kode_resi);
        $stmt->bindParam(':pelanggan_id', $user_id);
        $stmt->bindParam(':nama_pelanggan', $nama_pelanggan);
        $stmt->bindParam(':layanan', $layanan);
        $stmt->bindParam(':jenis_barang', $jenis_barang);
        $stmt->bindParam(':berat_kg', $berat_kg);
        $stmt->bindParam(':panjang_m', $panjang_m);
        $stmt->bindParam(':lebar_m', $lebar_m);
        $stmt->bindParam(':total_harga', $total_harga);
        $stmt->bindParam(':jumlah_bayar', $jumlah_bayar);
        $stmt->bindParam(':status_pembayaran', $status_pembayaran);
        $stmt->bindParam(':metode_pembayaran', $metode_pembayaran);
        $stmt->bindParam(':alamat_pickup', $alamat);
        $stmt->bindParam(':catatan', $catatan);
        $stmt->execute();

        header("Location: dashboard.php");
        exit();
    }

    if (isset($_POST['delete_order'])) {
        $order_id = $_POST['order_id'];
        $orderObj->deleteOrder($order_id, $user_id);
        header("Location: dashboard.php");
        exit();
    }

    if (isset($_POST['update_driver_status'])) {
        $order_id = $_POST['order_id'];
        $status = $_POST['status'];
        $orderObj->updateStatus($order_id, $status, $user_id);
        header("Location: dashboard.php");
        exit();
    }

    if (isset($_POST['update_admin_status'])) {
        $order_id = $_POST['order_id'];
        $berat = $_POST['berat_kg'];
        $total = $_POST['total_harga'];
        $jumlah_bayar = $_POST['jumlah_bayar'];
        $status_pakaian = $_POST['status'];

        if ($jumlah_bayar <= 0) {
            $status_pembayaran = 'Belum Bayar';
        } elseif ($jumlah_bayar < $total) {
            $status_pembayaran = 'DP (Sebagian)';
        } else {
            $status_pembayaran = 'Lunas';
        }

        $queryUpdate = "UPDATE orders SET berat_kg = :berat, total_harga = :total, jumlah_bayar = :jb, status_pembayaran = :sp, status_pakaian = :stat WHERE id = :id";
        $stmtUpdate = $db->prepare($queryUpdate);
        $stmtUpdate->bindParam(':berat', $berat);
        $stmtUpdate->bindParam(':total', $total);
        $stmtUpdate->bindParam(':jb', $jumlah_bayar);
        $stmtUpdate->bindParam(':sp', $status_pembayaran);
        $stmtUpdate->bindParam(':stat', $status_pakaian);
        $stmtUpdate->bindParam(':id', $order_id);
        $stmtUpdate->execute();

        header("Location: dashboard.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard <?= ucfirst($role); ?> - Rizky Laundry</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #fffdf5; color: #43302b; }
        .bg-custom-nav { background-color: #e6b800; }
        .btn-custom { background-color: #e6b800; color: #ffffff; font-weight: bold; }
        .btn-custom:hover { background-color: #cc9900; }
        .input-custom:focus { border-color: #e6b800; outline: none; ring: 2px solid #e6b800; }
    </style>
</head>
<body class="min-h-screen">
    <nav class="bg-custom-nav text-white p-4 flex justify-between items-center shadow">
        <h1 class="font-bold text-xl text-amber-950"><i class="fa-solid fa-shirt mr-2"></i>Rizky Laundry</h1>
        <div>
            <span class="mr-4 text-amber-950">Halo, <b><?= $nama; ?></b> (<?= ucfirst($role); ?>)</span>
            <a href="logout.php" class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-sm text-white">Logout</a>
        </div>
    </nav>

    <div class="container mx-auto p-6">
        <?php if ($role === 'pelanggan'): ?>
            <h2 class="text-2xl font-bold mb-4 text-amber-950">Lacak Status Pakaian Saya</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Form Request Penjemputan -->
                <div class="bg-white p-6 rounded-lg shadow border border-amber-100">
                    <h3 class="text-lg font-semibold mb-4 border-b pb-2 text-amber-900">Request Penjemputan Baru</h3>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="block text-sm font-semibold mb-1 text-amber-900">Nama Pelanggan</label>
                            <input type="text" name="nama_pelanggan" class="w-full border border-amber-200 p-2 rounded focus:border-amber-400 focus:ring-1 focus:ring-amber-400" placeholder="Masukkan nama pelanggan" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="block text-sm font-semibold mb-1 text-amber-900">Jenis Barang</label>
                            <select name="jenis_barang" id="jenis_barang" onchange="toggleFormPilihan(); hitungTotal();" class="w-full border border-amber-200 p-2 rounded focus:border-amber-400 focus:ring-1 focus:ring-amber-400" required>
                                <option value="Baju">Baju / Pakaian Harian</option>
                                <option value="Celana">Celana</option>
                                <option value="Boneka">Boneka</option>
                                <option value="Karpet">Karpet</option>
                                <option value="Selimut/Bedcover">Selimut / Bedcover</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div class="mb-3 hidden" id="field_custom">
                            <label class="block text-sm font-semibold mb-1 text-amber-900">Tuliskan Jenis Barang Baru</label>
                            <input type="text" name="jenis_barang_custom" placeholder="Contoh: Sepatu, Gorden, Jas" class="w-full border border-amber-200 p-2 rounded bg-amber-50">
                        </div>

                        <div class="mb-3">
                            <label class="block text-sm font-semibold mb-1 text-amber-900">Layanan Laundry</label>
                            <select name="layanan" id="layanan" onchange="hitungTotal()" class="w-full border border-amber-200 p-2 rounded focus:border-amber-400 focus:ring-1 focus:ring-amber-400" required>
                                <option value="Cuci Komplit (Cuci + Kering + Setrika)">Cuci Komplit (+Rp 3.000)</option>
                                <option value="Cuci Kering Saja">Cuci Kering Saja (+Rp 1.000)</option>
                                <option value="Setrika Saja">Setrika Saja (+Rp 0)</option>
                            </select>
                        </div>

                        <div class="mb-3" id="field_berat">
                            <label class="block text-sm font-semibold mb-1 text-amber-900">Perkiraan Berat (Kg)</label>
                            <input type="number" step="0.1" name="berat_kg" id="berat_kg" oninput="hitungTotal()" class="w-full border border-amber-200 p-2 rounded focus:border-amber-400 focus:ring-1 focus:ring-amber-400" placeholder="Contoh: 3.5">
                        </div>

                        <div class="mb-3 hidden bg-amber-50 p-3 rounded border border-amber-200" id="field_karpet">
                            <label class="block text-sm font-semibold text-amber-900 mb-2">Ukuran Karpet (Meter)</label>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs text-amber-800">Panjang (m)</label>
                                    <input type="number" step="0.1" name="panjang_m" id="panjang_m" oninput="hitungTotal()" class="w-full border border-amber-200 p-2 rounded bg-white" placeholder="2">
                                </div>
                                <div>
                                    <label class="block text-xs text-amber-800">Lebar (m)</label>
                                    <input type="number" step="0.1" name="lebar_m" id="lebar_m" oninput="hitungTotal()" class="w-full border border-amber-200 p-2 rounded bg-white" placeholder="3">
                                </div>
                            </div>
                        </div>

                        <div class="mb-4 bg-amber-50 border border-amber-300 p-3 rounded text-center">
                            <p class="text-xs text-amber-800 font-semibold">Estimasi Total Biaya:</p>
                            <p class="text-2xl font-bold text-amber-900" id="tampilan_total">Rp 0</p>
                            <p class="text-xs text-amber-700 mt-1" id="keterangan_tarif"></p>
                        </div>

                        <div class="mb-3">
                            <label class="block text-sm font-semibold mb-1 text-amber-900">Metode Pembayaran</label>
                            <select name="metode_pembayaran" id="metode_pembayaran" onchange="toggleQris()" class="w-full border border-amber-200 p-2 rounded focus:border-amber-400 focus:ring-1 focus:ring-amber-400" required>
                                <option value="Cash">Cash / Tunai</option>
                                <option value="QRIS">QRIS (Non-Tunai)</option>
                            </select>
                        </div>

                        <div id="qris_box" class="mb-4 hidden text-center border border-amber-200 p-4 rounded bg-amber-50">
                            <p class="text-sm font-semibold text-amber-900 mb-2">Scan Barcode QRIS di bawah ini:</p>
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=RizkyLaundry-PembayaranQRIS" alt="QRIS Barcode" class="mx-auto border p-1 bg-white shadow-sm">
                            <p class="text-xs text-amber-800 mt-2">Silakan scan menggunakan GoPay, OVO, Dana, atau Mobile Banking.</p>
                        </div>

                        <div class="mb-3">
                            <label class="block text-sm font-semibold mb-1 text-amber-900">Jumlah Uang yang Dibayarkan (DP / Lunas)</label>
                            <input type="number" name="jumlah_bayar" class="w-full border border-amber-200 p-2 rounded focus:border-amber-400 focus:ring-1 focus:ring-amber-400" placeholder="Isi 0 jika belum bayar / isi nominal DP">
                        </div>

                        <div class="mb-3">
                            <label class="block text-sm font-semibold mb-1 text-amber-900">Alamat Penjemputan</label>
                            <textarea name="alamat_pickup" class="w-full border border-amber-200 p-2 rounded focus:border-amber-400 focus:ring-1 focus:ring-amber-400" required></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-semibold mb-1 text-amber-900">Catatan Tambahan (Opsional)</label>
                            <input type="text" name="catatan" class="w-full border border-amber-200 p-2 rounded focus:border-amber-400 focus:ring-1 focus:ring-amber-400">
                        </div>
                        <button type="submit" name="create_order" class="w-full btn-custom py-2 rounded shadow">Kirim Order</button>
                    </form>
                </div>

                <!-- Riwayat Order -->
                <div class="bg-white p-6 rounded-lg shadow border border-amber-100">
                    <h3 class="text-lg font-semibold mb-4 border-b pb-2 text-amber-900">Status & Riwayat Pakaian</h3>
                    <?php $myOrders = $orderObj->getOrdersByCustomer($user_id); ?>
                    <?php if (empty($myOrders)): ?>
                        <p class="text-gray-500 text-sm">Belum ada orderan.</p>
                    <?php endif; ?>
                    <?php foreach ($myOrders as $o): ?>
                        <div class="border border-amber-200 p-4 rounded mb-3 bg-amber-50/50 relative">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-bold text-amber-900"><?= $o['kode_resi']; ?></span>
                                <span class="text-xs bg-amber-200 text-amber-900 font-bold px-2 py-1 rounded"><?= $o['status_pakaian']; ?></span>
                            </div>
                            <p class="text-xs text-amber-800 mb-1"><i class="fa-solid fa-user mr-1"></i> Pelanggan: <b><?= htmlspecialchars($o['nama_pelanggan'] ?? $nama); ?></b></p>
                            <p class="text-sm font-semibold text-gray-800">Barang: <?= htmlspecialchars($o['jenis_barang']); ?> (<?= $o['layanan']; ?>)</p>
                            
                            <?php if ($o['jenis_barang'] === 'Karpet' && $o['panjang_m'] > 0): ?>
                                <p class="text-xs text-gray-600">Ukuran: <?= $o['panjang_m']; ?>m x <?= $o['lebar_m']; ?>m</p>
                            <?php else: ?>
                                <p class="text-xs text-gray-600">Berat: <?= $o['berat_kg']; ?> Kg</p>
                            <?php endif; ?>

                            <div class="mt-2 text-xs bg-white p-2 border border-amber-200 rounded">
                                <p>Total Harga: <b class="text-gray-800">Rp <?= number_format($o['total_harga'], 0, ',', '.'); ?></b></p>
                                <p>Sudah Dibayar: <b class="text-amber-700">Rp <?= number_format($o['jumlah_bayar'], 0, ',', '.'); ?></b></p>
                                <p>Status Bayar: 
                                    <span class="font-bold 
                                        <?php 
                                            if($o['status_pembayaran'] == 'Lunas') echo 'text-green-600';
                                            elseif($o['status_pembayaran'] == 'DP (Sebagian)') echo 'text-amber-600';
                                            else echo 'text-red-600';
                                        ?>">
                                        <?= $o['status_pembayaran']; ?>
                                    </span>
                                </p>
                            </div>

                            <div class="flex justify-between items-center mt-2 pt-2 border-t border-amber-200 text-xs">
                                <span>Metode: <b><?= $o['metode_pembayaran']; ?></b></span>
                                <div class="flex items-center gap-2">
                                    <form method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus orderan ini?');">
                                        <input type="hidden" name="order_id" value="<?= $o['id']; ?>">
                                        <button type="submit" name="delete_order" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">
                                            <i class="fa-solid fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <script>
                function toggleFormPilihan() {
                    const jenis = document.getElementById('jenis_barang').value;
                    const fieldKarpet = document.getElementById('field_karpet');
                    const fieldCustom = document.getElementById('field_custom');
                    const fieldBerat = document.getElementById('field_berat');

                    if (jenis === 'Karpet') {
                        fieldKarpet.classList.remove('hidden');
                        fieldCustom.classList.add('hidden');
                        fieldBerat.classList.add('hidden');
                    } else if (jenis === 'Lainnya') {
                        fieldCustom.classList.remove('hidden');
                        fieldKarpet.classList.add('hidden');
                        fieldBerat.classList.remove('hidden');
                    } else {
                        fieldKarpet.classList.add('hidden');
                        fieldCustom.classList.add('hidden');
                        fieldBerat.classList.remove('hidden');
                    }
                }

                function hitungTotal() {
                    const jenis = document.getElementById('jenis_barang').value;
                    const layanan = document.getElementById('layanan').value;
                    
                    let tambahanLayanan = 0;
                    if (layanan.includes('Cuci Komplit')) {
                        tambahanLayanan = 3000;
                    } else if (layanan.includes('Cuci Kering Saja')) {
                        tambahanLayanan = 1000;
                    } else if (layanan.includes('Setrika Saja')) {
                        tambahanLayanan = 0;
                    }

                    let total = 0;
                    let infoTarif = '';

                    if (jenis === 'Karpet') {
                        const panjang = parseFloat(document.getElementById('panjang_m').value) || 0;
                        const lebar = parseFloat(document.getElementById('lebar_m').value) || 0;
                        const tarifFinalKarpet = 12000 + tambahanLayanan;
                        total = (panjang * lebar) * tarifFinalKarpet;
                        infoTarif = 'Tarif: (Rp 12.000 dasar + Rp ' + tambahanLayanan.toLocaleString('id-ID') + ' layanan) = Rp ' + tarifFinalKarpet.toLocaleString('id-ID') + ' / m²';
                    } else {
                        const berat = parseFloat(document.getElementById('berat_kg').value) || 0;
                        const tarifFinalKg = 7000 + tambahanLayanan;
                        total = berat * tarifFinalKg;
                        infoTarif = 'Tarif: (Rp 7.000 dasar + Rp ' + tambahanLayanan.toLocaleString('id-ID') + ' layanan) = Rp ' + tarifFinalKg.toLocaleString('id-ID') + ' / Kg';
                    }

                    document.getElementById('tampilan_total').innerText = 'Rp ' + total.toLocaleString('id-ID');
                    document.getElementById('keterangan_tarif').innerText = infoTarif;
                }

                function toggleQris() {
                    const metode = document.getElementById('metode_pembayaran').value;
                    const qrisBox = document.getElementById('qris_box');
                    if (metode === 'QRIS') {
                        qrisBox.classList.remove('hidden');
                    } else {
                        qrisBox.classList.add('hidden');
                    }
                }

                hitungTotal();
            </script>

        <?php elseif ($role === 'driver'): ?>
            <h2 class="text-2xl font-bold mb-4 text-amber-950">Daftar Tugas Penjemputan & Pengantaran</h2>
            <div class="bg-white p-6 rounded-lg shadow border border-amber-100">
                <?php $driverOrders = $orderObj->getPickupOrders(); ?>
                <?php foreach ($driverOrders as $o): ?>
                    <div class="border border-amber-200 p-4 rounded mb-4 flex flex-col md:flex-row justify-between items-start md:items-center bg-amber-50/30">
                        <div>
                            <span class="bg-amber-200 text-amber-900 text-xs font-bold px-2 py-1 rounded"><?= $o['status_pakaian']; ?></span>
                            <h4 class="font-bold text-lg mt-1 text-amber-950"><?= htmlspecialchars($o['nama_pelanggan']); ?> (<?= $o['no_hp'] ?? '-'; ?>)</h4>
                            <p class="text-sm text-gray-700">Jenis: <b><?= htmlspecialchars($o['jenis_barang']); ?></b> | Bayar: <b><?= $o['metode_pembayaran']; ?></b></p>
                            <p class="text-sm text-gray-600"><i class="fa-solid fa-location-dot"></i> <?= $o['alamat_pickup']; ?></p>
                        </div>
                        <form method="POST" class="mt-3 md:mt-0 flex gap-2">
                            <input type="hidden" name="order_id" value="<?= $o['id']; ?>">
                            <?php if ($o['status_pakaian'] == 'Menunggu Penjemputan'): ?>
                                <button type="submit" name="update_driver_status" value="1" class="btn-custom px-3 py-1 rounded text-sm">
                                    <input type="hidden" name="status" value="Proses Penjemputan">Jemput Pakaian
                                </button>
                            <?php elseif ($o['status_pakaian'] == 'Proses Penjemputan'): ?>
                                <button type="submit" name="update_driver_status" value="1" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                                    <input type="hidden" name="status" value="Tiba di Laundry">Serahkan ke Admin
                                </button>
                            <?php elseif ($o['status_pakaian'] == 'Siap Diantar'): ?>
                                <button type="submit" name="update_driver_status" value="1" class="bg-amber-700 hover:bg-amber-800 text-white px-3 py-1 rounded text-sm">
                                    <input type="hidden" name="status" value="Selesai">Telah Diantarkan
                                </button>
                            <?php endif; ?>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php elseif ($role === 'admin'): ?>
            <h2 class="text-2xl font-bold mb-4 text-amber-950">Panel Admin Operasional Laundry</h2>
            <div class="bg-white p-6 rounded-lg shadow overflow-x-auto border border-amber-100">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-amber-100/70 border-b border-amber-200 text-sm text-amber-950">
                            <th class="p-2">Resi</th>
                            <th class="p-2">Pelanggan</th>
                            <th class="p-2">Barang</th>
                            <th class="p-2">Berat</th>
                            <th class="p-2">Total Harga</th>
                            <th class="p-2">Dibayar (DP/Lunas)</th>
                            <th class="p-2">Status Bayar</th>
                            <th class="p-2">Proses Pakaian</th>
                            <th class="p-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php $allOrders = $orderObj->getAllOrders(); ?>
                        <?php foreach ($allOrders as $o): ?>
                            <tr class="border-b border-amber-100 hover:bg-amber-50/40">
                                <form method="POST">
                                    <input type="hidden" name="order_id" value="<?= $o['id']; ?>">
                                    <td class="p-2 font-bold text-amber-900"><?= $o['kode_resi']; ?></td>
                                    <td class="p-2 font-semibold text-gray-800"><?= htmlspecialchars($o['nama_pelanggan']); ?></td>
                                    <td class="p-2"><?= htmlspecialchars($o['jenis_barang']); ?></td>
                                    <td class="p-2">
                                        <?php if($o['jenis_barang'] === 'Karpet'): ?>
                                            <?= $o['panjang_m']; ?>x<?= $o['lebar_m']; ?>m
                                            <input type="hidden" name="berat_kg" value="0">
                                        <?php else: ?>
                                            <input type="number" step="0.1" name="berat_kg" value="<?= $o['berat_kg']; ?>" class="w-16 border border-amber-200 rounded p-1">
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-2"><input type="number" name="total_harga" value="<?= $o['total_harga']; ?>" class="w-24 border border-amber-200 rounded p-1"></td>
                                    <td class="p-2"><input type="number" name="jumlah_bayar" value="<?= $o['jumlah_bayar']; ?>" class="w-24 border border-amber-200 rounded p-1"></td>
                                    <td class="p-2">
                                        <span class="px-2 py-1 rounded text-xs font-bold 
                                            <?php 
                                                if($o['status_pembayaran'] == 'Lunas') echo 'bg-green-100 text-green-700';
                                                elseif($o['status_pembayaran'] == 'DP (Sebagian)') echo 'bg-amber-100 text-amber-800';
                                                else echo 'bg-red-100 text-red-700';
                                            ?>">
                                            <?= $o['status_pembayaran']; ?>
                                        </span>
                                    </td>
                                    <td class="p-2">
                                        <select name="status" class="border border-amber-200 rounded text-xs p-1">
                                            <option value="Menunggu Penjemputan" <?= $o['status_pakaian']=='Menunggu Penjemputan'?'selected':''; ?>>Menunggu Penjemputan</option>
                                            <option value="Pencucian" <?= $o['status_pakaian']=='Pencucian'?'selected':''; ?>>Pencucian</option>
                                            <option value="Pengeringan" <?= $o['status_pakaian']=='Pengeringan'?'selected':''; ?>>Pengeringan</option>
                                            <option value="Penyetrikaan" <?= $o['status_pakaian']=='Penyetrikaan'?'selected':''; ?>>Penyetrikaan</option>
                                            <option value="Siap Diantar" <?= $o['status_pakaian']=='Siap Diantar'?'selected':''; ?>>Siap Diantar</option>
                                            <option value="Selesai" <?= $o['status_pakaian']=='Selesai'?'selected':''; ?>>Selesai</option>
                                        </select>
                                    </td>
                                    <td class="p-2">
                                        <button type="submit" name="update_admin_status" class="btn-custom px-2 py-1 rounded text-xs shadow">Simpan</button>
                                    </td>
                                </form>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($role === 'pengelola'): ?>
            <h2 class="text-2xl font-bold mb-4 text-amber-950">Dashboard Laporan Pengelola</h2>
            <?php $stats = $orderObj->getDashboardStats(); ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white p-6 rounded-lg shadow border-l-4 border-amber-500 border-t border-r border-b border-amber-100">
                    <p class="text-gray-500 text-sm">Total Pemasukan Laundry</p>
                    <p class="text-3xl font-bold text-amber-950">Rp <?= number_format($stats['total_pemasukan'], 0, ',', '.'); ?></p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow border-l-4 border-amber-400 border-t border-r border-b border-amber-100">
                    <p class="text-gray-500 text-sm">Total Orderan Aktif</p>
                    <p class="text-3xl font-bold text-amber-950"><?= $stats['order_aktif']; ?> Order</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>