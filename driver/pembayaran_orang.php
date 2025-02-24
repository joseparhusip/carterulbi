<?php
include 'config.php';

// Periksa apakah session id_driver sudah ada
if (!isset($_SESSION['id_driver'])) {
    die("Session id_driver tidak ditemukan. Silakan login terlebih dahulu.");
}

// Ambil id_driver dari session
$id_driver = $_SESSION['id_driver'];

// Query untuk mengambil data pengantaran berdasarkan id_driver
$query = "SELECT po.id_pengantaran, u.nama as nama_user, d.nama as nama_driver, 
          po.biaya, po.catatan, po.status_pembayaran 
          FROM pengantaran_orang po
          INNER JOIN user u ON po.id_user = u.id_user
          INNER JOIN driver d ON po.id_driver = d.id_driver
          WHERE po.id_driver = ?";
$stmt = mysqli_prepare($koneksi, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $id_driver);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id_pengantaran, $nama_user, $nama_driver, $biaya, $catatan, $status_pembayaran);
} else {
    die("Gagal mempersiapkan query.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabel Pembayaran</title>
    <style>
        /* Reset default margin and padding */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            line-height: 1.6;
        }

        /* Table Wrapper */
        .table-wrapper {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Table Styling */
        .styled-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .styled-table th,
        .styled-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .styled-table th {
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
            text-transform: uppercase;
        }

        .styled-table tr:hover {
            background-color: #f1f1f1;
        }

        /* Dropdown Styling */
        .dropdown-status {
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 14px;
            color: white;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            border: none;
            outline: none;
        }

        .pending {
            background-color: #ffc107; /* Kuning */
        }

        .paid {
            background-color: #28a745; /* Hijau */
        }

        .failed {
            background-color: #dc3545; /* Merah */
        }

        .refunded {
            background-color: #6c757d; /* Abu-abu */
        }
    </style>
</head>
<body>
    <div class="table-wrapper">
        <h2 style="text-align: center; margin-bottom: 20px;">Daftar Pengantaran</h2>
        <table class="styled-table">
            <thead>
                <tr>
                    <th>ID Pengantaran</th>
                    <th>Nama Penumpang</th>
                    <th>Nama Driver</th>
                    <th>Biaya</th>
                    <th>Catatan</th>
                    <th>Status Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while (mysqli_stmt_fetch($stmt)) {
                    echo "<tr>";
                    echo "<td>$id_pengantaran</td>";
                    echo "<td>$nama_user</td>";
                    echo "<td>$nama_driver</td>";
                    echo "<td>Rp " . number_format($biaya, 0, ',', '.') . "</td>";
                    echo "<td>$catatan</td>";
                    echo "<td>
                            <form action='status_pembayaranorang.php' method='POST'>
                                <input type='hidden' name='id_pengantaran' value='$id_pengantaran'>
                                <select name='status_pembayaran' class='dropdown-status " . strtolower($status_pembayaran) . "' onchange='this.form.submit()'>
                                    <option value='PENDING' " . ($status_pembayaran == 'PENDING' ? 'selected' : '') . ">PENDING</option>
                                    <option value='PAID' " . ($status_pembayaran == 'PAID' ? 'selected' : '') . ">PAID</option>
                                    <option value='FAILED' " . ($status_pembayaran == 'FAILED' ? 'selected' : '') . ">FAILED</option>
                                    <option value='REFUNDED' " . ($status_pembayaran == 'REFUNDED' ? 'selected' : '') . ">REFUNDED</option>
                                </select>
                            </form>
                          </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php
mysqli_stmt_close($stmt);
mysqli_close($koneksi);
?>