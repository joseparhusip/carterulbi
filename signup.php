<?php
include 'config.php'; // Include the database configuration file

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $nama = $_POST['nama'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];

    // Cek apakah username sudah ada
    $stmt_check = $koneksi->prepare("SELECT username FROM user WHERE username = ?");
    $stmt_check->bind_param("s", $username);
    $stmt_check->execute();
    $stmt_check->bind_result($existing_username);
    $stmt_check->fetch();
    $stmt_check->close();

    if ($existing_username) {
        // Jika username sudah ada, tampilkan pesan kesalahan
        $error = "Username sudah ada, silahkan buat username baru.";
    } else {
        // Hash the password using MD5 (not recommended for production, use password_hash instead)
        $hashed_password = md5($password);

        // Prepare and execute the SQL statement to insert new user
        $stmt = $koneksi->prepare("INSERT INTO user (username, nama, password, email, no_hp) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $nama, $hashed_password, $email, $no_hp); // Bind parameters
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $_SESSION['username'] = $username;
            header("Location: login.php"); // Redirect to index page
            exit();
        } else {
            $error = "Gagal mendaftar!";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Sign Up</title>
    <style>
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
        .login-form input[type="password"],
        .login-form input[type="email"],
        .login-form input[type="tel"] {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.5);
            color: #333;
            font-size: 16px;
        }
        .login-form input[type="submit"] {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            background: #ff7e5f;
            color: #fff;
            font-size: 18px;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 10px;
        }
        .login-form input[type="submit"]:hover {
            background: #feb47b;
        }
        .illustration {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            padding: 20px;
            margin-left: 20px;
        }
        .circle {
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }
        .illustration img {
            width: 800px;
            max-width: 800px;
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
            text-align: center;
            margin-top: 10px;
        }
        .links a {
            color: #fff;
            text-decoration: none;
            margin: 0 10px;
            font-size: 16px;
        }
        .forgot-register {
            border: 2px solid #feb47b;
            border-radius: 15px;
            padding: 10px;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-form">
            <h2>Sign Up</h2>
            <form action="#" method="post">
                <div class="form-container">
                    <div class="login-inputs">
                        <input type="text" name="username" placeholder="Username" required>
                        <input type="text" name="nama" placeholder="Nama" required>
                        <input type="password" name="password" placeholder="Password" required>
                        <input type="email" name="email" placeholder="Email" required>
                        <input type="tel" name="no_hp" placeholder="WhatsApp Aktif" required>
                        <input type="submit" value="Sign Up">
                    </div>
                </div>
                <div class="forgot-register">
                    <div class="links">
                        <a href="login.php">Already have an account? Login</a>
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