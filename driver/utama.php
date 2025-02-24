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
    <title>Carter ULbi</title>
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
    
    <!-- Add Custom CSS for Sticky Header -->
    <style>
        .main-header {
            position: sticky;
            top: 0;
            z-index: 1000;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .main-header.is-sticky {
            padding: 10px 0;
        }

        /* Dark mode compatibility */
        [data-theme="dark"] .main-header {
            background-color: #1c2127;
        }

        /* Ensure header stays above sidebar */
        .main-header {
            z-index: 1040;
        }

        .navbar-aside {
            z-index: 1030;
        }

        /* Maintain layout structure */
        .main-wrap {
            position: relative;
        }

        /* Mobile responsiveness */
        @media (max-width: 992px) {
            .main-header {
                padding: 8px 0;
            }
        }
    </style>
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
                            <a href="utama.php?page=pembayaran_orang">Pembayaran Pengantaran Orang</a>
                        </li>
                        <li>
                            <a href="utama.php?page=pembayaran_makanan">Pembayaran Pengantaran Makanan</a>
                        </li>
                    </ul>
                </li>
                
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
                            <a href="utama.php?page=ulasanpengantaran">Ulasan Pengantaran Orang</a>
                        </li>
                        <li>
                            <a href="utama.php?page=ulasanpesanan">Ulasan Pesanan Makanan</a>
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
                } elseif ($_GET['page'] == 'profile') {
                    include "profile.php";
                } elseif ($_GET['page'] == 'editprofile') {
                    include "editprofile.php";
                } elseif ($_GET['page'] == 'pesanan_makanan') {
                    include "pesanan_makanan.php";
                } elseif ($_GET['page'] == 'pengantaran_orang') {
                    include "pengantaran_orang.php";
                } elseif ($_GET['page'] == 'detail_pesanan') {
                    include "detail_pesanan.php";
                } elseif ($_GET['page'] == 'pembayaran_pengantaranorang') {
                    include "pembayaran_pengantaranorang.php";
                } elseif ($_GET['page'] == 'pembayaran_pesananmakanan') {
                    include "pembayaran_pesananmakanan.php";
                } elseif ($_GET['page'] == 'ulasanpengantaran') {
                    include "ulasanpengantaran.php";
                } elseif ($_GET['page'] == 'ulasanpesanan') {
                    include "ulasanpesanan.php";
                } elseif ($_GET['page'] == 'pembayaran_makanan') {
                    include "pembayaran_makanan.php";
                } elseif ($_GET['page'] == 'pembayaran_orang') {
                    include "pembayaran_orang.php";                
                } else {
                    include "index.php";
                }
            } else {
                include "index.php";
            }
            ?>
        </section>
        </br>
        </br>
        </br>
        </br>
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

    <!-- Add Sticky Header JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const header = document.querySelector('.main-header');
            let lastScroll = 0;

            window.addEventListener('scroll', () => {
                const currentScroll = window.pageYOffset;

                // Add/remove sticky class based on scroll position
                if (currentScroll > 0) {
                    header.classList.add('is-sticky');
                } else {
                    header.classList.remove('is-sticky');
                }

                lastScroll = currentScroll;
            });

            // Handle dark mode compatibility
            const darkModeToggle = document.querySelector('.darkmode');
            if (darkModeToggle) {
                darkModeToggle.addEventListener('click', () => {
                    // Let the existing dark mode handler work, our CSS will handle the header colors
                });
            }
        });
    </script>
</body>

</html>