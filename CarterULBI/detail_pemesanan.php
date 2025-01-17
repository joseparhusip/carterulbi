<?php
include 'config.php'; // Mengimpor file konfigurasi koneksi database                            

// Cek apakah pengguna sudah login                            
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Jika belum login, redirect ke halaman login                            
    exit();
}

// Fungsi untuk mendapatkan detail pemesanan                            
function getOrderDetails()
{
    global $koneksi;
    $stmt = $koneksi->prepare("SELECT                               
        po.id_pengantaran,                              
        u.nama AS user_name,                              
        d.nama AS driver_name,                              
        d.plat_nomor,                              
        d.kendaraan,                              
        d.gambardriver,                              
        po.titik_jemput,                              
        po.titik_antar,                              
        po.biaya,  -- Mengambil kolom biaya                            
        po.tawarharga,  -- Mengambil kolom tawarharga                            
        po.status                              
    FROM                               
        pengantaran_orang po                              
    JOIN                               
        user u ON po.id_user = u.id_user                              
    JOIN                               
        driver d ON po.id_driver = d.id_driver");
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Ambil detail pemesanan                            
$orderDetails = getOrderDetails();

// Fungsi untuk memperbarui biaya atau tawarharga            
function updatePrice($idPengantaran, $field, $value)
{
    global $koneksi;
    $stmt = $koneksi->prepare("UPDATE pengantaran_orang SET $field = ? WHERE id_pengantaran = ?");
    $stmt->bind_param("di", $value, $idPengantaran);
    $stmt->execute();
    return $stmt->affected_rows > 0;
}

// Perbarui biaya atau tawarharga jika ada permintaan POST            
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPengantaran = $_POST['idPengantaran'];
    $field = $_POST['field'];
    $value = $_POST['value'];

    if (updatePrice($idPengantaran, $field, $value)) {
        echo json_encode(['success' => true, 'message' => 'Data berhasil diperbarui']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui data']);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pemesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            margin-top: 20px;
        }

        .card {
            border-radius: 15px;
            background-color: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-header {
            background-color: #ffffff;
            color: #333333;
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            border-bottom: 1px solid #dee2e6;
            padding: 15px;
        }

        .table {
            margin: 0;
            border-collapse: collapse;
            width: 100%;
        }

        .table thead th {
            background-color: #f8f9fa;
            color: #333333;
            text-align: left;
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
        }

        .table tbody tr:hover {
            background-color: #f1f3f5;
        }

        .table td {
            vertical-align: middle;
            text-align: left;
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
        }

        .table th {
            vertical-align: middle;
            text-align: left;
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
        }

        .driver-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
            transition: transform 0.3s;
        }

        .driver-image:hover {
            transform: scale(1.2);
        }

        .no-data {
            text-align: center;
            font-style: italic;
            color: #868e96;
            padding: 15px;
        }

        .price-control {
            display: flex;
            align-items: center;
        }

        .price-control button {
            background-color: #007bff;
            color: #ffffff;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 5px;
        }

        .price-control button:hover {
            background-color: #0056b3;
        }

        .price-control input {
            width: 100px;
            text-align: center;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 5px;
        }

        .update-button {
            margin-top: 10px;
        }

        .update-button button {
            background-color: #28a745;
            color: #ffffff;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .update-button button:hover {
            background-color: #218838;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {

            .table,
            .table tbody,
            .table tr,
            .table td {
                display: block;
                width: 100%;
            }

            .table tr {
                margin-bottom: 15px;
                border: 1px solid #dee2e6;
                border-radius: 10px;
            }

            .table td {
                text-align: left;
                position: relative;
                padding-left: 50%;
            }

            .table td:before {
                content: attr(data-label);
                position: absolute;
                left: 10px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                font-weight: bold;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                Detail Pemesanan
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID Pemesanan</th>
                            <th>Nama User</th>
                            <th>Nama Driver</th>
                            <th>Plat Nomor</th>
                            <th>Kendaraan</th>
                            <th>Gambar Driver</th>
                            <th>Titik Jemput</th>
                            <th>Titik Antar</th>
                            <th>Biaya</th> <!-- Menambahkan header untuk biaya -->
                            <th>Tawar Harga</th> <!-- Menambahkan header untuk tawar harga -->
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($orderDetails) > 0): ?>
                            <?php foreach ($orderDetails as $order): ?>
                                <tr>
                                    <td data-label="ID Pemesanan"><?= htmlspecialchars($order['id_pengantaran']) ?></td>
                                    <td data-label="Nama User"><?= htmlspecialchars($order['user_name']) ?></td>
                                    <td data-label="Nama Driver"><?= htmlspecialchars($order['driver_name']) ?></td>
                                    <td data-label="Plat Nomor"><?= htmlspecialchars($order['plat_nomor']) ?></td>
                                    <td data-label="Kendaraan"><?= htmlspecialchars($order['kendaraan']) ?></td>
                                    <td data-label="Gambar Driver">
                                        <img src="../gambardriver/<?= htmlspecialchars($order['gambardriver']) ?>"
                                            alt="Gambar Driver"
                                            class="driver-image">
                                    </td>
                                    <td data-label="Titik Jemput"><?= htmlspecialchars($order['titik_jemput']) ?></td>
                                    <td data-label="Titik Antar"><?= htmlspecialchars($order['titik_antar']) ?></td>
                                    <td data-label="Biaya">
                                        <div class="price-control">
                                            <input type="text" id="biaya_<?= htmlspecialchars($order['id_pengantaran']) ?>" value="Rp<?= number_format($order['biaya'], 2, ',', '.') ?>" readonly>
                                        </div>
                                    </td>
                                    <td data-label="Tawar Harga">
                                        <div class="price-control">
                                            <button onclick="updatePrice(<?= htmlspecialchars($order['id_pengantaran']) ?>, 'tawarharga', -3000)">-</button>
                                            <input type="text" id="tawarharga_<?= htmlspecialchars($order['id_pengantaran']) ?>" value="Rp<?= number_format($order['tawarharga'], 2, ',', '.') ?>">
                                            <button onclick="updatePrice(<?= htmlspecialchars($order['id_pengantaran']) ?>, 'tawarharga', 3000)">+</button>
                                        </div>
                                    </td>
                                    <td data-label="Status"><?= htmlspecialchars($order['status']) ?></td>
                                    <td data-label="Aksi">
                                        <div class="update-button">
                                            <button onclick="updatePriceManually(<?= htmlspecialchars($order['id_pengantaran']) ?>)">Update</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="12" class="no-data">Tidak ada pemesanan ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function updatePrice(idPengantaran, field, increment) {
            const currentValue = parseFloat(document.getElementById(field + '_' + idPengantaran).value.replace(/[^0-9.,]/g, '').replace(',', '.'));
            const newValue = currentValue + increment;

            // Pastikan nilai tawarharga adalah kelipatan 3000  
            const roundedValue = Math.round(newValue / 3000) * 3000;

            document.getElementById(field + '_' + idPengantaran).value = 'Rp' + roundedValue.toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });

            fetch('detail_pemesanan.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `idPengantaran=${idPengantaran}&field=${field}&value=${roundedValue}`
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memperbarui data.');
                });
        }

        function updatePriceManually(idPengantaran) {
            const tawarhargaInput = document.getElementById('tawarharga_' + idPengantaran);
            const tawarhargaValue = parseFloat(tawarhargaInput.value.replace(/[^0-9.,]/g, '').replace(',', '.'));

            // Pastikan nilai tawarharga adalah kelipatan 3000  
            if (tawarhargaValue % 3000 !== 0) {
                alert('Nilai tawar harga harus kelipatan 3000.');
                return;
            }

            fetch('detail_pemesanan.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `idPengantaran=${idPengantaran}&field=tawarharga&value=${tawarhargaValue}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        tawarhargaInput.value = 'Rp' + tawarhargaValue.toLocaleString('id-ID', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        });
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memperbarui data.');
                });
        }
    </script>
</body>

</html>