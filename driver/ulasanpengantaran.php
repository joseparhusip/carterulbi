<?php

include 'config.php';

// Check if driver is logged in
if (!isset($_SESSION['id_driver'])) {
    die("Anda belum login sebagai driver.");
}

$id_driver = $_SESSION['id_driver'];

// Query to get reviews with ratings > 0
$query = "
    SELECT 
        po.id_pengantaran,
        u.nama AS nama_user,
        po.rating
    FROM 
        pengantaran_orang po
    INNER JOIN 
        user u ON po.id_user = u.id_user
    WHERE 
        po.id_driver = ? 
        AND po.rating > 0
    ORDER BY 
        po.id_pengantaran DESC
";

$stmt = mysqli_prepare($koneksi, $query);

if (!$stmt) {
    die("Query gagal: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param($stmt, "i", $id_driver);

if (!mysqli_stmt_execute($stmt)) {
    die("Query gagal: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_result($stmt, $id_pengantaran, $nama_user, $rating);
?>

<section class="content-main">
    <div class="content-header">
        <h2 class="content-title">Ulasan Pengantaran</h2>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID Pengantaran</th>
                            <th>Nama Pengguna</th>
                            <th>Rating</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $row_found = false;
                        while (mysqli_stmt_fetch($stmt)) {
                            $row_found = true;
                            $full_stars = intval($rating);
                            $empty_stars = 5 - $full_stars;
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($id_pengantaran); ?></td>
                                <td><?php echo htmlspecialchars($nama_user); ?></td>
                                <td>
                                    <?php
                                    // Display filled stars
                                    for ($i = 0; $i < $full_stars; $i++) {
                                        echo '<span class="star filled">&#9733;</span>';
                                    }
                                    // Display empty stars
                                    for ($i = 0; $i < $empty_stars; $i++) {
                                        echo '<span class="star empty">&#9734;</span>';
                                    }
                                    echo " (" . htmlspecialchars($rating) . ")";
                                    ?>
                                </td>
                            </tr>
                        <?php
                        }
                        if (!$row_found) {
                            echo "<tr><td colspan='3' class='text-center'>Belum ada ulasan.</td></tr>";
                        }
                        ?>
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

    .table {
        margin-top: 20px;
    }

    .table th {
        background-color: #f8f9fa;
    }

    .content-header {
        margin-bottom: 20px;
    }

    .content-title {
        color: #333;
        font-weight: 600;
    }

    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: none;
        margin-bottom: 30px;
    }

    .card-body {
        padding: 1.5rem;
    }
</style>