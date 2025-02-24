<?php                    
include 'config.php';              
  
if (!isset($_SESSION['username'])) {                  
    header('Location: login.php');                     
    exit();                  
}                  
  
$id_produk = isset($_GET['id_produk']) ? intval($_GET['id_produk']) : 0;                  

// Modified query to fetch multiple images
$sql = "SELECT p.nama_produk, p.stok, p.harga, p.deskripsi, p.status, p.gambar,
        GROUP_CONCAT(pi.image_path) as additional_images 
        FROM produk_makanan p 
        LEFT JOIN product_images pi ON p.id_produk = pi.product_id 
        WHERE p.id_produk = $id_produk 
        GROUP BY p.id_produk";                  
$result = $koneksi->query($sql);                  
$product = $result->fetch_assoc();                  
  
if (!$product) {                  
    echo "<p>Produk tidak ditemukan.</p>";                  
    exit();                  
}

// Create array of all images
$images = [$product['gambar']];
if (!empty($product['additional_images'])) {
    $additional_images = explode(',', $product['additional_images']);
    $images = array_merge($images, $additional_images);
}
?>                  
  
<!DOCTYPE html>                  
<html lang="id">                  
<head>                  
    <meta charset="UTF-8">                  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">                  
    <title>Detail Produk - <?php echo $product['nama_produk']; ?></title>                  
    <link rel="stylesheet" href="styles.css">           
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
            border: 1px solid #000;            
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);                  
        }                  
              
        .product-card img {                  
            width: 100%;                  
            max-width: 300px;                  
            height: auto;                  
            border-radius: 10px;                  
            margin-bottom: 15px;                  
            border: 2px solid #001f3f;            
            display: block;                  
            margin-left: auto;                  
            margin-right: auto;                  
        }                  
              
        .product-name {                  
            font-size: 2em;                  
            color: #001f3f;                  
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
            justify-content: center;                  
            margin: 10px 0;                  
        }                  
              
        .quantity-controls button {                  
            padding: 10px;                  
            border: none;                  
            background-color: #001f3f;                  
            color: white;                  
            border-radius: 5px;                  
            cursor: pointer;                  
            margin: 0 5px;                  
            transition: background-color 0.3s;                  
        }                  
              
        .quantity-controls button:hover {                  
            background-color: #001a33;                  
        }                  
              
        .quantity-controls input {                  
            width: 60px;                  
            text-align: center;                  
            border: 1px solid #ddd;                  
            border-radius: 5px;                  
            margin: 0 5px;                  
        }                  
              
        .btn-buy {                  
            background-color: #001f3f;            
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
            background-color: #001a33;            
        }     

        .btn-buy:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        .out-of-stock-label {
            color: #ff0000;
            font-weight: bold;
            font-size: 1.2em;
            margin: 10px 0;
            padding: 5px 10px;
            background-color: #ffe6e6;
            border-radius: 5px;
            display: none;
        }

        /* Slider styles */
        .slider-container {
            position: relative;
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
            overflow: hidden;
        }

        .slider-wrapper {
            display: flex;
            transition: transform 0.3s ease-in-out;
        }

        .slider-wrapper img {
            width: 100%;
            max-width: 300px;
            height: auto;
            border-radius: 10px;
            border: 2px solid #001f3f;
            flex-shrink: 0;
        }

        .slider-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 10px;
            box-sizing: border-box;
        }

        .slider-nav button {
            background-color: rgba(0, 31, 63, 0.7);
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            z-index: 1;
        }

        .slider-nav button:hover {
            background-color: rgba(0, 31, 63, 0.9);
        }

        .slider-dots {
            display: flex;
            justify-content: center;
            margin-top: 10px;
            gap: 5px;
        }

        .slider-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #ccc;
            cursor: pointer;
        }

        .slider-dot.active {
            background-color: #001f3f;
        }
    </style>                  
