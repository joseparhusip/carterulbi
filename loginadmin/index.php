<?php 
// session_start(); 
include "config.php";

if(isset($_POST['submit'])){ 
$username = $_POST['username']; 
$password = md5($_POST['password']); 
$login = mysqli_query($koneksi,"SELECT * FROM admin WHERE username='$username' AND password ='$password'"); 
    $hasil = mysqli_num_rows($login); 
    $r     = mysqli_fetch_array($login); 
  
    if($hasil > 0) { 
        session_start(); 
        $_SESSION['username'] = $r['username'];  
        $_SESSION['nama']= $r['nama']; 
        header('location:utama.php?page=dashboard'); 
    } else { 
        echo ("<SCRIPT LANGUAGE='JavaScript'> 
            window.alert('Username atau Password SALAH!'); 
window.location.href='index.php' 
            </SCRIPT>"); 
    } 
} 
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 420px;
            transform: scale(0.9);
            transition: transform 0.3s;
        }
        .login-container:hover {
            transform: scale(1);
        }
        .login-container h2 {
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: bold;
            color: #333;
            animation: fadeInDown 1s ease;
        }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .form-control {
            border-radius: 30px;
            padding: 0.6rem 1.5rem;
            transition: box-shadow 0.3s ease;
        }
        .form-control:focus {
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.6);
            outline: none;
        }
        .btn-custom {
            background-color: black;
            color: white;
            border-radius: 30px;
            padding: 0.6rem 1.5rem;
            width: 100%;
            transition: background-color 0.3s, transform 0.3s;
        }
        .btn-custom:hover {
            background-color: #007bff;
            color: white;
            transform: scale(1.05);
        }
        .login-links {
            display: flex;
            justify-content: space-between;
        }
        .login-links a {
            font-size: 0.9rem;
            text-decoration: none;
            color: #007bff;
            transition: color 0.3s;
        }
        .login-links a:hover {
            color: #0056b3;
        }
        .form-check {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .form-check-input:checked {
            background-color: #007bff;
            border-color: #007bff;
        }

        /* Sign up link styling */
        .text-center p a {
            color: #007bff;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .text-center p a:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>


<div class="login-container">
    <h2>Login Administrator</h2>
    <form action="index.php" method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" required="required" class="form-control" placeholder="Enter your username">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" required="required" class="form-control" placeholder="Enter your password">
        </div>
        <div class="mb-3 d-flex justify-content-end">
            <a href="forgot_password.php" class="font-sm text-muted">Forget Password?</a>
        </div>
        <div class="mb-3 text-center">
            <input type="submit" name="submit" class="btn btn-custom" value="Login">
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
