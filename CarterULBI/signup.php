<?php        
include 'config.php'; // Include the database configuration file        
   
  
// Check if the form is submitted        
if ($_SERVER["REQUEST_METHOD"] == "POST") {        
    $username = $_POST['username'];        
    $name = $_POST['name'];        
    $password = $_POST['password'];        
    $email = $_POST['email'];        
  
    // Hash the password using MD5 (not recommended for production, use password_hash instead)  
    $hashed_password = md5($password);  
  
    // Prepare and execute the SQL statement to insert new user        
    $stmt = $koneksi->prepare("INSERT INTO user (username, name, password, email) VALUES (?, ?, ?, ?)");        
    $stmt->bind_param("ssss", $username, $name, $hashed_password, $email); // Bind parameters        
    $stmt->execute();        
  
    if ($stmt->affected_rows > 0) {        
        $_SESSION['username'] = $username;        
        header("Location: index.php"); // Redirect to index page        
        exit();        
    } else {        
        $error = "Gagal mendaftar!";        
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
            background-color: #8B0000; /* Mengubah warna latar belakang menjadi merah darah */                  
            display: flex;                  
            justify-content: center;                  
            align-items: center;                  
        }                  
        .container {                  
            display: flex;                  
            width: 90%; /* Memperbesar lebar container */                  
            max-width: 1200px; /* Memperbesar ukuran maksimum */                  
            background: rgba(255, 255, 255, 0.1);                  
            border-radius: 20px; /* Memperbesar radius border */                  
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Memperbesar bayangan */                  
            overflow: hidden;                  
        }                  
        .login-form {                  
            flex: 1;                  
            padding: 60px; /* Memperbesar padding untuk memperbaiki desain */                  
            color: #fff;                  
            display: flex;                  
            flex-direction: column; /* Mengubah arah flex menjadi vertikal */                  
            justify-content: center; /* Menengahkan konten secara vertikal */                  
        }                  
        .login-form h2 {                  
            margin-bottom: 30px; /* Memperbesar margin bawah */                  
            font-size: 32px; /* Memperbesar ukuran font */                  
            text-align: center; /* Menengahkan teks */                  
        }                  
        .login-inputs {                  
            border: 2px solid #feb47b; /* Menambahkan border di sekitar form dengan warna yang cocok */                  
            border-radius: 15px; /* Memperbesar radius border */                  
            padding: 30px; /* Menambahkan padding di dalam border */                  
            margin-bottom: 30px; /* Menambahkan jarak antara form dan lingkaran */                  
            background: rgba(255, 255, 255, 0.2); /* Latar belakang form */                  
        }                  
        .login-form input[type="text"],                  
        .login-form input[type="password"],                  
        .login-form input[type="email"] {                  
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
        .links {                  
            text-align: center; /* Menengahkan teks link */                  
            margin-top: 10px; /* Mengurangi jarak atas */                  
        }                  
        .links a {                  
            color: #fff; /* Warna teks link */                  
            text-decoration: none; /* Menghilangkan garis bawah */                  
            margin: 0 10px; /* Mengurangi jarak horizontal antara link */                  
            font-size: 18px; /* Memperbesar ukuran font link */                  
        }                  
        .forgot-register {                  
            border: 2px solid #feb47b; /* Menambahkan border di sekitar link */                  
            border-radius: 15px; /* Memperbesar radius border */                  
            padding: 15px; /* Menambahkan padding di dalam border */                  
            margin-top: 10px; /* Menambahkan jarak atas */                  
            text-align: center; /* Menengahkan teks link */                  
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
                        <input type="text" name="name" placeholder="Nama" required>                  
                        <input type="password" name="password" placeholder="Password" required>                  
                        <input type="email" name="email" placeholder="Email" required>                  
                        <input type="submit" value="Sign Up">                  
                    </div>                  
                </div>                  
                <div class="forgot-register">                  
                    <div class="links">                  
                        <a href="login.php">Already have an account? Login</a>                  
                    </div>                  
                </div>                  
                <?php if (isset($error)) { echo "<p style='color: red; text-align: center;'>$error</p>"; } ?> <!-- Display error message if any -->        
            </form>                  
        </div>                  
        <div class="illustration">                  
            <div class="circle">                  
                <img src="logo/motor.png" alt="Red Motorcycle">                  
            </div>                  
        </div>                  
    </div>                  
</body>                  
</html>                  
