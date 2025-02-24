<?php

include 'config.php';

// Periksa apakah session id_driver ada
if (!isset($_SESSION['id_driver'])) {
    die("Anda belum login sebagai driver.");
}

$id_driver = $_SESSION['id_driver'];

// Query untuk mengambil data pesanan berdasarkan id_driver
$query = "SELECT pm.id_pesanan, u.nama as nama_user, d.nama as nama_driver, 
          pm.harga, pm.quantity, pm.subtotal, pm.ongkir, pm.total_harga, 
          pm.status_pembayaran 
          FROM pesanan_makanan pm 
          LEFT JOIN user u ON pm.id_user = u.id_user 
          LEFT JOIN driver d ON pm.id_driver = d.id_driver
          WHERE pm.id_driver = $id_driver"; // Filter berdasarkan id_driver

$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pembayaran Pesanan Makanan</title>
    <style>
        /* Styling untuk wrapper */
        .dashboard-wrapper {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            margin: 20px auto;
            max-width: 1400px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Styling untuk judul */
        .judul-dashboard {
            text-align: center;
            color: #333;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        /* Styling untuk tabel */
        .tabel-pesanan {
            width: 100%;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .tabel-pesanan th,
        .tabel-pesanan td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .tabel-pesanan th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
        }

        .tabel-pesanan tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .tabel-pesanan tr:hover {
            background-color: #e0f7fa;
            transition: background-color 0.3s ease;
        }

        /* Styling untuk status pembayaran */
        .status-pending {
            color: #FFC107; /* Kuning */
            font-weight: bold;
        }

        .status-paid {
            color: #4CAF50; /* Hijau */
            font-weight: bold;
        }

        .status-failed {
            color: #f44336; /* Merah */
            font-weight: bold;
        }

        .status-refunded {
            color: #9E9E9E; /* Abu-abu */
            font-weight: bold;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .tabel-pesanan th,
            .tabel-pesanan td {
                padding: 10px;
                font-size: 14px;
            }

            .judul-dashboard {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
<div class="dashboard-wrapper">
    <h2 class="judul-dashboard">Data Pesanan Makanan</h2>
    <table class="tabel-pesanan">
        <thead>
            <tr>
                <th>ID Pesanan</th>
                <th>Nama Pelanggan</th>
                <th>Nama Driver</th>
                <th>Harga</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Ongkir</th>
                <th>Total Harga</th>
                <th>Status Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_array($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['id_pesanan'] . "</td>";
                    echo "<td>" . $row['nama_user'] . "</td>";
                    echo "<td>" . $row['nama_driver'] . "</td>";
                    echo "<td>Rp " . number_format($row['harga'], 0, ',', '.') . "</td>";
                    echo "<td>" . $row['quantity'] . "</td>";
                    echo "<td>Rp " . number_format($row['subtotal'], 0, ',', '.') . "</td>";
                    echo "<td>Rp " . number_format($row['ongkir'], 0, ',', '.') . "</td>";
                    echo "<td>Rp " . number_format($row['total_harga'], 0, ',', '.') . "</td>";

                    // Menentukan kelas CSS berdasarkan status_pembayaran
                    $statusClass = '';
                    switch ($row['status_pembayaran']) {
                        case 'PENDING':
                            $statusClass = 'status-pending';
                            break;
                        case 'PAID':
                            $statusClass = 'status-paid';
                            break;
                        case 'FAILED':
                            $statusClass = 'status-failed';
                            break;
                        case 'REFUNDED':
                            $statusClass = 'status-refunded';
                            break;
                        default:
                            $statusClass = '';
                            break;
                    }

                    echo "<td class='" . $statusClass . "'>" . $row['status_pembayaran'] . "</td>";
                    echo "</tr>";
                }
                mysqli_free_result($result);
            } else {
                echo "<tr><td colspan='9'>Tidak ada data pesanan.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
</body>
</html>