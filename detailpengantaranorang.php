<?php        
include 'config.php';        
    
if (!isset($_SESSION['username'])) {    
    die('Error: Anda harus login untuk melihat riwayat pesanan.');    
}    
    
$username = $_SESSION['username'];        
$query_user = "SELECT id_user FROM user WHERE username = ?";        
$stmt_user = $koneksi->prepare($query_user);        
$stmt_user->bind_param('s', $username);        
$stmt_user->execute();        
$stmt_user->bind_result($id_user);
if (!$stmt_user->fetch()) {    
    $stmt_user->close();
    die('Error: User tidak ditemukan.');    
}    
$stmt_user->close();
    
$query_order = "    
    SELECT     
        po.id_pengantaran,    
        u.id_user,    
        u.nama,    
        d.id_driver,    
        d.gambardriver,    
        d.nama,    
        d.plat_nomor,    
        po.titik_antar,    
        po.titik_jemput,    
        po.biaya,    
        po.catatan,    
        po.status,    
        po.rating    
    FROM     
        pengantaran_orang po    
    JOIN     
        user u ON po.id_user = u.id_user    
    JOIN     
        driver d ON po.id_driver = d.id_driver    
    WHERE     
        u.id_user = ?    
    ORDER BY     
        po.id_pengantaran DESC";    

$stmt_order = $koneksi->prepare($query_order);        
$stmt_order->bind_param('i', $id_user);        
$stmt_order->execute();        
$stmt_order->bind_result(
    $id_pengantaran, 
    $user_id, 
    $nama_user, 
    $id_driver, 
    $gambardriver, 
    $nama_driver, 
    $plat_nomor, 
    $titik_antar, 
    $titik_jemput, 
    $biaya, 
    $catatan, 
    $status, 
    $rating
);

$orders = array();
while ($stmt_order->fetch()) {
    $orders[] = array(
        'id_pengantaran' => $id_pengantaran,
        'id_user' => $user_id,
        'nama_user' => $nama_user,
        'id_driver' => $id_driver,
        'gambardriver' => $gambardriver,
        'nama_driver' => $nama_driver,
        'plat_nomor' => $plat_nomor,
        'titik_antar' => $titik_antar,
        'titik_jemput' => $titik_jemput,
        'biaya' => $biaya,
        'catatan' => $catatan,
        'status' => $status,
        'rating' => $rating
    );
}
$stmt_order->close();

function getStatusBadge($status) {
    $colors = [
        'menunggu' => '#6c757d',
        'dijemput' => '#007bff',
        'diantar' => '#ffc107',
        'selesai' => '#28a745',
        'dibatalkan' => '#dc3545'
    ];
    
    $color = isset($colors[$status]) ? $colors[$status] : '#6c757d';
    return "<span class='status-badge' style='background-color: {$color}'>" . ucfirst($status) . "</span>";
}
?>

<!DOCTYPE html>    
<html lang="id">    
<head>    
    <meta charset="UTF-8">    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    
    <title>Riwayat Pengantaran</title>    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>    
        :root {
            --primary-color: #2575fc;
            --secondary-color: #6a11cb;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-color: #f8f9fa;
            --dark-color: #1e293b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {    
            font-family: 'Poppins', sans-serif;    
            background-color: #f0f2f5;    
            color: var(--dark-color);
            line-height: 1.6;
        }    

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {    
            text-align: center;
            color: var(--dark-color);
            margin: 2rem 0;
            font-size: 2.5rem;
            font-weight: 600;
        }    

        .order-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            padding: 20px;
        }

        .order-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .order-card:hover {
            transform: translateY(-5px);
        }

        .order-header {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            color: white;
            padding: 20px;
            position: relative;
        }

        .order-id {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .order-body {
            padding: 20px;
        }

        .driver-info {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .driver-image {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
            border: 3px solid var(--primary-color);
        }

        .driver-details {
            flex-grow: 1;
        }

        .driver-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark-color);
        }

        .plat-nomor {
            font-size: 0.9rem;
            color: #666;
        }

        .order-info {
            margin-top: 15px;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .info-label {
            min-width: 120px;
            font-weight: 500;
            color: #555;
        }

        .info-value {
            flex-grow: 1;
            color: var(--dark-color);
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            color: white;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .price {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-top: 15px;
        }

        .rating-container {
            margin-top: 15px;
            text-align: center;
        }

        .stars {
            color: #FFD700;
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .rating-button {
            display: inline-block;
            padding: 8px 20px;
            background-color: var(--success-color);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .rating-button:hover {
            background-color: #218838;
            transform: scale(1.05);
        }

        .no-orders {
            text-align: center;
            padding: 40px;
            font-size: 1.2rem;
            color: #666;
            grid-column: 1 / -1;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            h1 {
                font-size: 2rem;
                margin: 1.5rem 0;
            }

            .order-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
                padding: 10px;
            }

            .order-card {
                margin-bottom: 20px;
            }
        }
    </style>    
</head>    
<body>    
    <div class="container">
        <h1>Riwayat Pengantaran</h1>    
        <div class="order-grid">
            <?php if (empty($orders)): ?>
                <div class="no-orders">
                    <i class="fas fa-history fa-3x" style="color: #666; margin-bottom: 15px;"></i>
                    <p>Belum ada riwayat pesanan.</p>
                </div>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-id">Order #<?php echo htmlspecialchars($order['id_pengantaran']); ?></div>
                            <?php echo getStatusBadge($order['status']); ?>
                        </div>
                        <div class="order-body">
                            <div class="driver-info">
                                <img src="gambardriver/<?php echo htmlspecialchars($order['gambardriver']); ?>" class="driver-image" alt="Driver">
                                <div class="driver-details">
                                    <div class="driver-name"><?php echo htmlspecialchars($order['nama_driver']); ?></div>
                                    <div class="plat-nomor"><?php echo htmlspecialchars($order['plat_nomor']); ?></div>
                                </div>
                            </div>
                            
                            <div class="order-info">
                                <div class="info-item">
                                    <span class="info-label"><i class="fas fa-map-marker-alt"></i> Dari:</span>
                                    <span class="info-value"><?php echo htmlspecialchars($order['titik_jemput']); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label"><i class="fas fa-flag-checkered"></i> Ke:</span>
                                    <span class="info-value"><?php echo htmlspecialchars($order['titik_antar']); ?></span>
                                </div>
                                <?php if ($order['catatan']): ?>
                                <div class="info-item">
                                    <span class="info-label"><i class="fas fa-comment-alt"></i> Catatan:</span>
                                    <span class="info-value"><?php echo htmlspecialchars($order['catatan']); ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="price">
                                    <i class="fas fa-tag"></i> Rp <?php echo number_format($order['biaya'], 0, ',', '.'); ?>
                                </div>
                            </div>

                            <div class="rating-container">
                                <?php if ($order['rating'] > 0): ?>
                                    <div class="stars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star" style="color: <?php echo $i <= $order['rating'] ? '#FFD700' : '#ddd'; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                <?php else: ?>
                                    <a href="utama.php?page=ratingdriver&id_pengantaran=<?php echo htmlspecialchars($order['id_pengantaran']); ?>" class="rating-button">
                                        <i class="fas fa-star"></i> Beri Rating
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>    
</html>