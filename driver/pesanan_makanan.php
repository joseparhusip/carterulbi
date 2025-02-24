<?php

include 'config.php';

if (!isset($_SESSION['id_driver'])) {
    die("Anda belum login sebagai driver.");
}

$id_driver = $_SESSION['id_driver'];

// Handle POST request for updating estimation time
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_estimasi'])) {
    $id_pesanan = $_POST['id_pesanan'];
    $estimasi_waktu = $_POST['estimasi_waktu'];

    $update_estimasi_query = "UPDATE pesanan_makanan SET estimasi_waktu = ? WHERE id_pesanan = ?";
    $update_estimasi_stmt = mysqli_prepare($koneksi, $update_estimasi_query);

    if (!$update_estimasi_stmt) {
        die("Query gagal: " . mysqli_error($koneksi));
    }

    mysqli_stmt_bind_param($update_estimasi_stmt, "si", $estimasi_waktu, $id_pesanan);

    if (mysqli_stmt_execute($update_estimasi_stmt)) {
        echo "<script>alert('Estimasi waktu berhasil diperbarui.');</script>";
    } else {
        echo "<script>alert('Gagal memperbarui estimasi waktu: " . mysqli_error($koneksi) . "');</script>";
    }

    mysqli_stmt_close($update_estimasi_stmt);
}

// Handle file upload for bukti pengiriman from camera
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_bukti'])) {
    header('Content-Type: application/json'); // Set JSON content type
    
    $id_pesanan = $_POST['id_pesanan'];
    $image_data = $_POST['image_data'];
    
    // Remove the data URL prefix and get only the base64 data
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
    exit;
}

// Handle POST request for updating order status with AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    header('Content-Type: application/json');
    
    $id_pesanan = $_POST['id_pesanan'];
    $new_status = $_POST['status_pesanan'];

    if (!in_array($new_status, ['menunggu', 'diproses', 'dikirim', 'selesai', 'dibatalkan'])) {
        echo json_encode(['error' => 'Status tidak valid']);
        exit;
    }

    $update_status_query = "UPDATE pesanan_makanan SET status_pesanan = ? WHERE id_pesanan = ? AND id_driver = ?";
    $update_status_stmt = mysqli_prepare($koneksi, $update_status_query);

    if (!$update_status_stmt) {
        echo json_encode(['error' => mysqli_error($koneksi)]);
        exit;
    }

    mysqli_stmt_bind_param($update_status_stmt, "sii", $new_status, $id_pesanan, $id_driver);

    if (mysqli_stmt_execute($update_status_stmt)) {
        echo json_encode(['success' => true, 'message' => 'Status berhasil diperbarui']);
    } else {
        echo json_encode(['error' => mysqli_error($koneksi)]);
    }

    mysqli_stmt_close($update_status_stmt);
    exit;
}

// Query untuk mengambil data pesanan
$query = "
    SELECT 
        pm.id_pesanan, 
        u.nama AS nama_user, 
        d.nama AS nama_driver, 
        pm.alamat_pengiriman, 
        pm.total_harga, 
        pm.status_pesanan, 
        pm.catatan, 
        pm.estimasi_waktu, 
        p.nama_produk, 
        pm.harga, 
        pm.ongkir, 
        pm.subtotal, 
        pm.rating, 
        pm.bukti_pembayaran,
        pm.bukti_pengiriman, 
        mp.nama_metode,
        pm.status_pembayaran
    FROM 
        pesanan_makanan pm
    INNER JOIN user u ON pm.id_user = u.id_user
    INNER JOIN driver d ON pm.id_driver = d.id_driver
    INNER JOIN produk_makanan p ON pm.id_produk = p.id_produk
    INNER JOIN metode_pembayaran mp ON pm.id_metode_pembayaran = mp.id_metode_pembayaran
    WHERE 
        d.id_driver = ?
    ORDER BY 
        CASE pm.status_pesanan
            WHEN 'menunggu' THEN 1
            WHEN 'diproses' THEN 2
            WHEN 'dikirim' THEN 3
            WHEN 'selesai' THEN 4
            WHEN 'dibatalkan' THEN 5
        END,
        pm.id_pesanan DESC
";

