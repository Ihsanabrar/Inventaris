<?php
include 'config.php';

// Mulai session jika belum dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'session.php';

// Ambil id_pembelian dari URL
$id_pembelian = $_GET['id'] ?? null;
if (!$id_pembelian) {
    die("Error: ID pembelian tidak ditemukan.");
}

// Pastikan pengguna sudah login dan memiliki id_user
if (!isset($_SESSION['id_user'])) {
    die("Error: Anda harus login terlebih dahulu.");
}
$id_user = $_SESSION['id_user'];

// Proses POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Jika tombol Selesai ditekan:
    if (isset($_POST['selesai'])) {
        // Hitung total harga dari detail pembelian
        $stmtTotal = $koneksi->prepare("SELECT SUM(sub_total) as total FROM detail_pembelian WHERE id_pembelian = ?");
        $stmtTotal->bind_param("i", $id_pembelian);
        $stmtTotal->execute();
        $resultTotal = $stmtTotal->get_result();
        $totalData = $resultTotal->fetch_assoc();
        $totalHarga = $totalData['total'] ?? 0;
        
        // Update total harga pada tabel pembelian
        $stmtUpdate = $koneksi->prepare("UPDATE pembelian SET total_harga = ? WHERE id_pembelian = ?");
        $stmtUpdate->bind_param("ii", $totalHarga, $id_pembelian);
        if ($stmtUpdate->execute()) {
            echo "<script>alert('Data berhasil disimpan!'); window.location.href='pembelian.php';</script>";
            exit;
        } else {
            die("Gagal menyimpan data: " . $stmtUpdate->error);
        }
    } else {
        // Proses tambah barang ke pembelian
        $id_barang = $_POST['id_barang'];
        $jumlah_barang = $_POST['jumlah_barang'];

        // Validasi data
        if (empty($id_barang) || empty($jumlah_barang)) {
            die("Data tidak valid!");
        }

        // Ambil harga barang dari database
        $stmt = $koneksi->prepare("SELECT harga_barang FROM barang WHERE id_barang = ?");
        $stmt->bind_param("i", $id_barang);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            die("Barang tidak ditemukan!");
        }

        $barang = $result->fetch_assoc();
        $sub_total = $barang['harga_barang'] * $jumlah_barang;

        // Insert ke detail_pembelian
        $stmt = $koneksi->prepare("INSERT INTO detail_pembelian (id_pembelian, id_barang, jumlah_barang, sub_total, id_user) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiii", $id_pembelian, $id_barang, $jumlah_barang, $sub_total, $id_user);
        
        if ($stmt->execute()) {
            echo "<script>alert('Berhasil menambahkan barang ke pembelian.'); window.location.href='detail_pembelian.php?id=" . $id_pembelian . "';</script>";
            exit;
        } else {
            die("Gagal menambahkan barang: " . $stmt->error);
        }
    }
}

// Ambil data detail pembelian beserta nama dan harga barang
$stmt = $koneksi->prepare("
    SELECT dp.*, b.nama_barang, b.harga_barang
    FROM detail_pembelian dp
    JOIN barang b ON dp.id_barang = b.id_barang
    WHERE dp.id_pembelian = ?
");
$stmt->bind_param("i", $id_pembelian);
$stmt->execute();
$result = $stmt->get_result();
$detail = $result->fetch_all(MYSQLI_ASSOC);

// Hitung total harga pembelian
$totalHarga = 0;
foreach ($detail as $d) {
    $totalHarga += $d['sub_total'];
}

// Ambil semua barang untuk dropdown
$barang_list_result = $koneksi->query("SELECT * FROM barang");
$barang_list = $barang_list_result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pembelian</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Styling dropdown select yang menarik */
        select#id_barang {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            color: #333;
            background: linear-gradient(45deg, #f06, #ff9);
            /* Menghilangkan panah default (tergantung browser) */
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url('data:image/svg+xml;charset=US-ASCII, %3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'10\' height=\'10\' viewBox=\'0 0 10 10\'%3E%3Cpolygon points=\'0,0 10,0 5,10\' fill=\'%23333\'/%3E%3C/svg%3E');
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 10px;
        }
        /* Styling tombol Selesai */
        .btn-selesai {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-selesai:hover {
            background-color: #45a049;
        }
    </style>
    <script>
        function hitungSubtotal() {
            const id_barang = document.getElementById('id_barang').value;
            const jumlah = document.getElementById('jumlah').value;
            
            // Cari harga barang yang dipilih
            let harga = 0;
            <?php foreach($barang_list as $b): ?>
                if (<?= $b['id_barang'] ?> == id_barang) {
                    harga = <?= $b['harga_barang'] ?>;
                }
            <?php endforeach; ?>
            
            const subtotal = harga * jumlah;
            document.getElementById('subtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
            document.getElementById('sub_total').value = subtotal;
        }
    </script>
</head>
<body>
    <?php include 'fungsi/navbar.php'; ?>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Detail Pembelian <?= $id_pembelian ?></h1>
            <a href="pembelian.php" class="btn-batal">Kembali</a>
        </div>

        <!-- Form Tambah Barang -->
        <div class="form-tambah">
            <h2>Tambah Pembelian                                                                                    </h2>
            <form method="POST">
                <div class="form-group">
                    <label>Pilih Barang</label>
                    <select id="id_barang" name="id_barang" onchange="hitungSubtotal()" required>
                        <option value="">-- Pilih Barang --</option>
                        <?php foreach($barang_list as $b): ?>
                            <option value="<?= $b['id_barang'] ?>">
                                <?= $b['nama_barang'] ?> (Rp <?= number_format($b['harga_barang'], 0, ',', '.') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Jumlah</label>
                    <input type="number" id="jumlah" name="jumlah_barang" oninput="hitungSubtotal()" required>
                </div>
                
                <div class="form-group">
                    <label>Subtotal</label>
                    <span id="subtotal" style="font-weight:bold">Rp 0</span>
                    <input type="hidden" id="sub_total" name="sub_total">
                </div>
                
                <button type="submit" class="btn-simpan">Tambah</button>
            </form>
        </div>

        <!-- Daftar Barang -->
        <div class="activity-section">
            <h2>Daftar Barang</h2>
            <div class="activity-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID Detail</th>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                            <th>Harga Satuan</th>
                            <th>Subtotal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($detail)): ?>
                            <?php foreach ($detail as $d): ?>
                                <tr>
                                    <td><?= $d['id_pembelian_detail'] ?? $d['id_pembelian'] ?></td>
                                    <td><?= $d['nama_barang'] ?></td>
                                    <td><?= $d['jumlah_barang'] ?></td>
                                    <td>Rp <?= number_format($d['harga_barang'], 0, ',', '.') ?></td>
                                    <td>Rp <?= number_format($d['sub_total'], 0, ',', '.') ?></td>
                                    <td>
                                        <a href="fungsi/hapus_detail.php?id=<?= $d['id_pembelian_detail'] ?>" 
                                           class="btn-hapus" 
                                           onclick="return confirm('Hapus barang ini?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">Belum ada barang.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="total-harga" style="margin-top: 20px; font-size: 18px; font-weight: bold;">
                Total Harga: Rp <?= number_format($totalHarga, 0, ',', '.') ?>
            </div>
            <!-- Tombol Selesai -->
            <form method="POST" style="margin-top: 20px;">
                <button type="submit" name="selesai" class="btn-selesai">Selesai</button>
            </form>
        </div>
    </div>
</body>
</html>