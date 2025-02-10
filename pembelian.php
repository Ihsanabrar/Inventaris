<?php 
include 'config.php';
session_start();
include 'session.php';
$query = $koneksi->query("SELECT * FROM pembelian");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembelian</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'fungsi/navbar.php'; ?>

    <!-- Konten -->
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Pembelian</h1>
            <p>Daftar pembelian barang</p>
        </div>

        <!-- Tombol Tambah Pembelian -->
        <div style="margin-bottom: 20px;">
            <a href="fungsi/tambah_pembelian.php" class="btn-tambah">Tambah Pembelian</a>
        </div>

        <!-- Tabel Pembelian -->
        <div class="activity-section">
            <h2>Daftar Pembelian</h2>
            <div class="activity-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID Pembelian</th>
                            <th>User</th>
                            <th>Supplier</th>
                            <th>Tanggal Pembelian</th>
                            <th>Total Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $query->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id_pembelian']) ?></td>
                            <td><?= htmlspecialchars($row['id_user']) ?></td>
                            <td><?= htmlspecialchars($row['id_supplier']) ?></td>
                            <td><?= htmlspecialchars($row['tanggal_pembelian']) ?></td>
                            <td><?= 'Rp ' . number_format($row['total_harga'], 0, ',', '.') ?></td>
                            <td>
                                <a href="detail_pembelian.php?id=<?= $row['id_pembelian'] ?>" class="btn-edit">Detail</a>
                                <a href="fungsi/hapus_pembelian.php?id=<?= $row['id_pembelian'] ?>" class="btn-hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>