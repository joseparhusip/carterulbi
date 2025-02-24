<?php                
// PHP code remains the same
include 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {                
    $username = $_POST['username'];                
    $password = $_POST['password'];                
      
    $stmt = $koneksi->prepare("SELECT username, password FROM user WHERE username = ?");                
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($db_username, $db_password);
    $stmt->fetch();
      
    if ($db_username && md5($password) === $db_password) {              
        $_SESSION['username'] = $username;                
        header("Location: index.php");               
        exit();                
    } else {              
        $error = "Username atau password salah!";                
    }              
    $stmt->close();
}                
?>                
      
<!DOCTYPE html>                          
<html lang="id">                          
<head>                          
    <meta charset="UTF-8">                          
    <meta name="viewport" content="width=device-width, initial-scale=1.0">                          
    <title>Halaman Login</title>                          
    <style>                          
        /* Original CSS remains the same until media query */
        body, html {                          
            margin: 0;                          
            padding: 0;                          
            height: 100%;                          
            font-family: Arial, sans-serif;                          
            background-color: #8B0000;                         
            display: flex;                          
            justify-content: center;                          
            align-items: center;                          
            overflow: hidden;                         
        }                          
        .container {                          
            display: flex;                          
            width: 100%;                          
            height: 100%;                         
            background: rgba(255, 255, 255, 0.1);                          
            border-radius: 20px;                         
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);                         
            justify-content: center;                         
            align-items: center;                         
        }                          
        .login-form {                          
            flex: 1;                          
            padding: 30px;                         
            color: #fff;                          
            display: flex;                          
            flex-direction: column;                         
            justify-content: center;                         
            max-width: 400px;                          
            position: relative;                           
            z-index: 1;                           
        }                          
        .login-form h2 {                          
            margin-bottom: 20px;                         
            font-size: 28px;                         
            text-align: center;                         
        }                          
        .login-inputs {                          
            border: 2px solid #feb47b;                         
            border-radius: 15px;                         
            padding: 20px;                         
            margin-bottom: 20px;                         
            background: rgba(255, 255, 255, 0.2);                         
        }                          
        .login-form input[type="text"],                          
        .login-form input[type="password"] {                          
            width: 90%;                         
            padding: 15px;                         
            margin: 15px 0;                         
            border: none;                          
            border-radius: 10px;                         
            background: rgba(255, 255, 255, 0.5);                         
            color: #333;                         
            font-size: 18px;                         
        }                          
        .login-form input[type="submit"] {                          
            width: 100%;                          
            padding: 20px;                         
            border: none;                          
            border-radius: 10px;                         
            background: #ff7e5f;                          
            color: #fff;                          
            font-size: 20px;                         
            cursor: pointer;                          
            transition: background 0.3s;                         
            margin-top: 15px;                         
        }                          
        .login-form input[type="submit"]:hover {                          
            background: #feb47b;                          
        }                          
        .illustration {                          
            display: flex;                          
            justify-content: center;                          
            align-items: center;                          
            position: relative;                          
            padding: 30px;                         
            margin-left: 30px;                         
        }                          
        .circle {                          
            width: 500px;                         
            height: 500px;                         
            border-radius: 50%;                          
            background: #fff;                         
            display: flex;                          
            justify-content: center;                          
            align-items: center;                          
            position: relative;                          
        }                          
        .illustration img {                          
            width: 1000px;                         
            max-width: 1000px;                         
            position: absolute;                         
            left: 50%;                          
            top: 50%;                          
            transform: translate(-50%, -50%);                         
        }                          
        .form-container {                          
            display: flex;                          
            align-items: center;                          
            flex-direction: column;                         
        }                          
        .links {                          
            display: flex;                         
            justify-content: center;                         
            margin-top: 10px;                         
        }                          
        .links a {                          
            color: #fff;                         
            text-decoration: none;                         
            margin: 0 10px;                         
            font-size: 18px;                         
            padding: 10px 20px;                         
            border: 2px solid #feb47b;                         
            border-radius: 10px;                         
            transition: background 0.3s, color 0.3s;                         
            display: inline-block;                         
            text-align: center;                         
        }                          
        .links a:hover {                          
            background: #feb47b;                         
            color: #fff;                         
        }

        @media screen and (max-width: 768px) {
            .container {
                justify-content: flex-start;
                overflow-y: auto;
                padding: 10px;
            }
            
            .login-form {
                padding: 15px;
                max-width: 100%;
                min-width: 200px;
            }
            
            .illustration {
                margin-left: 0;
                padding: 10px;
            }
            
            .circle {
                width: 150px;
                height: 150px;
            }
            
            .illustration img {
                width: 300px;
            }
            
            .links {
                flex-direction: column;
                gap: 10px;
            }
            
            .links a {
                width: 100%;
                margin: 5px 0;
                font-size: 16px;
                padding: 8px 15px;
            }
            
            .login-form input[type="text"],
            .login-form input[type="password"] {
                font-size: 16px;
                padding: 12px;
                width: 85%;
            }
            
            .login-form input[type="submit"] {
                padding: 12px;
                font-size: 16px;
            }
            
            .login-form h2 {
                font-size: 22px;
            }

            .login-inputs {
                padding: 15px;
            }
        }                          
    </style>                          
</head>                          
<body>                          
    <div class="container">                          
        <div class="login-form">                          
            <h2>Selamat Datang</h2>                          
            <form action="#" method="post">                          
                <div class="form-container">                          
                    <div class="login-inputs">                          
                        <input type="text" name="username" placeholder="Username" required>                          
                        <input type="password" name="password" placeholder="Password" required>                          
                        <input type="submit" value="Login">                          
                    </div>                          
                </div>                          
                <div class="forgot-register">                          
                    <div class="links">                          
                        <a href="forgotpassword.php">Forgot Password?</a>                          
                        <a href="signup.php">Sign Up</a>                          
                    </div>                        
                </div>                          
                <?php if (isset($error)) { echo "<p style='color: red; text-align: center;'>$error</p>"; } ?>
            </form>                          
        </div>                          
        <div class="illustration">                          
            <div class="circle">                          
                <img src="./CarterULBI/logo/motor.png" alt="Red Motorcycle">                          
            </div>                          
        </div>                          
    </div>                          
</body>                          
</html>