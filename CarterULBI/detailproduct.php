<?php                    
include 'config.php'; // Import database connection configuration              
  
if (!isset($_SESSION['username'])) {                  
    header('Location: login.php'); // Redirect to login page if not logged in                      
    exit();                  
}                  
  
// Get product ID from URL              
$id_produk = isset($_GET['id_produk']) ? intval($_GET['id_produk']) : 0;                  
  
// Fetch product details from the database              
$sql = "SELECT nama_produk, stok, harga, deskripsi, status, gambar FROM produk_makanan WHERE id_produk = $id_produk";                  
$result = $koneksi->query($sql);                  
$product = $result->fetch_assoc();                  
  
if (!$product) {                  
    echo "<p>Produk tidak ditemukan.</p>";                  
    exit();                  
}                  
?>                  
  
<!DOCTYPE html>                  
<html lang="id">                  
<head>                  
    <meta charset="UTF-8">                  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">                  
    <title>Detail Produk - <?php echo $product['nama_produk']; ?></title>                  
    <link rel="stylesheet" href="styles.css"> <!-- Link to external CSS file -->            
    <style>                  
        body {                  
            font-family: 'Arial', sans-serif;                  
            margin: 0;                  
            padding: 0;                  
            background-color: #f4f4f4;                  
        }                  
              
        .product-card {                  
            display: flex;                  
            flex-direction: column;                  
            align-items: center;                  
            text-align: center;                  
            max-width: 800px;                  
            margin: 20px auto;                  
            background-color: #fff;                  
            padding: 20px;                  
            border-radius: 10px;                  
            border: 1px solid #000; /* Border tipis hitam */            
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);                  
        }                  
              
        .product-card img {                  
            width: 100%;                  
            max-width: 300px;                  
            height: auto;                  
            border-radius: 10px;                  
            margin-bottom: 15px;                  
            border: 2px solid #001f3f; /* Border pada gambar */            
            display: block; /* Center the image */                  
            margin-left: auto; /* Center the image */                  
            margin-right: auto; /* Center the image */                  
        }                  
              
        .product-name {                  
            font-size: 2em;                  
            color: #001f3f; /* Change to match the button color */                  
            margin-bottom: 10px;                  
        }                  
              
        .product-card p {                  
            margin: 5px 0;                  
            color: #555;                  
            border: 1px solid #ddd;                  
            border-radius: 5px;                  
            padding: 10px;                  
            width: 100%;                  
            box-sizing: border-box;                  
            background-color: #f9f9f9;                  
        }                  
              
        .quantity-controls {                  
            display: flex;                  
            align-items: center;                  
            justify-content: center; /* Center the items horizontally */                  
            margin: 10px 0;                  
        }                  
              
        .quantity-controls button {                  
            padding: 10px;                  
            border: none;                  
            background-color: #001f3f; /* Change to match the button color */                  
            color: white;                  
            border-radius: 5px;                  
            cursor: pointer;                  
            margin: 0 5px;                  
            transition: background-color 0.3s;                  
        }                  
              
        .quantity-controls button:hover {                  
            background-color: #001a33; /* Darker color on hover */                  
        }                  
              
        .quantity-controls input {                  
            width: 60px;                  
            text-align: center;                  
            border: 1px solid #ddd;                  
            border-radius: 5px;                  
            margin: 0 5px;                  
        }                  
              
        .btn-buy {                  
            background-color: #001f3f; /* Navy blue */            
            color: white;                  
            padding: 10px 15px;                  
            border: none;                  
            border-radius: 5px;                  
            cursor: pointer;                  
            text-decoration: none;                  
            margin-top: 15px;                  
            transition: background-color 0.3s;                  
        }                  
              
        .btn-buy:hover {                  
            background-color: #001a33; /* Darker color on hover */            
        }                  
    </style>                  
</head>                  
<body>                  
    <div class="product-card">                  
        <form action="add_to_cart.php" method="POST"> <!-- Form for adding to cart -->          
            <img src="../gambarfood/<?php echo $product['gambar']; ?>" alt="<?php echo $product['nama_produk']; ?>">                  
            <h2 class="product-name"><?php echo $product['nama_produk']; ?></h2>                  
            <p><strong>Stok:</strong> <?php echo $product['stok']; ?></p>                  
            <p><strong>Harga:</strong> Rp<?php echo number_format($product['harga'], 0, ',', '.'); ?></p>                  
            <p><strong>Deskripsi:</strong> <?php echo $product['deskripsi']; ?></p>                  
            <p><strong>Status:</strong> <?php echo $product['status']; ?></p>                  
              
            <div class="quantity-controls">                  
                <button type="button" onclick="changeQuantity(-1)">-</button>                  
                <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stok']; ?>" onchange="updateTotalPrice()">                  
                <button type="button" onclick="changeQuantity(1)">+</button>                  
            </div>                  
              
            <input type="hidden" name="id_produk" value="<?php echo $id_produk; ?>">          
            <input type="hidden" name="harga" value="<?php echo $product['harga']; ?>">          
            <input type="hidden" name="gambar" value="<?php echo $product['gambar']; ?>">          
            <input type="hidden" name="nama_produk" value="<?php echo $product['nama_produk']; ?>">          
            <input type="hidden" name="id_user" value="<?php echo $_SESSION['username']; ?>"> <!-- Assuming username is used as user ID -->          
            <input type="hidden" name="total_harga" id="total_harga" value="<?php echo $product['harga']; ?>"> <!-- Hidden input for total price -->        
              
            <button type="submit" class="btn-buy">Beli</button>                  
        </form>          
    </div>                  
              
    <script>                  
        function changeQuantity(amount) {                  
            var quantityInput = document.getElementById('quantity');                  
            var currentQuantity = parseInt(quantityInput.value);                  
            var newQuantity = currentQuantity + amount;                  
              
            // Ensure quantity does not exceed stock                      
            if (newQuantity < 1) {                  
                newQuantity = 1;                  
            } else if (newQuantity > parseInt(quantityInput.max)) {                  
                newQuantity = parseInt(quantityInput.max);                  
            }                  
              
            quantityInput.value = newQuantity;                  
            updateTotalPrice(); // Update total price when quantity changes        
        }                  
        
        function updateTotalPrice() {        
            var quantityInput = document.getElementById('quantity');        
            var harga = parseFloat(document.querySelector('input[name="harga"]').value);        
            var totalHarga = quantityInput.value * harga;        
            document.getElementById('total_harga').value = totalHarga; // Set the total price in the hidden input        
        }        
    </script>                  
</body>                  
</html>  
