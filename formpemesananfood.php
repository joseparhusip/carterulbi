<?php    
// Include konfigurasi database            
include 'config.php';    
    
if (!isset($_SESSION['username'])) {    
    die('Error: Anda harus login untuk membuat pesanan.');    
}    
    
// Ambil data user yang login berdasarkan username            
$username = $_SESSION['username'];    
$query_user = "SELECT nama, alamat, no_hp FROM user WHERE username = ?";    
$stmt_user = $koneksi->prepare($query_user);    
$stmt_user->bind_param('s', $username);    
$stmt_user->execute();    
$result_user = $stmt_user->get_result();    
    
if ($result_user->num_rows === 0) {    
    die('Error: Data user tidak ditemukan.');    
}    
    
$user_data = $result_user->fetch_assoc(); // Ambil data user            
    
// Ambil data ID keranjang dari URL            
if (!isset($_GET['id_keranjang']) || empty($_GET['id_keranjang'])) {    
    die('Error: Tidak ada item yang dipilih untuk dipesan.');    
}    
    
$id_keranjang = explode(',', $_GET['id_keranjang']);    
    
// Ambil detail produk berdasarkan id_keranjang            
$placeholders = implode(',', array_fill(0, count($id_keranjang), '?'));    
$query_pesanan = "SELECT k.id_keranjang, k.id_produk, k.quantity, k.total_harga, k.harga, k.nama_produk, k.gambar             
                  FROM keranjang k            
                  JOIN produk_makanan p ON k.id_produk = p.id_produk            
                  WHERE k.id_keranjang IN ($placeholders)";    
$stmt_pesanan = $koneksi->prepare($query_pesanan);    
$stmt_pesanan->bind_param(str_repeat('i', count($id_keranjang)), ...$id_keranjang);    
$stmt_pesanan->execute();    
$result_pesanan = $stmt_pesanan->get_result();    
    
if ($result_pesanan->num_rows === 0) {    
    echo "<p>Keranjang kosong atau item tidak ditemukan.</p>";    
    exit;    
}    
    
// Ambil data driver dari tabel driver            
$query_driver = "SELECT id_driver, nama, kendaraan, plat_nomor, rating FROM driver";    
$result_driver = $koneksi->query($query_driver);    
    
if (!$result_driver) {    
    die('Error: Gagal mengambil data driver.');    
}    
    
// Ambil data metode pembayaran dari tabel metode_pembayaran            
$query_metode = "SELECT id_metode_pembayaran, nama_metode, deskripsi FROM metode_pembayaran";    
$result_metode = $koneksi->query($query_metode);    
    
