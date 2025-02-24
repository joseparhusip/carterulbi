<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_driver'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_bukti'])) {
    $id_pesanan = $_POST['id_pesanan'];
    $image_data = $_POST['image_data'];
    
    // Remove the data URL prefix
    $image_parts = explode(";base64,", $image_data);
    $image_base64 = isset($image_parts[1]) ? $image_parts[1] : $image_data;
    
    // Decode base64 data
    $image_decoded = base64_decode($image_base64);
    
    if ($image_decoded === false) {
        echo json_encode(['success' => false, 'error' => 'Invalid image data']);
        exit;
    }
    
    // Generate unique filename
    $file_name = time() . '_' . uniqid() . '.jpg';
    $target_path = '../buktipengiriman/' . $file_name;
    
    // Save image file
    if (file_put_contents($target_path, $image_decoded)) {
        // Update database
        $update_query = "UPDATE pesanan_makanan SET bukti_pengiriman = ? WHERE id_pesanan = ?";
        $update_stmt = mysqli_prepare($koneksi, $update_query);
        
        if ($update_stmt) {
            mysqli_stmt_bind_param($update_stmt, "si", $file_name, $id_pesanan);
            if (mysqli_stmt_execute($update_stmt)) {
                echo json_encode(['success' => true, 'message' => 'Bukti pengiriman berhasil diunggah.']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Gagal mengupdate database: ' . mysqli_error($koneksi)]);
            }
            mysqli_stmt_close($update_stmt);
        } else {
            echo json_encode(['success' => false, 'error' => 'Gagal mempersiapkan query']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Gagal menyimpan file']);
    }
    
    mysqli_close($koneksi);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Invalid request']);
exit;
?>