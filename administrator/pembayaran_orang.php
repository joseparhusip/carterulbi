<?php
include 'config.php';
$query = "SELECT po.id_pengantaran, u.nama as nama_user, d.nama as nama_driver, 
          po.biaya, po.catatan, po.status_pembayaran 
          FROM pengantaran_orang po
          INNER JOIN user u ON po.id_user = u.id_user
          INNER JOIN driver d ON po.id_driver = d.id_driver";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $id_pengantaran, $nama_user, $nama_driver, $biaya, $catatan, $status_pembayaran);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tabel Pembayaran</title>
    <style>
        .payment-wrapper {
            padding: 20px;
            margin: 20px auto;
            max-width: 1200px;
        }
        
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        
        .payment-table th, 
        .payment-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .payment-table th {
            background-color: #f4f4f4;
            font-weight: bold;
            color: #333;
        }
        
        .payment-table tr:hover {
            background-color: #f5f5f5;
        }
        
        .payment-status {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 14px;
            display: inline-block;
            font-weight: 500;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-failed {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .status-refunded {
            background-color: #e2e3e5;
            color: #383d41;
        }
    </style>
</head>
<body>
    <div class="payment-wrapper">
        <table class="payment-table">
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
                    // Determine status class based on payment status
                    $statusClass = '';
                    switch($status_pembayaran) {
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
                    }
                    
                    echo "<tr>";
                    echo "<td>$id_pengantaran</td>";
                    echo "<td>$nama_user</td>";
                    echo "<td>$nama_driver</td>";
                    echo "<td>Rp " . number_format($biaya, 0, ',', '.') . "</td>";
                    echo "<td>$catatan</td>";
                    echo "<td><span class='payment-status $statusClass'>$status_pembayaran</span></td>";
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