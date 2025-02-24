<?php
ob_start();
include 'config.php';

// Periksa apakah session username admin ada
if (!isset($_SESSION['username'])) {
    die("Anda belum login sebagai admin.");
}

$username = $_SESSION['username'];

// Ambil data admin berdasarkan username
$admin_query = "SELECT id_admin FROM admin WHERE username = ?";
$admin_stmt = mysqli_prepare($koneksi, $admin_query);

if (!$admin_stmt) {
    die("Query gagal: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param($admin_stmt, "s", $username);
mysqli_stmt_execute($admin_stmt);
mysqli_stmt_bind_result($admin_stmt, $admin_id);

if (!mysqli_stmt_fetch($admin_stmt)) {
    mysqli_stmt_close($admin_stmt);
    die("Anda tidak memiliki akses sebagai admin.");
}
mysqli_stmt_close($admin_stmt);

// Handle update status
if (isset($_POST['status'])) {
    $id_pengantaran = $_POST['id_pengantaran'];
    $new_status = $_POST['status'];

    $update_query = "UPDATE pengantaran_orang SET status = ? WHERE id_pengantaran = ?";
    $update_stmt = mysqli_prepare($koneksi, $update_query);

    if (!$update_stmt) {
        die("Query gagal: " . mysqli_error($koneksi));
    }

    mysqli_stmt_bind_param($update_stmt, "si", $new_status, $id_pengantaran);
    mysqli_stmt_execute($update_stmt);
    mysqli_stmt_close($update_stmt);
}

// Handle hapus data
if (isset($_POST['hapus'])) {
    $id_pengantaran = $_POST['id_pengantaran'];

    $delete_query = "DELETE FROM pengantaran_orang WHERE id_pengantaran = ?";
    $delete_stmt = mysqli_prepare($koneksi, $delete_query);

    if (!$delete_stmt) {
        die("Query gagal: " . mysqli_error($koneksi));
    }

    mysqli_stmt_bind_param($delete_stmt, "i", $id_pengantaran);
    mysqli_stmt_execute($delete_stmt);
    mysqli_stmt_close($delete_stmt);

    header("Location: utama.php?page=pengantaran_orang");
    exit;
}

// Query untuk mengambil data pengantaran orang
$query = "
    SELECT 
        po.id_pengantaran, 
        u.nama,
        u.no_hp, 
        d.nama,
        po.titik_jemput, 
        po.titik_antar, 
        po.biaya, 
        po.catatan, 
        po.status, 
        po.rating
    FROM 
        pengantaran_orang po
    INNER JOIN user u ON po.id_user = u.id_user
    INNER JOIN driver d ON po.id_driver = d.id_driver
";

$stmt = mysqli_prepare($koneksi, $query);
if (!$stmt) {
    die("Query gagal: " . mysqli_error($koneksi));
}

mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, 
    $id_pengantaran, 
    $nama_user, 
    $no_hp, 
    $nama_driver, 
    $titik_jemput, 
    $titik_antar, 
    $biaya, 
    $catatan, 
    $status, 
    $rating
);
?>

<section class="content-main">
    <div class="content-header">
        <h2 class="content-title">Pengantaran Orang - Admin</h2>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID Pengantaran</th>
                            <th>Nama User</th>
                            <th>No HP User</th>
                            <th>Nama Driver</th>
                            <th>Titik Jemput</th>
                            <th>Titik Antar</th>
                            <th>Biaya</th>
                            <th>Catatan</th>
                            <th>Status</th>
                            <th>Rating</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while (mysqli_stmt_fetch($stmt)) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($id_pengantaran); ?></td>
                                <td><?php echo htmlspecialchars($nama_user); ?></td>
                                <td><?php echo htmlspecialchars($no_hp); ?></td>
                                <td><?php echo htmlspecialchars($nama_driver); ?></td>
                                <td><?php echo htmlspecialchars($titik_jemput); ?></td>
                                <td><?php echo htmlspecialchars($titik_antar); ?></td>
                                <td><?php echo htmlspecialchars(number_format($biaya, 2)); ?></td>
                                <td><?php echo htmlspecialchars($catatan); ?></td>
                                <td>
                                    <form method="post">
                                        <input type="hidden" name="id_pengantaran" value="<?php echo htmlspecialchars($id_pengantaran); ?>">
                                        <select name="status" class="form-select" onchange="this.form.submit();">
                                            <option value="menunggu" <?php echo $status == 'menunggu' ? 'selected' : ''; ?>>Menunggu</option>
                                            <option value="dijemput" <?php echo $status == 'dijemput' ? 'selected' : ''; ?>>Dijemput</option>
                                            <option value="diantar" <?php echo $status == 'diantar' ? 'selected' : ''; ?>>Diantar</option>
                                            <option value="selesai" <?php echo $status == 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                                            <option value="dibatalkan" <?php echo $status == 'dibatalkan' ? 'selected' : ''; ?>>Dibatalkan</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <?php
                                    $rating_val = intval($rating);
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $rating_val) {
                                            echo '<span class="star filled">&#9733;</span>';
                                        } else {
                                            echo '<span class="star empty">&#9734;</span>';
                                        }
                                    }
                                    ?>
                                </td>
                                <td>
                                    <form method="post">
                                        <input type="hidden" name="id_pengantaran" value="<?php echo htmlspecialchars($id_pengantaran); ?>">
                                        <button type="submit" name="hapus" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php
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
</style>