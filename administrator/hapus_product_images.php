<?php
// Koneksi ke database
include("config.php");

// Cek apakah parameter id ada
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Tambahkan validasi dan sanitasi input
    if (!is_numeric($id)) {
        echo "<script>
            alert('ID tidak valid!');
            window.location.href='utama.php?page=product_images';
        </script>";
        exit;
    }
    
    $id = mysqli_real_escape_string($koneksi, $id);
    
    // Dapatkan path gambar sebelum menghapus record
    $query_get_image = "SELECT image_path FROM product_images WHERE id = '$id'";
    $result_image = mysqli_query($koneksi, $query_get_image);
    
    if ($result_image && mysqli_num_rows($result_image) > 0) {
        $row = mysqli_fetch_assoc($result_image);
        $image_path = "../gambarfood/" . $row['image_path'];
        
        // Hapus data dari database terlebih dahulu
        $query_delete = "DELETE FROM product_images WHERE id = '$id'";
        $delete_result = mysqli_query($koneksi, $query_delete);
        
        if ($delete_result) {
            // Jika data berhasil dihapus, coba hapus file fisiknya
            if (file_exists($image_path) && !empty($row['image_path'])) {
                unlink($image_path);
            }
            
            echo "<script>
                alert('Gambar produk berhasil dihapus!');
                window.location.href='utama.php?page=product_images';
            </script>";
        } else {
            echo "<script>
                alert('Gagal menghapus data: " . mysqli_error($koneksi) . "');
                window.location.href='utama.php?page=product_images';
            </script>";
        }
    } else {
        echo "<script>
            alert('Data tidak ditemukan!');
            window.location.href='utama.php?page=product_images';
        </script>";
    }
} else {
    echo "<script>
        alert('ID tidak ditemukan!');
        window.location.href='utama.php?page=product_images';
    </script>";
}
?>