</head>                  
<body>                  
    <div class="product-card">                  
        <form action="add_to_cart.php" method="POST">
            <!-- Replace single image with slider -->
            <div class="slider-container">
                <div class="slider-wrapper">
                    <?php foreach ($images as $image): ?>
                    <img src="gambarfood/<?php echo $image; ?>" alt="<?php echo $product['nama_produk']; ?>">
                    <?php endforeach; ?>
                </div>
                <div class="slider-nav">
                    <button type="button" class="slider-prev">&lt;</button>
                    <button type="button" class="slider-next">&gt;</button>
                </div>
                <div class="slider-dots">
                    <?php for($i = 0; $i < count($images); $i++): ?>
                    <span class="slider-dot <?php echo $i === 0 ? 'active' : ''; ?>" data-index="<?php echo $i; ?>"></span>
                    <?php endfor; ?>
                </div>
            </div>

            <h2 class="product-name"><?php echo $product['nama_produk']; ?></h2>                  
            <p><strong>Stok:</strong> <?php echo $product['stok']; ?></p>                  
            <p><strong>Harga:</strong> Rp<?php echo number_format($product['harga'], 0, ',', '.'); ?></p>                  
            <p><strong>Deskripsi:</strong> <?php echo $product['deskripsi']; ?></p>                  
            <p><strong>Status:</strong> <?php echo $product['status']; ?></p>                  
              
            <div class="quantity-controls" <?php echo ($product['stok'] == 0 ? 'style="display: none;"' : ''); ?>>                  
                <button type="button" onclick="changeQuantity(-1)">-</button>                  
                <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stok']; ?>" onchange="updateTotalPrice()">                  
                <button type="button" onclick="changeQuantity(1)">+</button>                  
            </div>                  
              
            <div class="out-of-stock-label" <?php echo ($product['stok'] == 0 ? 'style="display: block;"' : ''); ?>>
                Out of Stock
            </div>

            <input type="hidden" name="id_produk" value="<?php echo $id_produk; ?>">          
            <input type="hidden" name="harga" value="<?php echo $product['harga']; ?>">          
            <input type="hidden" name="gambar" value="<?php echo $product['gambar']; ?>">          
            <input type="hidden" name="nama_produk" value="<?php echo $product['nama_produk']; ?>">          
            <input type="hidden" name="id_user" value="<?php echo $_SESSION['username']; ?>">          
            <input type="hidden" name="total_harga" id="total_harga" value="<?php echo $product['harga']; ?>">        
              
            <button type="submit" class="btn-buy" <?php echo ($product['stok'] == 0 ? 'disabled' : ''); ?>>
                <?php echo ($product['stok'] == 0 ? 'Out of Stock' : 'Beli'); ?>
            </button>                  
        </form>          
    </div>                  
              
    <script>                  
        function changeQuantity(amount) {                  
            var quantityInput = document.getElementById('quantity');                  
            var currentQuantity = parseInt(quantityInput.value);                  
            var newQuantity = currentQuantity + amount;                  
              
            if (newQuantity < 1) {                  
                newQuantity = 1;                  
            } else if (newQuantity > parseInt(quantityInput.max)) {                  
                newQuantity = parseInt(quantityInput.max);                  
            }                  
              
            quantityInput.value = newQuantity;                  
            updateTotalPrice();        
        }                  
        
        function updateTotalPrice() {        
            var quantityInput = document.getElementById('quantity');        
            var harga = parseFloat(document.querySelector('input[name="harga"]').value);        
            var totalHarga = quantityInput.value * harga;        
            document.getElementById('total_harga').value = totalHarga;        
        }

        // Slider functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sliderWrapper = document.querySelector('.slider-wrapper');
            const slides = sliderWrapper.querySelectorAll('img');
            const prevButton = document.querySelector('.slider-prev');
            const nextButton = document.querySelector('.slider-next');
            const dots = document.querySelectorAll('.slider-dot');
            let currentIndex = 0;

            // Update slider position
            function updateSlider() {
                const offset = -currentIndex * 100;
                sliderWrapper.style.transform = `translateX(${offset}%)`;
                
                // Update dots
                dots.forEach((dot, index) => {
                    dot.classList.toggle('active', index === currentIndex);
                });
            }

            // Previous slide
            prevButton.addEventListener('click', () => {
                currentIndex = (currentIndex - 1 + slides.length) % slides.length;
                updateSlider();
            });

            // Next slide
            nextButton.addEventListener('click', () => {
                currentIndex = (currentIndex + 1) % slides.length;
                updateSlider();
            });

            // Dot navigation
            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    currentIndex = index;
                    updateSlider();
                });
            });

            // Auto-advance slides
            setInterval(() => {
                currentIndex = (currentIndex + 1) % slides.length;
                updateSlider();
            }, 5000);
        });
    </script>                  
</body>                  
</html>