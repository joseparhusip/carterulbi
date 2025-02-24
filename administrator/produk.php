<?php
// Koneksi ke database
include("config.php");

// Inisialisasi variabel pencarian
$search_query = "";
if (isset($_GET['search_query']) && !empty($_GET['search_query'])) {
    $search_query = trim($_GET['search_query']);
    $search_query = mysqli_real_escape_string($koneksi, $search_query); // Mencegah SQL Injection
}

// Query untuk mendapatkan data produk
$query = "SELECT pm.id_produk, pm.nama_produk, pm.deskripsi, pm.harga, pm.stok, pm.status, 
                 pm.gambar, kp.nama_kategori 
          FROM produk_makanan pm
          JOIN kategori_produk kp ON pm.id_kategori = kp.id_kategori";

// Tambahkan kondisi WHERE jika ada pencarian
if (!empty($search_query)) {
    $query .= " WHERE pm.nama_produk LIKE '%$search_query%'";
}

$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <style>
        .content-header {
            margin-bottom: 20px;
        }
        .table img {
            max-width: 80px;
            height: auto;
            border-radius: 5px;
        }
        .badge {
            font-size: 0.9rem;
            padding: 5px 10px;
        }
        .btn-sm {
            font-size: 0.875rem;
            padding: 5px 10px;
        }
        .searchform {
            width: 300px;
        }
        @media (max-width: 768px) {
            .searchform {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <section class="content-main">
            <div class="content-header d-flex justify-content-between align-items-center mb-4">
                <form class="searchform" method="GET" action="utama.php">
                    <div class="input-group">
                        <input type="text" name="search_query" class="form-control" placeholder="Search by product name..." value="<?php echo isset($_GET['search_query']) ? htmlspecialchars($_GET['search_query']) : ''; ?>" />
                        <input type="hidden" name="page" value="produk" />
                        <button class="btn btn-light bg" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>
                <a href="utama.php?page=tambahproduk" class="btn btn-primary btn-sm rounded">Create new</a>
            </div>

            <?php if (!empty($search_query)): ?>
                <div class="alert alert-info mt-3">
                    Showing results for: <strong><?php echo htmlspecialchars($search_query); ?></strong>
                </div>
            <?php endif; ?>

            <!-- Tabel Produk -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nama Produk</th>
                            <th scope="col">Deskripsi</th>
                            <th scope="col">Harga</th>
                            <th scope="col">Stok</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Gambar</th>
                            <th scope="col">Status</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1; // Inisialisasi nomor urut
                        while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $row['nama_produk']; ?></td>
                                <td><?php echo $row['deskripsi']; ?></td>
                                <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                <td><?php echo $row['stok']; ?></td>
                                <td><?php echo $row['nama_kategori']; ?></td>
                                <td>
                                    <div>
                                        <img src="../gambarfood/<?php echo $row['gambar']; ?>" alt="No image available" class="img-fluid">
                                    </div>
                                </td>
                                <td>
                                    <?php 
                                    if ($row['status'] == 'tersedia') {
                                        echo '<span class="badge bg-success">Tersedia</span>';
                                    } else {
                                        echo '<span class="badge bg-danger">Habis</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="utama.php?page=editproduk&id_produk=<?php echo $row['id_produk']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="utama.php?page=hapusproduk&id_produk=<?php echo $row['id_produk']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>