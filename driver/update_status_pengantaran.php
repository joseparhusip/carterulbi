<?php
include 'config.php'; // Koneksi ke database

// Periksa apakah data POST diterima
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari POST
    $id_pengantaran = isset($_POST['id_pengantaran']) ? intval($_POST['id_pengantaran']) : 0;
    $status_pengantaran = isset($_POST['status_pengantaran']) ? trim($_POST['status_pengantaran']) : '';

    // Validasi data yang diterima
    if ($id_pengantaran > 0 && !empty($status_pengantaran)) {
        // Update status_pengantaran di tabel pengantaran_orang
        $query = "UPDATE pengantaran_orang SET status_pengantaran = ? WHERE id_pengantaran = ?";
        $stmt = mysqli_prepare($koneksi, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $status_pengantaran, $id_pengantaran);
            $result = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            if ($result) {
                echo "success";
            } else {
                echo "error";
            }
        } else {
            echo "error";
        }
    } else {
        echo "error";
    }
} else {
    echo "error";
}
?>
