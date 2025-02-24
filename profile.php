<?php            
include 'config.php';            
    
// Initialize variables            
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';            
$id_user = null;
    
// Fetch user data based on username            
if (!empty($username)) {            
    // First, get the id_user based on the username      
    $query = "SELECT id_user FROM user WHERE username = ?";            
    $stmt = $koneksi->prepare($query);            
    $stmt->bind_param("s", $username);            
    $stmt->execute();            
    $stmt->bind_result($id_user);
    $stmt->fetch();            
    $stmt->close();            
    
    // Now fetch the full user data based on id_user      
    if ($id_user) {      
        $query = "SELECT id_user, username, nama, password, email, alamat, no_hp, gambar, tanggal_lahir, provinsi, kota, kabupaten, kecamatan, kode_pos FROM user WHERE id_user = ?";            
        $stmt = $koneksi->prepare($query);            
        $stmt->bind_param("i", $id_user);            
        $stmt->execute();            
        $stmt->bind_result($db_id_user, $db_username, $db_nama, $db_password, $db_email, $db_alamat, $db_no_hp, $db_gambar, $db_tanggal_lahir, $db_provinsi, $db_kota, $db_kabupaten, $db_kecamatan, $db_kode_pos);
        $stmt->fetch();            
        $stmt->close();            
    }      
}            
    
// Handle form submission            
if ($_SERVER['REQUEST_METHOD'] === 'POST') {            
    $email = $_POST['email'];            
    $alamat = $_POST['alamat'];            
    $no_hp = $_POST['no_hp'];            
    $tanggal_lahir = $_POST['tanggal-lahir'];            
    $provinsi = $_POST['provinsi'];            
    $kota = $_POST['kota'];            
    $kabupaten = $_POST['kabupaten'];            
    $kecamatan = $_POST['kecamatan'];            
    $kode_pos = $_POST['kode_pos'];            
    
    // Initialize $gambar with existing image
    $gambar = $db_gambar;

    // Handle file upload if a file is selected            
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {            
        $file_tmp = $_FILES['gambar']['tmp_name'];
        $file_name = $_FILES['gambar']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Generate unique filename
        $new_file_name = uniqid() . '.' . $file_ext;
        $target_dir = "profileuser/";            
        $target_file = $target_dir . $new_file_name;
        
        // Allowed file extensions
        $allowed_ext = array("jpg", "jpeg", "png", "gif");
        
        if (in_array($file_ext, $allowed_ext)) {
            // Check file size (5MB max)
            if ($_FILES['gambar']['size'] <= 5000000) {
                // Remove old file if exists
                if (!empty($db_gambar) && file_exists($target_dir . $db_gambar)) {
                    unlink($target_dir . $db_gambar);
                }
                
                // Upload new file
                if (move_uploaded_file($file_tmp, $target_file)) {
                    $gambar = $new_file_name;
                } else {
                    echo "<script>alert('Gagal mengupload file.');</script>";
                }
            } else {
                echo "<script>alert('Ukuran file terlalu besar. Maksimal 5MB.');</script>";
            }
        } else {
            echo "<script>alert('Format file tidak didukung. Gunakan JPG, JPEG, PNG, atau GIF.');</script>";
        }
    }
    
    // Prepare and bind            
    $query = "UPDATE user SET email = ?, alamat = ?, no_hp = ?, gambar = ?, tanggal_lahir = ?, provinsi = ?, kota = ?, kabupaten = ?, kecamatan = ?, kode_pos = ? WHERE id_user = ?";            
    $stmt = $koneksi->prepare($query);            
    $stmt->bind_param("ssssssssssi", $email, $alamat, $no_hp, $gambar, $tanggal_lahir, $provinsi, $kota, $kabupaten, $kecamatan, $kode_pos, $id_user);            
    
    // Execute the statement            
    if ($stmt->execute()) {            
        echo "<script>alert('Data berhasil diupdate!'); window.location.href=window.location.href;</script>";           
    } else {            
        echo "<script>alert('Error updating record: " . $stmt->error . "');</script>";            
    }            
    
    $stmt->close();            
}            
?>            
    
