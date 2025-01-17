<?php
include 'config.php';

// Query untuk mengambil data ulasan lengkap dengan informasi pengguna, driver, dan pesanan
$query = "
    SELECT 
        ulasan.id_ulasan, 
        user.nama AS nama_user, 
        driver.nama AS nama_driver, 
        driver.plat_nomor, 
        driver.kendaraan, 
        pesanan_makanan.total_harga, 
        ulasan.rating, 
        ulasan.komentar, 
        ulasan.created_at AS tanggal_ulasan
    FROM ulasan
    JOIN user ON ulasan.id_user = user.id_user
    JOIN driver ON ulasan.id_driver = driver.id_driver
    JOIN pesanan_makanan ON ulasan.id_pesanan = pesanan_makanan.id_pesanan
";

$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Query gagal: " . mysqli_error($koneksi)); // Menampilkan error jika query gagal
}

if (mysqli_num_rows($result) > 0) {
    // Inisialisasi nomor urut
    $nomor = 1;
?>
<section class="content-main">
    <div class="content-header">
        <div>
            <h2 class="content-title card-title">Ulasan Driver</h2>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Ulasan</th>
                            <th>Nama User</th>
                            <th>Nama Driver</th>
                            <th>Plat Nomor</th>
                            <th>Kendaraan</th>
                            <th>Total Harga</th>
                            <th>Rating</th>
                            <th>Komentar</th>
                            <th>Tanggal Ulasan</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    // Menampilkan data ulasan dengan penomoran dinamis
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Menampilkan rating sebagai bintang
                        $rating = (int)$row['rating']; // Pastikan rating berupa angka
                        $stars = '';
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $rating) {
                                $stars .= '<i class="fa fa-star text-warning"></i>'; // Bintang penuh
                            } else {
                                $stars .= '<i class="fa fa-star text-secondary"></i>'; // Bintang kosong
                            }
                        }
                    ?>
                        <tr>
                            <td><?php echo $nomor++; ?></td>
                            <td><?php echo htmlspecialchars($row['id_ulasan']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_user']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_driver']); ?></td>
                            <td><?php echo htmlspecialchars($row['plat_nomor']); ?></td>
                            <td><?php echo htmlspecialchars($row['kendaraan']); ?></td>
                            <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                            <td><?php echo $stars; ?></td>
                            <td><?php echo htmlspecialchars($row['komentar']); ?></td>
                            <td><?php echo htmlspecialchars($row['tanggal_ulasan']); ?></td>
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?php
} else {
    echo "<div class='alert alert-warning'>Data ulasan tidak ditemukan.</div>";
}
?>

<!-- Tambahkan link Font Awesome di bagian header HTML -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
<script src="../assets/js/vendors/jquery-3.6.0.min.js"></script>
<script src="../assets/js/vendors/bootstrap.bundle.min.js"></script>
<script src="../assets/js/vendors/select2.min.js"></script>
<script src="../assets/js/vendors/perfect-scrollbar.js"></script>
<script src="../assets/js/vendors/jquery.fullscreen.min.js"></script>
<script src="../assets/js/main.js" type="text/javascript"></script>
</body>
</html>
