<?php
include "config.php";   

if (!isset($_SESSION['id_driver'])) {
    die("Anda belum login sebagai driver.");
}

// Process status update if posted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status']) && isset($_POST['id_pengantaran'])) {
    $update_query = "UPDATE pengantaran_orang SET status = ? WHERE id_pengantaran = ?";
    $update_stmt = $koneksi->prepare($update_query);
    $update_stmt->bind_param("si", $_POST['status'], $_POST['id_pengantaran']);
    $update_stmt->execute();
    $update_stmt->close();
}

$id_driver = $_SESSION['id_driver'];

$query = "
    SELECT 
        po.id_pengantaran, 
        u.nama AS nama_user, 
        u.no_hp, 
        d.nama AS nama_driver, 
        po.titik_jemput, 
        po.titik_antar, 
        po.biaya, 
        po.catatan, 
        po.status,
        po.rating,
        po.status_pembayaran
    FROM 
        pengantaran_orang po
    INNER JOIN user u ON po.id_user = u.id_user
    INNER JOIN driver d ON po.id_driver = d.id_driver
    WHERE 
        d.id_driver = ?
    ORDER BY 
        CASE po.status
            WHEN 'menunggu' THEN 1
            WHEN 'dijemput' THEN 2
            WHEN 'diantar' THEN 3
            WHEN 'selesai' THEN 4
            WHEN 'dibatalkan' THEN 5
        END,
        po.id_pengantaran DESC";

$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $id_driver);
$stmt->execute();
$stmt->bind_result(
    $id_pengantaran, 
    $nama_user, 
    $no_hp, 
    $nama_driver, 
    $titik_jemput, 
    $titik_antar, 
    $biaya, 
    $catatan, 
    $status,
    $rating,
    $status_pembayaran
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pengantaran</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', 'Segoe UI', sans-serif;
        }

        body {
            background-color: #f8fafc;
            color: #1e293b;
            line-height: 1.5;
        }

        .main-wrapper {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        h1 {
            color: #1e293b;
            margin-bottom: 2rem;
            font-size: 1.875rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: white;
        }

        th {
            background-color: #f8fafc;
            color: #64748b;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #e2e8f0;
        }

        td {
            padding: 1.25rem 1rem;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        tr:hover {
            background-color: #f8fafc;
        }

        .status-select {
            padding: 0.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            width: 130px;
            font-size: 0.875rem;
            transition: all 0.2s;
            background-color: white;
            cursor: pointer;
        }

        .status-select:hover {
            border-color: #94a3b8;
        }

        .status-select:focus {
            border-color: #3b82f6;
            outline: none;
            ring: 2px solid #bfdbfe;
        }

        .customer-info {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .customer-name {
            font-weight: 600;
            color: #1e293b;
            font-size: 0.95rem;
        }

        .whatsapp-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #10b981;
            text-decoration: none;
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .whatsapp-link:hover {
            background-color: #ecfdf5;
        }

        .location-info {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .location-item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .location-label {
            color: #64748b;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .location-value {
            color: #1e293b;
            font-size: 0.875rem;
        }

        .price {
            font-weight: 600;
            color: #059669;
            font-size: 1rem;
        }

        .rating {
            color: #eab308;
            letter-spacing: 1px;
            font-size: 0.875rem;
        }

        .status-badge {
            display: inline-block;
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-lunas { 
            background-color: #dcfce7; 
            color: #166534; 
        }
        
        .status-belum-lunas { 
            background-color: #fee2e2; 
            color: #991b1b; 
        }

        .status-menunggu { background-color: #fef9c3; color: #854d0e; }
        .status-dijemput { background-color: #dbeafe; color: #1e40af; }
        .status-diantar { background-color: #f3e8ff; color: #6b21a8; }
        .status-selesai { background-color: #dcfce7; color: #166534; }
        .status-dibatalkan { background-color: #fee2e2; color: #991b1b; }

        .notes {
            font-size: 0.875rem;
            color: #64748b;
            max-width: 300px;
            white-space: pre-wrap;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #64748b;
        }

        @media (max-width: 1024px) {
            .main-wrapper {
                padding: 1rem;
            }

            .table-container {
                border-radius: 8px;
            }

            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>
    <div class="main-wrapper">
        <h1>
            <i class="fas fa-list-alt"></i>
            Daftar Pengantaran
        </h1>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pelanggan</th>
                        <th>Lokasi</th>
                        <th>Biaya</th>
                        <th>Status</th>
                        <th>Pembayaran</th>
                        <th>Rating</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $has_data = false;
                    while ($stmt->fetch()) {
                        $has_data = true;
                        $stars = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
                        // Format phone number for WhatsApp
                        $wa_number = preg_replace('/[^0-9]/', '', $no_hp);
                        if (substr($wa_number, 0, 1) === '0') {
                            $wa_number = '62' . substr($wa_number, 1);
                        }
                        $wa_link = "https://wa.me/{$wa_number}";
                    ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($id_pengantaran); ?></td>
                            <td>
                                <div class="customer-info">
                                    <span class="customer-name"><?php echo htmlspecialchars($nama_user); ?></span>
                                    <a href="<?php echo $wa_link; ?>" target="_blank" class="whatsapp-link">
                                        <i class="fab fa-whatsapp"></i>
                                        <?php echo htmlspecialchars($no_hp); ?>
                                    </a>
                                </div>
                            </td>
                            <td>
                                <div class="location-info">
                                    <div class="location-item">
                                        <span class="location-label">Titik Jemput</span>
                                        <span class="location-value"><?php echo htmlspecialchars($titik_jemput); ?></span>
                                    </div>
                                    <div class="location-item">
                                        <span class="location-label">Titik Antar</span>
                                        <span class="location-value"><?php echo htmlspecialchars($titik_antar); ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="price">
                                    Rp <?php echo number_format($biaya, 0, ',', '.'); ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" class="status-form">
                                    <input type="hidden" name="id_pengantaran" value="<?php echo htmlspecialchars($id_pengantaran); ?>">
                                    <select name="status" class="status-select" onchange="this.form.submit()">
                                        <option value="menunggu" <?php echo $status === 'menunggu' ? 'selected' : ''; ?>>Menunggu</option>
                                        <option value="dijemput" <?php echo $status === 'dijemput' ? 'selected' : ''; ?>>Dijemput</option>
                                        <option value="diantar" <?php echo $status === 'diantar' ? 'selected' : ''; ?>>Diantar</option>
                                        <option value="selesai" <?php echo $status === 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                                        <option value="dibatalkan" <?php echo $status === 'dibatalkan' ? 'selected' : ''; ?>>Dibatalkan</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $status_pembayaran)); ?>">
                                    <?php echo $status_pembayaran; ?>
                                </span>
                            </td>
                            <td>
                                <span class="rating"><?php echo $stars; ?></span>
                            </td>
                            <td>
                                <div class="notes">
                                    <?php echo $catatan ? htmlspecialchars($catatan) : '-'; ?>
                                </div>
                            </td>
                        </tr>
                    <?php 
                    }
                    if (!$has_data) {
                        echo '<tr><td colspan="8"><div class="empty-state">Tidak ada data pengantaran</div></td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelects = document.querySelectorAll('.status-select');
        statusSelects.forEach(select => {
            select.addEventListener('change', function() {
                this.style.opacity = '0.5';
                this.parentElement.submit();
                setTimeout(() => {
                    this.style.opacity = '1';
                }, 300);
            });
        });
    });
    </script>
</body>
</html>

<?php
$stmt->close();
$koneksi->close();
?>