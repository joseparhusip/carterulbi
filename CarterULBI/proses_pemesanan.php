<?php
include "config.php";
// Cek koneksi            
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Cek apakah user sudah login            
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// Query untuk mendapatkan data user berdasarkan username            
$query_user = "SELECT id_user, nama FROM user WHERE username = ?";
$stmt_user = $koneksi->prepare($query_user);
$stmt_user->bind_param('s', $username);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

// Mendapatkan id_driver dari request atau default            
$id_driver = isset($_POST['id_driver']) ? $_POST['id_driver'] : 1;

// Mengambil data dari form
$titik_antar = $_POST['titik_antar'];
$titik_jemput = $_POST['titik_jemput'];
$biaya = $_POST['biaya'];
$catatan = isset($_POST['catatan']) ? $_POST['catatan'] : null;

// Query untuk menyimpan data pemesanan ke tabel pengantaran_orang
$query = "INSERT INTO pengantaran_orang (id_user, id_driver, titik_antar, titik_jemput, biaya, catatan) 
          VALUES (?, ?, ?, ?, ?, ?)";

// Persiapkan statement
$stmt = $koneksi->prepare($query);

// Bind parameter
$stmt->bind_param("iissds", $user['id_user'], $id_driver, $titik_antar, $titik_jemput, $biaya, $catatan);

// Eksekusi query
if ($stmt->execute()) {
    // Jika berhasil, tampilkan pesan dan arahkan kembali ke halaman utama
    header("Location: utama.php?page=service"); // Redirect ke halaman service
    exit;
} else {
    // Jika gagal, tampilkan pesan error
    echo "Terjadi kesalahan: " . $stmt->error;
}

// Tutup statement dan koneksi
$stmt->close();
$koneksi->close();
?>
