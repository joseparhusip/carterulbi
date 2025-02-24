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
    die('Error: Data user tidak ditemukan.');        
}        
$stmt_user->close();
      
// Modifikasi query untuk menambahkan bukti_pengiriman
$query_pesanan = "        
    SELECT         
        pm.id_pesanan,        
        u.nama,        
        d.nama,        
        d.plat_nomor,        
        p.nama_produk,        
        pm.alamat_pengiriman,        
        pm.quantity,        
        pm.harga,        
        pm.ongkir,        
        pm.subtotal,        
        pm.total_harga,        
        pm.catatan,        
        pm.status_pesanan,        
        TIME_TO_SEC(TIMEDIFF(pm.estimasi_waktu, CURRENT_TIME)) as estimasi_detik,
        pm.rating,
        pm.bukti_pengiriman     
    FROM         
        pesanan_makanan pm        
    JOIN         
        user u ON pm.id_user = u.id_user        
    JOIN         
        driver d ON pm.id_driver = d.id_driver        
    JOIN         
        produk_makanan p ON pm.id_produk = p.id_produk        
    WHERE         
        pm.id_user = ?        
    ORDER BY         
        pm.id_pesanan DESC";        

$stmt_pesanan = $koneksi->prepare($query_pesanan);        
$stmt_pesanan->bind_param('i', $id_user);        
$stmt_pesanan->execute();        
$stmt_pesanan->bind_result(
    $id_pesanan,
    $nama_user,
    $nama_driver,
    $plat_nomor,
    $nama_produk,
    $alamat_pengiriman,
    $quantity,
    $harga,
    $ongkir,
    $subtotal,
    $total_harga,
    $catatan,
    $status_pesanan,
    $estimasi_detik,
    $rating,
    $bukti_pengiriman
);

$pesanan_items = array();
while ($stmt_pesanan->fetch()) {
    $pesanan_items[] = array(
        'id_pesanan' => $id_pesanan,
        'nama_user' => $nama_user,
        'nama_driver' => $nama_driver,
        'plat_nomor' => $plat_nomor,
        'nama_produk' => $nama_produk,
        'alamat_pengiriman' => $alamat_pengiriman,
        'quantity' => $quantity,
        'harga' => $harga,
        'ongkir' => $ongkir,
        'subtotal' => $subtotal,
        'total_harga' => $total_harga,
        'catatan' => $catatan,
        'status_pesanan' => $status_pesanan,
        'estimasi_detik' => max(0, $estimasi_detik),
        'rating' => $rating,
        'bukti_pengiriman' => $bukti_pengiriman
    );
}
$stmt_pesanan->close();

function getStatusBadge($status) {
    $colors = [
        'menunggu' => '#6B7280',
        'dikirm' => '#3B82F6',
        'dikirim' => '#F59E0B',
        'selesai' => '#10B981',
        'dibatalkan' => '#EF4444'
    ];
    
    $color = isset($colors[$status]) ? $colors[$status] : '#000000';
    return "<span class='status-badge' style='background-color: {$color};'>" . ucfirst($status) . "</span>";
}