$stmt = mysqli_prepare($koneksi, $query);
if (!$stmt) {
    die("Query gagal: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param($stmt, "i", $id_driver);

if (!mysqli_stmt_execute($stmt)) {
    die("Query gagal: " . mysqli_error($koneksi));
}

$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Makanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .star {
            font-size: 1.2em;
        }

        .star.filled {
            color: gold;
        }

        .star.empty {
            color: lightgray;
        }

        .status-dropdown {
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 120px;
        }

        .status-dropdown option[value="menunggu"] { background-color: #f0ad4e; color: white; }
        .status-dropdown option[value="diproses"] { background-color: #0275d8; color: white; }
        .status-dropdown option[value="dikirim"] { background-color: #5cb85c; color: white; }
        .status-dropdown option[value="selesai"] { background-color: #28a745; color: white; }
        .status-dropdown option[value="dibatalkan"] { background-color: #d9534f; color: white; }

        .action-button {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .action-button:hover {
            background-color: #0056b3;
        }

        .status-pembayaran {
            font-family: 'Courier New', Courier, monospace;
            font-size: 1.1em;
        }

        .estimasi-input {
            width: 200px;
            padding: 4px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .estimasi-form {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .status-dropdown[data-status="menunggu"] { background-color: #f0ad4e; color: white; }
        .status-dropdown[data-status="diproses"] { background-color: #0275d8; color: white; }
        .status-dropdown[data-status="dikirim"] { background-color: #5cb85c; color: white; }
        .status-dropdown[data-status="selesai"] { background-color: #28a745; color: white; }
        .status-dropdown[data-status="dibatalkan"] { background-color: #d9534f; color: white; }

        /* Camera styles */
        .camera-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            z-index: 1000;
        }

        .camera-container {
            position: relative;
            width: 100%;
            max-width: 640px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
        }

        #camera-feed {
            width: 100%;
            max-width: 640px;
            margin: 0 auto;
            display: block;
        }

        .camera-controls {
            margin-top: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <section class="content-main">
        <div class="content-header">
            <h2 class="content-title">Pesanan Makanan</h2>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th>ID Pesanan</th>
                                <th>Nama User</th>
                                <th>Nama Driver</th>
                                <th>Alamat Pengiriman</th>
                                <th>Total Harga</th>
                                <th>Status Pesanan</th>
                                <th>Status Pembayaran</th>
                                <th>Catatan</th>
                                <th>Estimasi Waktu</th>
                                <th>Nama Produk</th>
                                <th>Harga</th>
                                <th>Ongkir</th>
                                <th>Subtotal</th>
                                <th>Rating</th>
                                <th>Bukti Pembayaran</th>
                                <th>Bukti Pengiriman</th>
                                <th>Metode Pembayaran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $full_stars = intval($row['rating']);
                                    $empty_stars = 5 - $full_stars;
                                    $download_link = "buktitf/" . $row['bukti_pembayaran'];
                                    $bukti_pengiriman_link = "../buktipengiriman/" . $row['bukti_pengiriman'];
                                    $is_selesai = $row['status_pesanan'] === 'selesai';
                                    $formatted_estimasi = $row['estimasi_waktu'] ? date('Y-m-d\TH:i', strtotime($row['estimasi_waktu'])) : '';
                            ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="row-checkbox" 
                                               name="selected_rows[]" 
                                               value="<?php echo htmlspecialchars($row['id_pesanan']); ?>"
                                               <?php if ($is_selesai) echo 'disabled'; ?>>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['id_pesanan']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_user']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_driver']); ?></td>
                                    <td><?php echo htmlspecialchars($row['alamat_pengiriman']); ?></td>
                                    <td><?php echo htmlspecialchars(number_format($row['total_harga'], 2)); ?></td>
                                    <td>
                                        <select class="status-dropdown form-select" 
                                                data-id="<?php echo htmlspecialchars($row['id_pesanan']); ?>"
                                                data-status="<?php echo htmlspecialchars($row['status_pesanan']); ?>"
                                                <?php if ($is_selesai) echo 'disabled'; ?>>
                                            <option value="menunggu" <?php if ($row['status_pesanan'] == 'menunggu') echo 'selected'; ?>>Menunggu</option>
                                            <option value="diproses" <?php if ($row['status_pesanan'] == 'diproses') echo 'selected'; ?>>Diproses</option>
                                            <option value="dikirim" <?php if ($row['status_pesanan'] == 'dikirim') echo 'selected'; ?>>Dikirim</option>
                                            <option value="selesai" <?php if ($row['status_pesanan'] == 'selesai') echo 'selected'; ?>>Selesai</option>
                                            <option value="dibatalkan" <?php if ($row['status_pesanan'] == 'dibatalkan') echo 'selected'; ?>>Dibatalkan</option>
                                        </select>
                                    </td>
                                    <td class="<?php echo getStatusPembayaranColor($row['status_pembayaran']); ?> status-pembayaran">
                                        <?php echo htmlspecialchars($row['status_pembayaran']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['catatan']); ?></td>
<td>
                                        <form method="POST" action="" class="estimasi-form">
                                            <input type="hidden" name="id_pesanan" value="<?php echo htmlspecialchars($row['id_pesanan']); ?>">
                                            <input type="hidden" name="update_estimasi" value="1">
                                            <input type="datetime-local" 
                                                   name="estimasi_waktu" 
                                                   value="<?php echo htmlspecialchars($formatted_estimasi); ?>"
                                                   class="form-control estimasi-input"
                                                   <?php if ($is_selesai) echo 'disabled'; ?>>
                                            <?php if (!$is_selesai) { ?>
                                                <button type="submit" class="btn btn-sm btn-primary mt-1">Update</button>
                                            <?php } ?>
                                        </form>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['nama_produk']); ?></td>
                                    <td><?php echo htmlspecialchars(number_format($row['harga'], 2)); ?></td>
                                    <td><?php echo htmlspecialchars(number_format($row['ongkir'], 2)); ?></td>
                                    <td><?php echo htmlspecialchars(number_format($row['subtotal'], 2)); ?></td>
                                    <td>
                                        <?php
                                        for ($i = 0; $i < $full_stars; $i++) {
                                            echo '<span class="star filled">&#9733;</span>';
                                        }
                                        for ($i = 0; $i < $empty_stars; $i++) {
                                            echo '<span class="star empty">&#9734;</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo $download_link; ?>" 
                                           download="<?php echo htmlspecialchars($row['bukti_pembayaran']); ?>">
                                            <?php echo htmlspecialchars($row['bukti_pembayaran']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php if ($row['bukti_pengiriman']) { ?>
                                            <a href="<?php echo $bukti_pengiriman_link; ?>" 
                                               download="<?php echo htmlspecialchars($row['bukti_pengiriman']); ?>">
                                                <?php echo htmlspecialchars($row['bukti_pengiriman']); ?>
                                            </a>
                                        <?php } ?>
                                        <?php if (!$is_selesai) { ?>
                                            <button type="button" class="btn btn-primary btn-sm mt-2 open-camera" 
                                                    data-id="<?php echo htmlspecialchars($row['id_pesanan']); ?>">
                                                Buka Kamera
                                            </button>
                                        <?php } ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['nama_metode']); ?></td>
                                    <td>
                                        <?php if (!$is_selesai) { ?>
                                            <button type="button" 
                                                    class="btn btn-success action-button complete-order"
                                                    data-id="<?php echo htmlspecialchars($row['id_pesanan']); ?>">
                                                Selesai
                                            </button>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php
                                }
                            } else {
                                echo "<tr><td colspan='19' class='text-center'>Data pesanan makanan tidak ditemukan.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Add Camera Modal -->
    <div id="cameraModal" class="camera-modal">
        <div class="camera-container">
            <video id="camera-feed" autoplay playsinline></video>
            <div class="camera-controls">
                <button id="switchCamera" class="btn btn-info">Switch Camera</button>
                <button id="captureImage" class="btn btn-success">Ambil Foto</button>
                <button id="closeCamera" class="btn btn-danger">Tutup</button>
            </div>
            <canvas id="canvas" style="display:none;"></canvas>
        </div>
    </div>

    <!-- Camera JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStream;
    let facingMode = 'environment';
    let currentOrderId;

    async function initializeCamera(orderId) {
        currentOrderId = orderId;
        try {
            const constraints = {
                video: { 
                    facingMode: facingMode,
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            };
            
            if (currentStream) {
                currentStream.getTracks().forEach(track => track.stop());
            }
            
            currentStream = await navigator.mediaDevices.getUserMedia(constraints);
            const videoElement = document.getElementById('camera-feed');
            videoElement.srcObject = currentStream;
            document.getElementById('cameraModal').style.display = 'block';
        } catch (error) {
            console.error('Error accessing camera:', error);
            alert('Error accessing camera: ' + error.message);
        }
    }

    document.querySelectorAll('.open-camera').forEach(button => {
        button.addEventListener('click', function() {
            initializeCamera(this.dataset.id);
        });
    });

    document.getElementById('switchCamera').addEventListener('click', function() {
        facingMode = facingMode === 'environment' ? 'user' : 'environment';
        initializeCamera(currentOrderId);
    });

    document.getElementById('closeCamera').addEventListener('click', function() {
        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
        }
        document.getElementById('cameraModal').style.display = 'none';
    });

    document.getElementById('captureImage').addEventListener('click', async function() {
        try {
            const video = document.getElementById('camera-feed');
            const canvas = document.getElementById('canvas');
            
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            
            const context = canvas.getContext('2d');
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            const imageData = canvas.toDataURL('image/jpeg', 0.8);
            
            if (currentStream) {
                currentStream.getTracks().forEach(track => track.stop());
            }
            
            document.getElementById('cameraModal').style.display = 'none';
            
            const formData = new URLSearchParams();
            formData.append('upload_bukti', '1');
            formData.append('id_pesanan', currentOrderId);
            formData.append('image_data', imageData);
            
            // Send to upload_bukti.php instead of current page
            const response = await fetch('upload_bukti.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData.toString()
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            if (result.success) {
                alert('Bukti pengiriman berhasil diunggah.');
                location.reload();
            } else {
                throw new Error(result.error || 'Gagal mengunggah gambar');
            }
        } catch (error) {
            console.error('Error capturing/uploading image:', error);
            alert('Error: ' + error.message);
        }
    });
});
</script>
    
    <?php
        // Helper function untuk menentukan warna status pembayaran
        function getStatusPembayaranColor($status) {
            switch ($status) {
                case 'PENDING':
                    return 'text-secondary';
                case 'PAID':
                    return 'text-success';
                case 'FAILED':
                    return 'text-danger';
                case 'REFUNDED':
                    return 'text-warning';
                default:
                    return 'text-secondary';
            }
        }
    
        mysqli_stmt_close($stmt);
        mysqli_close($koneksi);
    ?>
    
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Select all checkbox functionality
            document.getElementById('selectAll').addEventListener('change', function() {
                var checkboxes = document.querySelectorAll('.row-checkbox');
                checkboxes.forEach(function(checkbox) {
                    if (!checkbox.disabled) {
                        checkbox.checked = this.checked;
                    }
                }, this);
            });
    
            // Status dropdown change handler
           // Status dropdown change handler with improved error handling
    document.querySelectorAll('.status-dropdown').forEach(function(dropdown) {
        dropdown.addEventListener('change', function() {
            const id_pesanan = this.getAttribute('data-id');
            const new_status = this.value;
            const originalStatus = this.getAttribute('data-status');
            
            // Update the dropdown color based on selection
            this.setAttribute('data-status', new_status);
    
            // Create FormData object
            const formData = new FormData();
            formData.append('update_status', '1');
            formData.append('id_pesanan', id_pesanan);
            formData.append('status_pesanan', new_status);
    
            // Send AJAX request to update status
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.log('Response text:', text);
                        throw new Error('Invalid JSON response');
                    }
                });
            })
            .then(data => {
                if (data.success) {
                    const row = this.closest('tr');
                    
                    // Update status dropdown
                    this.value = new_status;
                    this.setAttribute('data-status', new_status);
    
                    // If status is changed to 'selesai', update UI accordingly
                    if (new_status === 'selesai') {
                        this.disabled = true;
                        const checkbox = row.querySelector('.row-checkbox');
                        const completeButton = row.querySelector('.complete-order');
                        const estimasiInput = row.querySelector('.estimasi-input');
                        const estimasiButton = row.querySelector('.estimasi-form button');
                        
                        if (checkbox) checkbox.disabled = true;
                        if (completeButton) completeButton.style.display = 'none';
                        if (estimasiInput) estimasiInput.disabled = true;
                        if (estimasiButton) estimasiButton.style.display = 'none';
                    }
    
                    alert('Status pesanan berhasil diperbarui');
                } else {
                    throw new Error(data.error || 'Unknown error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Only show error alert if status wasn't actually updated
                const currentStatus = this.value;
                if (currentStatus !== new_status) {
                    alert('Terjadi kesalahan saat memperbarui status: ' + error.message);
                    // Revert the dropdown to its previous value
                    this.value = originalStatus;
                    this.setAttribute('data-status', originalStatus);
                }
            });
        });
    
        // Set initial color of dropdown based on current value
        dropdown.setAttribute('data-status', dropdown.value);
    });
    
            // Complete order button handler
            document.querySelectorAll('.complete-order').forEach(function(button) {
                button.addEventListener('click', function() {
                    const id_pesanan = this.getAttribute('data-id');
                    const row = this.closest('tr');
                    const statusDropdown = row.querySelector('.status-dropdown');
    
                    if (confirm('Apakah Anda yakin ingin menyelesaikan pesanan ini?')) {
                        statusDropdown.value = 'selesai';
                        statusDropdown.dispatchEvent(new Event('change'));
                    }
                });
            });
    
            // Set initial dropdown colors on page load
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.status-dropdown').forEach(function(dropdown) {
                    dropdown.setAttribute('data-status', dropdown.value);
                });
            });
        </script>
    </body>
    </html>