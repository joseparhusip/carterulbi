<?php  
include 'config.php'; // Import database connection configuration  
  
if ($_SERVER['REQUEST_METHOD'] == 'POST') {  
    $id_keranjang_array = $_POST['id_keranjang'];  
    $quantity_array = $_POST['quantity'];  
  
    // Loop untuk memperbarui setiap item yang dipilih  
    for ($i = 0; $i < count($id_keranjang_array); $i++) {  
        $id_keranjang = $id_keranjang_array[$i];  
        $quantity = $quantity_array[$i];  
  
        // Update quantity in the database  
        $sql_update = "UPDATE keranjang SET quantity = ? WHERE id_keranjang = ?";  
        $stmt_update = $koneksi->prepare($sql_update);  
        $stmt_update->bind_param("ii", $quantity, $id_keranjang);  
        $stmt_update->execute();  
    }  
  
    // Redirect back to the cart page  
    header('Location: utama.php?page=keranjang&id_keranjang'); // Ganti dengan nama file yang sesuai  
    exit();  
}  
?>  
