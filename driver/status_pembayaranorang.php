<?php
session_start(); // Mulai session
include 'config.php';

// Periksa apakah session id_driver sudah ada
if (!isset($_SESSION['id_driver'])) {
    die("Session id_driver tidak ditemukan. Silakan login terlebih dahulu.");
}

// Ambil id_driver dari session
$id_driver = $_SESSION['id_driver'];

// Ambil data dari POST
$id_pengantaran = $_POST['id_pengantaran'];
$status_pembayaran = $_POST['status_pembayaran'];

// Query untuk memperbarui status pembayaran
$query = "UPDATE pengantaran_orang SET status_pembayaran = ? WHERE id_pengantaran = ? AND id_driver = ?";
$stmt = mysqli_prepare($koneksi, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "sii", $status_pembayaran, $id_pengantaran, $id_driver);
    if (mysqli_stmt_execute($stmt)) {
        // Redirect kembali ke halaman utama dengan parameter page
        header("Location: utama.php?page=pembayaran_orang");
        exit();
    } else {
        echo "Gagal memperbarui status pembayaran.";
    }
    mysqli_stmt_close($stmt);
} else {
    echo "Gagal mempersiapkan query.";
}

mysqli_close($koneksi);
?>