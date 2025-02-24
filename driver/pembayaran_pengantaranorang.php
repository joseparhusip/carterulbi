<?php
include 'config.php'; // Koneksi ke database

// Periksa apakah session id_driver ada
if (!isset($_SESSION['id_driver'])) {
    die("Anda belum login sebagai driver.");
}

$id_driver = $_SESSION['id_driver'];

// Query untuk mengambil data pengantaran_orang berdasarkan id_driver
$query = "
    SELECT 
        po.id_pengantaran, 
        u.nama AS nama_user, 
        d.nama AS nama_driver, 
        po.titik_antar, 
        po.titik_jemput, 
        po.biaya, 
        po.catatan, 
        po.status, 
        po.rating, 
        po.status_pembayaran
    FROM 
        pengantaran_orang po
    INNER JOIN user u ON po.id_user = u.id_user
    INNER JOIN driver d ON po.id_driver = d.id_driver
    WHERE 
        po.id_driver = ?
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
    $id_pengantaran, 
    $nama_user, 
    $nama_driver, 
    $titik_antar, 
    $titik_jemput, 
    $biaya, 
    $catatan, 
    $status, 
    $rating, 
    $status_pembayaran
);
?>

<section class="content-main">
    <div class="content-header">
        <h2 class="content-title">Data Pengantaran Orang</h2>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID Pengantaran</th>
                            <th>Nama User</th>
                            <th>Nama Driver</th>
                            <th>Titik Antar</th>
                            <th>Titik Jemput</th>
                            <th>Biaya</th>
                            <th>Status Pembayaran</th>
                            <th>Status</th>
                            <th>Catatan</th>
                            <th>Rating</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $has_rows = false;
                        while (mysqli_stmt_fetch($stmt)) {
                            $has_rows = true;
                            $full_stars = intval($rating);
                            $empty_stars = 5 - $full_stars;

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
                                <td><?php echo htmlspecialchars($id_pengantaran); ?></td>
                                <td><?php echo htmlspecialchars($nama_user); ?></td>
                                <td><?php echo htmlspecialchars($nama_driver); ?></td>
                                <td><?php echo htmlspecialchars($titik_antar); ?></td>
                                <td><?php echo htmlspecialchars($titik_jemput); ?></td>
                                <td><?php echo htmlspecialchars(number_format($biaya, 2)); ?></td>
                                <td class="<?php echo $status_pembayaran_color; ?>">
                                    <?php echo htmlspecialchars($status_pembayaran); ?>
                                </td>
                                <td><?php echo htmlspecialchars($status); ?></td>
                                <td><?php echo htmlspecialchars($catatan); ?></td>
                                <td>
                                    <?php
                                    for ($i = 0; $i < $full_stars; $i++) {
                                        echo '<span class="star filled">&#9733;</span>';
                                    }
                                    for ($i = 0; $i < $empty_stars; $i++) {
                                        echo '<span class="star empty">&#9734;</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php
                        }
                        if (!$has_rows) {
                            echo "<tr><td colspan='10' class='text-center'>Data pengantaran tidak ditemukan.</td></tr>";
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
    .star {
        font-size: 1.2em;
    }

    .star.filled {
        color: gold;
    }

    .star.empty {
        color: lightgray;
    }

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