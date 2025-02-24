<?php
include "config.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);

$data = array();
$error = null;

$query = "SELECT 
    pm.id_pesanan,
    u.nama as nama_user,
    d.nama as nama_driver,
    pm.harga,
    pm.quantity,
    pm.subtotal,
    pm.ongkir,
    pm.total_harga,
    pm.bukti_pembayaran,
    pm.status_pembayaran
FROM pesanan_makanan pm
LEFT JOIN user u ON pm.id_user = u.id_user
LEFT JOIN driver d ON pm.id_driver = d.id_driver
ORDER BY pm.id_pesanan DESC";

if ($stmt = $koneksi->prepare($query)) {
    $stmt->execute();
    $stmt->bind_result(
        $id_pesanan,
        $nama_user,
        $nama_driver,
        $harga,
        $quantity,
        $subtotal,
        $ongkir,
        $total_harga,
        $bukti_pembayaran,
        $status_pembayaran
    );
    
    while ($stmt->fetch()) {
        $data[] = array(
            'id_pesanan' => $id_pesanan,
            'nama_user' => $nama_user,
            'nama_driver' => $nama_driver,
            'harga' => $harga,
            'quantity' => $quantity,
            'subtotal' => $subtotal,
            'ongkir' => $ongkir,
            'total_harga' => $total_harga,
            'bukti_pembayaran' => $bukti_pembayaran,
            'status_pembayaran' => $status_pembayaran
        );
    }
    
    if (empty($data)) {
        $error = 'Tidak ada data pesanan';
    }
    
    $stmt->close();
} else {
    $error = 'Gagal mempersiapkan query';
}

$koneksi->close();

function getStatusColor($status) {
    switch ($status) {
        case 'PAID':
            return '#28a745';
        case 'PENDING':
            return '#ffc107';
        case 'FAILED':
            return '#dc3545';
        case 'REFUNDED':
            return '#6c757d';
        default:
            return '#000000';
    }
}

