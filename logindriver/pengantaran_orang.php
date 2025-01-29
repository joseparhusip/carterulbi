<?php

include 'config.php'; // Koneksi ke database

// Periksa apakah session username driver ada
if (!isset($_SESSION['username'])) {
    die("Anda belum login sebagai driver.");
}

$username = $_SESSION['username'];

// Handle update status
if (isset($_POST['status'])) {
    $id_pengantaran = $_POST['id_pengantaran'];
    $new_status = $_POST['status'];

    // Query untuk mengupdate status pengantaran
    $update_query = "UPDATE pengantaran_orang SET status = ? WHERE id_pengantaran = ?";
    $update_stmt = mysqli_prepare($koneksi, $update_query);

    if (!$update_stmt) {
        die("Query gagal: " . mysqli_error($koneksi));
    }

    // Bind parameter
    mysqli_stmt_bind_param($update_stmt, "si", $new_status, $id_pengantaran);

    // Eksekusi query
    if (mysqli_stmt_execute($update_stmt)) {
        // Status berhasil diupdate
    } else {
        die("Update gagal: " . mysqli_error($koneksi));
    }

    // Tutup statement
    mysqli_stmt_close($update_stmt);
}

// Handle tambah detail pesanan
if (isset($_POST['selesai'])) {
    $id_pengantaran = $_POST['id_pengantaran'];

    // Query untuk menambahkan detail pesanan
    $insert_query = "INSERT INTO detail_pesanan (id_pengantaran) VALUES (?)";
    $insert_stmt = mysqli_prepare($koneksi, $insert_query);

    if (!$insert_stmt) {
        die("Query gagal: " . mysqli_error($koneksi));
    }

    // Bind parameter
    mysqli_stmt_bind_param($insert_stmt, "i", $id_pengantaran);

    // Eksekusi query
    if (mysqli_stmt_execute($insert_stmt)) {
        // Detail pesanan berhasil ditambahkan
    } else {
        die("Insert gagal: " . mysqli_error($koneksi));
    }

    // Tutup statement
    mysqli_stmt_close($insert_stmt);
}

// Query untuk mengambil data pengantaran orang berdasarkan username driver
$query = "
    SELECT 
        po.id_pengantaran, 
        u.nama AS nama_user, 
        u.no_hp, 
        d.nama AS nama_driver, 
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
    WHERE 
        d.username = ?
";

// Menggunakan prepared statement untuk mencegah SQL injection
$stmt = mysqli_prepare($koneksi, $query);

if (!$stmt) {
    die("Query gagal: " . mysqli_error($koneksi));
}

// Bind parameter
mysqli_stmt_bind_param($stmt, "s", $username);

// Eksekusi query
mysqli_stmt_execute($stmt);

// Ambil hasil
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die("Query gagal: " . mysqli_error($koneksi));
}

// Query untuk mengambil data pengantaran yang sudah ada di detail_pesanan
$completed_query = "SELECT id_pengantaran FROM detail_pesanan";
$completed_result = mysqli_query($koneksi, $completed_query);

if (!$completed_result) {
    die("Query gagal: " . mysqli_error($koneksi));
}

$completed_ids = [];
while ($completed_row = mysqli_fetch_assoc($completed_result)) {
    $completed_ids[] = $completed_row['id_pengantaran'];
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
                            <th>Select</th>
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
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $status = htmlspecialchars($row['status']);
                                $status_color = '';
                                switch ($status) {
                                    case 'menunggu':
                                        $status_color = 'text-warning';
                                        break;
                                    case 'dijemput':
                                        $status_color = 'text-primary';
                                        break;
                                    case 'diantar':
                                        $status_color = 'text-info';
                                        break;
                                    case 'selesai':
                                        $status_color = 'text-success';
                                        break;
                                    case 'dibatalkan':
                                        $status_color = 'text-danger';
                                        break;
                                    default:
                                        $status_color = 'text-secondary';
                                }

                                $rating = htmlspecialchars($row['rating']);
                                $full_stars = intval($rating);
                                $empty_stars = 5 - $full_stars;

                                $is_completed = in_array($row['id_pengantaran'], $completed_ids);
                        ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="id_pengantaran[]" value="<?php echo htmlspecialchars($row['id_pengantaran']); ?>" <?php echo $is_completed ? 'disabled' : ''; ?>>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['id_pengantaran']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_user']); ?></td>
                                    <td><?php echo htmlspecialchars($row['no_hp']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_driver']); ?></td>
                                    <td><?php echo htmlspecialchars($row['titik_jemput']); ?></td>
                                    <td><?php echo htmlspecialchars($row['titik_antar']); ?></td>
                                    <td><?php echo htmlspecialchars(number_format($row['biaya'], 2)); ?></td>
                                    <td><?php echo htmlspecialchars($row['catatan']); ?></td>
                                    <td>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="id_pengantaran" value="<?php echo htmlspecialchars($row['id_pengantaran']); ?>">
                                            <select name="status" class="form-select <?php echo $status_color; ?>" onchange="this.form.submit();" <?php echo $is_completed ? 'disabled' : ''; ?>>
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
                                        for ($i = 0; $i < $full_stars; $i++) {
                                            echo '<span class="star filled">&#9733;</span>';
                                        }
                                        for ($i = 0; $i < $empty_stars; $i++) {
                                            echo '<span class="star empty">&#9734;</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if (!$is_completed) { ?>
                                            <form method="post" style="display:inline;">
                                                <input type="hidden" name="id_pengantaran" value="<?php echo htmlspecialchars($row['id_pengantaran']); ?>">
                                                <button type="submit" name="selesai" class="btn btn-success">Selesai</button>
                                            </form>
                                        <?php } ?>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='12' class='text-center'>Data pengantaran tidak ditemukan.</td></tr>";
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
</style>
