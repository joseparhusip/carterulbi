<?php
include 'config.php'; // Koneksi ke database

// Query untuk mengambil data pesanan makanan dengan metode pembayaran
$query = "
    SELECT 
        pm.id_pesanan, 
        u.nama AS nama_user, 
        d.nama AS nama_driver, 
        d.plat_nomor, 
        p.metode_pembayaran, 
        p.bukti_transaksi, 
        pm.alamat_pengiriman, 
        pm.total_harga, 
        pm.status_pesanan, 
        pm.catatan, 
        pm.estimasi_waktu
    FROM 
        pesanan_makanan pm
    INNER JOIN user u ON pm.id_user = u.id_user
    INNER JOIN driver d ON pm.id_driver = d.id_driver
    INNER JOIN pembayaran p ON pm.id_pembayaran = p.id_pembayaran
";

$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Query gagal: " . mysqli_error($koneksi));
}
?>

<section class="content-main">
    <div class="content-header">
        <h2 class="content-title">Pesanan Makanan</h2>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID Pesanan</th>
                            <th>Nama User</th>
                            <th>Nama Driver</th>
                            <th>Plat Motor</th>
                            <th>Metode Pembayaran</th>
                            <th>Bukti Transfer</th>
                            <th>Alamat Pengiriman</th>
                            <th>Total Harga</th>
                            <th>Status Pesanan</th>
                            <th>Catatan</th>
                            <th>Estimasi Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id_pesanan']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_user']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_driver']); ?></td>
                                    <td><?php echo htmlspecialchars($row['plat_nomor']); ?></td>
                                    <td><?php echo htmlspecialchars($row['metode_pembayaran']); ?></td>
                                    <td><a href="../buktitransaksi/<?php echo htmlspecialchars($row['bukti_transaksi']); ?>" target="_blank">Lihat</a></td>
                                    <td><?php echo htmlspecialchars($row['alamat_pengiriman']); ?></td>
                                    <td><?php echo htmlspecialchars(number_format($row['total_harga'], 2)); ?></td>
                                    <td>
                                        <select class="form-select form-select-sm status-dropdown" data-id="<?php echo $row['id_pesanan']; ?>">
                                            <option value="Menunggu" <?php echo ($row['status_pesanan'] === 'Menunggu') ? 'selected' : ''; ?>>Menunggu</option>
                                            <option value="Diproses" <?php echo ($row['status_pesanan'] === 'Diproses') ? 'selected' : ''; ?>>Diproses</option>
                                            <option value="Dikirim" <?php echo ($row['status_pesanan'] === 'Dikirim') ? 'selected' : ''; ?>>Dikirim</option>
                                            <option value="Selesai" <?php echo ($row['status_pesanan'] === 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                                            <option value="Dibatalkan" <?php echo ($row['status_pesanan'] === 'Dibatalkan') ? 'selected' : ''; ?>>Dibatalkan</option>
                                        </select>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['catatan']); ?></td>
                                    <td>
                                        <input type="time" class="form-control estimasi-input" value="<?php echo htmlspecialchars($row['estimasi_waktu']); ?>" data-id="<?php echo $row['id_pesanan']; ?>">
                                    </td>

                                </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='11' class='text-center'>Data pesanan tidak ditemukan.</td></tr>";
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
 $(document).on('change', '.status-dropdown, .estimasi-input', function() {
    const idPesanan = $(this).data('id'); // Ambil ID pesanan
    const statusPesanan = $(this).closest('tr').find('.status-dropdown').val(); // Ambil status pesanan yang baru
    const estimasiWaktu = $(this).closest('tr').find('.estimasi-input').val(); // Ambil estimasi waktu yang baru

    $.ajax({
        url: 'update_status_pemesanan.php', // Arahkan ke backend
        method: 'POST',
        data: {
            id_pesanan: idPesanan,
            status_pesanan: statusPesanan,
            estimasi_waktu: estimasiWaktu
        },
        success: function(response) {
            const res = JSON.parse(response);
            if (res.status === 'success') {
                alert('Status dan estimasi waktu berhasil diperbarui.');
            } else {
                alert('Gagal memperbarui status atau estimasi waktu.');
            }
        },
        error: function() {
            alert('Terjadi kesalahan. Silakan coba lagi.');
        }
    });
});

</script>