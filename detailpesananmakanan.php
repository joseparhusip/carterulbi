<?php        
// Include konfigurasi database        
include 'config.php';        
      
if (!isset($_SESSION['username'])) {        
    die('Error: Anda harus login untuk melihat riwayat pesanan.');        
}        
      
// Ambil data user yang login berdasarkan username        
$username = $_SESSION['username'];        
$query_user = "SELECT id_user FROM user WHERE username = ?";        
$stmt_user = $koneksi->prepare($query_user);        
$stmt_user->bind_param('s', $username);        
$stmt_user->execute();        
$result_user = $stmt_user->get_result();        
      
if ($result_user->num_rows === 0) {        
    die('Error: Data user tidak ditemukan.');        
}        
      
$user_data = $result_user->fetch_assoc(); // Ambil data user        
$id_user = $user_data['id_user'];        
      
// Query untuk mengambil data pesanan berdasarkan id_user        
$query_pesanan = "        
    SELECT         
        pm.id_pesanan,        
        u.nama AS nama_user,        
        d.nama AS nama_driver,        
        d.plat_nomor,        
        p.nama_produk,        
        pm.alamat_pengiriman,        
        pm.quantity,        
        pm.harga,        
        pm.ongkir,        
        pm.subtotal,        
        pm.total_harga,        
        pm.catatan,        
        pm.status_pesanan,        
        pm.estimasi_waktu,        
        pm.rating -- Menambahkan kolom rating        
    FROM         
        pesanan_makanan pm        
    JOIN         
        user u ON pm.id_user = u.id_user        
    JOIN         
        driver d ON pm.id_driver = d.id_driver        
    JOIN         
        produk_makanan p ON pm.id_produk = p.id_produk        
    WHERE         
        pm.id_user = ?        
    ORDER BY         
        pm.id_pesanan DESC        
";        
$stmt_pesanan = $koneksi->prepare($query_pesanan);        
$stmt_pesanan->bind_param('i', $id_user);        
$stmt_pesanan->execute();        
$result_pesanan = $stmt_pesanan->get_result();        
?>        
      
<!DOCTYPE html>        
<html lang="id">        
<head>        
    <meta charset="UTF-8">        
    <meta name="viewport" content="width=device-width, initial-scale=1.0">        
    <title>Riwayat Pesanan Makanan</title>        
    <style>        
        body {        
            font-family: Arial, sans-serif;        
            background-color: #f9f9f9;        
            margin: 0;        
            padding: 20px;        
        }        
      
        h1 {        
            text-align: center;        
            color: #333;        
        }        
      
        .order-history {        
            margin-top: 20px;        
            max-width: 1000px;        
            margin-left: auto;        
            margin-right: auto;        
            background-color: #fff;        
            padding: 20px;        
            border-radius: 5px;        
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);        
            overflow-x: auto; /* Enable horizontal scrolling for small screens */        
        }        
      
        table {        
            width: 100%;        
            border-collapse: collapse;        
        }        
      
        th, td {        
            padding: 10px;        
            text-align: left;        
            border-bottom: 1px solid #ddd;        
        }        
      
        th {        
            background-color: #007bff;        
            color: white;        
        }        
      
        tr:hover {        
            background-color: #f1f1f1;        
        }        
      
        .driver-image {        
            width: 50px;        
            height: 50px;        
            border-radius: 50%;        
        }        
      
        .rating-button {        
            background-color: #28a745; /* Green */        
            color: white;        
            border: none;        
            padding: 8px 16px;        
            text-align: center;        
            text-decoration: none;        
            display: inline-block;        
            font-size: 14px;        
            margin: 4px 2px;        
            cursor: pointer;        
            border-radius: 5px;        
            transition: background-color 0.3s;        
        }        
      
        .rating-button:hover {        
            background-color: #218838;        
        }        
      
        /* Specific styles for the 'Catatan' column */      
        .catatan-column {      
            width: 200px; /* Adjust the width as needed */      
            white-space: pre-wrap; /* Allow text to wrap */      
        }      
    </style>        
</head>        
<body>        
      
    <h1>Riwayat Pesanan Makanan</h1>        
    <div class="order-history">        
        <table>        
            <thead>        
                <tr>        
                    <th>ID Pesanan</th>        
                    <th>Nama User</th>        
                    <th>Nama Driver</th>        
                    <th>Plat Nomor</th>        
                    <th>Nama Produk</th>        
                    <th>Alamat Pengiriman</th>        
                    <th>Quantity</th>        
                    <th>Harga</th>        
                    <th>Subtotal</th>        
                    <th>Ongkir</th>        
                    <th>Total</th>        
                    <th class="catatan-column">Catatan</th>        
                    <th>Status Pesanan</th>        
                    <th>Estimasi Waktu</th>        
                    <th>Berikan Rating</th>        
                </tr>        
            </thead>        
            <tbody>        
                <?php while ($row = $result_pesanan->fetch_assoc()) {   
                    $rating = $row['rating'];  
                    $rating_stars = '';  
                    if ($rating > 0) {  
                        for ($i = 1; $i <= 5; $i++) {  
                            if ($i <= $rating) {  
                                $rating_stars .= '<span style="color: gold;">★</span>';  
                            } else {  
                                $rating_stars .= '<span style="color: lightgray;">★</span>';  
                            }  
                        }  
                    }  
                ?>        
                    <tr>        
                        <td><?php echo htmlspecialchars($row['id_pesanan']); ?></td>        
                        <td><?php echo htmlspecialchars($row['nama_user']); ?></td>        
                        <td><?php echo htmlspecialchars($row['nama_driver']); ?></td>        
                        <td><?php echo htmlspecialchars($row['plat_nomor']); ?></td>        
                        <td><?php echo htmlspecialchars($row['nama_produk']); ?></td>        
                        <td><?php echo htmlspecialchars($row['alamat_pengiriman']); ?></td>        
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>        
                        <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>        
                        <td>Rp <?php echo number_format($row['subtotal'], 0, ',', '.'); ?></td>        
                        <td>Rp <?php echo number_format($row['ongkir'], 0, ',', '.'); ?></td>        
                        <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>        
                        <td class="catatan-column"><?php echo htmlspecialchars($row['catatan']); ?></td>        
                        <td><?php echo htmlspecialchars($row['status_pesanan']); ?></td>        
                        <td><?php echo htmlspecialchars($row['estimasi_waktu']); ?></td>        
                        <td>  
                            <?php if ($rating > 0) {   
                                echo $rating_stars;  
                            } else { ?>  
                                <a href="utama.php?page=ratingdriverfood&id_pesanan=<?php echo htmlspecialchars($row['id_pesanan']); ?>" class="rating-button">Berikan Rating</a>  
                            <?php } ?>  
                        </td>        
                    </tr>        
                <?php } ?>        
            </tbody>        
        </table>        
    </div>        
      
</body>        
</html>        
