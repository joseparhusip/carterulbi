<?php
// Koneksi ke database
include("config.php");

// Periksa apakah parameter id_produk ada dalam URL
if (isset($_GET['id_produk'])) {
    $id_produk = $_GET['id_produk'];

    // Query untuk menghapus data produk berdasarkan id_produk
    $query = "DELETE FROM produk_makanan WHERE id_produk = ?";
    
    // Gunakan prepared statement untuk mencegah SQL Injection
    if ($stmt = mysqli_prepare($koneksi, $query)) {
        // Bind parameter ke query
        mysqli_stmt_bind_param($stmt, "i", $id_produk);

        // Eksekusi query
        if (mysqli_stmt_execute($stmt)) {
            // Penghapusan berhasil, redirect ke halaman produk
            header("Location: utama.php?page=produk");
            exit();
        } else {
            // Jika terjadi kesalahan saat eksekusi query
            echo "Error: " . mysqli_error($koneksi);
        }

        // Tutup statement
        mysqli_stmt_close($stmt);
    } else {
        // Jika gagal membuat prepared statement
        echo "Error preparing statement: " . mysqli_error($koneksi);
    }
} else {
    // Jika parameter id_produk tidak ditemukan
    echo "ID Produk tidak ditemukan.";
}

// Tutup koneksi database
mysqli_close($koneksi);
?>