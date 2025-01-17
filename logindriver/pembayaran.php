<?php
include 'config.php';

// Pastikan user sudah login
if (!isset($_SESSION['id_driver'])) {
    header("Location: login.php"); // Redirect ke halaman login jika tidak ada sesi
    exit;
}

// Query untuk mendapatkan data pembayaran driver
$query = "SELECT 
            pd.id_pembayaran,
            d.nama AS nama_driver,
            u.nama AS nama_user,
            pd.jumlah_bayar,
            pd.metode_pembayaran,
            pd.tanggal_pembayaran,
            pd.status_pembayaran,
            pd.bukti_transaksi,
            pd.catatan
          FROM pembayaran pd
          INNER JOIN driver d ON pd.id_driver = d.id_driver
          INNER JOIN user u ON pd.id_driver = u.id_user";

$result = mysqli_query($koneksi, $query);
if (!$result) {
    die("Query gagal: " . mysqli_error($koneksi));
}

// Fungsi untuk memberi warna pada status pembayaran
function getStatusBadge($status)
{
    switch (strtolower($status)) {
        case 'pending':
            return '<span class="badge bg-warning text-dark">Pending</span>';
        case 'selesai':
            return '<span class="badge bg-success">Selesai</span>';
        case 'dibatalkan':
            return '<span class="badge bg-danger">Dibatalkan</span>';
        default:
            return '<span class="badge bg-secondary">Tidak Diketahui</span>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Pembayaran Driver</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">List Pembayaran Driver</h4>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($result) > 0) { ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>ID Pembayaran</th>
                                    <th>Nama Driver</th>
                                    <th>Nama User</th>
                                    <th>Jumlah Bayar</th>
                                    <th>Metode Pembayaran</th>
                                    <th>Tanggal Pembayaran</th>
                                    <th>Status Pembayaran</th>
                                    <th>Bukti Transaksi</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id_pembayaran']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_driver']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_user']); ?></td>
                                        <td>Rp <?php echo number_format($row['jumlah_bayar'], 0, ',', '.'); ?></td>
                                        <td><?php echo htmlspecialchars($row['metode_pembayaran']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tanggal_pembayaran']); ?></td>
                                        <td><?php echo getStatusBadge($row['status_pembayaran']); ?></td>
                                        <td>
                                            <?php if (!empty($row['bukti_transaksi'])) { ?>
                                                <a href="../buktitransaksi/<?php echo htmlspecialchars($row['bukti_transaksi']); ?>" target="_blank">Lihat Bukti</a>
                                            <?php } else { ?>
                                                Tidak Ada Bukti
                                            <?php } ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['catatan']); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } else { ?>
                    <p class="text-center">Tidak ada data pembayaran driver.</p>
                <?php } ?>
            </div>
        </div>
        <a href="utama.php" class="btn btn-primary mt-3">Kembali</a>
    </div>

    <script src="../assets/js/vendors/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/vendors/bootstrap.bundle.min.js"></script>
</body>

</html>
