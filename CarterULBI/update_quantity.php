<?php  
include 'config.php'; // Import database connection configuration  
  
if ($_SERVER['REQUEST_METHOD'] == 'POST') {  
    $id_keranjang_array = $_POST['id_keranjang'];  
    $quantity_array = $_POST['quantity'];  
  
    // Loop untuk memperbarui setiap item yang dipilih  
    for ($i = 0; $i < count($id_keranjang_array); $i++) {  
        $id_keranjang = $id_keranjang_array[$i];  
        $quantity = $quantity_array[$i];  
  
        // Get the price for the current item  
        $sql_price = "SELECT harga FROM keranjang WHERE id_keranjang = ?";  
        $stmt_price = $koneksi->prepare($sql_price);  
        $stmt_price->bind_param("i", $id_keranjang);  
        $stmt_price->execute();  
        $result_price = $stmt_price->get_result();  
        $row_price = $result_price->fetch_assoc();  
        $harga = $row_price['harga'];  
  
        // Calculate total price  
        $total_harga = $harga * $quantity;  
  
        // Update quantity and total_harga in the database  
        $sql_update = "UPDATE keranjang SET quantity = ?, total_harga = ? WHERE id_keranjang = ?";  
        $stmt_update = $koneksi->prepare($sql_update);  
        $stmt_update->bind_param("iii", $quantity, $total_harga, $id_keranjang);  
        $stmt_update->execute();  
    }  
  
    // Redirect back to the cart page  
    header('Location: utama.php?page=keranjang&id_keranjang'); // Ganti dengan nama file yang sesuai  
    exit();  
}  
?>  
