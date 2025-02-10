<?php
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['id_user'])) {
    die("Error: Anda harus login terlebih dahulu.");
}

// Cek apakah pengguna adalah admin
function isAdmin() {
    // Misalnya, cek apakah user yang login adalah admin
    return isset($_SESSION['level']) && $_SESSION['level'] == 'admin';
}

function isPetugas() {
    // Misalnya, cek apakah user yang login adalah petugas
    return isset($_SESSION['level']) && $_SESSION['level'] == 'petugas';
}