if (!$result_metode) {    
    die('Error: Gagal mengambil data metode pembayaran.');    
}  
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $alamat_pengiriman = $_POST['alamat_pengiriman'];
    $catatan = $_POST['catatan'];
    $id_driver = $_POST['id_driver'];
    $id_metode_pembayaran = $_POST['id_metode_pembayaran'];

    $errors = [];

    // Validasi metode pembayaran yang memerlukan bukti transfer
    if ($id_metode_pembayaran == 1 || $id_metode_pembayaran == 2) {
        // Cek apakah file bukti transfer telah diunggah
        if (empty($_FILES['bukti_transfer']['name'])) {
            $errors[] = "Upload bukti pembayaran wajib untuk metode pembayaran BCA atau DANA.";
        } else {
            // Validasi ekstensi file
            $file_name = $_FILES['bukti_transfer']['name'];
            $file_size = $_FILES['bukti_transfer']['size'];
            $file_tmp = $_FILES['bukti_transfer']['tmp_name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png'];

            if (!in_array($file_ext, $allowed_extensions)) {
                $errors[] = "Format file tidak valid. Hanya diperbolehkan JPG, JPEG, atau PNG.";
            }

            // Validasi ukuran file
            if ($file_size > 2097152) { // Maksimum 2MB
                $errors[] = "Ukuran file terlalu besar. Maksimum 2MB.";
            }
        }
    }

    if (empty($errors)) {
        // Jika tidak ada error, proses pengunggahan file
        $bukti_transfer = null;
        if ($id_metode_pembayaran == 1 || $id_metode_pembayaran == 2) {
            $upload_dir = "buktitf/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);  // Membuat direktori upload jika belum ada
            }
            // Mengganti nama file agar unik
            $new_file_name = uniqid() . '.' . $file_ext;
            move_uploaded_file($file_tmp, $upload_dir . $new_file_name);  // Pindahkan file yang diunggah
            $bukti_transfer = $new_file_name;  // Menyimpan nama file bukti transfer
        }

        // Menyimpan pesanan ke database
        while ($row = $result_pesanan->fetch_assoc()) {
            $id_produk = $row['id_produk'];
            $quantity = $row['quantity'];
            $harga = $row['harga'];
            $subtotal = $quantity * $harga;
            $ongkir = 5000;  // Ongkir tetap
            $total_harga = $subtotal + $ongkir;

            // Menyimpan data pesanan berdasarkan metode pembayaran
            if ($id_metode_pembayaran == 3) {
                // Jika metode pembayaran tidak memerlukan bukti pembayaran
                $query_order = "INSERT INTO pesanan_makanan (id_user, id_produk, id_driver, id_metode_pembayaran, alamat_pengiriman, harga, quantity, subtotal, ongkir, total_harga, catatan, created_at)
                                VALUES ((SELECT id_user FROM user WHERE username = ?), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                $stmt_order = $koneksi->prepare($query_order);
                $stmt_order->bind_param('siissidddds', $username, $id_produk, $id_driver, $id_metode_pembayaran, $alamat_pengiriman, $harga, $quantity, $subtotal, $ongkir, $total_harga, $catatan);
            } else {
                // Jika metode pembayaran memerlukan bukti pembayaran
                $query_order = "INSERT INTO pesanan_makanan (id_user, id_produk, id_driver, id_metode_pembayaran, alamat_pengiriman, harga, quantity, subtotal, ongkir, total_harga, catatan, bukti_pembayaran, created_at)
                                VALUES ((SELECT id_user FROM user WHERE username = ?), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                $stmt_order = $koneksi->prepare($query_order);
                $stmt_order->bind_param('siissidddsss', $username, $id_produk, $id_driver, $id_metode_pembayaran, $alamat_pengiriman, $harga, $quantity, $subtotal, $ongkir, $total_harga, $catatan, $bukti_transfer);
            }
            $stmt_order->execute();  // Eksekusi query untuk menyimpan pesanan
        }

        // Menghapus item dari keranjang setelah pesanan disimpan
        $query_delete_cart = "DELETE FROM keranjang WHERE id_keranjang IN ($placeholders)";
        $stmt_delete_cart = $koneksi->prepare($query_delete_cart);
        $stmt_delete_cart->bind_param(str_repeat('i', count($id_keranjang)), ...$id_keranjang);
        $stmt_delete_cart->execute();  // Menghapus item dari keranjang

        // Memberikan pesan sukses dan mengarahkan kembali ke halaman detail pesanan
        echo "<script>alert('Pesanan berhasil dibuat!'); window.location.href = 'utama.php?page=detailpesananmakanan';</script>";
        exit;
    } else {
        // Menampilkan pesan error jika ada kesalahan
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }
}
?>


    <!DOCTYPE html>    
<html lang="en">    
    
<head>    
    <meta charset="UTF-8">    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    
    <title>Form Pemesanan</title>    
    <script>
        function toggleUploadField() {
            const paymentMethod = document.getElementById('id_metode_pembayaran').value;
            const uploadField = document.getElementById('upload-section');
            
            // Show upload field only if id_metode_pembayaran is 1 or 2
            if (paymentMethod === '1' || paymentMethod === '2') {
                uploadField.style.display = 'block';
                document.getElementById('bukti_pembayaran').required = true;
            } else {
                uploadField.style.display = 'none';
                document.getElementById('bukti_pembayaran').required = false;
            }
        }
    </script>
