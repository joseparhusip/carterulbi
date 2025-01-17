<?php
ob_start(); // Aktifkan output buffering

include('config.php');

// Pastikan sesi pengguna sudah ada
$id_driver = $_SESSION['id_driver'] ?? null;
if (!$id_driver) {
    header('Location: index.php');
    exit;
}

// Ambil data driver dari database
$sql = "SELECT * FROM driver WHERE id_driver = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $id_driver);
$stmt->execute();
$result = $stmt->get_result();
$driver = $result->fetch_assoc();

$stmt->close();
$koneksi->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Driver</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
    <style>
        /* Styling untuk gambar kotak */
        .profile-img {
            width: 200px;
            height: 200px;
            object-fit: cover;
        }

        /* Styling tabel dengan border dan efek hover */
        .table-bordered {
            border: 2px solid #ddd;
        }

        .table-bordered th, .table-bordered td {
            border: 1px solid #ddd;
        }

        .table-bordered tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="text-center">Profil Driver</h3>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <!-- Gambar driver dalam kotak -->
                    <img src="../gambardriver/<?= htmlspecialchars($driver['gambardriver']) ?>" alt="Foto Driver" class="profile-img">
                </div>
                <table class="table table-bordered">
                    <tr>
                        <th>Nama</th>
                        <td><?= htmlspecialchars($driver['nama']) ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td><?= htmlspecialchars($driver['status']) ?></td>
                    </tr>
                    <tr>
                        <th>Tanggal Lahir</th>
                        <td><?= htmlspecialchars($driver['tanggal_lahir']) ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?= htmlspecialchars($driver['email']) ?></td>
                    </tr>
                    <tr>
                        <th>SIM</th>
                        <td><?= htmlspecialchars($driver['no_sim']) ?></td>
                    </tr>
                    <tr>
                        <th>Plat Kendaraan</th>
                        <td><?= htmlspecialchars($driver['plat_nomor']) ?></td>
                    </tr>
                    <tr>
                        <th>Merk Motor</th>
                        <td><?= htmlspecialchars($driver['kendaraan']) ?></td>
                    </tr>
                </table>
            </div>
            <div class="card-footer text-center">
                <a href="utama.php?page=editprofile" class="btn btn-warning">Edit Profil</a>
            </div>
        </div>
    </div>
</body>
</html>
