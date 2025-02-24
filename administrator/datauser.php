

<?php
include "config.php";

// Query untuk mengambil data dari tabel user
$sql = "SELECT id_user, username, password, email, alamat, no_hp, gambar, tanggal_lahir, provinsi, kota, kabupaten, kecamatan, kode_pos, created_at, nama
        FROM user";
$result = $koneksi->query($sql);

// Proses penghapusan user
if (isset($_POST['id_user'])) {
    $id_user = $_POST['id_user'];

    // Query untuk menghapus data user berdasarkan id_user
    $sql_delete = "DELETE FROM user WHERE id_user = ?";
    
    if ($stmt = $koneksi->prepare($sql_delete)) {
        $stmt->bind_param("i", $id_user);

        // Mengeksekusi query penghapusan
        if ($stmt->execute()) {
            header("Location: utama.php?page=datauser"); // Redirect setelah berhasil
            exit();
        } else {
            echo "Terjadi kesalahan saat menghapus data user.";
        }
        $stmt->close();
    } else {
        echo "Query gagal disiapkan.";
    }
}
?>

<section class="content-main">
    <div class="content-header">
        <h2 class="content-title">Data Pengguna</h2>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID User</th>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Alamat</th>
                            <th>No HP</th>
                            <th>Tanggal Lahir</th>
                            <th>Provinsi</th>
                            <th>Kota</th>
                            <th>Kabupaten</th>
                            <th>Kecamatan</th>
                            <th>Kode Pos</th>
                            <th>Tanggal Registrasi</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td>{$row['id_user']}</td>
                                    <td><img src='../profileuser/{$row['gambar']}' alt='Foto Pengguna' width='80'></td>
                                    <td>{$row['nama']}</td>
                                    <td>{$row['username']}</td>
                                    <td>{$row['email']}</td>
                                    <td>{$row['alamat']}</td>
                                    <td>{$row['no_hp']}</td>
                                    <td>{$row['tanggal_lahir']}</td>
                                    <td>{$row['provinsi']}</td>
                                    <td>{$row['kota']}</td>
                                    <td>{$row['kabupaten']}</td>
                                    <td>{$row['kecamatan']}</td>
                                    <td>{$row['kode_pos']}</td>
                                    <td>{$row['created_at']}</td>
                                    <td>
                                        <form method='post' action=''>
                                            <input type='hidden' name='id_user' value='{$row['id_user']}'>
                                            <button type='submit' class='btn btn-danger'>Hapus</button>
                                        </form>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='15' class='text-center'>Tidak ada data pengguna</td></tr>";
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
