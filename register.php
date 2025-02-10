<?php
session_start();
include 'config.php';
include 'session.php';

if (!isAdmin()) {
    header("Location: login.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
    $role = $_POST['level'];

    // Validasi username unik
    $stmt = $koneksi->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "<script>alert('Username sudah digunakan!');</script>";
    } else {
        // Simpan user baru ke database
        $stmt = $koneksi->prepare("INSERT INTO users (username, password, level) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password, $role);

        if ($stmt->execute()) {
            echo "<script>alert('Akun berhasil dibuat!'); window.location.href='dashboard.php';</script>";
        } else {
            echo "<script>alert('Gagal membuat akun!');</script>";
        }
    }
}

// Ambil data user dari database
$query_users = "SELECT * FROM users ORDER BY id_user DESC";
$result_users = $koneksi->query($query_users);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Pengguna</title>
    <link rel="stylesheet" href="style.css">
    <style>
      body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin-left: 60px auto;
            padding: 50px;
            background-color: #fff;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        h2 {
            color: #333;
            margin-top: 30px;
            margin-bottom: 15px;
        }

        .form-register {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .form-group label {
            font-weight: bold;
            color: #555;
        }

        .form-group input,
        .form-group select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #007bff;
            outline: none;
        }

        .btn-submit,
        .btn-kembali {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-submit {
            background-color: #007bff;
            color: #fff;
        }

        .btn-submit:hover {
            background-color: #0056b3;
        }

        .btn-kembali {
            background-color: #6c757d;
            color: #fff;
            text-decoration: none;
            text-align: center;
        }

        .btn-kembali:hover {
            background-color: #5a6268;
        }

        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .user-table th,
        .user-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .user-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }

        .user-table tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <?php include 'fungsi/navbar.php'; ?>
    <div class="container">
           <a href="fungsi/tambah_user.php" class="btn-tambah">Daftar</a>

        <h2>Daftar Pengguna Terdaftar</h2>
        <table class="user-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>nama</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_users->num_rows > 0) {
                    while ($row = $result_users->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['id_user']}</td>
                                <td>{$row['name']}</td>
                                <td>{$row['username']}</td>
                                <td>" . ucfirst($row['level']) . "</td>
                                <td>
                                    <a href='fungsi/delete_user.php?id={$row['id_user']}' class='btn-hapus' 
                                       onclick='return confirm(\"Yakin ingin menghapus user ini?\")'>
                                        Hapus
                                    </a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Belum ada user terdaftar</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
