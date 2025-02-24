<?php
session_start();
include 'config.php';

// Check if driver is logged in
if (!isset($_SESSION['id_driver'])) {
    $response = [
        'success' => false,
        'message' => 'Anda belum login sebagai driver.'
    ];
    echo json_encode($response);
    exit;
}

// Check if it's a POST request and has required data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id_pesanan = $_POST['id_pesanan'];
    $new_status = $_POST['status_pesanan'];
    
    // Validate the status value
    $allowed_statuses = ['menunggu', 'diproses', 'dikirim', 'selesai', 'dibatalkan'];
    if (!in_array($new_status, $allowed_statuses)) {
        $response = [
            'success' => false,
            'message' => 'Status pesanan tidak valid.'
        ];
        echo json_encode($response);
        exit;
    }

    // Prepare and execute the update query
    $update_query = "UPDATE pesanan_makanan SET status_pesanan = ? WHERE id_pesanan = ?";
    $stmt = mysqli_prepare($koneksi, $update_query);

    if (!$stmt) {
        $response = [
            'success' => false,
            'message' => 'Gagal mempersiapkan query: ' . mysqli_error($koneksi)
        ];
        echo json_encode($response);
        exit;
    }

    mysqli_stmt_bind_param($stmt, "si", $new_status, $id_pesanan);

    if (mysqli_stmt_execute($stmt)) {
        $response = [
            'success' => true,
            'message' => 'Status pesanan berhasil diperbarui',
            'new_status' => $new_status
        ];
    } else {
        $response = [
            'success' => false,
            'message' => 'Gagal memperbarui status: ' . mysqli_error($koneksi)
        ];
    }

    mysqli_stmt_close($stmt);
} else {
    $response = [
        'success' => false,
        'message' => 'Invalid request method or missing parameters.'
    ];
}

// Close database connection
mysqli_close($koneksi);

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);