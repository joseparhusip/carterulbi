<?php
// Koneksi ke database
include("config.php");

// Periksa apakah parameter id_kategori ada dalam URL
if (isset($_GET['id_kategori'])) {
    $id_kategori = $_GET['id_kategori'];

    // Query untuk menghapus data kategori berdasarkan id_kategori
    $query = "DELETE FROM kategori_produk WHERE id_kategori = ?";
    
    // Gunakan prepared statement untuk mencegah SQL Injection
    if ($stmt = mysqli_prepare($koneksi, $query)) {
        // Bind parameter ke query
        mysqli_stmt_bind_param($stmt, "i", $id_kategori);

        // Eksekusi query
        if (mysqli_stmt_execute($stmt)) {
            // Penghapusan berhasil, redirect ke halaman kategori
            header("Location: utama.php?page=kategori");
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
    // Jika parameter id_kategori tidak ditemukan
    echo "ID Kategori tidak ditemukan.";
}

// Tutup koneksi database
mysqli_close($koneksi);
?>