<?php
// Cek apakah session belum dimulai, jika belum baru jalankan session_start()
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['username'])){
    header('location:index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Home</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        /* Sidebar */
        .sidebar {
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background-color: #343a40;
            color: white;
            width: 250px;
            padding-top: 20px;
            transition: 0.3s;
        }

        .sidebar a {
            color: white;
            display: flex;
            align-items: center;
            padding: 10px;
            text-decoration: none;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background-color: #495057;
            text-decoration: none;
        }

        /* Navbar */
        .navbar {
            margin-left: 250px;
            width: calc(100% - 250px);
        }

        /* Content */
        .content {
            margin-left: 250px;
            padding: 20px;
        }

        /* Table styling */
        .table-container {
            background-color: white;
            border-radius: 8px;
            margin-top: 20px;
            padding: 15px;
        }

        /* Button styling */
        .btn-update {
            background-color: #007bff;
            color: white;
            margin-top: 10px;
        }

        /* Responsive table */
        @media (max-width: 768px) {
            th, td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="text-center">Menu Administrator</h4>
        <a href="#dashboard"><span class="emoji">📊</span>Dashboard</a>
        <a href="#reseller" data-toggle="collapse"><span class="emoji">🏪</span>Toko/Reseller</a>
        <div id="reseller" class="collapse">
            <a href="#data_konsumen" class="pl-3"><span class="emoji">👥</span>Data Konsumen</a>
            <a href="#data_reseller" class="pl-3"><span class="emoji">📦</span>Data Reseller</a>
            <a href="page-categories.html" class="pl-3"><span class="emoji">📋</span>Kategori Produk</a>
        </div>
        <a href="#transaksi" data-toggle="collapse"><span class="emoji">💼</span>Transaksi</a>
        <div id="transaksi" class="collapse">
            <a href="#pembelian" class="pl-3"><span class="emoji">🛒</span>Pembelian Ke Konsumen</a>
        </div>
        <a href="#report" data-toggle="collapse"><span class="emoji">📈</span>Report</a>
        <a href="#menu_utama" data-toggle="collapse"><span class="emoji">🌐</span>Menu Utama</a>
        <div id="menu_utama" class="collapse">
            <a href="#identitas_website" class="pl-3"><span class="emoji">🏷️</span>Identitas Website</a>
        </div>
        <a href="#modul_user" data-toggle="collapse"><span class="emoji">👤</span>Modul User</a>
        <a href="#edit_profile" class="pl-3"><span class="emoji">⚙️</span>Edit Profile</a>
        <a href="logout.php"><span class="emoji">🚪</span>Logout</a>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <span class="navbar-brand mx-auto">Administrator</span>
    </nav>

    <!-- Main Content -->
    <div class="content">
        <div class="dashboard">
            <h2 class="text-center">Welcome to Administrator Dashboard</h2>
            <p class="text-center">Manage website data, products, and reports from the sidebar menu.</p>
        </div>

        <!-- Profile Edit Section -->
        <div class="table-container">
            <h4>Edit Data Admin:</h4>
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td><strong>Username:</strong></td>
                        <td>Admin</td>
                    </tr>
                    <tr>
                        <td><strong>Nama Lengkap:</strong></td>
                        <td>ADMIN</td>
                    </tr>
                    <tr>
                        <td><strong>Alamat Email:</strong></td>
                        <td>info@.ac.id</td>
                    </tr>
                    <tr>
                        <td><strong>No Telepon:</strong></td>
                        <td>9</td>
                    </tr>
                    <tr>
                        <td><strong>Ganti Foto:</strong></td>
                        <td><input type="file"></td>
                    </tr>
                    <tr>
                        <td><strong>Blokir:</strong></td>
                        <td>
                            <label><input type="radio" name="block" value="yes"> Ya</label>
                            <label><input type="radio" name="block" value="no"> Tidak</label>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button class="btn btn-update btn-block">Update</button>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
