<?php 
require_once 'config.php';
session_start();

if(isset($_SESSION['id_driver'])) {
    header('Location: utama.php?page=dashboard');
    exit;
}

if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = md5($_POST['password']);
    
    $query = "SELECT id_driver, username, nama FROM driver WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($koneksi, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        
        $_SESSION['id_driver'] = $data['id_driver'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['nama'] = $data['nama'];
        
        $_SESSION['LAST_ACTIVITY'] = time();
        $_SESSION['EXPIRE_TIME'] = 30 * 60;
        
        header('Location: utama.php?page=dashboard');
        exit;
    } else {
        echo "<script>alert('Username or Password incorrect!'); window.location.href='index.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #000000;
            --secondary-color: #333333;
            --text-color: #000000;
            --border-radius: 8px;
            --transition: all 0.3s ease;
        }

        body {
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Helvetica Neue', Arial, sans-serif;
        }

        .login-container {
            background: #ffffff;
            padding: 2.5rem;
            border-radius: var(--border-radius);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            position: relative;
            border: 1px solid #e0e0e0;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h2 {
            color: var(--text-color);
            font-weight: 600;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: var(--text-color);
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: var(--border-radius);
            padding: 12px 16px;
            padding-left: 40px;
            transition: var(--transition);
            font-size: 1rem;
            height: auto;
            background: #ffffff;
            color: var(--text-color);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
        }

        .form-control::placeholder {
            color: #666666;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #666666;
            transition: var(--transition);
        }

        .form-control:focus + .input-icon {
            color: var(--primary-color);
        }

        .btn-custom {
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            padding: 12px 24px;
            font-weight: 500;
            letter-spacing: 0.5px;
            transition: var(--transition);
            width: 100%;
            margin-top: 1rem;
            position: relative;
            overflow: hidden;
        }

        .btn-custom:hover {
            background: var(--secondary-color);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-custom:active {
            transform: translateY(1px);
        }

        .links-container {
            display: flex;
            justify-content: space-between;
            margin: 1rem 0;
        }

        .auth-link {
            color: var(--text-color);
            text-decoration: none;
            font-size: 0.9rem;
            transition: var(--transition);
            position: relative;
            padding: 2px 0;
        }

        .auth-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 1px;
            bottom: 0;
            left: 0;
            background-color: var(--primary-color);
            transition: var(--transition);
        }

        .auth-link:hover {
            color: var(--primary-color);
        }

        .auth-link:hover::after {
            width: 100%;
        }

        .loading {
            position: relative;
            color: transparent !important;
        }

        .loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin: -10px 0 0 -10px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #ffffff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 576px) {
            .login-container {
                margin: 1rem;
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-header">
        <h2>Driver Login</h2>
        <p class="text-muted">Welcome back</p>
    </div>
    
    <form action="index.php" method="POST" onsubmit="return handleSubmit(event)">
        <div class="form-group">
            <input type="text" name="username" class="form-control" 
                   required placeholder="Username">
            <i class="fas fa-user input-icon"></i>
        </div>

        <div class="form-group">
            <input type="password" name="password" class="form-control" 
                   required placeholder="Password">
            <i class="fas fa-lock input-icon"></i>
        </div>

        <div class="links-container">
            <a href="forgot_password.php" class="auth-link">Forgot Password</a>
            <a href="signup.php" class="auth-link">Sign Up</a>
        </div>

        <button type="submit" name="submit" class="btn btn-custom" id="submitBtn">
            <i class="fas fa-sign-in-alt me-2"></i>Login
        </button>
    </form>

    <div class="social-login">
        <!-- Placeholder for future social login integration -->
    </div>
</div>

<script>
function handleSubmit(event) {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.classList.add('loading');
    return true;
}
</script>

</body>
</html>