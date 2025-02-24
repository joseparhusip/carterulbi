<?php
include 'config.php'; // Koneksi ke database

// Periksa apakah session id_driver ada
if (!isset($_SESSION['id_driver'])) {
    die("Anda belum login sebagai driver.");
}

$id_driver = $_SESSION['id_driver'];

// Query untuk mengambil data pesanan_makanan berdasarkan id_driver
$query = "
    SELECT 
        pm.id_pesanan, 
        pm.status_pembayaran, 
        pm.bukti_pembayaran, 
        pm.id_metode_pembayaran, 
        mp.nama_metode
    FROM 
        pesanan_makanan pm
    INNER JOIN metode_pembayaran mp ON pm.id_metode_pembayaran = mp.id_metode_pembayaran
    WHERE 
        pm.id_driver = ?
";

// Menggunakan prepared statement
$stmt = mysqli_prepare($koneksi, $query);

if (!$stmt) {
    die("Query gagal: " . mysqli_error($koneksi));
}

// Bind parameter
mysqli_stmt_bind_param($stmt, "i", $id_driver);

// Eksekusi query
mysqli_stmt_execute($stmt);

// Bind result variables
mysqli_stmt_bind_result($stmt, 
    $id_pesanan, 
    $status_pembayaran, 
    $bukti_pembayaran, 
    $id_metode_pembayaran, 
    $nama_metode
);
?>

<section class="content-main">
    <div class="content-header">
        <h2 class="content-title">Data Pembayaran Pesanan Makanan</h2>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID Pesanan</th>
                            <th>Status Pembayaran</th>
                            <th>Bukti Pembayaran</th>
                            <th>Metode Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $has_rows = false;
                        while (mysqli_stmt_fetch($stmt)) {
                            $has_rows = true;
                            
                            // Menentukan warna untuk status pembayaran
                            $status_pembayaran_color = '';
                            switch ($status_pembayaran) {
                                case 'PENDING':
                                    $status_pembayaran_color = 'text-secondary';
                                    break;
                                case 'PAID':
                                    $status_pembayaran_color = 'text-success';
                                    break;
                                case 'FAILED':
                                    $status_pembayaran_color = 'text-danger';
                                    break;
                                case 'REFUNDED':
                                    $status_pembayaran_color = 'text-warning';
                                    break;
                                default:
                                    $status_pembayaran_color = 'text-secondary';
                            }
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($id_pesanan); ?></td>
                                <td class="<?php echo $status_pembayaran_color; ?>">
                                    <?php echo htmlspecialchars($status_pembayaran); ?>
                                </td>
                                <td><?php echo htmlspecialchars($bukti_pembayaran); ?></td>
                                <td><?php echo htmlspecialchars($nama_metode); ?></td>
                            </tr>
                        <?php
                        }
                        if (!$has_rows) {
                            echo "<tr><td colspan='4' class='text-center'>Data pesanan makanan tidak ditemukan.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php
// Tutup statement dan koneksi
mysqli_stmt_close($stmt);
mysqli_close($koneksi);
?>

<style>
    .table {
        width: 100%;
        margin: 20px 0;
        border-collapse: collapse;
    }

    .table th, .table td {
        padding: 12px;
        text-align: left;
        border: 1px solid #ddd;
    }

    .table th {
        background-color: #343a40;
        color: white;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #f9f9f9;
    }

    .table-striped tbody tr:hover {
        background-color: #f1f1f1;
    }

    .content-title {
        font-size: 24px;
        margin-bottom: 20px;
    }

    .card {
        border: 1px solid #ddd;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .card-body {
        padding: 20px;
    }

    .text-secondary {
        color: #6c757d;
    }

    .text-success {
        color: #28a745;
    }

    .text-danger {
        color: #dc3545;
    }

    .text-warning {
        color: #ffc107;
    }
</style>