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
        :root {  
            --bs-dark: #343a40; /* Define the dark color variable */  
        }  
  
        body {                  
            font-family: Arial, sans-serif;                  
            margin: 0;                  
            padding: 0;                  
            background-color: #f4f4f4;                  
        }                  
                
        .category-container {                  
            background-color: #fff;                  
            padding: 20px;                  
            margin: 20px;                  
            border-radius: 10px;                  
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);                  
            display: flex;                  
            justify-content: center; /* Center the content horizontally */                  
            align-items: center;                  
            flex-direction: column; /* Stack items vertically */                  
        }                  
                
        .search-box {                  
            display: flex;                  
            align-items: center;                  
            width: 100%; /* Full width for the search box */                  
            max-width: 600px; /* Limit the maximum width */                  
            margin: 20px 0; /* Add some margin */                  
            border-radius: 5px;                  
            overflow: hidden; /* Ensure the border radius is applied */                  
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Add shadow for depth */                  
            transition: box-shadow 0.3s ease; /* Smooth shadow transition */                  
        }                  
                
        .search-box:hover {                  
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); /* Darker shadow on hover */                  
        }                  
                
        .search-box input {                  
            padding: 10px;                  
            border: none; /* Remove default border */                  
            flex: 1; /* Allow input to take available space */                  
            font-size: 16px; /* Increase font size */                  
            border-radius: 5px 0 0 5px; /* Rounded corners on the left */                  
            outline: none; /* Remove outline on focus */                  
            transition: background-color 0.3s ease; /* Smooth background transition */                  
            background-color: var(--bs-dark); /* Set background color to var(--bs-dark) */                  
            color: white; /* Set text color to white for contrast */                  
        }                  
                
        .search-box input:focus {                  
            background-color: #495057; /* Lighten background on focus */                  
        }                  
                
        .search-box button {                  
            padding: 10px 15px;                  
            background-color: var(--bs-dark); /* Button color */                  
            color: white;                  
            border: none;                  
            cursor: pointer;                  
            transition: background-color 0.3s, transform 0.2s; /* Smooth color and transform transition */                  
            border-radius: 0 5px 5px 0; /* Rounded corners on the right */                  
        }                  
                
        .search-box button:hover {                  
            background-color: #0056b3; /* Darker color on hover */                  
            transform: scale(1.05); /* Slightly enlarge button on hover */                  
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
            width: 250px; /* Lebar card disesuaikan */                  
            height: 450px; /* Tinggi card disesuaikan untuk memberi ruang pada tombol */                  
            overflow: hidden; /* Mencegah konten meluap */                  
            position: relative; /* Memungkinkan penempatan tombol di bagian bawah */                  
        }                  
                
        .card:hover {                  
            transform: translateY(-5px);                  
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);                  
        }                  
                
        .card img {                  
            width: 100%; /* Mengatur lebar gambar 100% dari card */                  
            height: 200px; /* Mengatur tinggi gambar */                  
            object-fit: cover; /* Memastikan gambar tidak terdistorsi */                  
            border-radius: 10px;                  
            margin-bottom: 10px; /* Memberikan jarak antara gambar dan teks */                  
        }                  
                
        .card h3 {                  
            margin: 10px 0;                  
            font-size: 1.2em;                  
            color: #333;                  
        }                  
                
        .card p {                  
            color: #555;                  
            font-size: 0.9em;                  
            margin: 5px 0; /* Menambahkan margin untuk jarak antar paragraf */                  
            height: 50px; /* Menetapkan tinggi tetap untuk deskripsi */                  
            overflow: hidden; /* Mencegah deskripsi meluap */                  
            text-overflow: ellipsis; /* Menambahkan elipsis jika teks terlalu panjang */                  
            display: -webkit-box;                  
            -webkit-box-orient: vertical;                  
            -webkit-line-clamp: 2; /* Membatasi jumlah baris deskripsi */                  
        }                  
                
        .btn-detail {                  
            background-color: var(--bs-dark); /* Warna tombol hitam */                  
            color: white;                  
            border: none;                  
            border-radius: 5px;                  
            padding: 10px 15px;                  
            cursor: pointer;                  
            text-decoration: none;                  
            margin-top: 10px; /* Mengatur jarak atas tombol */                  
            display: flex; /* Menggunakan flexbox untuk memusatkan tombol */                  
            justify-content: center; /* Memusatkan tombol secara horizontal */                  
            align-items: center; /* Memusatkan tombol secara vertikal */                  
            transition: background-color 0.3s;                  
            width: 100%; /* Mengatur lebar tombol agar memenuhi card */                  
            box-sizing: border-box; /* Memastikan padding tidak mempengaruhi lebar total */                  
        }                  
                
        .btn-detail:hover {                  
            background-color: #333333; /* Warna saat hover */                  
        }                  
    </style>                  
</head>                  
<body>                  
    <div class="category-container">                  
        <div class="search-box">                  
            <input type="text" id="searchInput" placeholder="Cari Makanan??">                  
            <button type="button" onclick="fetchProducts(searchInput.value)">Cari</button> <!-- Search button -->                  
        </div>                  
    </div>                  
                
    <div class="category-list">                  
        <?php                  
        // Ambil kategori produk                                      
        echo "<a href='utama.php?page=productfood&id_kategori=0' class='category-item'>Semua</a>"; // Kategori Semua          
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
                
    <div class="product-container" id="productContainer">                  
        <?php                  
        // Ambil produk makanan berdasarkan id_kategori                                      
        if ($id_kategori === 0) { // Jika kategori "Semua" dipilih          
            $sql = "SELECT id_produk, nama_produk, harga, deskripsi, gambar FROM produk_makanan";                  
        } else {                  
            $sql = "SELECT id_produk, nama_produk, harga, deskripsi, gambar FROM produk_makanan WHERE id_kategori = $id_kategori"; // Ambil produk berdasarkan kategori                    
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
                
    <script>        
        document.getElementById('searchInput').addEventListener('input', function() {        
            var query = this.value;        
            if (query.length > 0) {        
                fetchProducts(query);        
            } else {        
                // Jika input kosong, tampilkan semua produk        
                window.location.href = 'utama.php?page=productfood&id_kategori=<?php echo $id_kategori; ?>';        
            }        
        });        
        
        function fetchProducts(query) {        
            var xhr = new XMLHttpRequest();        
            xhr.open('GET', 'search_products.php?query=' + encodeURIComponent(query), true);        
            xhr.onreadystatechange = function() {        
                if (xhr.readyState === 4 && xhr.status === 200) {        
                    var products = JSON.parse(xhr.responseText);        
                    var productContainer = document.getElementById('productContainer');        
                    productContainer.innerHTML = '';        
        
                    if (products.length > 0) {        
                        products.forEach(function(product) {        
                            var card = document.createElement('div');        
                            card.className = 'card';        
                            card.innerHTML = `        
                                <img src='../gambarfood/${product.gambar}' alt='${product.nama_produk}'>        
                                <h3>${product.nama_produk}</h3>        
                                <p>Harga: Rp${product.harga.toLocaleString('id-ID')}</p>        
                                <p>${product.deskripsi}</p>        
                                <a href='utama.php?page=detailproduct&id_produk=${product.id_produk}' class='btn-detail'>Detail Produk</a>        
                            `;        
                            productContainer.appendChild(card);        
                        });        
                    } else {        
                        productContainer.innerHTML = '<p>Tidak ada produk yang ditemukan untuk kategori ini.</p>';        
                    }        
                }        
            };        
            xhr.send();        
        }        
    </script>        
</body>                  
</html>    
