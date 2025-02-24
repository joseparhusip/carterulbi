<?php
include 'config.php';

// Handle delete request
if (isset($_POST['delete_rating'])) {
    $id_pesanan = $_POST['id_pesanan'];
    $query_delete = "UPDATE pesanan_makanan SET rating = NULL WHERE id_pesanan = ?";
    $stmt = mysqli_prepare($koneksi, $query_delete);
    mysqli_stmt_bind_param($stmt, 'i', $id_pesanan);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: utama.php?page=ulasanmakanan"); // Refresh halaman setelah delete
    exit();
}

// Query untuk mengambil data pesanan makanan lengkap
$query = "
    SELECT 
        pesanan_makanan.id_pesanan, 
        user.username AS nama_user, 
        produk_makanan.nama_produk,
        driver.username AS nama_driver, 
        driver.plat_nomor, 
        driver.kendaraan, 
        pesanan_makanan.total_harga, 
        pesanan_makanan.rating
    FROM pesanan_makanan
    JOIN user ON pesanan_makanan.id_user = user.id_user
    JOIN produk_makanan ON pesanan_makanan.id_produk = produk_makanan.id_produk
    JOIN driver ON pesanan_makanan.id_driver = driver.id_driver
";

$result = mysqli_query($koneksi, $query);
if (!$result) {
    die("Query gagal: " . mysqli_error($koneksi));
}

if (mysqli_num_rows($result) > 0) {
    $nomor = 1;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ulasan Driver Pemesanan Makanan</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</head>
<body>
    <section class="content-main">
        <div class="content-header">
            <h2 class="content-title card-title">Ulasan Driver Pemesanan Makanan</h2>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama User</th>
                                <th>Nama Produk</th>
                                <th>Nama Driver</th>
                                <th>Plat Nomor</th>
                                <th>Kendaraan</th>
                                <th>Total Harga</th>
                                <th>Rating</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $nomor++ . "</td>";
                            echo "<td>" . htmlspecialchars($row['nama_user']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['nama_produk']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['nama_driver']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['plat_nomor']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['kendaraan']) . "</td>";
                            echo "<td>Rp " . number_format($row['total_harga'], 0, ',', '.') . "</td>";
                            
                            // Menampilkan rating dalam bentuk bintang emas
                            echo "<td>";
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $row['rating']) {
                                    echo "<i class='fas fa-star text-warning'></i>"; // Bintang emas
                                } else {
                                    echo "<i class='far fa-star text-warning'></i>"; // Bintang kosong
                                }
                            }
                            echo "</td>";
                            
                            // Tombol Delete Rating
                            echo "<td>";
                            echo "<form method='POST' onsubmit='return confirm(\"Yakin ingin menghapus rating?\")'>";
                            echo "<input type='hidden' name='id_pesanan' value='" . $row['id_pesanan'] . "'>";
                            echo "<button type='submit' name='delete_rating' class='btn btn-danger btn-sm'>Hapus</button>";
                            echo "</form>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
<?php
} else {
    echo "<div class='alert alert-warning'>Data ulasan tidak ditemukan.</div>";
}
?>
