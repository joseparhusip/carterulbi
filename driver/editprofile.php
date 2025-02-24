<?php
ob_start(); // Aktifkan output buffering untuk mencegah error header
session_start(); // Memastikan session dimulai
include('config.php');

// Cek keberadaan session id_driver
if (!isset($_SESSION['id_driver'])) {
    header('Location: index.php');
    exit;
}

$id_driver = $_SESSION['id_driver'];

// Ambil data driver menggunakan bind_result
$sql = "SELECT nama, tanggal_lahir, email, no_sim, kendaraan, plat_nomor, status, gambardriver FROM driver WHERE id_driver = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $id_driver);
$stmt->execute();
$stmt->bind_result($nama, $tanggal_lahir, $email, $no_sim, $kendaraan, $plat_nomor, $status, $gambardriver);
$stmt->fetch();
$stmt->close();

// Simpan data dalam array untuk penggunaan lebih mudah
$driver = array(
    'nama' => $nama,
    'tanggal_lahir' => $tanggal_lahir,
    'email' => $email,
    'no_sim' => $no_sim,
    'kendaraan' => $kendaraan,
    'plat_nomor' => $plat_nomor,
    'status' => $status,
    'gambardriver' => $gambardriver
);

// Proses saat form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaDepan = trim($_POST['namaDepan']);
    $status = $_POST['status'];
    $tanggalLahir = $_POST['tanggalLahir'];
    $email = $_POST['email'];
    $noSim = $_POST['sim'];
    $platNomor = $_POST['platKendaraan'];
    $kendaraan = $_POST['merkMotor'];

    // Update nama tanpa status
    $namaLengkap = !empty($namaDepan) ? $namaDepan : $driver['nama'];

    // Upload gambar
    $gambarDriver = $driver['gambardriver'];
    if (!empty($_FILES['gambar']['name'])) {
        $targetDir = "../gambardriver/";
        $targetFile = $targetDir . basename($_FILES['gambar']['name']);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Validasi tipe file
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($imageFileType, $allowed_types)) {
            $check = getimagesize($_FILES['gambar']['tmp_name']);
            if ($check !== false && move_uploaded_file($_FILES['gambar']['tmp_name'], $targetFile)) {
                $gambarDriver = basename($_FILES['gambar']['name']);
            } else {
                echo "<script>alert('Gagal mengupload gambar.');</script>";
            }
        } else {
            echo "<script>alert('Hanya file JPG, JPEG, PNG & GIF yang diizinkan.');</script>";
        }
    }

    // Update data
    $sql = "UPDATE driver SET nama = ?, tanggal_lahir = ?, email = ?, no_sim = ?, kendaraan = ?, plat_nomor = ?, status = ?, gambardriver = ? WHERE id_driver = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("ssssssssi", $namaLengkap, $tanggalLahir, $email, $noSim, $kendaraan, $platNomor, $status, $gambarDriver, $id_driver);

    if ($stmt->execute()) {
        // Update session data jika diperlukan
        $_SESSION['nama'] = $namaLengkap;
        
        header('Location: utama.php?page=profile');
        exit;
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
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
        .profile-img {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border: 2px solid #ddd;
            border-radius: 50%;
        }

        .table-bordered {
            border: 2px solid #ddd;
        }

        .table-bordered th, .table-bordered td {
            border: 1px solid #ddd;
        }

        .table-bordered tr:hover {
            background-color: #f1f1f1;
        }
        
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-light">
    <div class="form-container container mt-5">
        <h2 class="text-center mb-4">Edit Profil Driver</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="text-center">
                <img src="../gambardriver/<?= htmlspecialchars($driver['gambardriver']) ?>" alt="Foto Driver" class="profile-img mb-3">
                <input type="file" name="gambar" class="form-control mb-3" accept="image/*">
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="namaDepan" class="form-label">Nama Depan</label>
                    <input type="text" class="form-control" id="namaDepan" name="namaDepan" value="<?= htmlspecialchars($driver['nama']) ?>" required>
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
            <div class="text-center">
                <button type="submit" class="btn btn-primary mt-4 px-5">Perbarui Profil</button>
            </div>
        </form>
    </div>
</body>
</html>