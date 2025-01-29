<?php        
  
// Include konfigurasi database        
include 'config.php';        
    
// Ensure the user is logged in by checking the session for 'username'    
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
    
if ($result_user->num_rows == 0) {    
    die('Error: User tidak ditemukan.');    
}    
    
$user_data = $result_user->fetch_assoc();    
$id_user = $user_data['id_user'];    
    
// Query to fetch order history    
$query_order = "    
    SELECT     
        po.id_pengantaran,    
        u.id_user,    
        u.nama AS nama_user,    
        d.id_driver,    
        d.gambardriver,    
        d.nama AS nama_driver,    
        d.plat_nomor,    
        po.titik_antar,    
        po.titik_jemput,    
        po.jarak_km,    
        po.biaya,    
        po.catatan,    
        po.status,    
        po.rating -- tambahkan kolom rating ke query    
    FROM     
        pengantaran_orang po    
    JOIN     
        user u ON po.id_user = u.id_user    
    JOIN     
        driver d ON po.id_driver = d.id_driver    
    WHERE     
        u.id_user = ?    
    ORDER BY     
        po.id_pengantaran DESC    
";    
    
$stmt_order = $koneksi->prepare($query_order);        
$stmt_order->bind_param('i', $id_user);        
$stmt_order->execute();        
$result_order = $stmt_order->get_result();        
    
if ($result_order->num_rows == 0) {    
    $order_history_html = '<p>Tidak ada riwayat pesanan.</p>';    
} else {    
    $order_history_html = '<table>    
        <tr>    
            <th>ID Pengantaran</th>    
            <th>ID User</th>    
            <th>Nama User</th>    
            <th>ID Driver</th>    
            <th>Gambar Driver</th>    
            <th>Nama Driver</th>    
            <th>Plat Nomor</th>    
            <th>Titik Antar</th>    
            <th>Titik Jemput</th>    
            <th>Jarak (KM)</th>    
            <th>Biaya</th>    
            <th>Catatan</th>    
            <th>Status</th>    
            <th>Beri Rating</th>    
        </tr>';    
    
    while ($row = $result_order->fetch_assoc()) {    
        $order_history_html .= '<tr>    
            <td>' . htmlspecialchars($row['id_pengantaran']) . '</td>    
            <td>' . htmlspecialchars($row['id_user']) . '</td>    
            <td>' . htmlspecialchars($row['nama_user']) . '</td>    
            <td>' . htmlspecialchars($row['id_driver']) . '</td>    
            <td><img src="gambardriver/' . htmlspecialchars($row['gambardriver']) . '" class="driver-image" alt="Driver Image"></td>    
            <td>' . htmlspecialchars($row['nama_driver']) . '</td>    
            <td>' . htmlspecialchars($row['plat_nomor']) . '</td>    
            <td>' . htmlspecialchars($row['titik_antar']) . '</td>    
            <td>' . htmlspecialchars($row['titik_jemput']) . '</td>    
            <td>' . htmlspecialchars($row['jarak_km']) . '</td>    
            <td>' . htmlspecialchars($row['biaya']) . '</td>    
            <td class="catatan-column">' . htmlspecialchars($row['catatan']) . '</td>    
            <td>' . htmlspecialchars($row['status']) . '</td>    
            <td>';    
          
        // Periksa apakah rating sudah diberikan    
        if ($row['rating'] > 0) {    
            // Jika rating sudah diberikan, tampilkan rating dalam bentuk bintang    
            $rating_stars = str_repeat('<span class="star">&#9733;</span>', $row['rating']);    
            $order_history_html .= $rating_stars;    
        } else {    
            // Jika rating belum diberikan, tampilkan tombol 'Beri Rating'    
            $order_history_html .= '<a href="utama.php?page=ratingdriver&id_pengantaran=' . htmlspecialchars($row['id_pengantaran']) . '" class="rating-button">Beri Rating</a>';    
        }    
          
        $order_history_html .= '</td>    
        </tr>';    
    }    
    
    $order_history_html .= '</table>';    
}    
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
    
        .catatan-column {    
            width: 200px; /* Adjust the width as needed */    
            white-space: pre-wrap; /* Allow text to wrap */    
        }    
  
        .rating-button {    
            display: inline-block;    
            padding: 8px 12px;    
            background-color: #28a745;    
            color: #fff;    
            text-decoration: none;    
            border-radius: 5px;    
            text-align: center;    
        }    
    
        .rating-button:hover {    
            background-color: #218838;    
        }    
  
        .star {    
            color: #FFD700; /* Warna kuning untuk bintang */    
            font-size: 18px; /* Ukuran bintang */    
        }    
    </style>    
</head>    
<body>    
    
    <h1>Riwayat Pesanan Makanan</h1>    
    <div class="order-history">    
        <?php echo $order_history_html; ?>    
    </div>    
    
</body>    
</html>  
