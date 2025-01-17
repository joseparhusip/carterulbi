<?php
include 'config.php'; // Koneksi ke database

// Query untuk mengambil data pengantaran orang dengan metode pembayaran dari tabel pembayaran
$query = "
    SELECT 
        po.id_pengantaran, 
        u.nama AS nama_user, 
        d.nama AS nama_driver, 
        d.plat_nomor, 
        d.kendaraan, 
        po.titik_jemput, 
        po.titik_antar, 
        po.jarak_km, 
        po.biaya, 
        p.metode_pembayaran, 
        po.status_pengantaran
    FROM 
        pengantaran_orang po
    INNER JOIN user u ON po.id_user = u.id_user
    INNER JOIN driver d ON po.id_driver = d.id_driver
    INNER JOIN pembayaran p ON po.id_pembayaran = p.id_pembayaran
";

$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Query gagal: " . mysqli_error($koneksi));
}
?>

<section class="content-main">
    <div class="content-header">
        <h2 class="content-title">Pengantaran Orang</h2>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID Pengantaran</th>
                            <th>Nama User</th>
                            <th>Nama Driver</th>
                            <th>Plat Motor</th>
                            <th>Kendaraan</th>
                            <th>Titik Jemput</th>
                            <th>Titik Antar</th>
                            <th>Jarak (km)</th>
                            <th>Biaya</th>
                            <th>Metode Pembayaran</th>
                            <th>Status Pengantaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id_pengantaran']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_user']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_driver']); ?></td>
                                    <td><?php echo htmlspecialchars($row['plat_nomor']); ?></td>
                                    <td><?php echo htmlspecialchars($row['kendaraan']); ?></td>
                                    <td><?php echo htmlspecialchars($row['titik_jemput']); ?></td>
                                    <td><?php echo htmlspecialchars($row['titik_antar']); ?></td>
                                    <td><?php echo htmlspecialchars($row['jarak_km']); ?></td>
                                    <td><?php echo htmlspecialchars(number_format($row['biaya'], 2)); ?></td>
                                    <td><?php echo htmlspecialchars($row['metode_pembayaran']); ?></td>
                                    <td>
                                        <select class="form-select form-select-sm status-dropdown text-white"
                                            data-id="<?php echo $row['id_pengantaran']; ?>"
                                            style="background-color: <?php echo getStatusColor($row['status_pengantaran']); ?>;">
                                            <option value="Menunggu" <?php echo ($row['status_pengantaran'] === 'Menunggu') ? 'selected' : ''; ?>>Menunggu</option>
                                            <option value="Dijemput" <?php echo ($row['status_pengantaran'] === 'Dijemput') ? 'selected' : ''; ?>>Dijemput</option>
                                            <option value="Dianter" <?php echo ($row['status_pengantaran'] === 'Dianter') ? 'selected' : ''; ?>>Dianter</option>
                                            <option value="Selesai" <?php echo ($row['status_pengantaran'] === 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                                            <option value="Dibatalkan" <?php echo ($row['status_pengantaran'] === 'Dibatalkan') ? 'selected' : ''; ?>>Dibatalkan</option>
                                        </select>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='11' class='text-center'>Data pengantaran tidak ditemukan.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    $(document).on('change', '.status-dropdown', function() {
        const idPengantaran = $(this).data('id');
        const statusPengantaran = $(this).val();

        $.ajax({
            url: 'update_status_pengantaran.php',
            method: 'POST',
            data: {
                id_pengantaran: idPengantaran,
                status_pengantaran: statusPengantaran
            },
            success: function(response) {
                if (response === "success") {
                    alert('Status pengantaran berhasil diperbarui!');
                    location.reload(); // Reload halaman untuk memperbarui tampilan
                } else {
                    alert('Gagal memperbarui status!');
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat memperbarui status pengantaran.');
            }
        });
    });
</script>

<?php
function getStatusColor($status)
{
    switch ($status) {
        case 'Menunggu':
            return '#6c757d';
        case 'Dijemput':
            return '#ffc107';
        case 'Dianter':
            return '#0d6efd';
        case 'Selesai':
            return '#198754';
        case 'Dibatalkan':
            return '#dc3545';
        default:
            return '#6c757d';
    }
}
?>