function getFileExtension($filename) {
    return pathinfo($filename, PATHINFO_EXTENSION);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pembayaran Makanan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }

        .payment-wrapper {
            margin: 20px auto;
            max-width: 1200px;
            background-color: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }

        .payment-title {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
            font-size: 24px;
            font-weight: 600;
        }

        .scroll-wrapper {
            overflow-x: auto;
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }

        .payment-table {
            width: 100%;
            border-collapse: collapse;
            white-space: nowrap;
            background-color: white;
        }

        .payment-table th,
        .payment-table td {
            padding: 15px;
            border: 1px solid #e2e8f0;
            text-align: center;
        }

        .payment-table th {
            background-color: #f8fafc;
            font-weight: 600;
            color: #4a5568;
            text-transform: uppercase;
            font-size: 14px;
            position: sticky;
            top: 0;
        }

        .payment-table tr:hover {
            background-color: #f8fafc;
        }

        .status-select {
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: 600;
            width: 130px;
            border: 2px solid #e2e8f0;
            outline: none;
            transition: all 0.3s ease;
        }

        .status-select:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52,152,219,0.2);
        }

        .error-box {
            color: #721c24;
            background-color: #f8d7da;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-top: 20px;
            border: 1px solid #f5c6cb;
        }

        .payment-proof {
            max-width: 80px;
            height: auto;
            border-radius: 4px;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .payment-proof:hover {
            transform: scale(1.1);
        }

        .price-cell {
            text-align: right;
            font-family: 'Courier New', Courier, monospace;
            font-weight: 600;
        }

        .btn-download {
            background-color: #2ecc71;
            padding: 6px 12px;
            font-size: 13px;
            border-radius: 6px;
            cursor: pointer;
            border: none;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-download:hover {
            background-color: #27ae60;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            max-width: 80%;
            max-height: 80vh;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            position: relative;
        }

        .modal-close {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            font-size: 24px;
            color: #666;
        }

        .modal-image {
            max-width: 100%;
            max-height: calc(80vh - 40px);
            object-fit: contain;
        }

        @media (max-width: 768px) {
            .payment-wrapper {
                margin: 10px;
                padding: 15px;
            }

            .payment-table th,
            .payment-table td {
                padding: 10px;
                font-size: 14px;
            }

            .btn-download {
                padding: 6px 12px;
                font-size: 13px;
            }

            .status-select {
                width: 110px;
                padding: 6px 8px;
            }
        }
    </style>
</head>
<body>
    <div class="payment-wrapper">
        <h1 class="payment-title">Daftar Pembayaran Makanan</h1>
        
        <?php if ($error): ?>
            <div class="error-box">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php else: ?>
            <div class="scroll-wrapper">
                <table class="payment-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pemesan</th>
                            <th>Driver</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                            <th>Ongkir</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Bukti</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id_pesanan']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_user']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_driver']); ?></td>
                            <td class="price-cell">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                            <td class="price-cell">Rp <?php echo number_format($row['subtotal'], 0, ',', '.'); ?></td>
                            <td class="price-cell">Rp <?php echo number_format($row['ongkir'], 0, ',', '.'); ?></td>
                            <td class="price-cell">Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                            <td>
                                <select class="status-select" onchange="updateStatus(<?php echo $row['id_pesanan']; ?>, this.value)" style="color: <?php echo getStatusColor($row['status_pembayaran']); ?>">
                                    <option value="PENDING" <?php echo $row['status_pembayaran'] == 'PENDING' ? 'selected' : ''; ?> style="color: <?php echo getStatusColor('PENDING'); ?>">PENDING</option>
                                    <option value="PAID" <?php echo $row['status_pembayaran'] == 'PAID' ? 'selected' : ''; ?> style="color: <?php echo getStatusColor('PAID'); ?>">PAID</option>
                                    <option value="FAILED" <?php echo $row['status_pembayaran'] == 'FAILED' ? 'selected' : ''; ?> style="color: <?php echo getStatusColor('FAILED'); ?>">FAILED</option>
                                    <option value="REFUNDED" <?php echo $row['status_pembayaran'] == 'REFUNDED' ? 'selected' : ''; ?> style="color: <?php echo getStatusColor('REFUNDED'); ?>">REFUNDED</option>
                                </select>
                            </td>
                            <td>
                                <?php if ($row['bukti_pembayaran']): ?>
                                    <div style="display: flex; flex-direction: column; align-items: center; gap: 5px;">
                                        <img src="../buktipembayaran/<?php echo htmlspecialchars($row['bukti_pembayaran']); ?>" 
                                             alt="Bukti Pembayaran" 
                                             class="payment-proof" 
                                             onclick="showImage('../buktipembayaran/<?php echo htmlspecialchars($row['bukti_pembayaran']); ?>')">
                                        <a href="../buktipembayaran/<?php echo htmlspecialchars($row['bukti_pembayaran']); ?>" 
                                           download 
                                           class="btn btn-download">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <em>-</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal for image preview -->
    <div id="imageModal" class="modal" onclick="closeModal()">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <img id="modalImage" class="modal-image" src="" alt="Preview">
        </div>
    </div>

    <script>
        function showImage(url) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            modal.style.display = 'flex';
            modalImg.src = url;
        }
        
        function closeModal() {
            const modal = document.getElementById('imageModal');
            modal.style.display = 'none';
        }
        
        // Close modal when clicking the close button
        document.querySelector('.modal-close').addEventListener('click', function(e) {
            e.stopPropagation();
            closeModal();
        });
        
        // Prevent modal from closing when clicking the image
        document.querySelector('.modal-content').addEventListener('click', function(e) {
            e.stopPropagation();
        });
        
       function updateStatus(id, status) {
    // Buat FormData object
    const formData = new FormData();
    formData.append('id_pesanan', id);
    formData.append('status', status);

    // Kirim request AJAX
    fetch('update_status.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Tampilkan notifikasi sukses
            alert(data.message);
            // Optional: refresh halaman untuk memperbarui data
            // window.location.reload();
        } else {
            // Tampilkan pesan error
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengupdate status');
    });
}
    </script>
</body>
</html>