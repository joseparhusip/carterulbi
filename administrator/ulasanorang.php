<?php
include 'config.php';

// Jika tombol hapus diklik
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_pengantaran'])) {
    $id_pengantaran = $_POST['id_pengantaran'];

    // Query untuk menghapus rating (set ke NULL)
    $query = "UPDATE pengantaran_orang SET rating = NULL WHERE id_pengantaran = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id_pengantaran);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Rating berhasil dihapus.'); window.location.href='utama.php?page=ulasanorang';</script>";
        } else {
            echo "<script>alert('Gagal menghapus rating.'); window.history.back();</script>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Terjadi kesalahan pada query.'); window.history.back();</script>";
    }
}

// Query untuk mengambil data pengantaran
$query = "
    SELECT 
        pengantaran_orang.id_pengantaran, 
        user.username AS nama_user, 
        driver.username AS nama_driver, 
        driver.plat_nomor, 
        driver.kendaraan, 
        pengantaran_orang.biaya AS total_harga, 
        pengantaran_orang.rating
    FROM pengantaran_orang
    JOIN user ON pengantaran_orang.id_user = user.id_user
    JOIN driver ON pengantaran_orang.id_driver = driver.id_driver
";

$result = mysqli_query($koneksi, $query);
if (!$result) {
    die("Query gagal: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ulasan Driver Pengantaran Orang</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</head>
<body>
    <section class="content-main">
        <div class="content-header">
            <h2 class="content-title">Ulasan Driver Pengantaran Orang</h2>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama User</th>
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
                        $nomor = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $nomor++ . "</td>";
                            echo "<td>" . htmlspecialchars($row['nama_user']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['nama_driver']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['plat_nomor']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['kendaraan']) . "</td>";
                            echo "<td>Rp " . number_format($row['total_harga'], 0, ',', '.') . "</td>";
                            
                            echo "<td>";
                            for ($i = 0; $i < 5; $i++) {
                                if ($i < $row['rating']) {
                                    echo "<i class='fas fa-star text-warning'></i> ";
                                } else {
                                    echo "<i class='far fa-star text-warning'></i> ";
                                }
                            }
                            echo "</td>";
                            
                            echo "<td>";
                            echo "<form method='POST' action=''>";
                            echo "<input type='hidden' name='id_pengantaran' value='" . $row['id_pengantaran'] . "'>";
                            echo "<button type='submit' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus rating ini?\")'>Hapus</button>";
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
