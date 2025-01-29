<?php  
// Include konfigurasi database  
include 'config.php';  

// Pastikan user sudah login  
if (!isset($_SESSION['username'])) {  
    die('Error: Anda harus login untuk mengakses keranjang.');
}

// Ambil username dari session  
$username = $_SESSION['username'];  

// Ambil id_user berdasarkan username  
$query_user = "SELECT id_user FROM user WHERE username = ?";  
$stmt_user = $koneksi->prepare($query_user);  
$stmt_user->bind_param('s', $username);  
$stmt_user->execute();  
$result_user = $stmt_user->get_result();  

if ($result_user->num_rows > 0) {  
    $user_data = $result_user->fetch_assoc();  
    $id_user = $user_data['id_user'];  

    // Query untuk mendapatkan data keranjang berdasarkan id_user  
    $query_cart = "SELECT id_keranjang, gambar, nama_produk, quantity, harga, total_harga FROM keranjang WHERE id_user = ?";  
    $stmt_cart = $koneksi->prepare($query_cart);  
    $stmt_cart->bind_param('i', $id_user);  
    $stmt_cart->execute();  
    $result_cart = $stmt_cart->get_result();  
} else {  
    echo "<p>User tidak ditemukan.</p>";  
    exit;  
}  

// Fungsi untuk update quantity  
if (isset($_POST['update_quantity'])) {  
    foreach ($_POST['quantity'] as $id_keranjang => $quantity) {  
        $query_update = "UPDATE keranjang SET quantity = ?, total_harga = quantity * harga WHERE id_keranjang = ?";  
        $stmt_update = $koneksi->prepare($query_update);  
        $stmt_update->bind_param('ii', $quantity, $id_keranjang);  
        $stmt_update->execute();  
    }  
    // Gunakan JavaScript untuk pengalihan halaman
    echo "<script>window.location.href = 'utama.php?page=keranjang';</script>";
    exit;  
}  

// Fungsi untuk menghapus item keranjang  
if (isset($_GET['hapus_id'])) {  
    $hapus_id = explode(',', $_GET['hapus_id']);  
    foreach ($hapus_id as $id) {  
        $query_delete = "DELETE FROM keranjang WHERE id_keranjang = ?";  
        $stmt_delete = $koneksi->prepare($query_delete);  
        $stmt_delete->bind_param('i', $id);  
        $stmt_delete->execute();  
    }  
    // Gunakan JavaScript untuk pengalihan halaman
    echo "<script>window.location.href = 'utama.php?page=keranjang';</script>";
    exit;  
}  
?>  

<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title>Keranjang Belanja</title>  
</head>
<style>
        .disabled {
            pointer-events: none;
            opacity: 0.6;
        }
        body {          
            font-family: 'Arial', sans-serif;          
            margin: 0;          
            padding: 0;          
            background-color: #f4f4f9;          
        }          
          
        .cart-container {          
            background-color: #ffffff;          
            padding: 40px;          
            border-radius: 15px;          
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);          
            width: 90%;          
            max-width: 1000px;          
            margin: 50px auto;          
        }          
          
        h1 {          
            text-align: center;          
            margin-bottom: 30px;          
            color: #2c3e50;          
            font-size: 32px;          
            font-weight: 700;          
        }          
          
        .cart-table {          
            width: 100%;          
            border-collapse: collapse;          
            margin-bottom: 20px;          
            border-radius: 10px;          
            overflow: hidden;          
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);          
        }          
          
        .cart-table th {          
            background-color: #34495e;          
            color: #ffffff;          
            padding: 15px;          
            text-align: left;          
            font-size: 16px;          
        }          
          
        .cart-table td {          
            padding: 15px;          
            text-align: left;          
            border-bottom: 1px solid #ecf0f1;          
            font-size: 14px;          
            color: #2c3e50;          
        }          
          
        .cart-table img {          
            max-width: 70px;          
            height: auto;          
            display: block;          
            margin: 0 auto;          
            border-radius: 8px;          
            border: 1px solid #bdc3c7;          
        }          
          
        .action-btn {          
            background-color: #e74c3c;          
            color: #ffffff;          
            border: none;          
            padding: 10px 20px;          
            border-radius: 8px;          
            cursor: pointer;          
            transition: 0.3s;          
            font-size: 14px;          
        }          
          
        .action-btn:hover {          
            background-color: #c0392b;          
        }          
            
        .update-btn, .order-btn {    
            background-color: #2980b9;    
            color: #ffffff;    
            border: none;    
            padding: 10px 20px;    
            border-radius: 8px;    
            cursor: pointer;    
            transition: 0.3s;    
            font-size: 14px;    
            margin-top: 10px;    
        }    
            
        .update-btn:hover, .order-btn:hover {    
            background-color: #2471a3;    
        }    
            
        #select-all {          
            cursor: pointer;          
        }          
            
        @media (max-width: 768px) {          
            .cart-container {          
                padding: 20px;          
            }          
            
            .cart-table th,          
            .cart-table td {          
                padding: 10px;          
            }          
            
            .cart-table img {          
                max-width: 50px;          
            }          
            
            .action-btn, .update-btn, .order-btn {          
                padding: 8px 16px;          
                font-size: 12px;          
            }          
        }          
    </style>