function generateStars($rating) {
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $stars .= '<span class="star filled">★</span>';
        } else {
            $stars .= '<span class="star">★</span>';
        }
    }
    return $stars;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan Makanan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            color: #1f2937;
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            color: #1e293b;
            margin-bottom: 40px;
            font-size: 2.5rem;
            font-weight: 600;
        }

        .order-grid {
            display: grid;
            gap: 30px;
            grid-template-columns: 1fr;
        }

        .order-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 
                        0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s ease;
        }

        .order-card:hover {
            transform: translateY(-5px);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e5e7eb;
        }

        .order-id {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2563eb;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 9999px;
            color: white;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .order-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .detail-group {
            margin-bottom: 15px;
        }

        .detail-label {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .detail-value {
            font-weight: 500;
            color: #1f2937;
        }

        .countdown-timer {
            background-color: #e5e7eb;
            padding: 8px 12px;
            border-radius: 8px;
            font-weight: 600;
            color: #1f2937;
            display: inline-block;
            min-width: 120px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .countdown-timer.urgent {
            background-color: #fee2e2;
            color: #ef4444;
            animation: pulse 1s infinite;
        }

        .countdown-timer.expired {
            background-color: #f3f4f6;
            color: #6b7280;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
        }

        .modal-content {
            position: relative;
            margin: auto;
            padding: 20px;
            width: 80%;
            max-width: 700px;
            top: 50%;
            transform: translateY(-50%);
        }

        .modal img {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .close {
            position: absolute;
            right: 25px;
            top: 0;
            color: #fff;
            font-size: 35px;
            font-weight: bold;
            cursor: pointer;
        }

        .proof-link {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
        }

        .proof-link:hover {
            text-decoration: underline;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .price-details {
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .total-row {
            border-top: 2px dashed #e5e7eb;
            margin-top: 8px;
            padding-top: 8px;
            font-weight: 600;
            color: #2563eb;
        }

        .actions {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            text-align: center;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .btn-confirm {
            background-color: #ef4444;
            color: white;
        }

        .btn-rating {
            background-color: #10b981;
            color: white;
        }

        .btn-invoice {
            background-color: #3b82f6;
            color: white;
        }

        .btn:hover {
            transform: scale(1.05);
            opacity: 0.9;
        }

        .star {
            color: #d1d5db;
            font-size: 1.25rem;
        }

        .star.filled {
            color: #fbbf24;
        }

        .notes {
            background-color: #fffbeb;
            padding: 12px;
            border-radius: 8px;
            border-left: 4px solid #f59e0b;
            margin-top: 15px;
        }

        @media (max-width: 768px) {
            .order-details {
                grid-template-columns: 1fr;
            }

            .actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }

            .modal-content {
                width: 95%;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Riwayat Pesanan Makanan</h1>
        
        <div class="order-grid">
            <?php foreach ($pesanan_items as $pesanan) { ?>
                <div class="order-card">
                    <div class="order-header">
                        <div class="order-id">Order #<?php echo htmlspecialchars($pesanan['id_pesanan']); ?></div>
                        <?php echo getStatusBadge($pesanan['status_pesanan']); ?>
                    </div>

                    <div class="order-details">
                        <div class="detail-group">
                            <div class="detail-label">Produk</div>
                            <div class="detail-value"><?php echo htmlspecialchars($pesanan['nama_produk']); ?></div>
                        </div>

                        <div class="detail-group">
                            <div class="detail-label">Driver</div>
                            <div class="detail-value">
                                <?php echo htmlspecialchars($pesanan['nama_driver']); ?>
                                (<?php echo htmlspecialchars($pesanan['plat_nomor']); ?>)
                            </div>
                        </div>

                        <div class="detail-group">
                            <div class="detail-label">Alamat Pengiriman</div>
                            <div class="detail-value"><?php echo htmlspecialchars($pesanan['alamat_pengiriman']); ?></div>
                        </div>

                        <div class="detail-group">
                            <div class="detail-label">Estimasi Waktu</div>
                            <div class="detail-value">
                                <div id="timer-<?php echo $pesanan['id_pesanan']; ?>" 
                                     class="countdown-timer"
                                     data-seconds="<?php echo $pesanan['estimasi_detik']; ?>"
                                     data-status="<?php echo $pesanan['status_pesanan']; ?>">
                                    Menghitung...
                                </div>
                            </div>
                        </div>

                        <?php if ($pesanan['bukti_pengiriman']) { ?>
                        <div class="detail-group">
                            <div class="detail-label">Bukti Pengiriman</div>
                            <div class="detail-value">
                                <a href="#" 
                                   class="proof-link" 
                                   onclick="showProof('buktipengiriman/<?php echo htmlspecialchars($pesanan['bukti_pengiriman']); ?>')">
                                    Lihat Bukti Pengiriman
                                </a>
                            </div>
                        </div>
                        <?php } ?>
                    </div>

                    <div class="price-details">
                        <div class="price-row">
                            <span>Harga (<?php echo htmlspecialchars($pesanan['quantity']); ?> item)</span>
                            <span>Rp <?php echo number_format($pesanan['harga'], 0, ',', '.'); ?></span>
                        </div>
                    <div class="price-row">
                            <span>Subtotal</span>
                            <span>Rp <?php echo number_format($pesanan['subtotal'], 0, ',', '.'); ?></span>
                        </div>
                        <div class="price-row">
                            <span>Ongkir</span>
                            <span>Rp <?php echo number_format($pesanan['ongkir'], 0, ',', '.'); ?></span>
                        </div>
                        <div class="price-row total-row">
                            <span>Total</span>
                            <span>Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?></span>
                        </div>
                    </div>

                    <?php if ($pesanan['catatan']) { ?>
                        <div class="notes">
                            <div class="detail-label">Catatan:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($pesanan['catatan']); ?></div>
                        </div>
                    <?php } ?>

                    <div class="actions">
                        <?php if($pesanan['status_pesanan'] === 'dikirim'): ?>
                            <a href="konfirmasi_pesanan.php?id_pesanan=<?php echo htmlspecialchars($pesanan['id_pesanan']); ?>" 
                               class="btn btn-confirm">
                                Konfirmasi Pesanan
                            </a>
                        <?php endif; ?>

                        <?php if ($pesanan['rating'] > 0) { ?>
                            <div class="rating-display">
                                <?php echo generateStars($pesanan['rating']); ?>
                            </div>
                        <?php } else if ($pesanan['status_pesanan'] === 'selesai') { ?>
                            <a href="utama.php?page=ratingdriverfood&id_pesanan=<?php echo htmlspecialchars($pesanan['id_pesanan']); ?>" 
                               class="btn btn-rating">
                                Berikan Rating</a>
                        <?php } ?>

                        <a href="cetak.php?id_pesanan=<?php echo htmlspecialchars($pesanan['id_pesanan']); ?>" 
                           class="btn btn-invoice" 
                           onclick="window.open(this.href, 'cetak_invoice', 'width=800,height=600'); return false;">
                            Cetak Invoice
                        </a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Modal for proof of delivery -->
    <div id="proofModal" class="modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <div class="modal-content">
            <img id="proofImage" src="" alt="Bukti Pengiriman">
        </div>
    </div>

    <script>
        function formatTime(seconds) {
            if (seconds <= 0) return "Waktu Habis";
            
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = seconds % 60;
            
            return `${minutes} menit ${remainingSeconds} detik`;
        }

        function updateTimer(timerElement) {
            let seconds = parseInt(timerElement.dataset.seconds);
            const status = timerElement.dataset.status;
            
            // Jika pesanan sudah selesai atau dibatalkan, tidak perlu countdown
            if (status === 'selesai' || status === 'dibatalkan') {
                timerElement.textContent = 'Pesanan ' + status;
                timerElement.classList.add('expired');
                return;
            }
            
            function countdown() {
                if (seconds <= 0) {
                    timerElement.textContent = "Waktu Habis";
                    timerElement.classList.add('expired');
                    return;
                }

                // Update tampilan timer
                timerElement.textContent = formatTime(seconds);
                
                // Tambahkan class urgent jika waktu kurang dari 5 menit
                if (seconds < 300) {
                    timerElement.classList.add('urgent');
                }
                
                seconds--;
                timerElement.dataset.seconds = seconds;
                
                // Lanjutkan countdown
                setTimeout(countdown, 1000);
            }
            
            countdown();
        }

        // Modal functions
        function showProof(imagePath) {
            const modal = document.getElementById('proofModal');
            const img = document.getElementById('proofImage');
            img.src = imagePath;
            modal.style.display = 'block';
        }

        function closeModal() {
            const modal = document.getElementById('proofModal');
            modal.style.display = 'none';
        }

        // Close modal when clicking outside the image
        window.onclick = function(event) {
            const modal = document.getElementById('proofModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        // Inisialisasi semua timer saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            const timers = document.querySelectorAll('.countdown-timer');
            timers.forEach(updateTimer);
        });
    </script>
</body>
</html>