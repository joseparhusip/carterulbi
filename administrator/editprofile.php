<?php
include "config.php";

$admin_data = array();

if (isset($_GET['id_admin'])) {
    $id_admin = $_GET['id_admin'];
    $sql = "SELECT id_admin, username, nama, email, password, gambar FROM admin WHERE id_admin = ?";
    if ($stmt = mysqli_prepare($koneksi, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id_admin);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, 
            $admin_data['id_admin'],
            $admin_data['username'],
            $admin_data['nama'],
            $admin_data['email'],
            $admin_data['password'],
            $admin_data['gambar']
        );
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_admin = $_POST['id_admin'];
    $username = $_POST['username'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $gambar = $admin_data['gambar'];

    if (!empty($_FILES['gambar']['name'])) {
        $new_gambar = $_FILES['gambar']['name'];
        $target_dir = "../gambaradmin/";
        $target_file = $target_dir . basename($new_gambar);
        
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
            if (!empty($admin_data['gambar']) && file_exists("../gambaradmin/" . $admin_data['gambar'])) {
                unlink("../gambaradmin/" . $admin_data['gambar']);
            }
            $gambar = $new_gambar;
        } else {
            echo "Gagal mengunggah gambar.";
        }
    }

    $sql_update = "UPDATE admin SET username = ?, nama = ?, email = ?, password = ?, gambar = ? WHERE id_admin = ?";
    if ($stmt = mysqli_prepare($koneksi, $sql_update)) {
        mysqli_stmt_bind_param($stmt, "sssssi", $username, $nama, $email, $password, $gambar, $id_admin);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: utama.php?page=profile");
            exit();
        } else {
            echo "Gagal memperbarui data: " . mysqli_error($koneksi);
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .edit-profile-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background: linear-gradient(145deg, #ffffff, #f0f0f0);
            border-radius: 15px;
            box-shadow: 10px 10px 20px rgba(0, 0, 0, 0.1), -10px -10px 20px rgba(255, 255, 255, 0.9);
        }
        .edit-profile-container h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.8rem;
            color: #333;
            font-weight: bold;
        }
        .form-control:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
        }
        .btn-save {
            background-color: #ffc107;
            border: none;
            color: #000;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn-save:hover {
            background-color: #e0a800;
            transform: scale(1.05);
        }
        .profile-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: block;
            border: 3px solid #ffc107;
        }
        .file-upload {
            text-align: center;
            margin-bottom: 20px;
        }
        .file-upload label {
            background-color: #ffc107;
            color: #000;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .file-upload label:hover {
            background-color: #e0a800;
        }
        .file-upload input[type="file"] {
            display: none;
        }
    </style>
</head>
<body>
    <div class="edit-profile-container">
        <h2>Edit Profile Admin</h2>
        <form method="post" action="" enctype="multipart/form-data">
            <input type="hidden" name="id_admin" value="<?php echo htmlspecialchars($admin_data['id_admin']); ?>">

            <div class="file-upload">
                <img src="../gambaradmin/<?php echo htmlspecialchars($admin_data['gambar']); ?>" alt="Foto Admin" class="profile-image" id="previewImage">
                <label for="gambar"><i class="fas fa-upload"></i> Pilih Gambar Baru</label>
                <input type="file" id="gambar" name="gambar" accept="image/*" onchange="previewFile()">
            </div>

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($admin_data['username']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($admin_data['nama']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($admin_data['email']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" value="<?php echo htmlspecialchars($admin_data['password']); ?>" required>
            </div>

            <button type="submit" class="btn btn-save w-100">Simpan Perubahan</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewFile() {
            const preview = document.getElementById('previewImage');
            const file = document.querySelector('input[type=file]').files[0];
            const reader = new FileReader();

            reader.addEventListener("load", function() {
                preview.src = reader.result;
            }, false);

            if (file) {
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>
<?php
mysqli_close($koneksi);
?>