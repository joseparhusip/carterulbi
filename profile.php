<?php            
include 'config.php';            
    
// Initialize variables            
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';            
$userData = [];            
$id_user = null; // Initialize id_user      
    
// Fetch user data based on username            
if (!empty($username)) {            
    // First, get the id_user based on the username      
    $query = "SELECT id_user FROM user WHERE username = ?";            
    $stmt = $koneksi->prepare($query);            
    $stmt->bind_param("s", $username);            
    $stmt->execute();            
    $result = $stmt->get_result();            
    $userRow = $result->fetch_assoc();            
    $id_user = $userRow['id_user']; // Get the id_user      
    $stmt->close();            
    
    // Now fetch the full user data based on id_user      
    if ($id_user) {      
        $query = "SELECT id_user, username, nama, password, email, alamat, no_hp, gambar, tanggal_lahir, provinsi, kota, kabupaten, kecamatan, kode_pos FROM user WHERE id_user = ?";            
        $stmt = $koneksi->prepare($query);            
        $stmt->bind_param("i", $id_user);            
        $stmt->execute();            
        $result = $stmt->get_result();            
        $userData = $result->fetch_assoc();            
        $stmt->close();            
    }      
}            
    
// Handle form submission            
if ($_SERVER['REQUEST_METHOD'] === 'POST') {            
    $email = $_POST['email'];            
    $alamat = $_POST['alamat'];            
    $no_hp = $_POST['no_hp'];            
    $gambar = isset($_FILES['gambar']['name']) ? $_FILES['gambar']['name'] : '';            
    $tanggal_lahir = $_POST['tanggal-lahir'];            
    $provinsi = $_POST['provinsi'];            
    $kota = $_POST['kota'];            
    $kabupaten = $_POST['kabupaten'];            
    $kecamatan = $_POST['kecamatan'];            
    $kode_pos = $_POST['kode_pos'];            
    
    // Handle file upload if a file is selected            
    if (!empty($gambar)) {            
        $target_dir = "../profileuser/";            
        $target_file = $target_dir . basename($gambar);            
        $uploadOk = 1;            
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));            
    
        // Check if file is an actual image            
        $check = getimagesize($_FILES["gambar"]["tmp_name"]);            
        if ($check !== false) {            
            $uploadOk = 1;            
        } else {            
            echo "File is not an image.";            
            $uploadOk = 0;            
        }            
            
        // Check if file already exists            
        if (file_exists($target_file)) {            
            echo "Sorry, file already exists.";            
            $uploadOk = 0;            
        }            
            
        // Check file size            
        if ($_FILES["gambar"]["size"] > 500000) {            
            echo "Sorry, your file is too large.";            
            $uploadOk = 0;            
        }            
            
        // Allow certain file formats            
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"            
        && $imageFileType != "gif") {            
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";            
            $uploadOk = 0;            
        }            
            
        // Check if $uploadOk is set to 0 by an error            
        if ($uploadOk == 0) {            
            echo "Sorry, your file was not uploaded.";            
        } else {            
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {            
                echo "The file ". htmlspecialchars( basename( $_FILES["gambar"]["name"])). " has been uploaded.";            
            } else {            
                echo "Sorry, there was an error uploading your file.";            
            }            
        }            
    } else {            
        // If no file is uploaded, use the existing image            
        $gambar = isset($userData['gambar']) ? $userData['gambar'] : '';            
    }            
    
    // Prepare and bind            
    $query = "UPDATE user SET email = ?, alamat = ?, no_hp = ?, gambar = ?, tanggal_lahir = ?, provinsi = ?, kota = ?, kabupaten = ?, kecamatan = ?, kode_pos = ? WHERE id_user = ?";            
    $stmt = $koneksi->prepare($query);            
    $stmt->bind_param("ssssssssssi", $email, $alamat, $no_hp, $gambar, $tanggal_lahir, $provinsi, $kota, $kabupaten, $kecamatan, $kode_pos, $id_user);            
    
    // Execute the statement            
    if ($stmt->execute()) {            
        echo "Record updated successfully";            
    } else {            
        echo "Error updating record: " . $stmt->error;            
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
        }            
    
        .profile-picture button {            
            margin-top: 10px;            
            padding: 10px 20px;            
            border: none;            
            background-color: #6f8df7;            
            color: white;            
            border-radius: 5px;            
            cursor: pointer;            
        }            
    
        .profile-picture button:hover {            
            background-color: #5a76d1;            
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
                grid-template-columns: 1fr; /* Tampil satu kolom pada layar kecil */            
            }            
        }            
    </style>            
