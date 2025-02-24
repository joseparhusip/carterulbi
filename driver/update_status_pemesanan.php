<?php
include 'config.php'; // Koneksi ke database

// Pastikan data id_pesanan, status_pesanan, dan estimasi_waktu ada
if (isset($_POST['id_pesanan']) && isset($_POST['status_pesanan']) && isset($_POST['estimasi_waktu'])) {
    $idPesanan = $_POST['id_pesanan'];
    $statusPesanan = $_POST['status_pesanan'];
    $estimasiWaktu = $_POST['estimasi_waktu'];

    // Update status pesanan dan estimasi waktu pada tabel pesanan_makanan
    $query = "UPDATE pesanan_makanan SET status_pesanan = ?, estimasi_waktu = ? WHERE id_pesanan = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("ssi", $statusPesanan, $estimasiWaktu, $idPesanan);

    // Eksekusi query
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'updated_status' => $statusPesanan, 'updated_estimasi_waktu' => $estimasiWaktu]);
    } else {
        echo json_encode(['status' => 'error']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
}
?>
