<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'config.php';
session_start();
if (!isset($_GET['id_pesanan'])) {
    die("ID Pesanan tidak ditemukan.");
}
$id_pesanan = $_GET['id_pesanan'];
// Query untuk mendapatkan data pesanan
$sql = "SELECT 
    pm.id_pesanan,
    u.nama,
    p.nama_produk,
    pm.harga,
    pm.quantity,
    pm.ongkir,
    pm.subtotal,
    pm.total_harga,
    pm.created_at
FROM 
    pesanan_makanan pm
LEFT JOIN 
    user u ON pm.id_user = u.id_user
LEFT JOIN 
    produk_makanan p ON pm.id_produk = p.id_produk
WHERE 
    pm.id_pesanan = ?";
// Prepare statement
$stmt = $koneksi->prepare($sql);
// Check if prepare was successful
if ($stmt === false) {
    die("Prepare failed: " . $koneksi->error);
}
// Bind parameter
if (!$stmt->bind_param("i", $id_pesanan)) {
    die("Binding parameters failed: " . $stmt->error);
}
// Execute statement
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}
// Bind result
if (!$stmt->bind_result(
    $id_pesanan,
    $nama_user,
    $nama_produk,
    $harga,
    $quantity,
    $ongkir,
    $subtotal,
    $total_harga,
    $created_at
)) {
    die("Binding results failed: " . $stmt->error);
}
// Fetch the results
if (!$stmt->fetch()) {
    die("Data pesanan tidak ditemukan.");
}
$stmt->close();
$koneksi->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Order Receipt</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            background: #f9f9f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .receipt-container {
            background: #fff;
            max-width: 500px;
            width: 100%;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .logo img {
            max-width: 120px;
            height: auto;
            margin-bottom: 20px;
        }
        h2 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #333;
        }
        .order-id {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }
        .customer-info {
            font-size: 16px;
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            font-weight: bold;
            color: #333;
        }
        table td {
            color: #555;
        }
        .total-row {
            font-weight: bold;
            color: #333;
        }
        .footer {
            font-size: 14px;
            color: #666;
            margin-top: 20px;
        }
        .contact-info {
            font-size: 12px;
            color: #888;
            margin-top: 10px;
        }
        @media (max-width: 600px) {
            .receipt-container {
                padding: 20px;
            }
            table {
                font-size: 14px;
            }
            h2 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="logo">
            <img src="./CarterULBI/logo/carter.png" alt="Logo">
        </div>
        <h2>Food Order Receipt</h2>
        <p class="order-id">Order ID: #<?php echo htmlspecialchars($id_pesanan); ?></p>
        <p class="order-id"><?php echo date('d F Y H:i', strtotime($created_at)); ?></p>
        <p class="customer-info"><strong>Customer:</strong> <?php echo htmlspecialchars($nama_user); ?></p>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo htmlspecialchars($nama_produk); ?></td>
                    <td><?php echo $quantity; ?></td>
                    <td>Rp <?php echo number_format($harga, 0, ',', '.'); ?></td>
                    <td>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="total-row">Subtotal:</td>
                    <td class="total-row">Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="total-row">Shipping Cost:</td>
                    <td class="total-row">Rp <?php echo number_format($ongkir, 0, ',', '.'); ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="total-row">Grand Total:</td>
                    <td class="total-row">Rp <?php echo number_format($total_harga, 0, ',', '.'); ?></td>
                </tr>
            </tfoot>
        </table>
        <div class="footer">
            <p>Thank you for your order!</p>
        </div>
        <div class="contact-info">
            <p>Contact Us: Cabi@gmail.com</p>
            <p>WhatsApp: 081292690095</p>
        </div>
    </div>
    <script type="text/javascript">
        window.print();
    </script>
</body>
</html>