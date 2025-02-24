<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'config.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Cek apakah ada parameter id
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>
            alert('ID tidak ditemukan!');
            window.location.href = 'utama.php?page=product_images';
          </script>";
    exit();
}

$id = $_GET['id'];

// Ambil data gambar produk berdasarkan id
$query_image = "SELECT pi.*, pm.nama_produk 
                FROM product_images pi
                JOIN produk_makanan pm ON pi.product_id = pm.id_produk
                WHERE pi.id = '$id'";
$result_image = mysqli_query($koneksi, $query_image);

if (mysqli_num_rows($result_image) == 0) {
    echo "<script>
            alert('Data tidak ditemukan!');
            window.location.href = 'utama.php?page=product_images';
          </script>";
    exit();
}

$data_image = mysqli_fetch_assoc($result_image);

// Query untuk mendapatkan data produk makanan untuk dropdown
$query_produk = "SELECT id_produk, nama_produk FROM produk_makanan";
$result_produk = mysqli_query($koneksi, $query_produk);

// Proses update data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $old_image = $data_image['image_path'];
    
    // Cek apakah ada file gambar baru yang diupload
    if ($_FILES["image_path"]["size"] > 0) {
        $target_dir = "../gambarfood/";
        $image_path = basename($_FILES["image_path"]["name"]);
        $target_file = $target_dir . $image_path;
        
        // Upload file baru
        if (move_uploaded_file($_FILES["image_path"]["tmp_name"], $target_file)) {
            // Hapus gambar lama jika ada dan bukan gambar default
            if (!empty($old_image) && file_exists($target_dir . $old_image)) {
                unlink($target_dir . $old_image);
            }
        } else {
            echo "<script>alert('Maaf, terjadi kesalahan saat mengupload gambar baru.');</script>";
            $image_path = $old_image; // Tetap gunakan gambar lama jika upload gagal
        }
    } else {
        $image_path = $old_image; // Tetap gunakan gambar lama jika tidak ada upload baru
    }
    
    // Update data di database
    $query_update = "UPDATE product_images 
                    SET product_id = '$product_id', image_path = '$image_path'
                    WHERE id = '$id'";
    
    if (mysqli_query($koneksi, $query_update)) {
        echo "<script>
                alert('Data berhasil diperbarui!');
                window.location.href = 'utama.php?page=product_images';
              </script>";
        exit();
    } else {
        echo "<script>alert('Error: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Gambar Produk</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .form-header {
            background-color: #f8f9fa;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-bottom: 1px solid #dee2e6;
        }
        .form-header h2 {
            color: #2c3e50;
            font-weight: 600;
            margin: 0;
        }
        .form-header i {
            font-size: 2rem;
            color: #3498db;
            margin-bottom: 1rem;
        }
        .form-section {
            background-color: #ffffff;
            padding: 2rem;
            margin-bottom: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }
        .section-title {
            color: #2c3e50;
            font-size: 1.25rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #f8f9fa;
        }
        .form-label {
            font-weight: 500;
            color: #2c3e50;
        }
        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        .btn-submit {
            padding: 0.8rem 2rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .preview-image {
            max-width: 300px;
            margin-top: 1rem;
        }
        .input-group-text {
            background-color: #f8f9fa;
            color: #2c3e50;
        }
        .form-text {
            color: #6c757d;
            font-size: 0.875rem;
        }
        .action-buttons {
            background-color: #ffffff;
            padding: 1rem;
            position: sticky;
            bottom: 0;
            box-shadow: 0 -5px 15px rgba(0, 0, 0, 0.05);
        }
        .current-image {
            border: 1px solid #dee2e6;
            padding: 0.5rem;
            border-radius: 0.25rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="bg-light">
    <div class="form-header text-center">
        <div class="container">
            <i class="fas fa-edit mb-3"></i>
            <h2>Edit Gambar Produk</h2>
            <p class="text-muted mb-0">Ubah informasi dan gambar produk</p>
        </div>
    </div>

    <div class="container-fluid px-4">
        <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
            <div class="row">
                <div class="col-lg-6 mx-auto">
                    <!-- Informasi Gambar Produk -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-image me-2"></i>Informasi Gambar Produk
                        </h3>
                        <div class="mb-4">
                            <label for="product_id" class="form-label">Produk</label>
                            <select class="form-select" name="product_id" required>
                                <?php 
                                mysqli_data_seek($result_produk, 0); // Reset pointer
                                while ($row = mysqli_fetch_assoc($result_produk)) { 
                                    $selected = ($row['id_produk'] == $data_image['product_id']) ? 'selected' : '';
                                ?>
                                    <option value="<?php echo $row['id_produk']; ?>" <?php echo $selected; ?>>
                                        <?php echo $row['nama_produk']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <div class="invalid-feedback">
                                Silahkan pilih produk
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Gambar Saat Ini</label>
                            <div class="current-image text-center">
                                <img src="../gambarfood/<?php echo $data_image['image_path']; ?>" class="img-fluid" style="max-height: 200px;" alt="Current Image">
                                <div class="mt-2 text-muted"><?php echo $data_image['image_path']; ?></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image_path" class="form-label">Upload Gambar Baru (Opsional)</label>
                            <input type="file" class="form-control" name="image_path" id="image_path" accept="image/*">
                            <div class="form-text">
                                Format yang didukung: JPG, PNG, GIF. Maksimal 2MB. Biarkan kosong jika tidak ingin mengubah gambar.
                            </div>
                        </div>
                        <div id="imagePreview" class="preview-image mx-auto d-block"></div>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="action-buttons">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6 offset-md-3">
                            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                <button type="submit" class="btn btn-primary btn-lg btn-submit px-5">
                                    <i class="fas fa-save me-2"></i>Simpan Perubahan
                                </button>
                                <a href="utama.php?page=product_images" class="btn btn-secondary btn-lg px-5">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script>
        // Form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()

        // Image preview
        document.getElementById('image_path').addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview');
            const file = e.target.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded" alt="Preview">`;
            }

            if (file) {
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = ''; // Clear preview if no file selected
            }
        });
    </script>
</body>
</html>