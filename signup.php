<?php                  
include 'config.php'; // Include the database configuration file                  
          
// Check if the form is submitted                  
if ($_SERVER["REQUEST_METHOD"] == "POST") {                  
    $username = $_POST['username'];                  
    $nama = $_POST['nama'];                  
    $password = $_POST['password'];                  
    $email = $_POST['email'];                  
        
    // Cek apakah username sudah ada        
    $stmt_check = $koneksi->prepare("SELECT * FROM user WHERE username = ?");        
    $stmt_check->bind_param("s", $username);        
    $stmt_check->execute();        
    $result_check = $stmt_check->get_result();        
        
    if ($result_check->num_rows > 0) {        
        // Jika username sudah ada, tampilkan pesan kesalahan        
        $error = "Username sudah ada, silahkan buat username baru.";        
    } else {        
        // Hash the password using MD5 (not recommended for production, use password_hash instead)            
        $hashed_password = md5($password);            
        
        // Prepare and execute the SQL statement to insert new user                  
        $stmt = $koneksi->prepare("INSERT INTO user (username, nama, password, email) VALUES (?, ?, ?, ?)");                  
        $stmt->bind_param("ssss", $username, $nama, $hashed_password, $email); // Bind parameters                  
        $stmt->execute();                  
        
        if ($stmt->affected_rows > 0) {                  
            $_SESSION['username'] = $username;                  
            header("Location: index.php"); // Redirect to index page                  
            exit();                  
        } else {                  
            $error = "Gagal mendaftar!";                  
        }                  
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
            max-width: 400px; /* Memperbesar ukuran maksimum form */ /* Atur lebar maksimum form */                           
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
        .login-form input[type="password"],                            
        .login-form input[type="email"] {                            
            width: 90%; /* Mempersempit lebar input */                            
            padding: 10px; /* Mengurangi padding */                            
            margin: 10px 0; /* Mengurangi margin */                            
            border: none;                            
            border-radius: 10px; /* Memperbesar radius border */                            
            background: rgba(255, 255, 255, 0.5); /* Latar belakang input */                            
            color: #333; /* Warna teks input */                            
            font-size: 16px; /* Memperbesar ukuran font */                            
        }                            
        .login-form input[type="submit"] {                            
            width: 100%;                            
            padding: 15px; /* Mengurangi padding */                            
            border: none;                            
            border-radius: 10px; /* Memperbesar radius border */                            
            background: #ff7e5f;                            
            color: #fff;                            
            font-size: 18px; /* Memperbesar ukuran font */                            
            cursor: pointer;                            
            transition: background 0.3s; /* Efek transisi saat hover */                            
            margin-top: 10px; /* Mengurangi jarak atas */                            
        }                            
        .login-form input[type="submit"]:hover {                            
            background: #feb47b;                            
        }                            
        .illustration {                            
            display: flex;                            
            justify-content: center;                            
            align-items: center;                            
            position: relative;                            
            padding: 20px; /* Mengurangi padding untuk ilustrasi */                            
            margin-left: 20px; /* Mengurangi jarak antara form dan lingkaran */                            
        }                            
        .circle {                            
            width: 400px; /* Memperbesar ukuran lingkaran */                            
            height: 400px; /* Memperbesar ukuran lingkaran */                            
            border-radius: 50%;                            
            background: #fff; /* Mengubah warna latar belakang menjadi putih */                            
            display: flex;                            
            justify-content: center;                            
            align-items: center;                            
            position: relative;                            
        }                            
        .illustration img {                            
            width: 800px; /* Memperbesar ukuran gambar motor */                            
            max-width: 800px; /* Memperbesar ukuran maksimum gambar motor */                            
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
            font-size: 16px; /* Memperbesar ukuran font link */                            
        }                            
        .forgot-register {                            
            border: 2px solid #feb47b; /* Menambahkan border di sekitar link */                            
            border-radius: 15px; /* Memperbesar radius border */                            
            padding: 10px; /* Mengurangi padding di dalam border */                            
            margin-top: 10px; /* Mengurangi jarak atas */                            
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
                        <input type="text" name="nama" placeholder="Nama" required>                            
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
                <img src="./CarterULBI/logo/motor.png" alt="Red Motorcycle">                            
            </div>                            
        </div>                            
    </div>                            
</body>                            
</html>                            
