<?php
include 'config.php'; // Koneksi ke database

// Periksa apakah session username driver ada
if (!isset($_SESSION['username'])) {
    die("Anda belum login sebagai driver.");
}

$username = $_SESSION['username'];

// Query untuk mengambil data dari tabel detail_pesanan berdasarkan username driver
$query = "
    SELECT 
        dp.id_detail_pesanan, 
        dp.id_pengantaran, 
        dp.id_pesanan
    FROM 
        detail_pesanan dp
    INNER JOIN 
        pesanan_makanan pm ON dp.id_pesanan = pm.id_pesanan
    INNER JOIN 
        driver d ON pm.id_driver = d.id_driver
    WHERE 
        d.username = ?
";

// Menggunakan prepared statement untuk mencegah SQL injection
$stmt = mysqli_prepare($koneksi, $query);

if (!$stmt) {
    die("Query gagal: " . mysqli_error($koneksi));
}

// Bind parameter
mysqli_stmt_bind_param($stmt, "s", $username);

// Eksekusi query
mysqli_stmt_execute($stmt);

// Ambil hasil
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die("Query gagal: " . mysqli_error($koneksi));
}

// Menampilkan hasil
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "ID Detail Pesanan: " . htmlspecialchars($row['id_detail_pesanan']) . "<br>";
        echo "ID Pengantaran: " . htmlspecialchars($row['id_pengantaran']) . "<br>";
        echo "ID Pesanan: " . htmlspecialchars($row['id_pesanan']) . "<br><br>";
    }
} else {
    echo "Data detail pesanan tidak ditemukan.";
}

// Tutup statement dan koneksi
mysqli_stmt_close($stmt);
mysqli_close($koneksi);
?>
