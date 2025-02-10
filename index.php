<?php
include 'config.php';
session_start();
include 'session.php';

if (!isAdmin() && !isPetugas()) {
    header("Location: login.php");
    exit();
}

$query = "SELECT * FROM barang";
$result = $koneksi->query($query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Barang</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php 
    include 'fungsi/navbar.php';
    ?>

    <div class="container">
    <a href="fungsi/tambah_barang.php" class="btn-tambah">Tambah Barang</a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Barang</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($result->num_rows > 0){
                while ($row = $result->fetch_assoc()){
                    echo"<tr>
                            <td>{$row['id_barang']}</td>
                            <td>{$row['nama_barang']}</td>
                            <td>Rp " . number_format($row['harga_barang'], 0 , ",", ",") . "</td>
                            <td>{$row['stok']}</td>
                            <td>
                                <a href='fungsi/edit_barang.php?id={$row['id_barang']}' class='btn-edit'>Edit</a>
                                <a href='fungsi/hapus_barang.php?id={$row['id_barang']}' class='btn-hapus'>hapus</a>
                            </td>
                        </tr>";
                }
            }else {
                echo"<tr><td colspan='5'>Tidak ada data barang.</td></tr>";
            }            
            ?>
        </tbody>
    </table>

    <br>

    
    </div>    
</body>
</html>