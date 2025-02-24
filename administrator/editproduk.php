<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Mulai sesi hanya jika belum dimulai
}

include 'config.php';
// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Check if the id_produk is provided via GET request
if (isset($_GET['id_produk'])) {
    $id_produk = $_GET['id_produk'];

    // Query to retrieve product data based on id_produk
    $query = "SELECT * FROM produk_makanan WHERE id_produk = '$id_produk'";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
    } else {
        echo "Product not found.";
        exit;
    }
} else {
    // Redirect or handle the case where id_produk is not provided
    header("Location: index.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_produk = $_POST['id_produk'];
    $id_kategori = $_POST['id_kategori'];
    $nama_produk = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $harga = mysqli_real_escape_string($koneksi, $_POST['harga']);
    $stok = mysqli_real_escape_string($koneksi, $_POST['stok']);
    $gambar = $product['gambar']; // Default to current image

    // Handle file upload
    if ($_FILES['gambar']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "../gambarfood/";
        $target_file = $target_dir . basename($_FILES["gambar"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if file is an actual image
        $check = getimagesize($_FILES["gambar"]["tmp_name"]);
        if ($check !== false) {
            // Check if file already exists
            if (file_exists($target_file)) {
                echo "Sorry, file already exists.";
            } else {
                // Check file size
                if ($_FILES["gambar"]["size"] > 500000) {
                    echo "Sorry, your file is too large.";
                } else {
                    // Allow certain file formats
                    if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                            $gambar = basename($_FILES["gambar"]["name"]);
                        } else {
                            echo "Sorry, there was an error uploading your file.";
                        }
                    } else {
                        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                    }
                }
            }
        } else {
            echo "File is not an image.";
        }
    }

    // Query to update product data
    $update_query = "UPDATE produk_makanan SET 
                        id_kategori = '$id_kategori', 
                        nama_produk = '$nama_produk', 
                        deskripsi = '$deskripsi', 
                        harga = '$harga', 
                        stok = '$stok', 
                        gambar = '$gambar' 
                     WHERE id_produk = '$id_produk'";

    if (mysqli_query($koneksi, $update_query)) {
        header("Location: utama.php?page=produk");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($koneksi);
    }
}

// Query to retrieve all categories for the dropdown
$query_categories = "SELECT id_kategori, nama_kategori FROM kategori_produk";
$result_categories = mysqli_query($koneksi, $query_categories);
?>

<!-- HTML Form for editing product -->
<form action="editproduk.php?id_produk=<?php echo $id_produk; ?>" method="post" enctype="multipart/form-data">
    <section class="content-main">
        <div class="content-header">
            <div>
                <h2 class="content-title card-title">Edit Produk</h2>
            </div>
            <div>
                <a href="utama.php?page=produk" class="btn btn-secondary btn-sm rounded">Kembali</a>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <div class="form-group">
                    <label for="id_kategori">Kategori</label>
                    <select name="id_kategori" id="id_kategori" class="form-control" required>
                        <option value="" disabled>Select category</option>
                        <?php
                        // Fetch and display each category as an option
                        while ($category = mysqli_fetch_assoc($result_categories)) {
                            $selected = ($category['id_kategori'] == $product['id_kategori']) ? 'selected' : '';
                            echo "<option value='{$category['id_kategori']}' $selected>{$category['nama_kategori']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="nama_produk">Nama Produk</label>
                    <input type="text" name="nama_produk" id="nama_produk" class="form-control" value="<?php echo htmlspecialchars($product['nama_produk']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control" required><?php echo htmlspecialchars($product['deskripsi']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="harga">Harga</label>
                    <input type="number" name="harga" id="harga" class="form-control" value="<?php echo htmlspecialchars($product['harga']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="stok">Stok</label>
                    <input type="number" name="stok" id="stok" class="form-control" value="<?php echo htmlspecialchars($product['stok']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="gambar">Gambar Produk</label>
                    <input type="file" name="gambar" id="gambar" class="form-control" accept="image/*">
                    <img src="../gambarfood/<?php echo $product['gambar']; ?>" alt="Product Image" style="max-width: 100px; margin-top: 10px;">
                </div>
                <input type="hidden" name="id_produk" value="<?php echo $product['id_produk']; ?>">
                <div class="form-group text-end">
                    <button type="submit" class="btn btn-primary btn-sm rounded">Update Product</button>
                </div>
            </div>
        </div>
    </section>
</form>
