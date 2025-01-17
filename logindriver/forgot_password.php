<?php
include "config.php"; // Konfigurasi koneksi database

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Cek apakah username terdaftar di database
    $query = mysqli_query($koneksi, "SELECT * FROM driver WHERE username='$username'");
    
    if (mysqli_num_rows($query) > 0) {
        // Jika username terdaftar, cek apakah password konfirmasi cocok
        if ($new_password === $confirm_password) {
            // Hash password baru dengan md5
            $hashed_password = md5($new_password);
            
            // Update password di database
            $update_query = mysqli_query($koneksi, "UPDATE driver SET password='$hashed_password' WHERE username='$username'");

            if ($update_query) {
                echo "<script>alert('Password berhasil direset.'); window.location.href='index.php';</script>";
            } else {
                echo "<script>alert('Terjadi kesalahan saat mengupdate password.'); window.location.href='forgot_password.php';</script>";
            }
        } else {
            echo "<script>alert('Password dan konfirmasi password tidak cocok.'); window.location.href='forgot_password.php';</script>";
        }
    } else {
        echo "<script>alert('Username tidak terdaftar.'); window.location.href='forgot_password.php';</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f7f9fc;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .reset-password-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .reset-password-container h2 {
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .form-control {
            border-radius: 30px;
        }
        .btn-custom {
            background-color: #ffc107;
            color: white;
            border-radius: 30px;
            padding: 0.5rem 1.5rem;
        }
        .btn-custom:hover {
            background-color: #e0a800;
        }
        .btn-back {
            background-color: transparent;
            border: none;
            color: #007bff;
            margin-right: 10px;
        }
        .btn-back:hover {
            color: #0056b3;
        }
        .btn-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>

<div class="reset-password-container">
    <h2>Reset Password</h2>
    <form action="forgot_password.php" method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="username" name="username" required="required" class="form-control" placeholder="Enter your username">
        </div>
        <div class="mb-3">
            <label for="new_password" class="form-label">New Password</label>
            <input type="password" name="new_password" required="required" class="form-control" placeholder="Enter new password">
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm New Password</label>
            <input type="password" name="confirm_password" required="required" class="form-control" placeholder="Confirm new password">
        </div>
        <div class="btn-container mb-3 text-center">
            <a href="index.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <input type="submit" name="submit" class="btn btn-custom" value="Reset Password">
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