</head>    
<style>    
        body {    
            font-family: 'Arial', sans-serif;    
            margin: 0;    
            padding: 0;    
            background: linear-gradient(to right, #4facfe, #00f2fe);    
            color: #333;    
        }    
    
        .container {    
            display: flex;    
            justify-content: center;    
            margin-top: 50px;    
            gap: 20px;    
        }    
    
        .order-form,    
        .product-card {    
            max-width: 400px;    
            background: #fff;    
            border-radius: 10px;    
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);    
            padding: 20px;    
            margin-bottom: 20px;    
        }    
    
        .order-form h1,    
        .product-card h2 {    
            text-align: center;    
            font-size: 2rem;    
            margin-bottom: 20px;    
            color: #007BFF;    
        }    
    
        .order-form form div {    
            margin-bottom: 15px;    
        }    
    
        .order-form label,    
        .product-card label {    
            display: block;    
            font-weight: bold;    
            margin-bottom: 5px;    
        }    
    
        .order-form input[type="text"],    
        .order-form textarea,    
        .product-card input[type="text"],    
        .product-card textarea {    
            width: 100%;    
            padding: 10px;    
            border: 1px solid #ddd;    
            border-radius: 5px;    
            font-size: 1rem;    
        }    
    
        .order-form textarea,    
        .product-card textarea {    
            resize: none;    
            height: 80px;    
        }    
    
        .order-form button[type="submit"] {    
            display: block;    
            width: 100%;    
            padding: 15px;    
            border: none;    
            background: #007BFF;    
            color: #fff;    
            font-size: 1rem;    
            border-radius: 5px;    
            cursor: pointer;    
            transition: background 0.3s;    
        }    
    
        .order-form button[type="submit"]:hover {    
            background: #0056b3;    
        }    
    
        .product-card .product-item {    
            display: flex;    
            align-items: center;    
            margin-bottom: 15px;    
            padding: 15px;    
            border: 1px solid #ddd;    
            border-radius: 5px;    
            background: #f9f9f9;    
            transition: box-shadow 0.3s;    
        }    
    
        .product-card .product-item:hover {    
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);    
        }    
    
        .product-card .product-item img {    
            width: 100px;    
            height: auto;    
            margin-right: 20px;    
            border-radius: 5px;    
        }    
    
        .product-card .product-item .product-details {    
            flex: 1;    
        }    
    
        .product-card .product-item .product-details .product-name {    
            font-size: 1.2rem;    
            margin-bottom: 5px;    
            color: #333;    
        }    
    
        .product-card .product-item .product-details .product-quantity {    
            font-size: 1rem;    
            color: #555;    
        }    
    
        .product-card .product-item .product-details .product-total {    
            font-size: 1.1rem;    
            font-weight: bold;    
            color: #007BFF;    
        }    
    
        .product-card .product-item .product-details .product-price {    
            font-size: 1rem;    
            color: #555;    
        }    
    
        @media (max-width: 768px) {    
            .container {    
                flex-direction: column;    
                align-items: center;    
            }    
    
            .order-form,    
            .product-card {    
                margin-right: 0;    
                margin-bottom: 20px;    
            }    
        }    
    
        select {    
            width: 100%;    
            padding: 10px;    
            border: 1px solid #ddd;    
            border-radius: 5px;    
            font-size: 1rem;    
            background: #f9f9f9;    
            appearance: none;    
            cursor: pointer;    
            transition: border-color 0.3s ease-in-out;    
        }    
    
        select:focus {    
            outline: none;    
            border-color: #007BFF;    
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);    
        }    
    
        select option {    
            padding: 10px;    
            font-size: 1rem;    
        }    
    
        div label {    
            font-size: 1rem;    
            color: #333;    
        }    
    </style>
