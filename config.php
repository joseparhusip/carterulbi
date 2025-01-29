<?php
$host = "localhost";     // atau bisa juga "127.0.0.1"
$user = "root";          // biasanya "root" untuk XAMPP
$password = "";          // biasanya kosong jika menggunakan XAMPP default
$database = "carterulbi";  // pastikan nama database benar
$koneksi = mysqli_connect($host, $user, $password, $database);

if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}
?>
