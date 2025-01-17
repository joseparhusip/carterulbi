<?php
include 'config.php';

if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    // Validasi username unik
    $check_user = mysqli_query($koneksi, "SELECT * FROM driver WHERE username='$username'");
    if (mysqli_num_rows($check_user) > 0) {
        echo ("<SCRIPT LANGUAGE='JavaScript'>
            window.alert('Username sudah digunakan!');
            window.location.href='signup.php';
            </SCRIPT>");
    } else {
        // Insert data baru
        $query = "INSERT INTO driver (nama, username, password) VALUES ('$nama', '$username', '$password')";
        if (mysqli_query($koneksi, $query)) {
            echo ("<SCRIPT LANGUAGE='JavaScript'>
                window.alert('Registrasi Berhasil! Silakan Login.');
                window.location.href='index.php';
                </SCRIPT>");
        } else {
            echo "Error: " . $query . "<br>" . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Driver</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e0e4eb9c;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }
        .register-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 420px;
        }
        .register-container h2 {
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: bold;
            color: #333;
        }
        .form-control {
            border-radius: 30px;
            padding: 0.6rem 1.5rem;
        }
        .btn-custom {
            background-color: black;
            color: white;
            border-radius: 30px;
            padding: 0.6rem 1.5rem;
            width: 100%;
        }
        .btn-custom:hover {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>

<div class="register-container">
    <h2>Sign Up</h2>
    <form action="signup.php" method="POST">
        <div class="mb-3">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" name="nama" required="required" class="form-control" placeholder="Enter your name">
        </div>
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" required="required" class="form-control" placeholder="Enter your username">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" required="required" class="form-control" placeholder="Enter your password">
        </div>
        <div class="mb-3 text-center">
            <input type="submit" name="submit" class="btn btn-custom" value="Sign Up">
        </div>
        <div class="mb-3 text-center">
            <a href="index.php" class="text-primary">Already have an account? Login</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
