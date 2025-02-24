<?php
include "config.php";

// Query untuk mengambil data dari tabel driver
$sql = "SELECT id_driver, nama, username, email, tanggal_lahir, no_sim, kendaraan, plat_nomor, status, lokasi_sekarang, rating, pengalaman_kerja, jumlah_trip, created_at, gambardriver 
        FROM driver";
$result = $koneksi->query($sql);

// Proses penghapusan driver
if (isset($_POST['id_driver'])) {
    $id_driver = $_POST['id_driver'];

    // Query untuk menghapus data driver berdasarkan id_driver
    $sql_delete = "DELETE FROM driver WHERE id_driver = ?";
    
    if ($stmt = $koneksi->prepare($sql_delete)) {
        $stmt->bind_param("i", $id_driver);

        // Mengeksekusi query penghapusan
        if ($stmt->execute()) {
            header("Location: utama.php?page=datadriver"); // Redirect setelah berhasil
            exit();
        } else {
            echo "Terjadi kesalahan saat menghapus data driver.";
        }
        $stmt->close();
    } else {
        echo "Query gagal disiapkan.";
    }
}
?>

<section class="content-main">
    <div class="content-header">
        <h2 class="content-title">Data Driver</h2>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID Driver</th>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Tanggal Lahir</th>
                            <th>No SIM</th>
                            <th>Kendaraan</th>
                            <th>Plat Nomor</th>
                            <th>Status</th>
                            <th>Lokasi Sekarang</th>
                            <th>Rating</th>
                            <th>Pengalaman Kerja</th>
                            <th>Jumlah Trip</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td>{$row['id_driver']}</td>
                                    <td><img src='../gambardriver/{$row['gambardriver']}' alt='Foto Driver' width='80'></td>
                                    <td>{$row['nama']}</td>
                                    <td>{$row['username']}</td>
                                    <td>{$row['email']}</td>
                                    <td>{$row['tanggal_lahir']}</td>
                                    <td>{$row['no_sim']}</td>
                                    <td>{$row['kendaraan']}</td>
                                    <td>{$row['plat_nomor']}</td>
                                    <td>{$row['status']}</td>
                                    <td>{$row['lokasi_sekarang']}</td>
                                    <td>{$row['rating']}</td>
                                    <td>{$row['pengalaman_kerja']}</td>
                                    <td>{$row['jumlah_trip']}</td>
                                    <td>
                                        <form method='post' action=''>
                                            <input type='hidden' name='id_driver' value='{$row['id_driver']}'>
                                            <button type='submit' class='btn btn-danger'>Hapus</button>
                                        </form>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='15' class='text-center'>Tidak ada data driver</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php
$koneksi->close();
?>
