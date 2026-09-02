<?php
require_once "Laundry.php";
session_start();

// Proteksi Halaman Login
if (!isset($_SESSION['user_logged'])) {
    header("Location: login.php");
    exit;
}

// Inisialisasi Data Default
if (!isset($_SESSION['transaksi']) || !is_array($_SESSION['transaksi'])) {
    $_SESSION['transaksi'] = [];
}

if (isset($_GET['action']) && $_GET['action'] === 'update_status' && isset($_GET['key'])) {
    $key = $_GET['key'];
    if (isset($_SESSION['transaksi'][$key])) {
        $_SESSION['transaksi'][$key]->setStatus("Selesai");
    }
    header("Location: index.php?page=data");
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['key'])) {
    $key = $_GET['key'];
    if (isset($_SESSION['transaksi'][$key])) {
        unset($_SESSION['transaksi'][$key]);
        $_SESSION['transaksi'] = array_values($_SESSION['transaksi']);
    }
    header("Location: index.php?page=data");
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'reset') {
    unset($_SESSION['transaksi']);
    header("Location: index.php?page=data");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['tambah'])) {
        $nama = $_POST['nama'];
        $jenis = $_POST['jenis'];
        $metodeBayar = $_POST['metode_bayar'];

        if ($jenis === 'kiloan') {
            $berat = (float)$_POST['berat'];
            $tglKembali = date('Y-m-d', strtotime('+2 days'));
            $_SESSION['transaksi'][] = new \Data\Layanan\LaundryKiloan($nama, $berat, $tglKembali, $metodeBayar);
        } else {
            $p = (float)$_POST['panjang'];
            $l = (float)$_POST['lebar'];
            $tglKembali = date('Y-m-d', strtotime('+4 days')); 
            $_SESSION['transaksi'][] = new \Data\Layanan\LaundryKarpet($nama, $p, $l, $tglKembali, $metodeBayar);
        }
        
        header("Location: index.php?page=data");
        exit;
    }

    if (isset($_POST['update'])) {
        $key = $_POST['key'];
        if (isset($_SESSION['transaksi'][$key])) {
            $nama = $_POST['nama'];
            $metodeBayar = $_POST['metode_bayar'];
            $status = $_POST['status'];
            $tglKembali = $_SESSION['transaksi'][$key]->tglKembali; 

            if ($_SESSION['transaksi'][$key] instanceof \Data\Layanan\LaundryKiloan) {
                $berat = (float)$_POST['berat'];
                $itemUpdate = new \Data\Layanan\LaundryKiloan($nama, $berat, $tglKembali, $metodeBayar);
            } else {
                $p = (float)$_POST['panjang'];
                $l = (float)$_POST['lebar'];
                $itemUpdate = new \Data\Layanan\LaundryKarpet($nama, $p, $l, $tglKembali, $metodeBayar);
            }
            
            $itemUpdate->setStatus($status);
            $_SESSION['transaksi'][$key] = $itemUpdate;
        }

        header("Location: index.php?page=data");
        exit;
    }
}

