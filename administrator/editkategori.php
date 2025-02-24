<?php
// Koneksi ke database
include("config.php");

$nama_kategori = '';
$error_message = '';

// Periksa apakah parameter id_kategori ada dalam URL
if (isset($_GET['id_kategori'])) {
    $id_kategori = $_GET['id_kategori'];
    
    // Query untuk mendapatkan data kategori berdasarkan id_kategori
    $query = "SELECT nama_kategori FROM kategori_produk WHERE id_kategori = ?";
    if ($stmt = mysqli_prepare($koneksi, $query)) {
        mysqli_stmt_bind_param($stmt, "i", $id_kategori);
        mysqli_stmt_execute($stmt);
        
        // Bind result
        mysqli_stmt_bind_result($stmt, $nama_kategori);
        
        // Fetch value
        if (!mysqli_stmt_fetch($stmt)) {
            $error_message = "Kategori tidak ditemukan.";
        }
        
        mysqli_stmt_close($stmt);
    } else {
        $error_message = "Error preparing statement: " . mysqli_error($koneksi);
    }
} else {
    $error_message = "ID Kategori tidak ditemukan.";
}

// Proses update jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($error_message)) {
    $new_nama_kategori = $_POST['nama_kategori'];
    
    // Query untuk memperbarui data kategori
    $update_query = "UPDATE kategori_produk SET nama_kategori = ? WHERE id_kategori = ?";
    if ($update_stmt = mysqli_prepare($koneksi, $update_query)) {
        mysqli_stmt_bind_param($update_stmt, "si", $new_nama_kategori, $id_kategori);
        
        if (mysqli_stmt_execute($update_stmt)) {
            // Redirect ke halaman kategori setelah berhasil diupdate
            header("Location: utama.php?page=kategori");
            exit();
        } else {
            $error_message = "Error updating category: " . mysqli_error($koneksi);
        }
        
        mysqli_stmt_close($update_stmt);
    } else {
        $error_message = "Error preparing update statement: " . mysqli_error($koneksi);
    }
}

// Jika ada error, tampilkan pesan error
if (!empty($error_message)) {
    echo $error_message;
    echo "<br><a href='utama.php?page=kategori' class='btn btn-primary'>Kembali</a>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kategori</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Edit Kategori</h2>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="nama_kategori" class="form-label">Nama Kategori</label>
                <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" 
                       value="<?php echo htmlspecialchars($nama_kategori); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="utama.php?page=kategori" class="btn btn-secondary">Batal</a>
        </form>
    </div>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>