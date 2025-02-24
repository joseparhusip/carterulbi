<?php
include "config.php";
header('Content-Type: application/json');

// Validasi request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed'
    ]);
    exit;
}

// Ambil data dari POST request
$id_pesanan = isset($_POST['id_pesanan']) ? (int)$_POST['id_pesanan'] : 0;
$status = isset($_POST['status']) ? $_POST['status'] : '';

// Validasi input
if (!$id_pesanan || !in_array($status, ['PENDING', 'PAID', 'FAILED', 'REFUNDED'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid input parameters'
    ]);
    exit;
}

// Update status di database
$query = "UPDATE pesanan_makanan SET status_pembayaran = ? WHERE id_pesanan = ?";

try {
    if ($stmt = $koneksi->prepare($query)) {
        $stmt->bind_param("si", $status, $id_pesanan);
        
        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Status pembayaran berhasil diperbarui'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal mengupdate status pembayaran'
            ]);
        }
        
        $stmt->close();
    } else {
        throw new Exception("Gagal mempersiapkan query");
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

$koneksi->close();
?>