<body>  
    <div class="cart-container">  
        <h1>Keranjang Belanja</h1>  
        <form id="cartForm" method="POST" action="utama.php?page=keranjang">  
            <table class="cart-table">  
                <thead>  
                    <tr>  
                        <th><input type="checkbox" id="select-all" onclick="toggleAllCheckboxes(this)"></th>  
                        <th>ID Keranjang</th>  
                        <th>Gambar</th>  
                        <th>Nama Produk</th>  
                        <th>Quantity</th>  
                        <th>Harga</th>  
                        <th>Total Harga</th>  
                        <th>Aksi</th>  
                    </tr>  
                </thead>  
                <tbody>  
                    <?php while ($row = $result_cart->fetch_assoc()) { ?>  
                        <tr>  
                            <td><input type="checkbox" class="select-item" name="selected_items[]" value="<?php echo $row['id_keranjang']; ?>" onchange="toggleActionButtons()"></td>  
                            <td><?php echo $row['id_keranjang']; ?></td>  
                            <td><img src="gambarfood/<?php echo $row['gambar']; ?>" alt="<?php echo $row['nama_produk']; ?>"></td>  
                            <td><?php echo $row['nama_produk']; ?></td>  
                            <td>  
                                <input type="number" name="quantity[<?php echo $row['id_keranjang']; ?>]" value="<?php echo $row['quantity']; ?>" min="1">  
                            </td>  
                            <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>  
                            <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>  
                            <td>  
                                <a href="javascript:void(0)" class="action-btn" id="deleteBtn" onclick="deleteSelected()">Hapus</a>  
                                <button type="submit" name="update_quantity" class="action-btn">Update Quantity</button>  
                            </td>  
                        </tr>  
                    <?php } ?>  
                </tbody>  
            </table>  
            <!-- Button Pesan -->  
            <a href="javascript:void(0)" class="order-btn" id="orderBtn" onclick="orderSelected()">Pesan</a>  
        </form>  
    </div>  

    <script>  
        function toggleAllCheckboxes(source) {  
            const checkboxes = document.querySelectorAll('.select-item');  
            checkboxes.forEach((checkbox) => {  
                checkbox.checked = source.checked;  
            });  
        }  

        function deleteSelected() {  
            const selectedItems = getSelectedItems();  
            if (selectedItems.length === 0) {  
                alert('Pilih setidaknya satu item untuk dihapus.');  
                return;  
            }  
            const confirmed = confirm('Yakin ingin menghapus item yang dipilih?');  
            if (confirmed) {  
                window.location.href = `utama.php?page=keranjang&hapus_id=${selectedItems.join(',')}`;  
            }  
        }  

        function orderSelected() {  
            const selectedItems = getSelectedItems();  
            if (selectedItems.length === 0) {  
                alert('Pilih setidaknya satu item untuk dipesan.');  
                return;  
            }  
            const url = `utama.php?page=formpemesananfood&id_keranjang=${selectedItems.join(',')}`;  
            window.location.href = url;  
        }  

        function getSelectedItems() {  
            const selectedItems = [];  
            document.querySelectorAll('.select-item:checked').forEach(item => {  
                selectedItems.push(item.value);  
            });  
            return selectedItems;  
        }  
    </script>  
</body>  
</html>
