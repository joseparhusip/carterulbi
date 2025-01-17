<?php
ob_start(); // Aktifkan output buffering untuk mencegah error header
include('config.php');

// Pastikan sesi sudah diatur di 'utama.php'
$id_driver = $_SESSION['id_driver'] ?? null;

if (!$id_driver) {
    header('Location: index.php'); // Redirect jika tidak ada sesi
    exit;
}

// Ambil data driver
$sql = "SELECT * FROM driver WHERE id_driver = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $id_driver);
$stmt->execute();
$result = $stmt->get_result();
$driver = $result->fetch_assoc();

// Proses saat form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaDepan = trim($_POST['namaDepan']);
    $status = $_POST['status'];
    $namaLengkap = $namaDepan . ' ' . $status;
    $tanggalLahir = $_POST['tanggalLahir'];
    $email = $_POST['email'];
    $noSim = $_POST['sim'];
    $platNomor = $_POST['platKendaraan'];
    $kendaraan = $_POST['merkMotor'];

    // Upload gambar
    $gambarDriver = $driver['gambardriver'];
    if (!empty($_FILES['gambar']['name'])) {
        $targetDir = "../gambardriver/";
        $targetFile = $targetDir . basename($_FILES['gambar']['name']);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Validasi tipe file
        $check = getimagesize($_FILES['gambar']['tmp_name']);
        if ($check !== false && move_uploaded_file($_FILES['gambar']['tmp_name'], $targetFile)) {
            $gambarDriver = basename($_FILES['gambar']['name']);
        } else {
            echo "<script>alert('Gagal mengupload gambar.');</script>";
            exit;
        }
    }

    // Update data
    $sql = "UPDATE driver SET nama = ?, tanggal_lahir = ?, email = ?, no_sim = ?, kendaraan = ?, plat_nomor = ?, status = ?, gambardriver = ? WHERE id_driver = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("ssssssssi", $namaLengkap, $tanggalLahir, $email, $noSim, $kendaraan, $platNomor, $status, $gambarDriver, $id_driver);

    if ($stmt->execute()) {
        header('Location: utama.php?page=profile');
        exit;
        // Penting untuk menghentikan eksekusi
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$koneksi->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil Driver</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
    <style>
        /* Styling untuk gambar kotak */
        .profile-img {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border: 2px solid #ddd;
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
    <div class="form-container container mt-5">
        <form method="POST" enctype="multipart/form-data">
            <div class="text-center">
                <img src="../gambardriver/<?= htmlspecialchars($driver['gambardriver']) ?>" alt="Foto Driver" class="profile-img mb-3">
                <input type="file" name="gambar" class="form-control mb-3">
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="namaDepan" class="form-label">Nama Depan</label>
                    <input type="text" class="form-control" id="namaDepan" name="namaDepan" value="<?= htmlspecialchars(explode(' ', $driver['nama'])[0]) ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="Tersedia" <?= $driver['status'] == 'Tersedia' ? 'selected' : '' ?>>Tersedia</option>
                        <option value="Tidak Tersedia" <?= $driver['status'] == 'Tidak Tersedia' ? 'selected' : '' ?>>Tidak Tersedia</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="tanggalLahir" class="form-label">Tanggal Lahir</label>
                    <input type="date" class="form-control" id="tanggalLahir" name="tanggalLahir" value="<?= htmlspecialchars($driver['tanggal_lahir']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($driver['email']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="sim" class="form-label">SIM (Kalau ada)</label>
                    <input type="text" class="form-control" id="sim" name="sim" value="<?= htmlspecialchars($driver['no_sim']) ?>">
                </div>
                <div class="col-md-6">
                    <label for="platKendaraan" class="form-label">Plat Kendaraan</label>
                    <input type="text" class="form-control" id="platKendaraan" name="platKendaraan" value="<?= htmlspecialchars($driver['plat_nomor']) ?>">
                </div>
                <div class="col-md-6">
                    <label for="merkMotor" class="form-label">Merk Motor</label>
                    <input type="text" class="form-control" id="merkMotor" name="merkMotor" value="<?= htmlspecialchars($driver['kendaraan']) ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-4">Perbarui</button>
        </form>
    </div>
</body>
</html>
