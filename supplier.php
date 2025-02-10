<?php
// Mulai session dan koneksi ke database
session_start();
include('config.php');

// Cek apakah user memiliki akses
if (!isset($_SESSION['level']) || ($_SESSION['level'] != 'admin' && $_SESSION['level'] != 'petugas')) {
    header("Location: login.php");
    exit();
}

// Query untuk mendapatkan data supplier
$query = "SELECT * FROM supplier";
$result = mysqli_query($koneksi, $query);

// Proses menambahkan supplier
if (isset($_POST['submit'])) {
    $nama_supplier = $_POST['nama_supplier'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];

    // Cari ID terakhir dan tambahkan 1
    $last_id_query = "SELECT MAX(id_supplier) as last_id FROM supplier";
    $last_id_result = mysqli_query($koneksi, $last_id_query);
    $last_id_row = mysqli_fetch_assoc($last_id_result);
    $new_id = $last_id_row['last_id'] + 1;

    // Insert data dengan ID baru
    $insert_query = "INSERT INTO supplier (id_supplier, nama_supplier, alamat, no_hp) 
                     VALUES ('$new_id', '$nama_supplier', '$alamat', '$no_hp')";

    if (mysqli_query($koneksi, $insert_query)) {
        echo "<script>alert('Supplier berhasil ditambahkan!');</script>";
        header("Location: supplier.php");
    } else {
        echo "<script>alert('Terjadi kesalahan, coba lagi.');</script>";
    }
}


// Proses menghapus supplier
if (isset($_GET['hapus'])) {
    $id_supplier = $_GET['hapus'];
    $delete_query = "DELETE FROM supplier WHERE id_supplier = $id_supplier";

    if (mysqli_query($koneksi, $delete_query)) {
        // Reset ID supplier agar berurutan
        mysqli_query($koneksi, "SET @count = 0;");
        mysqli_query($koneksi, "UPDATE supplier SET id_supplier = @count:= @count + 1;");
        mysqli_query($koneksi, "ALTER TABLE supplier AUTO_INCREMENT = 1;");

        echo "<script>alert('Supplier berhasil dihapus!');</script>";
        header("Location: supplier.php");
    } else {
        echo "<script>alert('Terjadi kesalahan, coba lagi.');</script>";
    }
}


// Proses mengedit supplier
if (isset($_POST['edit'])) {
    $id_supplier = $_POST['id_supplier'];
    $nama_supplier = $_POST['nama_supplier'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];

    $update_query = "UPDATE supplier SET nama_supplier = '$nama_supplier', alamat = '$alamat', no_hp = '$no_hp' 
                     WHERE id_supplier = $id_supplier";

    if (mysqli_query($koneksi, $update_query)) {
        echo "<script>alert('Supplier berhasil diupdate!');</script>";
        header("Location: supplier.php");
    } else {
        echo "<script>alert('Terjadi kesalahan, coba lagi.');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Supplier</title>
    <link rel="stylesheet" href="style.css"> <!-- CSS File -->
</head>
<body>

<?php include 'fungsi/navbar.php'; ?>

<div class="supplier-container">
    <header>
        <h1>Daftar Supplier</h1>
    </header>

    <!-- Tabel Supplier -->
    <div class="supplier-table-container">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Supplier</th>
                    <th>Alamat</th>
                    <th>No HP</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['id_supplier']; ?></td>
                        <td><?php echo $row['nama_supplier']; ?></td>
                        <td><?php echo $row['alamat']; ?></td>
                        <td><?php echo $row['no_hp']; ?></td>
                        <td>
                        <a href="fungsi/edit_supplier.php?id=<?= $row['id_supplier']; ?>" class="btn-edit">Edit</a>
                        <a href="fungsi/supplier.php?hapus=<?= $row['id_supplier']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus supplier ini?')" class="btn-hapus">Hapus</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>


        <a href="fungsi/tambah_supplier.php" class="btn-tambah">TAMBAH</a>
    </div>
</div>

</body>
</html>

<?php
// Tutup koneksi ke database
mysqli_close($koneksi);
?>