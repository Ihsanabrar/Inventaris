<?php
include 'config.php';
session_start();
include 'session.php';

if (!isAdmin() && !isPetugas()) {
    header("Location: login.php");
    exit();
}

$query_total_barang = "SELECT COUNT(*) as total FROM barang";
$total_barang = $koneksi->query($query_total_barang)->fetch_assoc()['total'];

$query_total_stok = "SELECT SUM(stok) as total FROM barang";
$total_stok = $koneksi->query($query_total_stok)->fetch_assoc()['total'];

// Query untuk menghitung total supplier
$query_total_supplier = "SELECT COUNT(*) as total FROM supplier";
$total_supplier = $koneksi->query($query_total_supplier)->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Inventaris</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'fungsi/navbar.php'; ?>

    <div class="dashboard-container">
        <!-- Header -->
        <div class="dashboard-header">
            <h1>📊 Dashboard Inventaris</h1>
            <p>Selamat datang, <?php echo $_SESSION['username']; ?>!</p>
        </div>

        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="summary-card bg-blue">
                <div class="card-icon">📦</div>
                <h3>Total Barang</h3>
                <p><?= $total_barang ?></p>
            </div>
            <div class="summary-card bg-green">
                <div class="card-icon">📊</div>
                <h3>Total Stok</h3>
                <p><?= $total_stok ?></p>
            </div>
            <div class="summary-card bg-orange">
                <div class="card-icon">👥</div>
                <h3>Total Supplier</h3>
                <p><?= $total_supplier ?></p>
            </div>
        </div>

    </div>
</body>
</html>