<?php
include 'config.php'; // Mengimpor file konfigurasi koneksi database                                          

// Cek apakah pengguna sudah login                                          
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Jika belum login, redirect ke halaman login                                          
    exit();
}

// Ambil id_kategori dari URL jika ada    
$id_kategori = isset($_GET['id_kategori']) ? intval($_GET['id_kategori']) : 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk Makanan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .category-container {
            background-color: #fff;
            padding: 15px;
            margin: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .category-container h2 {
            margin: 0 0 10px;
        }

        .search-box {
            display: flex;
            align-items: center;
        }

        .search-box input {
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-left: 10px;
        }

        .category-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .category-item {
            background-color: #e7e7e7;
            padding: 10px 15px;
            border-radius: 5px;
            text-align: center;
            flex: 1;
            min-width: 100px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .category-item:hover {
            background-color: #d0d0d0;
        }

        .product-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            padding: 20px;
        }

        .card {
            background-color: #ffffff;
            border: 2px solid #000000;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            margin: 15px;
            padding: 10px;
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
            display: inline-block;
            width: 250px;
            /* Lebar card disesuaikan */
            height: 400px;
            /* Tinggi card disesuaikan */
            overflow: hidden;
            /* Mencegah konten meluap */
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        .card img {
            width: 100%;
            /* Mengatur lebar gambar 100% dari card */
            height: 200px;
            /* Mengatur tinggi gambar */
            object-fit: cover;
            /* Memastikan gambar tidak terdistorsi */
            border-radius: 10px;
            margin-bottom: 10px;
            /* Memberikan jarak antara gambar dan teks */
        }

        .card h3 {
            margin: 10px 0;
            font-size: 1.2em;
            color: #333;
        }

        .card p {
            color: #555;
            font-size: 0.9em;
            margin: 5px 0;
            /* Menambahkan margin untuk jarak antar paragraf */
        }

        .btn-detail {
            background-color: #000000;
            /* Warna tombol hitam */
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            cursor: pointer;
            text-decoration: none;
            margin-top: 10px;
            display: inline-block;
            transition: background-color 0.3s;
        }

        .btn-detail:hover {
            background-color: #333333;
            /* Warna saat hover */
        }
    </style>
</head>

<body>
    <div class="category-container">
        <h2>Kategori Produk</h2>
        <div class="search-box">
            <input type="text" placeholder="Cari kategori...">
        </div>
    </div>

    <div class="category-list">
        <?php
        // Ambil kategori produk                    
        $sql = "SELECT id_kategori, nama_kategori FROM kategori_produk";
        $result = $koneksi->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Menambahkan tautan ke kategori berdasarkan id_kategori                    
                echo "<a href='utama.php?page=productfood&id_kategori=" . $row['id_kategori'] . "' class='category-item'>" . $row['nama_kategori'] . "</a>";
            }
        }
        ?>
    </div>

    <div class="product-container">
        <?php
        // Ambil produk makanan berdasarkan id_kategori                    
        if ($id_kategori) {
            $sql = "SELECT id_produk, nama_produk, harga, deskripsi, gambar FROM produk_makanan WHERE id_kategori = $id_kategori";
        } else {
            $sql = "SELECT id_produk, nama_produk, harga, deskripsi, gambar FROM produk_makanan"; // Ambil semua produk jika tidak ada kategori  
        }

        $result = $koneksi->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='card'>";
                echo "<img src='../gambarfood/" . $row['gambar'] . "' alt='" . $row['nama_produk'] . "'>";
                echo "<h3>" . $row['nama_produk'] . "</h3>";
                echo "<p>Harga: Rp" . number_format($row['harga'], 0, ',', '.') . "</p>";
                echo "<p>" . $row['deskripsi'] . "</p>";
                echo "<a href='utama.php?page=detailproduct&id_produk=" . $row['id_produk'] . "' class='btn-detail'>Detail Produk</a>"; // Tombol detail produk                    
                echo "</div>";
            }
        } else {
            echo "<p>Tidak ada produk yang ditemukan untuk kategori ini.</p>";
        }
        ?>
    </div>
</body>

</html>