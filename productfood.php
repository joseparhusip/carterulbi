<?php                  
include 'config.php';
                
if (!isset($_SESSION['username'])) {                  
    header('Location: login.php');                   
    exit();                  
}                  
                
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
            --bs-dark: #343a40;
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
            justify-content: center;                  
            align-items: center;                  
            flex-direction: column;                  
        }                  
                
        .search-box {                  
            display: flex;                  
            align-items: center;                  
            width: 100%;                  
            max-width: 600px;                  
            margin: 20px 0;                  
            border-radius: 5px;                  
            overflow: hidden;                  
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);                  
            transition: box-shadow 0.3s ease;                  
        }                  
                
        .search-box:hover {                  
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);                  
        }                  
                
        .search-box input {                  
            padding: 10px;                  
            border: none;                  
            flex: 1;                  
            font-size: 16px;                  
            border-radius: 5px 0 0 5px;                  
            outline: none;                  
            transition: background-color 0.3s ease;                  
            background-color: var(--bs-dark);                  
            color: white;                  
        }                  
                
        .search-box input:focus {                  
            background-color: #495057;                  
        }                  
                
        .search-box button {                  
            padding: 10px 15px;                  
            background-color: var(--bs-dark);                  
            color: white;                  
            border: none;                  
            cursor: pointer;                  
            transition: background-color 0.3s, transform 0.2s;                  
            border-radius: 0 5px 5px 0;                  
        }                  
                
        .search-box button:hover {                  
            background-color: #0056b3;                  
            transform: scale(1.05);                  
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
            height: 450px;                  
            overflow: hidden;                  
            position: relative;                  
        }                  
                
        .card:hover {                  
            transform: translateY(-5px);                  
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);                  
        }                  
                
        .card img {                  
            width: 100%;                  
            height: 200px;                  
            object-fit: cover;                  
            border-radius: 10px;                  
            margin-bottom: 10px;                  
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
            height: 50px;                  
            overflow: hidden;                  
            text-overflow: ellipsis;                  
            display: -webkit-box;                  
            -webkit-box-orient: vertical;                  
            -webkit-line-clamp: 2;                  
        }                  
                
        .btn-detail {                  
            background-color: var(--bs-dark);                  
            color: white;                  
            border: none;                  
            border-radius: 5px;                  
            padding: 10px 15px;                  
            cursor: pointer;                  
            text-decoration: none;                  
            margin-top: 10px;                  
            display: flex;                  
            justify-content: center;                  
            align-items: center;                  
            transition: background-color 0.3s;                  
            width: 100%;                  
            box-sizing: border-box;                  
        }                  
                
        .btn-detail:hover {                  
            background-color: #333333;                  
        }

        .out-of-stock {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: rgba(255, 0, 0, 0.8);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
            z-index: 1;
        }
    </style>                  
</head>                  
<body>                  
    <div class="category-container">                  
        <div class="search-box">                  
            <input type="text" id="searchInput" placeholder="Cari Makanan??">                  
            <button type="button" onclick="fetchProducts(searchInput.value)">Cari</button>                 
        </div>                  
    </div>                  
                
    <div class="category-list">                  
        <?php                  
        echo "<a href='utama.php?page=productfood&id_kategori=0' class='category-item'>Semua</a>";
        $sql = "SELECT id_kategori, nama_kategori FROM kategori_produk";                  
        $result = $koneksi->query($sql);                  
                
        if ($result->num_rows > 0) {                  
            while ($row = $result->fetch_assoc()) {                  
                echo "<a href='utama.php?page=productfood&id_kategori=" . $row['id_kategori'] . "' class='category-item'>" . $row['nama_kategori'] . "</a>";                  
            }                  
        }                  
        ?>                  
    </div>                  
                
    <div class="product-container" id="productContainer">                  
        <?php                  
        if ($id_kategori === 0) {
            $sql = "SELECT id_produk, nama_produk, harga, stok, deskripsi, gambar FROM produk_makanan";                  
        } else {                  
            $sql = "SELECT id_produk, nama_produk, harga, stok, deskripsi, gambar FROM produk_makanan WHERE id_kategori = $id_kategori";                    
        }                  
                
        $result = $koneksi->query($sql);                  
                
        if ($result->num_rows > 0) {                  
            while ($row = $result->fetch_assoc()) {                  
                echo "<div class='card'>";
                if ($row['stok'] == 0) {
                    echo "<div class='out-of-stock'>Out of Stock</div>";
                }
                echo "<img src='gambarfood/" . $row['gambar'] . "' alt='" . $row['nama_produk'] . "'>";                  
                echo "<h3>" . $row['nama_produk'] . "</h3>";                  
                echo "<p>Harga: Rp" . number_format($row['harga'], 0, ',', '.') . "</p>";                  
                echo "<p>" . $row['deskripsi'] . "</p>";                  
                echo "<a href='utama.php?page=detailproduct&id_produk=" . $row['id_produk'] . "' class='btn-detail'>Detail Produk</a>";                                     
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
                            
                            // Add out of stock label if stock is 0
                            let outOfStockLabel = '';
                            if (product.stok == 0) {
                                outOfStockLabel = '<div class="out-of-stock">Out of Stock</div>';
                            }

                            card.innerHTML = `        
                                ${outOfStockLabel}
                                <img src='gambarfood/${product.gambar}' alt='${product.nama_produk}'>        
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