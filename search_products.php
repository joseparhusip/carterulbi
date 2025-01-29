<?php  
include 'config.php'; // Mengimpor file konfigurasi koneksi database  
  
// Cek apakah ada query pencarian  
if (isset($_GET['query'])) {  
    $query = $_GET['query'];  
  
    // Query untuk mencari produk berdasarkan nama produk  
    $sql = "SELECT id_produk, nama_produk, harga, deskripsi, gambar FROM produk_makanan WHERE nama_produk LIKE ?";  
    $stmt = $koneksi->prepare($sql);  
    $searchTerm = "%" . $query . "%";  
    $stmt->bind_param("s", $searchTerm);  
    $stmt->execute();  
    $result = $stmt->get_result();  
  
    $products = [];  
    if ($result->num_rows > 0) {  
        while ($row = $result->fetch_assoc()) {  
            $products[] = $row;  
        }  
    }  
  
    // Mengembalikan hasil dalam format JSON  
    header('Content-Type: application/json');  
    echo json_encode($products);  
} else {  
    // Jika tidak ada query pencarian, kembalikan array kosong  
    echo json_encode([]);  
}  
?>  
