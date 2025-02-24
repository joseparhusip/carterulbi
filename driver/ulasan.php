<?php
session_start(); // Add session_start() at the beginning
include 'config.php';

// Periksa apakah session id_driver ada
if (!isset($_SESSION['id_driver'])) {
    die("Anda belum login sebagai driver.");
}

$id_driver = $_SESSION['id_driver'];

// Handle POST request for updating status pesanan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pesanan = $_POST['id_pesanan'];
    $new_status = $_POST['status_pesanan'];

    // Query untuk mengupdate status pesanan
    $update_query = "UPDATE pesanan_makanan SET status_pesanan = ? WHERE id_pesanan = ?";
    $update_stmt = mysqli_prepare($koneksi, $update_query);

    if (!$update_stmt) {
        die("Query gagal: " . mysqli_error($koneksi));
    }

    // Bind parameter
    mysqli_stmt_bind_param($update_stmt, "si", $new_status, $id_pesanan);

    // Eksekusi query
    if (mysqli_stmt_execute($update_stmt)) {
        echo "Status pesanan berhasil diperbarui.";

        // Jika status pesanan diubah menjadi 'selesai', tambahkan data ke tabel detail_pesanan
        if ($new_status === 'selesai') {
            // Query untuk mendapatkan id_produk dari pesanan_makanan
            $get_produk_query = "SELECT id_produk FROM pesanan_makanan WHERE id_pesanan = ?";
            $get_produk_stmt = mysqli_prepare($koneksi, $get_produk_query);

            if (!$get_produk_stmt) {
                die("Query gagal: " . mysqli_error($koneksi));
            }

            // Bind parameter
            mysqli_stmt_bind_param($get_produk_stmt, "i", $id_pesanan);

            // Eksekusi query
            if (mysqli_stmt_execute($get_produk_stmt)) {
                // Bind result
                mysqli_stmt_bind_result($get_produk_stmt, $id_produk);
                
                if (mysqli_stmt_fetch($get_produk_stmt)) {
                    // Query untuk menambahkan data ke tabel detail_pesanan
                    $insert_detail_query = "INSERT INTO detail_pesanan (id_pesanan, id_produk) VALUES (?, ?)";
                    $insert_detail_stmt = mysqli_prepare($koneksi, $insert_detail_query);

                    if (!$insert_detail_stmt) {
                        die("Query gagal: " . mysqli_error($koneksi));
                    }

                    // Bind parameter
                    mysqli_stmt_bind_param($insert_detail_stmt, "ii", $id_pesanan, $id_produk);

                    // Eksekusi query
                    if (mysqli_stmt_execute($insert_detail_stmt)) {
                        echo "Data detail pesanan berhasil ditambahkan.";
                    } else {
                        echo "Gagal menambahkan data detail pesanan: " . mysqli_error($koneksi);
                    }

                    // Tutup statement
                    mysqli_stmt_close($insert_detail_stmt);
                } else {
                    echo "Tidak dapat menemukan id_produk untuk id_pesanan ini.";
                }
            } else {
                echo "Gagal mendapatkan id_produk: " . mysqli_error($koneksi);
            }

            // Tutup statement
            mysqli_stmt_close($get_produk_stmt);
        }
    } else {
        echo "Gagal memperbarui status pesanan: " . mysqli_error($koneksi);
    }

    // Tutup statement
    mysqli_stmt_close($update_stmt);
}

// Query untuk mengambil data pesanan makanan berdasarkan id_driver
$query = "
    SELECT 
        pm.id_pesanan, 
        u.nama AS nama_user, 
        d.nama AS nama_driver, 
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
        mp.nama_metode,
        pm.status_pembayaran
    FROM 
        pesanan_makanan pm
    INNER JOIN user u ON pm.id_user = u.id_user
    INNER JOIN driver d ON pm.id_driver = d.id_driver
    INNER JOIN produk_makanan p ON pm.id_produk = p.id_produk
    INNER JOIN metode_pembayaran mp ON pm.id_metode_pembayaran = mp.id_metode_pembayaran
    WHERE 
        d.id_driver = ?
";

// Menggunakan prepared statement untuk mencegah SQL injection
$stmt = mysqli_prepare($koneksi, $query);

if (!$stmt) {
    die("Query gagal: " . mysqli_error($koneksi));
}

// Bind parameter
mysqli_stmt_bind_param($stmt, "i", $id_driver);

// Eksekusi query
if (!mysqli_stmt_execute($stmt)) {
    die("Query gagal: " . mysqli_error($koneksi));
}

// Bind result variables
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
    $nama_metode,
    $status_pembayaran
);
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
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>ID Pesanan</th>
                            <th>Nama User</th>
                            <th>Nama Driver</th>
                            <th>Alamat Pengiriman</th>
                            <th>Total Harga</th>
                            <th>Status Pesanan</th>
                            <th>Status Pembayaran</th>
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
                        $row_found = false;
                        while (mysqli_stmt_fetch($stmt)) {
                            $row_found = true;
                            $full_stars = intval($rating);
                            $empty_stars = 5 - $full_stars;
                            $download_link = "buktitf/" . $bukti_pembayaran;
                            $is_selesai = $status_pesanan === 'selesai';

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
                                <td><input type="checkbox" class="row-checkbox" name="selected_rows[]" value="<?php echo htmlspecialchars($id_pesanan); ?>" <?php if ($is_selesai) echo 'disabled'; ?>></td>
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
                                <td class="<?php echo $status_pembayaran_color; ?> status-pembayaran">
                                    <?php echo htmlspecialchars($status_pembayaran); ?>
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
                                    <a href="<?php echo $download_link; ?>" download="<?php echo htmlspecialchars($bukti_pembayaran); ?>">
                                        <?php echo htmlspecialchars($bukti_pembayaran); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($nama_metode); ?></td>
                                <td>
                                    <?php if (!$is_selesai) { ?>
                                        <form method="POST" action="">
                                            <input type="hidden" name="id_pesanan" value="<?php echo htmlspecialchars($id_pesanan); ?>">
                                            <input type="hidden" name="status_pesanan" value="selesai">
                                            <button type="submit" class="btn btn-success action-button">Selesai</button>
                                        </form>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php
                        }
                        if (!$row_found) {
                            echo "<tr><td colspan='18' class='text-center'>Data pesanan makanan tidak ditemukan.</td></tr>";
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

    .status-dropdown {
        padding: 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .status-dropdown option[value="menunggu"] {
        background-color: #f0ad4e;
        color: white;
    }

    .status-dropdown option[value="diproses"] {
        background-color: #0275d8;
        color: white;
    }

    .status-dropdown option[value="dikirim"] {
        background-color: #5cb85c;
        color: white;
    }

    .status-dropdown option[value="selesai"] {
        background-color: #28a745;
        color: white;
    }

    .status-dropdown option[value="dibatalkan"] {
        background-color: #d9534f;
        color: white;
    }

    .action-button {
        padding: 5px 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .action-button:hover {
        background-color: #0056b3;
    }

    .status-pembayaran {
        font-family: 'Courier New', Courier, monospace;
        font-size: 1.1em;
    }
</style>

<script>
    document.getElementById('selectAll').addEventListener('change', function() {
        var checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = this.checked;
        }, this);
    });
</script>