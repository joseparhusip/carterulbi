<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'config.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    
    $target_dir = "../gambarfood/";
    $image_path = basename($_FILES["image_path"]["name"]);
    $target_file = $target_dir . $image_path;
    
    if (move_uploaded_file($_FILES["image_path"]["tmp_name"], $target_file)) {
        $query = "INSERT INTO product_images (product_id, image_path) 
                  VALUES ('$product_id', '$image_path')";
        
        if (mysqli_query($koneksi, $query)) {
            echo "<script>
                    alert('Gambar produk berhasil ditambahkan!');
                    window.location.href = 'utama.php?page=product_images';
                  </script>";
            exit();
        } else {
            echo "<script>alert('Error: " . mysqli_error($koneksi) . "');</script>";
        }
    } else {
        echo "<script>alert('Maaf, terjadi kesalahan saat mengupload gambar.');</script>";
    }
}

// Query untuk mendapatkan data produk makanan untuk dropdown
$query_produk = "SELECT id_produk, nama_produk FROM produk_makanan";
$result_produk = mysqli_query($koneksi, $query_produk);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Gambar Produk</title>
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
    </style>
</head>
<body class="bg-light">
    <div class="form-header text-center">
        <div class="container">
            <i class="fas fa-images mb-3"></i>
            <h2>Tambah Gambar Produk</h2>
            <p class="text-muted mb-0">Silahkan upload gambar tambahan untuk produk</p>
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
                            <label for="product_id" class="form-label">Pilih Produk</label>
                            <select class="form-select" name="product_id" required>
                                <option value="">Pilih Produk</option>
                                <?php while ($row = mysqli_fetch_assoc($result_produk)) { ?>
                                    <option value="<?php echo $row['id_produk']; ?>">
                                        <?php echo $row['nama_produk']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <div class="invalid-feedback">
                                Silahkan pilih produk
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image_path" class="form-label">Upload Gambar</label>
                            <input type="file" class="form-control" name="image_path" id="image_path" accept="image/*" required>
                            <div class="form-text">
                                Format yang didukung: JPG, PNG, GIF. Maksimal 2MB.
                            </div>
                            <div class="invalid-feedback">
                                Silahkan pilih gambar produk
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
                                    <i class="fas fa-save me-2"></i>Simpan Gambar
                                </button>
                                <button type="reset" class="btn btn-secondary btn-lg px-5">
                                    <i class="fas fa-undo me-2"></i>Reset Form
                                </button>
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
            }
        });
    </script>
</body>
</html>