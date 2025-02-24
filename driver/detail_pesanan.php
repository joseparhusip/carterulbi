<?php
include 'config.php'; // Koneksi ke database

// Periksa apakah session id_driver ada
if (!isset($_SESSION['id_driver'])) {
    die("Anda belum login sebagai driver.");
}

$id_driver = $_SESSION['id_driver'];

// Query untuk mengambil data dari tabel detail_pesanan berdasarkan id_driver
$query = "
    SELECT 
        dp.id_detail_pesanan, 
        dp.id_pengantaran, 
        dp.id_pesanan
    FROM 
        detail_pesanan dp
    INNER JOIN 
        pesanan_makanan pm ON dp.id_pesanan = pm.id_pesanan
    INNER JOIN 
        driver d ON pm.id_driver = d.id_driver
    WHERE 
        d.id_driver = ?
";

// Menggunakan prepared statement
$stmt = mysqli_prepare($koneksi, $query);
if (!$stmt) {
    die("Query gagal: " . mysqli_error($koneksi));
}

// Bind parameter
mysqli_stmt_bind_param($stmt, "i", $id_driver); // Menggunakan 'i' untuk integer id_driver

// Eksekusi query
mysqli_stmt_execute($stmt);

// Bind result variables
mysqli_stmt_bind_result($stmt, $id_detail_pesanan, $id_pengantaran, $id_pesanan);

// Variabel untuk menghitung jumlah data
$data_found = false;

// Fetch dan tampilkan hasil
while (mysqli_stmt_fetch($stmt)) {
    $data_found = true;
    ?>
    <div class="detail-pesanan">
        <p>ID Detail Pesanan: <?php echo htmlspecialchars($id_detail_pesanan); ?></p>
        <p>ID Pengantaran: <?php echo htmlspecialchars($id_pengantaran); ?></p>
        <p>ID Pesanan: <?php echo htmlspecialchars($id_pesanan); ?></p>
        <hr>
    </div>
    <?php
}

// Jika tidak ada data yang ditemukan
if (!$data_found) {
    echo "<div class='alert alert-info'>Data detail pesanan tidak ditemukan.</div>";
}

// Tutup statement dan koneksi
mysqli_stmt_close($stmt);
mysqli_close($koneksi);
?>
<style>
.detail-pesanan {
    padding: 15px;
    margin-bottom: 10px;
    background-color: #f8f9fa;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.detail-pesanan p {
    margin: 5px 0;
    color: #333;
}
.detail-pesanan hr {
    margin: 10px 0;
    border-color: #dee2e6;
}
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
}
.alert-info {
    color: #0c5460;
    background-color: #d1ecf1;
    border-color: #bee5eb;
}
</style>