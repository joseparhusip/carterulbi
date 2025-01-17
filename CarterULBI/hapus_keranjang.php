<?php  
include 'config.php'; // Import database connection configuration  
  
session_start(); // Mulai sesi  
  
if (!isset($_SESSION['username'])) {  
    header('Location: login.php'); // Redirect ke halaman login jika belum login  
    exit();  
}  
  
// Cek jika ada id_keranjang yang diterima dari URL  
if (isset($_GET['id_keranjang'])) {  
    $id_keranjang = $_GET['id_keranjang'];  
  
    // Query untuk menghapus item dari keranjang  
    $sql_hapus = "DELETE FROM keranjang WHERE id_keranjang = ?";  
    $stmt_hapus = $koneksi->prepare($sql_hapus);  
    $stmt_hapus->bind_param("i", $id_keranjang); // Pastikan id_keranjang adalah integer  
  
    if ($stmt_hapus->execute()) {  
        // Redirect kembali ke halaman keranjang setelah penghapusan  
        header('Location: utama.php?page=keranjang');  
        exit();  
    } else {  
        echo "<p>Gagal menghapus item dari keranjang.</p>";  
    }  
  
    $stmt_hapus->close();  
} else {  
    echo "<p>ID keranjang tidak ditemukan.</p>";  
}  
  
$koneksi->close();  
?>  
