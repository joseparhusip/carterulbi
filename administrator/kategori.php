<?php
// Koneksi ke database
include("config.php");

// Query untuk mendapatkan data kategori
$query = "SELECT * FROM kategori_produk";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Produk</title>
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
        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #ddd;
        }
        .card-body {
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <section class="content-main">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Kategori Produk</h4>
                    <a href="utama.php?page=tambahkategori" class="btn btn-primary btn-sm rounded">Create new</a>
                </div>
                <div class="card-body">
                    <!-- Tabel Kategori -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">ID Kategori</th>
                                    <th scope="col">Nama Kategori</th>
                                    <th scope="col">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1; // Inisialisasi nomor urut
                                while ($row = mysqli_fetch_assoc($result)) { ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo $row['id_kategori']; ?></td>
                                        <td><?php echo $row['nama_kategori']; ?></td>
                                        <td>
                                            <a href="utama.php?page=editkategori&id_kategori=<?php echo $row['id_kategori']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                            <a href="hapuskategori.php?id_kategori=<?php echo $row['id_kategori']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>