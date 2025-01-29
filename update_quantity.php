<?php
include "config.php";
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// Query untuk mendapatkan data user berdasarkan username
$query_user = "SELECT id_user FROM user WHERE username = ?";
$stmt_user = $koneksi->prepare($query_user);
$stmt_user->bind_param('s', $username);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

// Periksa jika ada perubahan quantity
if (isset($_POST['quantity'])) {
    foreach ($_POST['quantity'] as $id_produk => $quantity) {
        if (isset($quantity) && is_numeric($quantity) && $quantity > 0) {
            // Update quantity di tabel keranjang
            $query_update = "UPDATE keranjang SET quantity = ? WHERE id_user = ? AND id_produk = ?";
            $stmt_update = $koneksi->prepare($query_update);
            $stmt_update->bind_param('iii', $quantity, $user['id_user'], $id_produk);

            if (!$stmt_update->execute()) {
                echo "Terjadi kesalahan: " . $stmt_update->error;
            }

            $stmt_update->close();
        }
    }
}

header("Location: keranjang.php"); // Kembali ke halaman keranjang
exit;
?>