<!DOCTYPE html>            
<html lang="id">            
<head>            
    <meta charset="UTF-8">            
    <meta name="viewport" content="width=device-width, initial-scale=1.0">            
    <title>Form Pendaftaran</title>            
    <style>            
        .container1 {            
            background-color: white;            
            max-width: 800px;            
            margin: 20px auto;            
            border-radius: 15px;            
            padding: 20px;            
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);            
        }            
    
        .profile-picture {            
            display: flex;            
            flex-direction: column;            
            align-items: center;            
            margin-bottom: 20px;            
        }            
    
        .profile-picture img {            
            width: 100px;            
            height: 100px;            
            border-radius: 50%;            
            object-fit: cover;            
            border: 2px solid #6f8df7;
        }            
    
        .profile-picture .upload-btn-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            margin-top: 10px;
        }

        .profile-picture .btn {            
            padding: 10px 20px;            
            border: none;            
            background-color: #6f8df7;            
            color: white;            
            border-radius: 5px;            
            cursor: pointer;            
        }            
    
        .profile-picture .btn:hover {            
            background-color: #5a76d1;            
        }

        .profile-picture .upload-btn-wrapper input[type=file] {
            font-size: 100px;
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
        }
    
        .form-group {            
            display: grid;            
            grid-template-columns: repeat(2, 1fr);            
            gap: 20px;            
        }            
    
        .form-group label {            
            display: block;            
            margin-bottom: 5px;            
            font-weight: bold;            
        }            
    
        .form-group input {            
            width: 100%;            
            padding: 10px;            
            margin-bottom: 15px;            
            border: 1px solid #ddd;            
            border-radius: 5px;            
            font-size: 14px;            
        }            
    
        .form-group input:focus {            
            outline: none;            
            border-color: #6f8df7;            
        }            
    
        @media (max-width: 768px) {            
            .form-group {            
                grid-template-columns: 1fr;            
            }            
        }            
    </style>            
</head>            
<body>            
    
<div class="container1">            
    <form method="post" enctype="multipart/form-data">
        <div class="profile-picture">            
            <img src="<?php echo isset($db_gambar) && !empty($db_gambar) ? 'profileuser/' . $db_gambar : 'https://via.placeholder.com/100'; ?>" alt="Profile Picture" id="preview-image">
            <div class="upload-btn-wrapper">
                <button type="button" class="btn">Tambahkan foto*</button>
                <input type="file" name="gambar" id="gambar" accept="image/*" onchange="previewImage(this)"/>
            </div>
        </div>            
    
        <div class="form-group">            
            <div>            
                <label for="id_user">ID User</label>            
                <input type="text" id="id_user" name="id_user" value="<?php echo $db_id_user ?? ''; ?>" readonly>            
            </div>            
            <div>            
                <label for="nama">Nama</label>            
                <input type="text" id="nama" name="nama" value="<?php echo $db_nama ?? ''; ?>" readonly>            
            </div>            
            <div>            
                <label for="username">Username</label>            
                <input type="text" id="username" name="username" value="<?php echo $db_username ?? ''; ?>" readonly>            
            </div>            
            <div>            
                <label for="password">Password</label>            
                <input type="password" id="password" name="password" value="<?php echo $db_password ?? ''; ?>" readonly>            
            </div>            
            <div>            
                <label for="email">Email</label>            
                <input type="email" id="email" name="email" value="<?php echo $db_email ?? ''; ?>">            
            </div>            
            <div>            
                <label for="alamat">Alamat</label>            
                <input type="text" id="alamat" name="alamat" value="<?php echo $db_alamat ?? ''; ?>">            
            </div>            
            <div>            
                <label for="no_hp">No HP</label>            
                <input type="text" id="no_hp" name="no_hp" value="<?php echo $db_no_hp ?? ''; ?>">            
            </div>            
            <div>            
                <label for="tanggal-lahir">Tanggal Lahir</label>            
                <input type="date" id="tanggal-lahir" name="tanggal-lahir" value="<?php echo $db_tanggal_lahir ?? ''; ?>">            
            </div>            
            <div>            
                <label for="provinsi">Provinsi</label>            
                <input type="text" id="provinsi" name="provinsi" value="<?php echo $db_provinsi ?? ''; ?>">            
            </div>            
            <div>            
                <label for="kota">Kota</label>            
                <input type="text" id="kota" name="kota" value="<?php echo $db_kota ?? ''; ?>">            
            </div>            
            <div>            
                <label for="kabupaten">Kabupaten</label>            
                <input type="text" id="kabupaten" name="kabupaten" value="<?php echo $db_kabupaten ?? ''; ?>">            
            </div>            
            <div>            
                <label for="kecamatan">Kecamatan</label>            
                <input type="text" id="kecamatan" name="kecamatan" value="<?php echo $db_kecamatan ?? ''; ?>">            
            </div>            
            <div>            
                <label for="kode_pos">Kode Pos</label>            
                <input type="text" id="kode_pos" name="kode_pos" value="<?php echo $db_kode_pos ?? ''; ?>">            
            </div>            
            <div>            
                <button type="submit" name="submit" class="btn">Simpan</button>            
            </div>            
        </div>            
    </form>            
</div>   

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            document.getElementById('preview-image').src = e.target.result;
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
         
</body>            
</html>