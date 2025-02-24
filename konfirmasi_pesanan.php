<?php
include 'config.php';
session_start();

if (!isset($_SESSION['username'])) {
    die('Error: Anda harus login untuk mengkonfirmasi pesanan.');
}

if (!isset($_GET['id_pesanan'])) {
    die('Error: ID pesanan tidak ditemukan.');
}

$id_pesanan = intval($_GET['id_pesanan']);
$username = $_SESSION['username'];

// Verifikasi bahwa pesanan ini milik user yang sedang login
$query_verify = "SELECT pm.id_pesanan 
                FROM pesanan_makanan pm
                JOIN user u ON pm.id_user = u.id_user
                WHERE pm.id_pesanan = ? 
                AND u.username = ? 
                AND pm.status_pesanan = 'dikirim'
                AND pm.konfirmasi_pesanan = 0";
                
$stmt_verify = $koneksi->prepare($query_verify);
$stmt_verify->bind_param('is', $id_pesanan, $username);
$stmt_verify->execute();

if (!$stmt_verify->fetch()) {
    $stmt_verify->close();
    die('Error: Anda tidak memiliki akses untuk mengkonfirmasi pesanan ini atau pesanan sudah dikonfirmasi.');
}
$stmt_verify->close();

// Mulai transaksi
$koneksi->begin_transaction();

try {
    // Update status pesanan menjadi selesai dan set konfirmasi_pesanan = 1
    $query_update = "UPDATE pesanan_makanan 
                    SET status_pesanan = 'selesai',
                        konfirmasi_pesanan = 1 
                    WHERE id_pesanan = ?";
    $stmt_update = $koneksi->prepare($query_update);
    $stmt_update->bind_param('i', $id_pesanan);

    if ($stmt_update->execute()) {
        // Commit transaksi jika berhasil
        $koneksi->commit();
        echo "<script>
                alert('Pesanan berhasil dikonfirmasi!');
                window.location.href = 'utama.php?page=detailpesananmakanan';
              </script>";
    } else {
        // Rollback jika gagal
        $koneksi->rollback();
        echo "<script>
                alert('Gagal mengkonfirmasi pesanan.');
                window.location.href = 'utama.php?page=detailpesananmakanan';
              </script>";
    }

    $stmt_update->close();
} catch (Exception $e) {
    // Rollback jika terjadi error
    $koneksi->rollback();
    echo "<script>
            alert('Terjadi kesalahan saat mengkonfirmasi pesanan.');
            window.location.href = 'utama.php?page=detailpesananmakanan';
          </script>";
}
?>