<?php
include 'config.php'; // Koneksi ke database

// Pastikan pengguna sudah login
date_default_timezone_set('Asia/Jakarta');
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Silakan login terlebih dahulu.'); window.location = 'login.php';</script>";
    exit;
}

// Ambil id_pengantaran dari URL
$id_pengantaran = isset($_GET['id_pengantaran']) ? $_GET['id_pengantaran'] : 0;

// Ambil data driver berdasarkan id_pengantaran
$query = "SELECT d.gambardriver, d.nama, d.plat_nomor, d.kendaraan FROM pengantaran_orang p
          JOIN driver d ON p.id_driver = d.id_driver
          WHERE p.id_pengantaran = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param('i', $id_pengantaran);
$stmt->execute();
$result = $stmt->get_result();
$driver = $result->fetch_assoc();
if (!$driver) {
    echo "<script>alert('Data pengantaran tidak ditemukan.'); window.location = 'utama.php';</script>";
    exit;
}
?>

    <div class="rating-container">  
        <h1>Rating Driver</h1>  
        <div class="driver-info">  
            <img src="gambardriver/<?php echo htmlspecialchars($driver['gambardriver']); ?>" alt="Gambar Driver">  
            <div class="driver-details">  
                <p><strong>Nama Driver:</strong> <?php echo htmlspecialchars($driver['nama']); ?></p>  
                <p><strong>Plat Nomor:</strong> <?php echo htmlspecialchars($driver['plat_nomor']); ?></p>  
                <p><strong>Kendaraan:</strong> <?php echo htmlspecialchars($driver['kendaraan']); ?></p>  
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
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;

    if ($rating > 0) {
        $query = "UPDATE pengantaran_orang SET rating = ? WHERE id_pengantaran = ?";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param('ii', $rating, $id_pengantaran);
        if ($stmt->execute()) {
            echo "<script>alert('Rating berhasil diberikan.'); window.location = 'utama.php?page=detailpengantaranorang&id_pengantaran=$id_pengantaran';</script>";
        } else {
            echo "<script>alert('Gagal memberikan rating.');</script>";
        }
    } else {
        echo "<script>alert('Silakan pilih rating terlebih dahulu.');</script>";
    }
}
?>

</body>  
</html>


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

