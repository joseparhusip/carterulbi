<?php
include 'config.php';

// Pastikan admin sudah login
if (!isset($_SESSION['username'])) {
    die("Silakan login sebagai admin terlebih dahulu.");
}

$username_admin = $_SESSION['username'];

// Handle POST request untuk update status pesanan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_pesanan'], $_POST['status_pesanan'])) {
        $id_pesanan = $_POST['id_pesanan'];
        $new_status = $_POST['status_pesanan'];

        $update_query = "UPDATE pesanan_makanan SET status_pesanan = ? WHERE id_pesanan = ?";
        $update_stmt = mysqli_prepare($koneksi, $update_query);

        if ($update_stmt) {
            mysqli_stmt_bind_param($update_stmt, "si", $new_status, $id_pesanan);
            if (mysqli_stmt_execute($update_stmt)) {
                echo "Status pesanan berhasil diperbarui.";
            } else {
                echo "Gagal memperbarui status pesanan: " . mysqli_error($koneksi);
            }
            mysqli_stmt_close($update_stmt);
        }
    }

    // Handle penghapusan pesanan
    if (isset($_POST['hapus_pesanan'])) {
        $id_pesanan_hapus = $_POST['hapus_pesanan'];

        $delete_query = "DELETE FROM pesanan_makanan WHERE id_pesanan = ?";
        $delete_stmt = mysqli_prepare($koneksi, $delete_query);

        if ($delete_stmt) {
            mysqli_stmt_bind_param($delete_stmt, "i", $id_pesanan_hapus);
            if (mysqli_stmt_execute($delete_stmt)) {
                echo "Pesanan berhasil dihapus.";
            } else {
                echo "Gagal menghapus pesanan: " . mysqli_error($koneksi);
            }
            mysqli_stmt_close($delete_stmt);
        }
    }
}

// Query untuk menampilkan pesanan dengan bind_result
$query = "
    SELECT 
        pm.id_pesanan, 
        u.nama,
        IFNULL(d.nama, 'Tidak ada driver'),
        pm.alamat_pengiriman, 
        pm.total_harga, 
        pm.status_pesanan, 
        pm.catatan, 
        pm.estimasi_waktu, 
        p.nama_produk, 
        pm.harga, 
        pm.ongkir, 
        pm.subtotal, 
        pm.rating, 
        pm.bukti_pembayaran, 
        mp.nama_metode
    FROM pesanan_makanan pm
    INNER JOIN user u ON pm.id_user = u.id_user
    LEFT JOIN driver d ON pm.id_driver = d.id_driver
    LEFT JOIN produk_makanan p ON pm.id_produk = p.id_produk
    LEFT JOIN metode_pembayaran mp ON pm.id_metode_pembayaran = mp.id_metode_pembayaran
";

$stmt = mysqli_prepare($koneksi, $query);
if (!$stmt) {
    die("Query gagal: " . mysqli_error($koneksi));
}

mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, 
    $id_pesanan, 
    $nama_user,
    $nama_driver,
    $alamat_pengiriman,
    $total_harga,
    $status_pesanan,
    $catatan,
    $estimasi_waktu,
    $nama_produk,
    $harga,
    $ongkir,
    $subtotal,
    $rating,
    $bukti_pembayaran,
    $nama_metode
);
?>

<section class="content-main">
    <div class="content-header">
        <h2 class="content-title">Pesanan Makanan - Admin</h2>
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
                            <th>Alamat Pengiriman</th>
                            <th>Total Harga</th>
                            <th>Status Pesanan</th>
                            <th>Catatan</th>
                            <th>Estimasi Waktu</th>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Ongkir</th>
                            <th>Subtotal</th>
                            <th>Rating</th>
                            <th>Bukti Pembayaran</th>
                            <th>Metode Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $has_data = false;
                        while (mysqli_stmt_fetch($stmt)) {
                            $has_data = true;
                            $full_stars = intval($rating);
                            $empty_stars = 5 - $full_stars;
                            $download_link = "buktitf/" . $bukti_pembayaran;
                            $is_selesai = $status_pesanan === 'selesai';
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($id_pesanan); ?></td>
                                <td><?php echo htmlspecialchars($nama_user); ?></td>
                                <td><?php echo htmlspecialchars($nama_driver); ?></td>
                                <td><?php echo htmlspecialchars($alamat_pengiriman); ?></td>
                                <td><?php echo htmlspecialchars(number_format($total_harga, 2)); ?></td>
                                <td>
                                    <form method="POST" action="">
                                        <input type="hidden" name="id_pesanan" value="<?php echo htmlspecialchars($id_pesanan); ?>">
                                        <select name="status_pesanan" onchange="this.form.submit()" class="status-dropdown" <?php if ($is_selesai) echo 'disabled'; ?>>
                                            <option value="menunggu" <?php if ($status_pesanan == 'menunggu') echo 'selected'; ?>>Menunggu</option>
                                            <option value="diproses" <?php if ($status_pesanan == 'diproses') echo 'selected'; ?>>Diproses</option>
                                            <option value="dikirim" <?php if ($status_pesanan == 'dikirim') echo 'selected'; ?>>Dikirim</option>
                                            <option value="selesai" <?php if ($status_pesanan == 'selesai') echo 'selected'; ?>>Selesai</option>
                                            <option value="dibatalkan" <?php if ($status_pesanan == 'dibatalkan') echo 'selected'; ?>>Dibatalkan</option>
                                        </select>
                                    </form>
                                </td>
                                <td><?php echo htmlspecialchars($catatan); ?></td>
                                <td><?php echo htmlspecialchars($estimasi_waktu); ?></td>
                                <td><?php echo htmlspecialchars($nama_produk); ?></td>
                                <td><?php echo htmlspecialchars(number_format($harga, 2)); ?></td>
                                <td><?php echo htmlspecialchars(number_format($ongkir, 2)); ?></td>
                                <td><?php echo htmlspecialchars(number_format($subtotal, 2)); ?></td>
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
                                <td>
                                    <a href="<?php echo $download_link; ?>" download="<?php echo $bukti_pembayaran; ?>">
                                        <?php echo htmlspecialchars($bukti_pembayaran); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($nama_metode); ?></td>
                                <td>
                                    <form method="POST" action="">
                                        <input type="hidden" name="hapus_pesanan" value="<?php echo htmlspecialchars($id_pesanan); ?>">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus pesanan ini?');">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php 
                        }
                        if (!$has_data) {
                            echo "<tr><td colspan='16' class='text-center'>Data pesanan tidak ditemukan.</td></tr>";
                        }
                        mysqli_stmt_close($stmt);
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

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
    .status-dropdown {
        padding: 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    .status-dropdown option[value="menunggu"] { background-color: #f0ad4e; color: white; }
    .status-dropdown option[value="diproses"] { background-color: #0275d8; color: white; }
    .status-dropdown option[value="dikirim"] { background-color: #5cb85c; color: white; }
    .status-dropdown option[value="selesai"] { background-color: #28a745; color: white; }
    .status-dropdown option[value="dibatalkan"] { background-color: #d9534f; color: white; }
    .action-button {
        padding: 5px 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    .action-button:hover {
        background-color: #0056b3;
    }
</style>