$page = $_GET['page'] ?? 'tambah';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Laundry</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #fffdf5; color: #444; }
        
        header { 
            background: #fef08a; 
            color: #713f12; 
            padding: 18px 40px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            box-shadow: 0 4px 12px rgba(234, 179, 8, 0.15);
        }
        header h2 { font-weight: 800; letter-spacing: 0.5px; font-size: 24px; color: #854d0e; }

        .nav-menu { display: flex; gap: 12px; align-items: center; }
        .nav-btn { 
            background: #fef9c3; 
            color: #854d0e; 
            padding: 10px 20px; 
            border-radius: 8px; 
            text-decoration: none; 
            font-size: 14px; 
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .nav-btn:hover, .nav-btn.active { 
            background: #ca8a04; 
            color: white; 
            box-shadow: 0 2px 6px rgba(202, 138, 4, 0.3);
        }
        .btn-logout { 
            background: #fecaca; 
            color: #991b1b; 
            padding: 10px 20px; 
            border-radius: 8px; 
            text-decoration: none; 
            font-size: 14px; 
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .btn-logout:hover { background: #ef4444; color: white; }

        /* Container */
        .container { max-width: 1050px; margin: 40px auto; padding: 0 20px; }
        .card { 
            background: white; 
            padding: 32px; 
            border-radius: 16px; 
            box-shadow: 0 10px 25px rgba(234, 179, 8, 0.08); 
            border: 1px solid #fef08a;
            margin-bottom: 30px; 
        }
        
        .form-group { margin-bottom: 20px; }
        .form-row { display: flex; gap: 15px; }
        .form-row .form-group { flex: 1; }
        label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: #713f12; }
        input[type="text"], input[type="number"], select { 
            width: 100%; 
            padding: 12px 16px; 
            border: 1.5px solid #fde047; 
            border-radius: 8px; 
            outline: none;
            background: #fffbeb;
            transition: border-color 0.2s;
            font-size: 14px;
        }
        input:focus, select:focus {
            border-color: #ca8a04;
            background: #ffffff;
        }
        
        .radio-options { display: flex; gap: 15px; margin-bottom: 20px; }
        .radio-label { 
            display: flex; 
            align-items: center; 
            gap: 10px; 
            font-weight: 600; 
            cursor: pointer; 
            background: #fffbeb; 
            padding: 14px 18px; 
            border: 1.5px solid #fde047; 
            border-radius: 10px; 
            flex: 1; 
            color: #713f12;
            transition: all 0.2s;
        }
        .radio-label:hover { background: #fef08a; }
        .radio-label input { accent-color: #ca8a04; width: 16px; height: 16px; }

        button { 
            background: #eab308; 
            color: white; 
            border: none; 
            padding: 14px; 
            border-radius: 8px; 
            cursor: pointer; 
            width: 100%; 
            font-size: 16px; 
            font-weight: bold; 
            margin-top: 10px; 
            transition: background 0.2s, transform 0.1s;
        }
        button:hover { background: #ca8a04; }
        button:active { transform: scale(0.99); }

        table { width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 10px; overflow: hidden; border-radius: 10px; border: 1px solid #fef08a; }
        th, td { padding: 12px 14px; text-align: left; }
        th { background: #fef9c3; color: #713f12; font-weight: 700; font-size: 13px; border-bottom: 2px solid #fde047; }
        td { border-bottom: 1px solid #fef9c3; font-size: 13px; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background-color: #fffbeb; }
        
        .badge { padding: 5px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; display: inline-block; }
        .badge-warning { background-color: #fef08a; color: #854d0e; }
        .badge-success { background-color: #dcfce7; color: #166534; }
        .badge-pay { background-color: #e0f2fe; color: #0369a1; }

        .status-container { display: flex; align-items: center; gap: 8px; }

        .btn-action-group { display: flex; gap: 6px; align-items: center; }
        .btn-finish { background: #eab308; color: white; padding: 4px 8px; border-radius: 6px; text-decoration: none; font-size: 11px; font-weight: 600; white-space: nowrap; }
        .btn-finish:hover { background: #ca8a04; }
        .btn-edit { background: #3b82f6; color: white; padding: 6px 10px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600; }
        .btn-edit:hover { background: #1d4ed8; }
        .btn-delete { background: #ef4444; color: white; padding: 6px 10px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600; }
        .btn-delete:hover { background: #b91c1c; }
        .btn-cancel { background: #9ca3af; color: white; padding: 14px; border-radius: 8px; text-decoration: none; font-size: 16px; font-weight: bold; display: inline-block; text-align: center; width: 100%; margin-top: 10px; }
        .btn-cancel:hover { background: #6b7280; }
        .btn-reset { color: #dc2626; font-size: 13px; font-weight: 600; text-decoration: none; border: 1px solid #fca5a5; padding: 6px 12px; border-radius: 6px; background: #fff5f5; transition: all 0.2s; }
        .btn-reset:hover { background: #dc2626; color: white; }
    </style>
</head>
<body>

<header>
    <h2>Laundry</h2>
    <div class="nav-menu">
        <a href="index.php?page=tambah" class="nav-btn <?= $page === 'tambah' ? 'active' : ''; ?>">Form Tambah Transaksi</a>
        <a href="index.php?page=data" class="nav-btn <?= $page === 'data' ? 'active' : ''; ?>">Daftar Data Transaksi</a>
        <a href="logout.php" class="btn-logout">Logout</a>
    </div>
</header>

<div class="container">

    <?php if ($page === 'tambah'): ?>
        
        <div class="card">
            <h3 style="margin-bottom: 20px; color: #713f12;">Form Tambah Transaksi</h3>
            <form method="POST">
                <input type="hidden" name="tambah" value="1">
                
                <div class="form-group">
                    <label>Nama Pelanggan:</label>
                    <input type="text" name="nama" required placeholder="Masukkan nama pelanggan">
                </div>

                <div class="form-group">
                    <label>Pilih Jenis Layanan:</label>
                    <div class="radio-options">
                        <label class="radio-label">
                            <input type="radio" name="jenis" value="kiloan" checked onclick="toggleInput('kiloan')"> Laundry Kiloan (Estimasi 2 Hari)
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="jenis" value="karpet" onclick="toggleInput('karpet')"> Laundry Karpet (Estimasi 4 Hari)
                        </label>
                    </div>
                </div>

                <div id="field-kiloan" class="form-group">
                    <label>Berat (Kg):</label>
                    <input type="number" step="0.1" name="berat" id="input-berat" required placeholder="Contoh: 3.5">
                </div>

                <div id="field-karpet" class="form-row" style="display: none;">
                    <div class="form-group">
                        <label>Panjang Karpet (m):</label>
                        <input type="number" step="0.1" name="panjang" id="input-panjang" placeholder="Contoh: 3">
                    </div>
                    <div class="form-group">
                        <label>Lebar Karpet (m):</label>
                        <input type="number" step="0.1" name="lebar" id="input-lebar" placeholder="Contoh: 2">
                    </div>
                </div>

                <div class="form-group">
                    <label>Metode Pembayaran:</label>
                    <select name="metode_bayar" required>
                        <option value="Cash (Tunai)">Cash (Tunai)</option>
                        <option value="QRIS">QRIS</option>
                        <option value="Transfer Bank">Transfer Bank</option>
                    </select>
                </div>

                <button type="submit">Proses Transaksi</button>
            </form>
        </div>

    <?php elseif ($page === 'edit' && isset($_GET['key']) && isset($_SESSION['transaksi'][$_GET['key']])): 
        $key = $_GET['key'];
        $item = $_SESSION['transaksi'][$key];
        $isKiloan = $item instanceof \Data\Layanan\LaundryKiloan;
    ?>

        <div class="card">
            <h3 style="margin-bottom: 20px; color: #713f12;">Edit Data Transaksi</h3>
            <form method="POST">
                <input type="hidden" name="update" value="1">
                <input type="hidden" name="key" value="<?= $key; ?>">

                <div class="form-group">
                    <label>Nama Pelanggan:</label>
                    <input type="text" name="nama" value="<?= htmlspecialchars($item->namaPelanggan); ?>" required>
                </div>

                <?php if ($isKiloan): 
                    $ref = new ReflectionClass($item);
                    $propBerat = $ref->getProperty('berat');
                    $propBerat->setAccessible(true);
                    $valBerat = $propBerat->getValue($item);
                ?>
                    <div class="form-group">
                        <label>Berat (Kg):</label>
                        <input type="number" step="0.1" name="berat" value="<?= $valBerat; ?>" required>
                    </div>
                <?php else: 
                    $ref = new ReflectionClass($item);
                    $pProp = $ref->getProperty('panjang');
                    $pProp->setAccessible(true);
                    $valP = $pProp->getValue($item);

                    $lProp = $ref->getProperty('lebar');
                    $lProp->setAccessible(true);
                    $valL = $lProp->getValue($item);
                ?>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Panjang Karpet (m):</label>
                            <input type="number" step="0.1" name="panjang" value="<?= $valP; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Lebar Karpet (m):</label>
                            <input type="number" step="0.1" name="lebar" value="<?= $valL; ?>" required>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label>Metode Pembayaran:</label>
                    <select name="metode_bayar" required>
                        <option value="Cash (Tunai)" <?= $item->metodeBayar === 'Cash (Tunai)' ? 'selected' : ''; ?>>Cash (Tunai)</option>
                        <option value="QRIS" <?= $item->metodeBayar === 'QRIS' ? 'selected' : ''; ?>>QRIS</option>
                        <option value="Transfer Bank" <?= $item->metodeBayar === 'Transfer Bank' ? 'selected' : ''; ?>>Transfer Bank</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Status Transaksi:</label>
                    <select name="status" required>
                        <option value="Diproses" <?= $item->getStatus() === 'Diproses' ? 'selected' : ''; ?>>Diproses</option>
                        <option value="Selesai" <?= $item->getStatus() === 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                    </select>
                </div>

                <button type="submit">Simpan Perubahan</button>
                <a href="index.php?page=data" class="btn-cancel">Batal</a>
            </form>
        </div>

    <?php elseif ($page === 'data'): ?>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="color: #713f12;">Daftar Transaksi</h2>
            <a href="index.php?action=reset" class="btn-reset" onclick="return confirm('Reset seluruh data transaksi?')">Reset Seluruh Data</a>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 15px; color: #854d0e;">🧺 Transaksi Laundry Kiloan</h3>
            <table>
                <thead>
                    <tr>
                        <th>Pelanggan</th>
                        <th>Detail Layanan</th>
                        <th>Tgl Pengembalian</th>
                        <th>Pembayaran</th>
                        <th>Total Biaya</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $adaKiloan = false;
                    foreach ($_SESSION['transaksi'] as $key => $t): 
                        if ($t instanceof \Data\Layanan\LaundryKiloan):
                            $adaKiloan = true;
                    ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($t->namaPelanggan); ?></strong></td>
                            <td><?= $t->getDetail(); ?></td>
                            <td><?= !empty($t->tglKembali) ? date('d-m-Y', strtotime($t->tglKembali)) : '-'; ?></td>
                            <td><span class="badge badge-pay"><?= !empty($t->metodeBayar) ? htmlspecialchars($t->metodeBayar) : '-'; ?></span></td>
                            <td><strong>Rp <?= number_format($t->hitungTotal(), 0, ',', '.'); ?></strong></td>
                            <td>
                                <div class="status-container">
                                    <?php if ($t->getStatus() === 'Selesai'): ?>
                                        <span class="badge badge-success">Selesai</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Diproses</span>
                                        <a href="index.php?action=update_status&key=<?= $key; ?>" class="btn-finish">Tandai Selesai</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="btn-action-group">
                                    <a href="index.php?page=edit&key=<?= $key; ?>" class="btn-edit">Edit</a>
                                    <a href="index.php?action=delete&key=<?= $key; ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                                </div>
                            </td>
                        </tr>
                    <?php 
                        endif;
                    endforeach; 
                    ?>

                    <?php if (!$adaKiloan): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: #a1a1aa; padding: 20px;">Belum ada data transaksi Kiloan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 15px; color: #854d0e;">🧹 Transaksi Laundry Karpet</h3>
            <table>
                <thead>
                    <tr>
                        <th>Pelanggan</th>
                        <th>Detail Layanan</th>
                        <th>Tgl Pengembalian</th>
                        <th>Pembayaran</th>
                        <th>Total Biaya</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $adaKarpet = false;
                    foreach ($_SESSION['transaksi'] as $key => $t): 
                        if ($t instanceof \Data\Layanan\LaundryKarpet):
                            $adaKarpet = true;
                    ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($t->namaPelanggan); ?></strong></td>
                            <td><?= $t->getDetail(); ?></td>
                            <td><?= !empty($t->tglKembali) ? date('d-m-Y', strtotime($t->tglKembali)) : '-'; ?></td>
                            <td><span class="badge badge-pay"><?= !empty($t->metodeBayar) ? htmlspecialchars($t->metodeBayar) : '-'; ?></span></td>
                            <td><strong>Rp <?= number_format($t->hitungTotal(), 0, ',', '.'); ?></strong></td>
                            <td>
                                <div class="status-container">
                                    <?php if ($t->getStatus() === 'Selesai'): ?>
                                        <span class="badge badge-success">Selesai</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Diproses</span>
                                        <a href="index.php?action=update_status&key=<?= $key; ?>" class="btn-finish">Tandai Selesai</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="btn-action-group">
                                    <a href="index.php?page=edit&key=<?= $key; ?>" class="btn-edit">Edit</a>
                                    <a href="index.php?action=delete&key=<?= $key; ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                                </div>
                            </td>
                        </tr>
                    <?php 
                        endif;
                    endforeach; 
                    ?>

                    <?php if (!$adaKarpet): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: #a1a1aa; padding: 20px;">Belum ada data transaksi Karpet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>

</div>

<script>
function toggleInput(jenis) {
    const fieldKiloan = document.getElementById('field-kiloan');
    const fieldKarpet = document.getElementById('field-karpet');
    const inputBerat = document.getElementById('input-berat');
    const inputPanjang = document.getElementById('input-panjang');
    const inputLebar = document.getElementById('input-lebar');

    if (jenis === 'kiloan') {
        fieldKiloan.style.display = 'block';
        fieldKarpet.style.display = 'none';
        
        inputBerat.required = true;
        inputPanjang.required = false;
        inputLebar.required = false;
    } else {
        fieldKiloan.style.display = 'none';
        fieldKarpet.style.display = 'flex';
        
        inputBerat.required = false;
        inputPanjang.required = true;
        inputLebar.required = true;
    }
}
</script>

</body>
</html>