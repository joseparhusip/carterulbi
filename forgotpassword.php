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
    $stmt = $koneksi->prepare("SELECT * FROM user WHERE username = ?");    
    $stmt->bind_param("s", $username);    
    $stmt->execute();    
    $result = $stmt->get_result();    
    
    // Check if a user was found    
    if ($result->num_rows > 0) {    
        $user = $result->fetch_assoc(); // Fetch user data    
        // Verify the old password using MD5    
        if (md5($old_password) === $user['password']) {    
            // Old password is correct, update the new password    
            $new_password_hashed = md5($new_password);    
            $update_stmt = $koneksi->prepare("UPDATE user SET password = ? WHERE username = ?");    
            $update_stmt->bind_param("ss", $new_password_hashed, $username);    
            if ($update_stmt->execute()) {    
                $success = "Password berhasil diperbarui!";    
                // Redirect to login.php after successful password update    
                header("Location: login.php");    
                exit();    
            } else {    
                $error = "Terjadi kesalahan saat memperbarui password!";    
            }    
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
            background-color: #8B0000; /* Mengubah warna latar belakang menjadi merah darah */    
            display: flex;    
            justify-content: center;    
            align-items: center;    
            overflow: hidden; /* Mencegah scroll */    
        }    
        .container {    
            display: flex;    
            width: 100%; /* Memperbesar lebar container menjadi 100% */    
            height: 100%; /* Memastikan tinggi container 100% */    
            background: rgba(255, 255, 255, 0.1);    
            border-radius: 20px; /* Memperbesar radius border */    
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Memperbesar bayangan */    
            justify-content: center; /* Menjaga konten di tengah */    
            align-items: center; /* Menjaga konten di tengah */    
        }    
        .login-form {    
            flex: 1;    
            padding: 30px; /* Mengurangi padding untuk memperbaiki desain */    
            color: #fff;    
            display: flex;    
            flex-direction: column; /* Mengubah arah flex menjadi vertikal */    
            justify-content: center; /* Menengahkan konten secara vertikal */    
            max-width: 400px; /* Atur lebar maksimum form */    
            position: relative; /* Menjaga posisi relatif */    
            z-index: 1; /* Menjaga form di atas */    
        }    
        .login-form h2 {    
            margin-bottom: 20px; /* Mengurangi margin bawah */    
            font-size: 28px; /* Memperbesar ukuran font */    
            text-align: center; /* Menengahkan teks */    
        }    
        .login-inputs {    
            border: 2px solid #feb47b; /* Menambahkan border di sekitar form dengan warna yang cocok */    
            border-radius: 15px; /* Memperbesar radius border */    
            padding: 20px; /* Mengurangi padding di dalam border */    
            margin-bottom: 20px; /* Mengurangi jarak antara form dan lingkaran */    
            background: rgba(255, 255, 255, 0.2); /* Latar belakang form */    
        }    
        .login-form input[type="text"],    
        .login-form input[type="password"] {    
            width: 90%; /* Mempersempit lebar input */    
            padding: 15px; /* Memperbesar padding */    
            margin: 15px 0; /* Memperbesar margin */    
            border: none;    
            border-radius: 10px; /* Memperbesar radius border */    
            background: rgba(255, 255, 255, 0.5); /* Latar belakang input */    
            color: #333; /* Warna teks input */    
            font-size: 18px; /* Memperbesar ukuran font */    
        }    
        .login-form input[type="submit"] {    
            width: 100%;    
            padding: 20px; /* Memperbesar padding */    
            border: none;    
            border-radius: 10px; /* Memperbesar radius border */    
            background: #ff7e5f;    
            color: #fff;    
            font-size: 20px; /* Memperbesar ukuran font */    
            cursor: pointer;    
            transition: background 0.3s; /* Efek transisi saat hover */    
            margin-top: 15px; /* Menambahkan jarak atas */    
        }    
        .login-form input[type="submit"]:hover {    
            background: #feb47b;    
        }    
        .illustration {    
            display: flex;    
            justify-content: center;    
            align-items: center;    
            position: relative;    
            padding: 30px; /* Menambahkan padding untuk ilustrasi */    
            margin-left: 30px; /* Menambahkan jarak antara form dan lingkaran */    
        }    
        .circle {    
            width: 500px; /* Memperbesar ukuran lingkaran */    
            height: 500px; /* Memperbesar ukuran lingkaran */    
            border-radius: 50%;    
            background: #fff; /* Mengubah warna latar belakang menjadi putih */    
            display: flex;    
            justify-content: center;    
            align-items: center;    
            position: relative;    
        }    
        .illustration img {    
            width: 1000px; /* Memperbesar ukuran gambar motor */    
            max-width: 1000px; /* Memperbesar ukuran maksimum gambar motor */    
            position: absolute; /* Memastikan gambar motor berada di tengah lingkaran */    
            left: 50%;    
            top: 50%;    
            transform: translate(-50%, -50%); /* Menengahkan gambar motor */    
        }    
        .form-container {    
            display: flex;    
            align-items: center;    
            flex-direction: column; /* Mengubah arah flex menjadi kolom */    
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
