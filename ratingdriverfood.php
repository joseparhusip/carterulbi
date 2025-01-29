<?php    
include 'config.php'; // Koneksi ke database    
    
// Pastikan pengguna sudah login    
date_default_timezone_set('Asia/Jakarta');    
 
if (!isset($_SESSION['username'])) {    
    echo "<script>alert('Silakan login terlebih dahulu.'); window.location = 'login.php';</script>";    
    exit;    
}    
    
// Ambil id_pesanan dari URL    
$id_pesanan = isset($_GET['id_pesanan']) ? intval($_GET['id_pesanan']) : 0; // Ensure id_pesanan is an integer  
    
// Ambil data driver, nama produk, dan total harga berdasarkan id_pesanan    
$query = "SELECT d.gambardriver, d.nama, d.plat_nomor, d.kendaraan, pm.total_harga, pm.id_produk, pr.nama_produk AS nama_produk    
          FROM pesanan_makanan pm    
          JOIN driver d ON pm.id_driver = d.id_driver    
          JOIN produk_makanan pr ON pm.id_produk = pr.id_produk    
          WHERE pm.id_pesanan = ?";    
$stmt = $koneksi->prepare($query);    
$stmt->bind_param('i', $id_pesanan);    
$stmt->execute();    
$result = $stmt->get_result();    
$data = $result->fetch_assoc();    
if (!$data) {    
    echo "<script>alert('Data pengantaran tidak ditemukan.'); window.location = 'utama.php';</script>";    
    exit;    
}    
?>    
    
<!DOCTYPE html>    
<html lang="id">    
<head>    
    <meta charset="UTF-8">    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    
    <title>Rating Driver</title>    
    <style>    
        .rating-container {    
            max-width: 600px;    
            margin: 0 auto;    
            margin-top: 50px !important;    
            background-color: #fff;    
            padding: 20px;    
            border-radius: 5px;    
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);    
        }    
    
        h1 {    
            text-align: center;    
            color: #333;    
        }    
    
        .driver-info {    
            display: flex;    
            align-items: center;    
            margin-bottom: 20px;    
        }    
    
        .driver-info img {    
            width: 80px;    
            height: auto;    
            border-radius: 5px;    
            margin-right: 20px;    
        }    
    
        .driver-details {    
            flex: 1;    
        }    
    
        .driver-details p {    
            margin: 5px 0;    
            color: #555;    
        }    
    
        .star-rating {    
            display: flex;    
            justify-content: center;    
            margin: 20px 0;    
        }    
    
        .star {    
            font-size: 30px;    
            color: #ccc;    
            cursor: pointer;    
            transition: color 0.3s;    
        }    
    
        .star:hover,    
        .star.selected {    
            color: #f39c12;    
        }    
    
        .submit-button {    
            background-color: #007bff;    
            color: white;    
            border: none;    
            padding: 10px 15px;    
            border-radius: 5px;    
            cursor: pointer;    
            transition: background-color 0.3s;    
            display: block;    
            margin: 0 auto;    
        }    
    
        .submit-button:hover {    
            background-color: #0056b3;    
        }    
    </style>    
</head>    
<body>    
    
<div class="rating-container">    
    <h1>Rating Driver</h1>    
    <div class="driver-info">    
        <img src="gambardriver/<?php echo htmlspecialchars($data['gambardriver']); ?>" alt="Gambar Driver">    
        <div class="driver-details">    
            <p><strong>Nama Driver:</strong> <?php echo htmlspecialchars($data['nama']); ?></p>    
            <p><strong>Plat Nomor:</strong> <?php echo htmlspecialchars($data['plat_nomor']); ?></p>    
            <p><strong>Kendaraan:</strong> <?php echo htmlspecialchars($data['kendaraan']); ?></p>    
            <p><strong>Nama Produk:</strong> <?php echo htmlspecialchars($data['nama_produk']); ?></p>    
            <p><strong>Total Harga:</strong> Rp <?php echo number_format($data['total_harga'], 0, ',', '.'); ?></p>    
        </div>    
    </div>    
    <form method="POST" action="">    
        <div class="star-rating">    
            <span class="star" data-value="1">&#9733;</span>    
            <span class="star" data-value="2">&#9733;</span>    
            <span class="star" data-value="3">&#9733;</span>    
            <span class="star" data-value="4">&#9733;</span>    
            <span class="star" data-value="5">&#9733;</span>    
        </div>    
        <input type="hidden" name="rating" id="rating" value="0">    
        <button type="submit" class="submit-button">Berikan Rating</button>    
    </form>    
</div>    
    
<script>    
    const stars = document.querySelectorAll('.star');    
    const ratingInput = document.getElementById('rating');    
    
    stars.forEach(star => {    
        star.addEventListener('click', () => {    
            const rating = star.getAttribute('data-value');    
            ratingInput.value = rating;    
            updateStars(rating);    
        });    
    });    
    
    function updateStars(rating) {    
        stars.forEach(star => {    
            star.classList.remove('selected');    
            if (star.getAttribute('data-value') <= rating) {    
                star.classList.add('selected');    
            }    
        });    
    }    
</script>    
    
<?php    
if ($_SERVER['REQUEST_METHOD'] === 'POST') {    
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0; // Ensure rating is an integer  
    
    if ($rating > 0 && $rating <= 5) { // Validate rating value  
        $query = "UPDATE pesanan_makanan SET rating = ? WHERE id_pesanan = ?";    
        $stmt = $koneksi->prepare($query);    
        $stmt->bind_param('ii', $rating, $id_pesanan);    
        if ($stmt->execute()) {    
            echo "<script>alert('Rating berhasil diberikan.'); window.location = 'utama.php?page=detailpesananmakanan&id_pesanan=$id_pesanan';</script>";    
        } else {    
            echo "<script>alert('Gagal memberikan rating.');</script>";    
        }    
    } else {    
        echo "<script>alert('Silakan pilih rating antara 1 hingga 5.');</script>";    
    }    
}    
?>    
    
</body>    
</html>  
