<?php
ob_start(); // Mencegah output sebelum header
session_start(); // Mulai sesi
require 'config.php'; // Pastikan file koneksi database sudah benar

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header('Location: index.php'); // Arahkan ke login jika tidak ada sesi
    exit;
}

// Ambil username dari sesi
$username = $_SESSION['username'];

// Query untuk memastikan username ada di tabel admin
$query = "SELECT username, nama, id_admin FROM admin WHERE username = ?";
$stmt = mysqli_prepare($koneksi, $query);

if ($stmt) {
    // Bind parameter
    mysqli_stmt_bind_param($stmt, "s", $username);
    
    // Execute statement
    mysqli_stmt_execute($stmt);
    
    // Bind result variables
    mysqli_stmt_bind_result($stmt, $db_username, $db_nama, $db_id_admin);
    
    // Fetch value
    if (!mysqli_stmt_fetch($stmt)) {
        // Jika username tidak ditemukan di tabel admin
        mysqli_stmt_close($stmt);
        session_destroy();
        header('Location: index.php?error=unauthorized');
        exit;
    }
    
    // Simpan data admin dalam variabel jika diperlukan
    $admin = array(
        'username' => $db_username,
        'nama' => $db_nama,
        'id_admin' => $db_id_admin
    );
    
    // Tutup statement
    mysqli_stmt_close($stmt);
} else {
    // Jika ada kesalahan dalam prepared statement
    session_destroy();
    header('Location: index.php?error=system');
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Carter ULBI</title>
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:title" content="" />
    <meta property="og:type" content="" />
    <meta property="og:url" content="" />
    <meta property="og:image" content="" />
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="../picture/black.png" />
    <!-- Template CSS -->
    <link href="../assets/css/main.css?v=1.1" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="screen-overlay"></div>
    <aside class="navbar-aside" id="offcanvas_aside">
        <div class="aside-top">
            <a href="dashboard.php" class="brand-wrap">
                <img src="./gambaradmin/Carter.png" class="logo" alt="Naila Shop" />
            </a>
            <div>
                <button class="btn btn-icon btn-aside-minimize"><i class="text-muted material-icons md-menu_open"></i></button>
            </div>
        </div>
        <nav>
            <ul class="menu-aside">
                <li class="menu-item">
                    <a class="menu-link" href="utama.php?page=dashboard">
                        <i class="icon material-icons md-home"></i>
                        <span class="text">Dashboard</span>
                    </a>
                </li>
                <li class="menu-item has-submenu">
                    <a class="menu-link" href="#">
                        <i class="icon material-icons md-shopping_bag"></i>
                        <span class="text">Produk</span>
                    </a>
                    <ul class="submenu">
                        <li>
                            <a href="utama.php?page=kategori">Kategori Produk</a>
                        </li>
                        <li>
                            <a href="utama.php?page=produk">Produk Makanan</a>
                        </li>
                    </ul>
                </li>
                
                <li class="menu-item has-submenu">
                    <a class="menu-link" href="#">
                        <i class="icon material-icons md-local_shipping"></i>
                        <span class="text">Pengantaran Orang</span>
                    </a>
                    <ul class="submenu">
                        <li>
                            <a href="utama.php?page=pengantaran_orang">Pengantaran Orang</a>
                        </li>
                    </ul>
                </li>

                <!-- Pemesanan Makanan Menu Item -->
                <li class="menu-item">
                    <a class="menu-link" href="utama.php?page=pesanan_makanan">
                        <i class="icon material-icons md-restaurant"></i>
                        <span class="text">Pemesanan Makanan</span>
                    </a>
                </li>
                <!-- End Pemesanan Makanan Menu Item -->


                <li class="menu-item has-submenu">
                    <a class="menu-link" href="#">
                        <i class="icon material-icons md-supervisor_account"></i>
                        <span class="text">Master</span>
                    </a>
                    <ul class="submenu">
                        <li>
                            <a href="utama.php?page=datauser">Data User</a>
                        </li>
                        <li>
                            <a href="utama.php?page=datadriver">Data Driver</a>
                        </li>
                    </ul>
                </li>

                <!-- Pembayaran Menu Item -->
                <li class="menu-item has-submenu">
                    <a class="menu-link" href="#">
                        <i class="icon material-icons md-attach_money"></i>
                        <span class="text">Pembayaran</span>
                    </a>
                    <ul class="submenu">
                        <li>
                            <a href="utama.php?page=pembayaran_orang">Pembayaran Pengantaran Orang</a>
                        </li>
                        <li>
                            <a href="utama.php?page=pembayaran_makanan">Pembayaran Pengantaran Makanan</a>
                        </li>
                    </ul>
                </li>
                <!-- End Pembayaran Menu Item -->

                <!-- Edit Profile menu item -->
                <li class="menu-item">
                    <a class="menu-link" href="utama.php?page=profile">
                        <i class="icon material-icons md-perm_identity"></i>
                        <span class="text">Edit Profile</span>
                    </a>
                </li>

                <!-- Ulasan Menu Item -->
                <li class="menu-item has-submenu">
                    <a class="menu-link" href="#">
                        <i class="icon material-icons md-rate_review"></i>
                        <span class="text">Ulasan</span>
                    </a>
                    <ul class="submenu">
                        <li>
                            <a href="utama.php?page=ulasanorang&id_ulasan=">Pengantaran Orang</a>
                        </li>
                        <li>
                            <a href="utama.php?page=ulasanmakanan&id_ulasan=">Pemesanan Makanan</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </aside>


    <main class="main-wrap">
        <header class="main-header navbar">
            <div class="container-fluid d-flex justify-content-end">  
                <button class="btn btn-icon btn-mobile me-auto" data-trigger="#offcanvas_aside"><i class="material-icons md-apps"></i></button>  
                <ul class="nav">  
                     <li class="nav-item">
                        <a class="nav-link btn-icon darkmode" href="#"><i class="material-icons md-nights_stay"></i></a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="requestfullscreen nav-link btn-icon"><i class="material-icons md-cast"></i></a>
                    </li>
                    <li class="dropdown nav-item">
                        <a class="dropdown-toggle" data-bs-toggle="dropdown" href="#" id="dropdownAccount" aria-expanded="false"><img class="img-xs rounded-circle" src="../assets/imgs/people/avatar-2.png" alt="User" /></a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownAccount">
                            <a class="dropdown-item text-danger" href="logout.php"><i class="material-icons md-exit_to_app"></i>Logout</a>
                        </div>
                    </li>
                </ul>  
            </div> 
        </header>

        <!-- Bagian untuk menampilkan konten halaman -->
        <section class="content">
            <?php
            if (isset($_GET['page'])) {
                if ($_GET['page'] == 'home') {
                    include "home.php";
                } elseif ($_GET['page'] == 'logout') {
                    include "logout.php";
                } elseif ($_GET['page'] == 'dashboard') {
                    include "dashboard.php";
                } elseif ($_GET['page'] == 'categories') {
                    include "categories.php";
                } elseif ($_GET['page'] == 'pengantaran_orang') {
                    include "pengantaran_orang.php";
                } elseif ($_GET['page'] == 'pesanan_makanan') {
                    include "pesanan_makanan.php";
                }  elseif ($_GET['page'] == 'datadriver') {
                    include "datadriver.php";
                }  elseif ($_GET['page'] == 'datauser') {
                    include "datauser.php";
                }  elseif ($_GET['page'] == 'ulasanorang') {
                    include "ulasanorang.php";
                }  elseif ($_GET['page'] == 'ulasanmakanan') {
                    include "ulasanmakanan.php";
                } elseif ($_GET['page'] == 'profile') {
                    include "profile.php";
                } elseif ($_GET['page'] == 'editprofile') {
                    include "editprofile.php";
                } elseif ($_GET['page'] == 'editkategori') {
                    include "editkategori.php";
                } elseif ($_GET['page'] == 'tambahkategori') {
                    include "tambahkategori.php";
                } elseif ($_GET['page'] == 'hapuskategori') {
                    include "hapusprofile.php";
                }  elseif ($_GET['page'] == 'kategori') {
                    include "kategori.php";
                }  elseif ($_GET['page'] == 'produk') {
                    include "produk.php";
                }  elseif ($_GET['page'] == 'tambahproduk') {
                    include "tambahproduk.php";
                }  elseif ($_GET['page'] == 'editproduk') {
                    include "editproduk.php";
                }  elseif ($_GET['page'] == 'hapusproduk') {
                    include "hapusproduk.php";
                }  elseif ($_GET['page'] == 'pembayaran_makanan') {
                    include "pembayaran_makanan.php";   
                }  elseif ($_GET['page'] == 'pembayaran_orang') {
                    include "pembayaran_orang.php";                       
                } else {
                    include "index.php"; // Jika page tidak ditemukan, tampilkan index.php
                }
            } else {
                include "index.php"; // Jika tidak ada page yang dipilih, tampilkan index.php
            }
            ?>
        </section>
        
    </main>

    <!-- JavaScript Files -->
    <script src="../assets/js/vendors/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/vendors/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/vendors/select2.min.js"></script>
    <script src="../assets/js/vendors/perfect-scrollbar.js"></script>
    <script src="../assets/js/vendors/jquery.fullscreen.min.js"></script>
    <script src="../assets/js/vendors/chart.js"></script>
    <script src="../assets/js/main.js?v=1.1"></script>
    <script src="../assets/js/custom-chart.js"></script>
</body>

</html>