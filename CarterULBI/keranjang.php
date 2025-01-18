<?php  
include 'config.php'; // Import database connection configuration  
  
if (!isset($_SESSION['username'])) {  
    header('Location: login.php'); // Redirect to login page if not logged in  
    exit();  
}  
  
// Ambil id_user dari session  
$username = $_SESSION['username'];  
  
// Query untuk mengambil id_user berdasarkan username  
$sql_user = "SELECT id_user FROM user WHERE username = ?";  
$stmt_user = $koneksi->prepare($sql_user);  
$stmt_user->bind_param("s", $username);  
$stmt_user->execute();  
$result_user = $stmt_user->get_result();  
  
if ($result_user->num_rows > 0) {  
    $row_user = $result_user->fetch_assoc();  
    $id_user = $row_user['id_user']; // Ambil id_user dari hasil query  
} else {  
    echo "<p>Pengguna tidak ditemukan.</p>";  
    exit();  
}  
  
// Query untuk mengambil data dari tabel keranjang berdasarkan id_user  
$sql_keranjang = "SELECT id_keranjang, gambar, nama_produk, quantity, harga, total_harga FROM keranjang WHERE id_user = ?";  
$stmt_keranjang = $koneksi->prepare($sql_keranjang);  
$stmt_keranjang->bind_param("i", $id_user); // Pastikan id_user adalah integer  
$stmt_keranjang->execute();  
$result_keranjang = $stmt_keranjang->get_result();  
  
// Cek apakah ada data  
if ($result_keranjang->num_rows > 0) {  
    echo "<style>  
            body {  
                font-family: Arial, sans-serif;  
                background-color: #f9f9f9;  
                margin: 0;  
                padding: 20px;  
            }  
            h2 {  
                text-align: center;  
                color: #333;  
            }  
            table {  
                margin: 0 auto;  
                border-collapse: collapse;  
                width: 80%;  
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);  
            }  
            th, td {  
                padding: 15px;  
                text-align: center;  
                border: 2px solid var(--bs-dark);  
            }  
            th {  
                background-color: #f2f2f2;  
                color: navy;  
            }  
            tr:nth-child(even) {  
                background-color: #f9f9f9;  
            }  
            tr:hover {  
                background-color: #e0e0e0;  
            }  
            img {  
                width: 100px;  
                height: auto;  
            }  
            .button {  
                background-color: var(--bs-dark);  
                color: white;  
                border: none;  
                padding: 10px 20px;  
                font-size: 16px;  
                cursor: pointer;  
                border-radius: 5px;  
                transition: background-color 0.3s, transform 0.2s;  
                margin: 0 5px;  
                width: 150px;  
            }  
            .button:hover {  
                background-color: #343a40;  
                transform: scale(1.05);  
            }  
            .button:active {  
                background-color: #212529;  
                transform: scale(0.95);  
            }  
            .button-container {  
                text-align: center;  
                margin-top: 20px;  
            }  
          </style>";  
  
    echo "<h2>Keranjang Belanja</h2>";  
    echo "<form action='update_quantity.php' method='POST'>"; // Form untuk update quantity  
    echo "<table>  
            <tr>  
                <th><input type='checkbox' id='selectAll' onclick='toggleSelectAll(this)'></th>  
                <th>ID Keranjang</th>  
                <th>Nama Pengguna</th>  
                <th>Gambar</th>  
                <th>Nama Produk</th>  
                <th>Quantity</th>  
                <th>Harga</th>  
                <th>Total Harga</th>  
                <th>Aksi</th>  
            </tr>";  
  
    // Loop melalui hasil dan tampilkan dalam tabel  
    while ($row_keranjang = $result_keranjang->fetch_assoc()) {  
        echo "<tr>  
                <td><input type='checkbox' name='select[]' value='" . $row_keranjang['id_keranjang'] . "'></td>  
                <td>" . $row_keranjang['id_keranjang'] . "</td>  
                <td>" . htmlspecialchars($username) . "</td>  
                <td><img src='../gambarfood/" . htmlspecialchars($row_keranjang['gambar']) . "' alt='" . htmlspecialchars($row_keranjang['nama_produk']) . "'></td>  
                <td>" . htmlspecialchars($row_keranjang['nama_produk']) . "</td>  
                <td>  
                    <input type='hidden' name='id_keranjang[]' value='" . $row_keranjang['id_keranjang'] . "'>  
                    <input type='number' name='quantity[]' id='quantity_" . $row_keranjang['id_keranjang'] . "' value='" . $row_keranjang['quantity'] . "' min='1' style='width: 50px; text-align: center;' onchange='updateTotalPrice(" . $row_keranjang['id_keranjang'] . ", " . $row_keranjang['harga'] . ")'>  
                </td>  
                <td>Rp" . number_format($row_keranjang['harga'], 0, ',', '.') . "</td>  
                <td id='total_" . $row_keranjang['id_keranjang'] . "'>Rp" . number_format($row_keranjang['total_harga'], 0, ',', '.') . "</td>  
                <td><a href='hapus_keranjang.php?id_keranjang=" . $row_keranjang['id_keranjang'] . "' onclick='return confirm(\"Apakah Anda yakin ingin menghapus item ini?\");'>X</a></td>  
              </tr>";  
    }  
  
    echo "</table>";  
    echo "<div class='button-container'>";  
    echo "<input type='submit' value='Update Quantity' class='button'>";  
    echo "<a href='checkout.php' class='button'>Pesan</a>";  
    echo "</div>";  
    echo "</form>"; // Tutup form untuk update quantity  
  
    // Delivery Options Section  
    echo "<h2>Opsi Pengantaran</h2>";  
    echo "<form action='checkout.php' method='POST'>"; // Form for delivery options  
    echo "<table style='margin: 0 auto; width: 80%;'>";  
    echo "<tr>  
            <th>Opsi Pengantaran</th>  
            <th>Biaya</th>  
            <th>Pilih</th>  
          </tr>";  
    echo "<tr>  
            <td>Hemat</td>  
            <td>Rp2.000</td>  
            <td><input type='radio' name='delivery_option' value='2000' required></td>  
          </tr>";  
    echo "<tr>  
            <td>Standar</td>  
            <td>Rp5.000</td>  
            <td><input type='radio' name='delivery_option' value='5000' required></td>  
          </tr>";  
    echo "<tr>  
            <td>Prioritas</td>  
            <td>Rp10.000</td>  
            <td><input type='radio' name='delivery_option' value='10000' required></td>  
          </tr>";  
    echo "</table>";  
    echo "</form>"; // Close delivery options form  
} else {  
    echo "<p style='text-align: center;'>Keranjang Anda kosong.</p>";  
}  
  
$stmt_user->close();  
$stmt_keranjang->close();  
$koneksi->close();  
?>  
  
<script>  
function updateTotalPrice(id, price) {  
    var quantityInput = document.getElementById('quantity_' + id);  
    var quantity = parseInt(quantityInput.value);  
    var totalPrice = quantity * price;  
    document.getElementById('total_' + id).innerText = 'Rp' + totalPrice.toLocaleString('id-ID');  
}  
  
function toggleSelectAll(selectAllCheckbox) {  
    // Get all checkboxes in the table  
    var checkboxes = document.querySelectorAll('input[name="select[]"]');  
    // Set each checkbox to the state of the "Select All" checkbox  
    checkboxes.forEach(function(checkbox) {  
        checkbox.checked = selectAllCheckbox.checked;  
    });  
}  
</script>  
