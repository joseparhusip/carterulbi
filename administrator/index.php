<?php 
include "config.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(isset($_POST['submit'])){ 
    $username = mysqli_real_escape_string($koneksi, $_POST['username']); 
    $password = md5($_POST['password']); 
    
    $login = mysqli_query($koneksi,"SELECT * FROM admin WHERE username='$username' AND password='$password'"); 
    $hasil = mysqli_num_rows($login); 
    $r = mysqli_fetch_array($login); 
  
    if($hasil > 0) { 
        session_start(); 
        $_SESSION['username'] = $r['username'];  
        $_SESSION['nama'] = $r['nama']; 
        header('location:utama.php?page=dashboard'); 
    } else { 
        echo "<script>
            window.alert('Username atau Password salah!');
            window.location.href='index.php';
        </script>"; 
    } 
} 
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
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

        .login-container {
            background: #ffffff;
            padding: 3rem;
            border-radius: var(--border-radius);
            width: 100%;
            max-width: 420px;
            position: relative;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
            position: relative;
            padding-bottom: 1.5rem;
        }

        .login-header::after {
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

        .login-header h2 {
            color: #000000;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0.75rem;
            letter-spacing: -0.5px;
        }

        .login-header p {
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

        .forgot-password {
            display: inline-block;
            color: #666666;
            text-decoration: none;
            font-size: 0.875rem;
            transition: var(--transition);
            font-weight: 500;
        }

        .forgot-password:hover {
            color: #000000;
            text-decoration: none;
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
            margin-top: 1rem;
        }

        .btn-custom:hover {
            background: #333333;
            transform: translateY(-2px);
        }

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
            .login-container {
                padding: 2rem;
            }
            
            .login-header h2 {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2>Administrator Login</h2>
            <p>Welcome back, please login to your account</p>
        </div>

        <form action="index.php" method="POST" onsubmit="return handleSubmit(event)">
            <div class="form-group">
                <label for="username" class="form-label">Username</label>
                <div class="input-wrapper">
                    <input type="text" 
                           name="username" 
                           id="username"
                           class="form-control" 
                           required 
                           placeholder="Enter your username"
                           autocomplete="username">
                    <i class="fas fa-user input-icon"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="input-wrapper">
                    <input type="password" 
                           name="password" 
                           id="password"
                           class="form-control" 
                           required 
                           placeholder="Enter your password"
                           autocomplete="current-password">
                    <i class="fas fa-lock input-icon"></i>
                </div>
            </div>

            <div class="mb-4 text-end">
                <a href="forgot_password.php" class="forgot-password">
                    Forgot Password?
                </a>
            </div>

            <button type="submit" 
                    name="submit" 
                    class="btn-custom" 
                    id="submitBtn">
                <i class="fas fa-sign-in-alt"></i>
                <span>Login</span>
            </button>
        </form>
    </div>

    <script>
    function handleSubmit(event) {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.classList.add('loading');
        return true;
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>