<?php
include 'config.php'; // Pastikan sudah terkoneksi ke database

// Cek koneksi database
if (!$koneksi) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    echo "";
}

// Cek jika form dikirimkan untuk menambahkan data baru
if (isset($_POST['submit'])) {
    $nama_kategori = $_POST['nama_kategori'];

    if (!empty($nama_kategori)) {
        // Query untuk menambahkan data baru ke tabel kategori_produk
        $query_insert = "INSERT INTO kategori_produk (nama_kategori) VALUES (?)";
        
        if ($stmt = mysqli_prepare($koneksi, $query_insert)) {
            mysqli_stmt_bind_param($stmt, "s", $nama_kategori);
            
            if (mysqli_stmt_execute($stmt)) {
                echo '<script>alert("Berhasil menambahkan kategori."); document.location="utama.php?page=kategori";</script>';
            } else {
                echo '<div style="color:red">Gagal menambahkan kategori. Error: ' . mysqli_stmt_error($stmt) . '</div>';
            }
            mysqli_stmt_close($stmt);
        } else {
            echo '<div style="color:red">Gagal menyiapkan query. Error: ' . mysqli_error($koneksi) . '</div>';
        }
    } else {
        echo '<div style="color:red">Nama kategori tidak boleh kosong.</div>';
    }
}
?>

<form action="" method="post">
<section class="content-main">
    <div class="row">
        <div class="col-9">
            <div class="content-header">
                <h2 class="content-title">Tambah Kategori</h2>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Input Kategori</h4>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label">Kategori</label>
                        <textarea name="nama_kategori" placeholder="Type here" class="form-control" rows="4"></textarea>
                    </div>
                </div>
            </div>
            <div>
                <!-- Button submit untuk tambah kategori -->
                <button type="submit" name="submit" class="btn btn-md rounded font-sm hover-up">Save</button>
                <a href="utama.php?page=kategori" class="btn btn-light rounded font-sm mr-5 text-body hover-up">Back</a>
            </div>
        </div>
    </div>
</section>
</form>
