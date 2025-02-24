<?php  
include 'config.php';

if (isset($_GET['query'])) {  
    $query = $_GET['query'];  
  
    // Query untuk mencari produk berdasarkan nama produk atau nama kategori
    $sql = "SELECT DISTINCT p.id_produk, p.nama_produk, p.harga, p.stok, p.deskripsi, p.gambar 
            FROM produk_makanan p 
            LEFT JOIN kategori_produk k ON p.id_kategori = k.id_kategori 
            WHERE p.nama_produk LIKE ? OR k.nama_kategori LIKE ?";
            
    $stmt = $koneksi->prepare($sql);  
    $searchTerm = "%" . $query . "%";  
    $stmt->bind_param("ss", $searchTerm, $searchTerm);  
    $stmt->execute();  
    
    // Menggunakan bind_result untuk mengikat hasil ke variabel
    $stmt->bind_result($id_produk, $nama_produk, $harga, $stok, $deskripsi, $gambar);
    
    $products = [];
    // Mengambil data dengan fetch
    while ($stmt->fetch()) {  
        $products[] = [
            'id_produk' => $id_produk,
            'nama_produk' => $nama_produk,
            'harga' => $harga,
            'stok' => $stok,
            'deskripsi' => $deskripsi,
            'gambar' => $gambar
        ];  
    }  
    
    // Menutup statement
    $stmt->close();
  
    // Mengembalikan hasil dalam format JSON  
    header('Content-Type: application/json');  
    echo json_encode($products);  
} else {  
    // Jika tidak ada query pencarian, kembalikan array kosong  
    header('Content-Type: application/json');
    echo json_encode([]);  
}  
?>