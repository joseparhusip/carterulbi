<?php
include "config.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Check if username exists
    $check_user = mysqli_query($koneksi, "SELECT * FROM admin WHERE username='$username'");
    if(mysqli_num_rows($check_user) > 0) {
        // Check if passwords match
        if($new_password === $confirm_password) {
            // Update password
            $hashed_password = md5($new_password);
            $update_query = mysqli_query($koneksi, "UPDATE admin SET password='$hashed_password' WHERE username='$username'");
            
            if($update_query) {
                echo "<script>
                    window.alert('Password berhasil diperbarui!');
                    window.location.href='index.php';
                </script>";
            } else {
                echo "<script>
                    window.alert('Gagal memperbarui password!');
                    window.location.href='forgot_password.php';
                </script>";
            }
        } else {
            echo "<script>
                window.alert('Password baru dan konfirmasi password tidak cocok!');
                window.location.href='forgot_password.php';
            </script>";
        }
    } else {
        echo "<script>
            window.alert('Username tidak ditemukan!');
            window.location.href='forgot_password.php';
        </script>";
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
        :root {
            --primary-color: #000000;
            --border-radius: 12px;
            --transition: all 0.3s ease;
        }

        body {
            background-color: #ffffff;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            margin: 0;
            padding: 1rem;
        }

        .reset-container {
            background: #ffffff;
            padding: 3rem;
            border-radius: var(--border-radius);
            width: 100%;
            max-width: 420px;
            position: relative;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }

        .reset-header {
            text-align: center;
            margin-bottom: 2.5rem;
            position: relative;
            padding-bottom: 1.5rem;
        }

        .reset-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: #000000;
            border-radius: 2px;
        }

        .reset-header h2 {
            color: #000000;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0.75rem;
            letter-spacing: -0.5px;
        }

        .reset-header p {
            color: #666666;
            font-size: 0.95rem;
            margin: 0;
        }

        .form-group {
            margin-bottom: 1.75rem;
            position: relative;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #000000;
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            padding-left: 3rem;
            background-color: #ffffff;
            border: 2px solid #e5e7eb;
            border-radius: var(--border-radius);
            font-size: 1rem;
            color: #000000;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: #000000;
            box-shadow: 0 0 0 4px rgba(0, 0, 0, 0.1);
            outline: none;
        }

        .form-control::placeholder {
            color: #999999;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            color: #666666;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            transition: var(--transition);
        }

        .form-control:focus + .input-icon {
            color: #000000;
        }

        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn-custom {
            width: 100%;
            padding: 1rem;
            background: #000000;
            color: #ffffff;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            font-size: 1rem;
            transition: var(--transition);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            text-decoration: none;
        }

        .btn-custom:hover {
            background: #333333;
            transform: translateY(-2px);
        }

        .btn-back {
            width: 100%;
            padding: 1rem;
            background: transparent;
            color: #000000;
            border: 2px solid #000000;
            border-radius: var(--border-radius);
            font-weight: 600;
            font-size: 1rem;
            transition: var(--transition);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            text-decoration: none;
        }

        .btn-back:hover {
            background: rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }

        .btn-back:active,
        .btn-custom:active {
            transform: translateY(0);
        }

        .loading {
            position: relative;
            color: transparent !important;
        }

        .loading::after {
            content: '';
            position: absolute;
            width: 1.25rem;
            height: 1.25rem;
            top: 50%;
            left: 50%;
            margin: -0.625rem 0 0 -0.625rem;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            border-top-color: #ffffff;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 480px) {
            .reset-container {
                padding: 2rem;
            }
            
            .reset-header h2 {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-header">
            <h2>Reset Password</h2>
            <p>Enter your username and new password below</p>
        </div>

        <form method="POST" action="">
            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <div class="input-wrapper">
                    <input type="text" 
                           class="form-control" 
                           id="username" 
                           name="username" 
                           placeholder="Enter your username"
                           required>
                    <i class="fas fa-user input-icon"></i>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="new_password">New Password</label>
                <div class="input-wrapper">
                    <input type="password" 
                           class="form-control" 
                           id="new_password" 
                           name="new_password" 
                           placeholder="Enter new password"
                           required>
                    <i class="fas fa-lock input-icon"></i>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="confirm_password">Confirm Password</label>
                <div class="input-wrapper">
                    <input type="password" 
                           class="form-control" 
                           id="confirm_password" 
                           name="confirm_password" 
                           placeholder="Confirm new password"
                           required>
                    <i class="fas fa-lock input-icon"></i>
                </div>
            </div>

            <div class="btn-group">
                <button type="submit" name="submit" class="btn-custom">
                    <i class="fas fa-key"></i>
                    <span>Reset Password</span>
                </button>

                <a href="index.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Login</span>
                </a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>