<body>    
    <div class="container">    
        <div class="product-card">    
            <h2>Detail Produk</h2>    
            <?php   
            $total_all = 0;  
            while ($row = $result_pesanan->fetch_assoc()) {   
                $subtotal = $row['quantity'] * $row['harga'];  
                $ongkir = 5000;  
                $total_harga = $subtotal + $ongkir;  
                $total_all += $total_harga;  
            ?>    
                <div class="product-item">    
                    <img src="gambarfood/<?php echo htmlspecialchars($row['gambar']); ?>" alt="Gambar <?php echo htmlspecialchars($row['nama_produk']); ?>">    
                    <div class="product-details">    
                        <div class="product-name"><?php echo htmlspecialchars($row['nama_produk']); ?></div>    
                        <div class="product-quantity">Quantity: <?php echo htmlspecialchars($row['quantity']); ?></div>    
                        <div class="product-price">Harga: Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></div>    
                        <div class="product-subtotal">Subtotal: Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></div>    
                        <div class="product-ongkir">Ongkir: Rp <?php echo number_format($ongkir, 0, ',', '.'); ?></div>    
                        <div class="product-total">Total Harga: Rp <?php echo number_format($total_harga, 0, ',', '.'); ?></div>    
                    </div>    
                </div>    
            <?php } ?>    
            <div class="product-item">    
                <div class="product-details">    
                    <div class="product-total">Total Semua: Rp <?php echo number_format($total_all, 0, ',', '.'); ?></div>    
                </div>    
            </div>    
        </div>    
        <div class="order-form">    
            <h1>Form Pemesanan</h1>    
            <form method="POST" action="" enctype="multipart/form-data">    
                <div>    
                    <label for="nama_pemesan">Nama Pemesan</label>    
                    <input type="text" id="nama_pemesan" name="nama_pemesan" value="<?php echo htmlspecialchars($user_data['nama']); ?>" required>    
                </div>    
                <div>    
                    <label for="alamat_pengiriman">Alamat Pengiriman</label>    
                    <textarea id="alamat_pengiriman" name="alamat_pengiriman" required><?php echo htmlspecialchars($user_data['alamat']); ?></textarea>    
                </div>    
                <div>    
                    <label for="no_hp">No HP</label>    
                    <input type="text" id="no_hp" name="no_hp" value="<?php echo htmlspecialchars($user_data['no_hp']); ?>" required>    
                </div>    
                <div>    
                    <label for="id_driver">Pilih Driver</label>    
                    <select id="id_driver" name="id_driver" required>    
                        <option value="">-- Pilih Driver --</option>    
                        <?php while ($driver = $result_driver->fetch_assoc()) { ?>    
                            <option value="<?php echo $driver['id_driver']; ?>">    
                                <?php echo htmlspecialchars($driver['nama']) . " - " . htmlspecialchars($driver['kendaraan']) . " (" . htmlspecialchars($driver['plat_nomor']) . ") - Rating: " . htmlspecialchars($driver['rating']); ?>    
                            </option>    
                        <?php } ?>    
                    </select>    
                </div>    
                <div>    
                    <label for="id_metode_pembayaran">Pilih Metode Pembayaran</label>    
                    <select id="id_metode_pembayaran" name="id_metode_pembayaran" required onchange="toggleUploadField()">    
                        <option value="">-- Pilih Metode Pembayaran --</option>    
                        <?php while ($metode = $result_metode->fetch_assoc()) { ?>    
                            <option value="<?php echo $metode['id_metode_pembayaran']; ?>">    
                                <?php echo htmlspecialchars($metode['nama_metode']) . " - " . htmlspecialchars($metode['deskripsi']); ?>    
                            </option>    
                        <?php } ?>    
                    </select>    
                </div>    
                <div id="upload-section" style="display: none;">    
                    <label for="bukti_pembayaran">Unggah Bukti Pembayaran</label>    
                    <input type="file" id="bukti_pembayaran" name="bukti_transfer" accept="image/*">
                </div>    
                <div>    
                    <label for="catatan">Catatan</label>    
                    <input type="text" id="catatan" name="catatan" placeholder="Masukkan Catatan" required>    
                </div>    
                <button type="submit" name="submit_order">Kirim Pesanan</button>    
            </form>    
        </div>    
    </div>    
</body>    
    
</html>