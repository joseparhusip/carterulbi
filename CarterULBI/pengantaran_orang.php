<?php  
include "config.php";  
// Cek koneksi              
if ($koneksi->connect_error) {  
    die("Koneksi gagal: " . $koneksi->connect_error);  
}  

// Cek apakah user sudah login              
if (!isset($_SESSION['username'])) {  
    header("Location: login.php");  
    exit;  
}  

$username = $_SESSION['username'];  

// Query untuk mendapatkan data user berdasarkan username              
$query_user = "SELECT nama FROM user WHERE username = ?";  
$stmt_user = $koneksi->prepare($query_user);  
$stmt_user->bind_param('s', $username);  
$stmt_user->execute();  
$result_user = $stmt_user->get_result();  
$user = $result_user->fetch_assoc();  

// Mendapatkan id_driver dari request atau default              
$id_driver = isset($_GET['id_driver']) ? $_GET['id_driver'] : 1;  

// Query untuk mendapatkan data driver berdasarkan id_driver              
$query_driver = "SELECT nama AS driver_nama, no_sim, kendaraan, plat_nomor FROM driver WHERE id_driver = ?";  
$stmt_driver = $koneksi->prepare($query_driver);  
$stmt_driver->bind_param('i', $id_driver);  
$stmt_driver->execute();  
$result_driver = $stmt_driver->get_result();  

// Ambil data driver jika ditemukan              
$driver = $result_driver->fetch_assoc();  

// Tutup statement          
$stmt_user->close();  
$stmt_driver->close();  

// Tutup koneksi database          
$koneksi->close();  
?>  

<!DOCTYPE html>  
<html lang="id">  

<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title>Detail Driver dan Pemesanan</title>  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">  
    <style>  
        body {  
            background-color: #f8f9fa;  
        }  

        .container {  
            margin-top: 20px;  
        }

        /* Style tambahan untuk memisahkan kolom detail driver dan form pemesanan */
        .row {
            display: flex;
            justify-content: space-between;
        }

        .card {  
            height: 100%;  
        }

        /* Responsive: Memastikan layout tetap rapi di ukuran layar kecil */
        @media (max-width: 768px) {  
            .col-md-6 {  
                flex: 1 1 100%;  
                margin-bottom: 15px;
            }  
        }

        .form-label {  
            font-weight: bold;  
        }

        .btn-primary {  
            background-color: #007bff;  
            border: none;  
        }

        .btn-primary:hover {  
            background-color: #0056b3;  
        }
    </style>  
</head>  

<body>  
    <div class="container">  
        <div class="row">  
            <!-- Kolom kiri: Detail Driver -->  
            <div class="col-md-6">  
                <div class="card">  
                    <div class="card-body">  
                        <h2 class="text-center mb-4">Detail Driver</h2>  
                        <?php if ($driver): ?>  
                            <div class="mb-3">  
                                <label class="form-label">Nama Driver</label>  
                                <input type="text" class="form-control" value="<?= htmlspecialchars($driver['driver_nama']) ?>" readonly>  
                            </div>  
                            <div class="mb-3">  
                                <label class="form-label">No SIM</label>  
                                <input type="text" class="form-control" value="<?= htmlspecialchars($driver['no_sim']) ?>" readonly>  
                            </div>  
                            <div class="mb-3">  
                                <label class="form-label">Plat Nomor</label>  
                                <input type="text" class="form-control" value="<?= htmlspecialchars($driver['plat_nomor']) ?>" readonly>  
                            </div>  
                            <div class="mb-3">  
                                <label class="form-label">Kendaraan</label>  
                                <input type="text" class="form-control" value="<?= htmlspecialchars($driver['kendaraan']) ?>" readonly>  
                            </div>  
                        <?php else: ?>  
                            <div class="alert alert-danger">Data driver tidak ditemukan.</div>  
                        <?php endif; ?>  
                    </div>  
                </div>  
            </div>  

            <!-- Kolom kanan: Form Pemesanan -->  
            <div class="col-md-6">  
                <div class="card">  
                    <div class="card-body">  
                        <h2 class="text-center mb-4">Form Pemesanan</h2>  
                        <form action="proses_pemesanan.php" method="POST">  
                            <input type="hidden" name="id_driver" value="<?php echo $id_driver; ?>">  
                            <div class="mb-3">  
                                <label for="user_name" class="form-label">Nama User</label>  
                                <input type="text" name="user_name" class="form-control" value="<?php echo htmlspecialchars($user['nama']); ?>" readonly>  
                            </div>  
                            <div class="mb-3">  
                                <label for="titik_antar" class="form-label">Titik Antar</label>  
                                <input type="text" name="titik_antar" id="titik_antar" class="form-control" placeholder="Titik Antar" required>  
                            </div>  
                            <div class="mb-3">  
                                <label for="titik_jemput" class="form-label">Titik Jemput</label>  
                                <input type="text" name="titik_jemput" id="titik_jemput" class="form-control" placeholder="Titik Jemput" required>  
                            </div>  
                            <div class="mb-3">  
                                <label for="biaya" class="form-label">Biaya</label>  
                                <input type="number" step="1000" name="biaya" id="biaya" class="form-control" value="8000" required readonly>  
                                <div class="mt-2">  
                                    <button type="button" class="btn btn-secondary" id="decrease">- 1,000</button>  
                                    <button type="button" class="btn btn-secondary" id="increase">+ 1,000</button>  
                                </div>  
                                <small class="form-text text-muted">*Lakukan tawaran harga di sini</small>
                            </div>   
                            <div class="mb-3">  
                                <label for="catatan" class="form-label">Catatan</label>  
                                <textarea name="catatan" id="catatan" class="form-control" placeholder="Catatan (opsional)"></textarea>  
                            </div>  
                            <button type="submit" class="btn btn-primary w-100">Pesan</button>  
                        </form>  
                        <script>  
    const biayaInput = document.getElementById('biaya');  
    const increaseButton = document.getElementById('increase');  
    const decreaseButton = document.getElementById('decrease');  
  
    increaseButton.addEventListener('click', function() {  
        let currentValue = parseInt(biayaInput.value);  
        biayaInput.value = currentValue + 1000;  
    });  
  
    decreaseButton.addEventListener('click', function() {  
        let currentValue = parseInt(biayaInput.value);  
        if (currentValue > 8000) { // Prevent going below the initial price  
            biayaInput.value = currentValue - 1000;  
        }  
    });  
</script>  

                    </div>  
                </div>  
            </div>  
        </div>  
    </div>  
</body>  

</html>
