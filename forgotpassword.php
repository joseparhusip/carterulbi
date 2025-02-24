<?php
include 'config.php'; // Include the database configuration file
session_start(); // Start the session

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $old_password = $_POST['password'];
    $new_password = $_POST['newpassword'];

    // Prepare and execute the SQL statement to fetch user data
    $stmt = $koneksi->prepare("SELECT password FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($stored_password);
    $stmt->fetch();
    $stmt->close();

    // Check if a user was found
    if ($stored_password) {
        // Verify the old password using MD5
        if (md5($old_password) === $stored_password) {
            // Old password is correct, update the new password
            $new_password_hashed = md5($new_password);
            $update_stmt = $koneksi->prepare("UPDATE user SET password = ? WHERE username = ?");
            $update_stmt->bind_param("ss", $new_password_hashed, $username);
            if ($update_stmt->execute()) {
                $success = "Password berhasil diperbarui!";
                $update_stmt->close();
                // Redirect to login.php after successful password update
                header("Location: login.php");
                exit();
            } else {
                $error = "Terjadi kesalahan saat memperbarui password!";
            }
            $update_stmt->close();
        } else {
            $error = "Password lama tidak cocok!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>
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
        .message {
            text-align: center;
            margin-bottom: 15px;
        }
        .message.error {
            color: red;
        }
        .message.success {
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-form">
            <h2>Lupa Password</h2>
            <form action="#" method="post">
                <div class="form-container">
                    <div class="login-inputs">
                        <input type="text" name="username" placeholder="Username" required>
                        <input type="password" name="password" placeholder="Password Lama" required>
                        <input type="password" name="newpassword" placeholder="Password Baru" required>
                        <input type="submit" value="Reset Password">
                    </div>
                </div>
                <div class="message <?php if (isset($error)) echo 'error'; else if (isset($success)) echo 'success'; ?>">
                    <?php 
                    if (isset($error)) echo $error;
                    else if (isset($success)) echo $success;
                    ?>
                </div>
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