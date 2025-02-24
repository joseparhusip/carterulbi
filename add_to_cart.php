<?php
include 'config.php'; // Import database connection configuration

session_start(); // Start the session

if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

// Ambil username dari session
$username = $_SESSION['username'];

// Query untuk mengambil id_user berdasarkan username
$sql_user = "SELECT id_user FROM user WHERE username = ?";
$stmt_user = $koneksi->prepare($sql_user);
$stmt_user->bind_param("s", $username);
$stmt_user->execute();
$stmt_user->bind_result($id_user_result); // Menggunakan bind_result

if ($stmt_user->fetch()) {
    $id_user = $id_user_result; // Menyimpan hasil ke variabel
    $_SESSION['id_user'] = $id_user; // Simpan id_user ke dalam session
    $stmt_user->close(); // Tutup statement sebelum membuat statement baru
} else {
    $stmt_user->close();
    echo "<p>ID pengguna tidak ditemukan. Silakan login kembali.</p>";
    exit();
}

// Ambil data dari request POST
$id_produk = $_POST['id_produk'];
$quantity = $_POST['quantity'];
$harga = $_POST['harga'];
$gambar = $_POST['gambar'];
$nama_produk = $_POST['nama_produk'];
$total_harga = $_POST['total_harga']; // Pastikan ini adalah string jika diperlukan

// Insert into keranjang table
$sql = "INSERT INTO keranjang (id_user, id_produk, quantity, harga, total_harga, gambar, nama_produk) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $koneksi->prepare($sql);

// Pastikan tipe data sesuai
$stmt->bind_param("iiidsss", $id_user, $id_produk, $quantity, $harga, $total_harga, $gambar, $nama_produk);

if ($stmt->execute()) {
    // Redirect to keranjang page after successful insertion
    header('Location: utama.php?page=keranjang');
    exit();
} else {
    echo "<p>Gagal menambahkan produk ke keranjang.</p>";
}

$stmt->close();
$koneksi->close();
?>