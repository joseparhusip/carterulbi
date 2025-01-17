<?php 
// Sertakan file konfigurasi database
require_once 'config.php';

// Proses login
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Enkripsi password dengan MD5

    // Query untuk mencocokkan data username dan password dari tabel driver
    $query = "SELECT id_driver, username, nama FROM driver WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) > 0) {
        // Jika data cocok
        session_start();
        $data = mysqli_fetch_assoc($result);
        
        // Simpan data ke SESSION
        $_SESSION['id_driver'] = $data['id_driver'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['nama'] = $data['nama'];

        // Redirect ke halaman utama setelah login
        header('Location: utama.php?page=dashboard');
        exit;
    } else {
        // Jika data tidak cocok
        echo "<script>alert('Username atau Password salah!'); window.location.href='index.php';</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Driver</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        .btn-custom {
            background-color: black;
            color: white;
            border-radius: 25px;
            width: 100%;
        }
        .btn-custom:hover {
            background-color: #007bff;
            color: white;
        }
        a {
            text-decoration: none;
            color: #007bff;
        }
        a:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>
<div class="login-container">
    <h2 class="text-center mb-4">Login Driver</h2>
    <form action="index.php" method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required placeholder="Masukkan Username">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required placeholder="Masukkan Password">
        </div>
        <div class="mb-3 d-flex justify-content-between">
            <a href="forgot_password.php">Lupa Password?</a>
            <a href="signup.php" class="text-end">Sign Up</a>
        </div>
        <button type="submit" name="submit" class="btn btn-custom">Login</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
