<?php  
// Include konfigurasi database  
include 'config.php';  
  
// Pastikan user sudah login  
if (!isset($_SESSION['username'])) {  
    header('Location: login.php'); // Arahkan ke halaman login jika belum login  
    exit;  
}  
  
// Ambil id_keranjang dari parameter URL  
if (isset($_GET['id_keranjang'])) {  
    $id_keranjang = $_GET['id_keranjang'];  
  
    // Query untuk menghapus item dari keranjang berdasarkan id_keranjang  
    $query_delete = "DELETE FROM keranjang WHERE id_keranjang = ?";  
    $stmt_delete = $koneksi->prepare($query_delete);  
    $stmt_delete->bind_param('i', $id_keranjang);  
  
    if ($stmt_delete->execute()) {  
        // Item berhasil dihapus  
        header('Location: utama.php?page=keranjang'); // Redirect kembali ke halaman keranjang  
        exit;  
    } else {  
        // Terjadi kesalahan saat menghapus item  
        echo "Terjadi kesalahan saat menghapus item dari keranjang: " . $stmt_delete->error;  
    }  
  
    $stmt_delete->close();  
} else {  
    // id_keranjang tidak ditemukan  
    echo "ID keranjang tidak ditemukan.";  
}  
?>  
