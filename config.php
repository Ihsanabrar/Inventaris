<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'inventaris';

$koneksi = new mysqli ($host, $user, $pass, $db) ;

if ($koneksi->connect_error){
    die("Gagal" . $koneksi->connect_error);
}



?>