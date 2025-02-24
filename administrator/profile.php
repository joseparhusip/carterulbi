<?php
include "config.php";

// Query untuk mengambil data dari tabel admin
$sql = "SELECT id_admin, username, status, nama, email, password, gambar 
        FROM admin";
$result = $koneksi->query($sql);

// Proses penghapusan admin
if (isset($_POST['id_admin'])) {
    $id_admin = $_POST['id_admin'];
    // Query untuk menghapus data admin berdasarkan id_admin
    $sql_delete = "DELETE FROM admin WHERE id_admin = ?";
    
    if ($stmt = $koneksi->prepare($sql_delete)) {
        $stmt->bind_param("i", $id_admin);
        // Mengeksekusi query penghapusan
        if ($stmt->execute()) {
            header("Location: utama.php?page=dataadmin"); // Redirect setelah berhasil
            exit();
        } else {
            echo "Terjadi kesalahan saat menghapus data admin.";
        }
        $stmt->close();
    } else {
        echo "Query gagal disiapkan.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .content-main {
            margin: 20px;
        }
        .table img {
            border-radius: 50%;
            object-fit: cover;
            width: 80px;
            height: 80px;
        }
        .btn-edit {
            background-color: #ffc107;
            color: white;
            border: none;
        }
        .btn-edit:hover {
            background-color: #e0a800;
        }
        .btn-danger {
            background-color: #dc3545;
            border: none;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
        }
    </style>
</head>
<body>
<section class="content-main">
    <div class="content-header">
        <h2 class="content-title text-center">Data Admin</h2>
    </div>
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>ID Admin</th>
                            <th>Foto</th>
                            <th>Username</th>
                            <th>Status</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                // Default image jika gambar tidak tersedia
                                $gambar = !empty($row['gambar']) ? "./gambaradmin/{$row['gambar']}" : "./gambaradmin/default.jpg";
                                echo "<tr>
                                    <td>{$row['id_admin']}</td>
                                    <td><img src='{$gambar}' alt='Foto Admin' width='80'></td>
                                    <td>{$row['username']}</td>
                                    <td>{$row['status']}</td>
                                    <td>{$row['nama']}</td>
                                    <td>{$row['email']}</td>
                                    <td>{$row['password']}</td>
                                    <td class='action-buttons'>
                                        <a href='utama.php?page=editprofile&id_admin={$row['id_admin']}' class='btn btn-edit'><i class='fas fa-edit'></i> Edit</a>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' class='text-center'>Tidak ada data admin</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Bootstrap JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$koneksi->close();
?>