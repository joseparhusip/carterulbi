<?php
ob_start(); // Tambahkan ini di awal untuk mencegah output sebelum header
session_start(); // Mulai sesi

// Cek apakah pengguna sudah login
if (!isset($_SESSION['id_driver'])) {
    // Jika belum login, arahkan ke halaman login
    header('Location: index.php');
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Naila Shop</title>
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
    <link href="../assets/css/main.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="screen-overlay"></div>
    <aside class="navbar-aside" id="offcanvas_aside">
        <div class="aside-top">
            <a href="dashboard.php" class="brand-wrap">
                <img src="../picture/black.png" class="logo" alt="Naila Shop" />
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

                <!-- Category menu item with sub-menu -->
                <li class="menu-item has-submenu">
                    <a class="menu-link" href="#">
                        <i class="icon material-icons md-shopping_bag"></i>
                        <span class="text">Category</span>
                    </a>
                    <ul class="submenu">
                        <li>
                            <a href="utama.php?page=pengantaran_orang">Pengantaran Orang</a>
                        </li>
                        <li>
                            <a href="utama.php?page=pesanan_makanan">Makanan</a>
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
                            <a href="utama.php?page=pembayaran">Pembayaran</a>
                        </li>
                        <li>
                            <a href="utama.php?page=detailpembayaran">Detail Pembayaran</a>
                        </li>
                    </ul>
                </li>
                <!-- End Pembayaran Menu Item -->
                <!-- Edit Profile Menu Item -->
                <li class="menu-item has-submenu">
                    <a class="menu-link" href="#">
                        <i class="icon material-icons md-edit"></i>
                        <span class="text">Profile</span>
                    </a>
                    <ul class="submenu">
                        <li>
                            <a href="utama.php?page=profile">Profile</a>
                        </li>
                    </ul>
                </li>

                <!-- Ulasan Menu Item -->
                <li class="menu-item has-submenu">
                    <a class="menu-link" href="#">
                        <i class="icon material-icons md-rate_review"></i>
                        <span class="text">Ulasan</span>
                    </a>
                    <ul class="submenu">
                        <li>
                            <a href="utama.php?page=ulasan">Ulasan</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </aside>



    <main class="main-wrap">
        <header class="main-header navbar">
            <div class="col-search">
                <form class="searchform">
                    <div class="input-group">
                        <input list="search_terms" type="text" class="form-control" placeholder="Search term" />
                        <button class="btn btn-light bg" type="button"><i class="material-icons md-search"></i></button>
                    </div>
                    <datalist id="search_terms">
                        <option value="Products"></option>
                        <option value="New orders"></option>
                        <option value="Apple iphone"></option>
                        <option value="Ahmed Hassan"></option>
                    </datalist>
                </form>
            </div>
            <div class="col-nav">
                <button class="btn btn-icon btn-mobile me-auto" data-trigger="#offcanvas_aside"><i class="material-icons md-apps"></i></button>
                <ul class="nav">
                    <li class="nav-item">
                        <a class="nav-link btn-icon" href="#">
                            <i class="material-icons md-notifications animation-shake"></i>
                            <span class="badge rounded-pill">3</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn-icon darkmode" href="#"><i class="material-icons md-nights_stay"></i></a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="requestfullscreen nav-link btn-icon"><i class="material-icons md-cast"></i></a>
                    </li>
                    <li class="dropdown nav-item">
                        <a class="dropdown-toggle" data-bs-toggle="dropdown" href="#" id="dropdownAccount" aria-expanded="false"><img class="img-xs rounded-circle" src="../assets/imgs/people/avatar-2.png" alt="User" /></a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownAccount">
                            <a class="dropdown-item" href="utama.php?page=editprofile&id_driver"><i class="material-icons md-perm_identity"></i>Edit Profile</a>
                            <a class="dropdown-item" href="#"><i class="material-icons md-settings"></i>Account Settings</a>
                            <a class="dropdown-item" href="#"><i class="material-icons md-account_balance_wallet"></i>Wallet</a>
                            <a class="dropdown-item" href="#"><i class="material-icons md-receipt"></i>Billing</a>
                            <a class="dropdown-item" href="#"><i class="material-icons md-help_outline"></i>Help center</a>
                            <div class="dropdown-divider"></div>
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
                } elseif ($_GET['page'] == 'profile') {
                    include "profile.php";                
                } elseif ($_GET['page'] == 'editprofile') {
                    include "editprofile.php";
                } elseif ($_GET['page'] == 'pesanan_makanan') {
                    include "pesanan_makanan.php";
                } elseif ($_GET['page'] == 'update_status_pemesanan') {
                    include "update_status_pemesanan.php";
                } elseif ($_GET['page'] == 'pengantaran_orang') {
                    include "pengantaran_orang.php";
                } elseif ($_GET['page'] == 'update_status_pengantaran') {
                    include "update_status_pengantaran.php";
                } elseif ($_GET['page'] == 'pembayaran') {
                    include "pembayaran.php";
                } elseif ($_GET['page'] == 'ulasan') {
                    include "ulasan.php";              
                } else {
                    include "index.php"; // Jika page tidak ditemukan, tampilkan index.php
                }
            } else {
                include "index.php"; // Jika tidak ada page yang dipilih, tampilkan index.php
            }
            ?>
        </section>
        </br>
        <!-- content-main end// -->
        <footer class="main-footer font-xs">
            <div class="row pb-30 pt-15">
                <div class="col-sm-6">
                    <script>
                        document.write(new Date().getFullYear());
                    </script>
                    &copy; Naila Shop
                </div>
                <div class="col-sm-6">
                    <div class="text-sm-end">All rights reserved</div>
                </div>
            </div>
        </footer>
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