</head>            
<body>            
    
<div class="container1">            
    <div class="profile-picture">            
        <img src="<?php echo isset($userData['gambar']) ? 'profileuser/' . $userData['gambar'] : 'https://via.placeholder.com/100'; ?>" alt="Profile Picture">            
        <button>Tambahkan foto*</button>            
    </div>            
    
    <form method="post" enctype="multipart/form-data">            
        <div class="form-group">            
            <div>            
                <label for="id_user">ID User</label>            
                <input type="text" id="id_user" name="id_user" value="<?php echo isset($userData['id_user']) ? $userData['id_user'] : ''; ?>" readonly>            
            </div>            
            <div>            
                <label for="nama">Nama</label>            
                <input type="text" id="nama" name="nama" value="<?php echo isset($userData['nama']) ? $userData['nama'] : ''; ?>" readonly>            
            </div>            
            <div>            
                <label for="username">Username</label>            
                <input type="text" id="username" name="username" value="<?php echo isset($userData['username']) ? $userData['username'] : ''; ?>" readonly>            
            </div>            
            <div>            
                <label for="password">Password</label>            
                <input type="password" id="password" name="password" value="<?php echo isset($userData['password']) ? $userData['password'] : ''; ?>" readonly>            
            </div>            
            <div>            
                <label for="email">Email</label>            
                <input type="email" id="email" name="email" value="<?php echo isset($userData['email']) ? $userData['email'] : ''; ?>">            
            </div>            
            <div>            
                <label for="alamat">Alamat</label>            
                <input type="text" id="alamat" name="alamat" value="<?php echo isset($userData['alamat']) ? $userData['alamat'] : ''; ?>">            
            </div>            
            <div>            
                <label for="no_hp">No HP</label>            
                <input type="text" id="no_hp" name="no_hp" value="<?php echo isset($userData['no_hp']) ? $userData['no_hp'] : ''; ?>">            
            </div>            
            <div>            
                <label for="gambar">Gambar</label>            
                <input type="file" id="gambar" name="gambar">            
            </div>            
            <div>            
                <label for="tanggal-lahir">Tanggal Lahir</label>            
                <input type="date" id="tanggal-lahir" name="tanggal-lahir" value="<?php echo isset($userData['tanggal_lahir']) ? $userData['tanggal_lahir'] : ''; ?>">            
            </div>            
            <div>            
                <label for="provinsi">Provinsi</label>            
                <input type="text" id="provinsi" name="provinsi" value="<?php echo isset($userData['provinsi']) ? $userData['provinsi'] : ''; ?>">            
            </div>            
            <div>            
                <label for="kota">Kota</label>            
                <input type="text" id="kota" name="kota" value="<?php echo isset($userData['kota']) ? $userData['kota'] : ''; ?>">            
            </div>            
            <div>            
                <label for="kabupaten">Kabupaten</label>            
                <input type="text" id="kabupaten" name="kabupaten" value="<?php echo isset($userData['kabupaten']) ? $userData['kabupaten'] : ''; ?>">            
            </div>            
            <div>            
                <label for="kecamatan">Kecamatan</label>            
                <input type="text" id="kecamatan" name="kecamatan" value="<?php echo isset($userData['kecamatan']) ? $userData['kecamatan'] : ''; ?>">            
            </div>            
            <div>            
                <label for="kode_pos">Kode Pos</label>            
                <input type="text" id="kode_pos" name="kode_pos" value="<?php echo isset($userData['kode_pos']) ? $userData['kode_pos'] : ''; ?>">            
            </div>            
            <div>            
                <button type="submit" name="submit">Simpan</button>            
            </div>            
        </div>            
    </form>            
</div>            
</body>            
</html>  
