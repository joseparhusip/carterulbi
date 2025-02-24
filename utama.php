<?php
session_start();
include "config.php";

if (!isset($_SESSION['username'])) {
    header('location:login.php');
    exit();
}

// Get current page from URL
$current_page = isset($_GET['page']) ? $_GET['page'] : 'index';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>CARTER ULBI</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,400;0,700;0,900;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="./CarterULBI/lib/animate/animate.min.css" rel="stylesheet">
    <link href="./CarterULBI/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="./CarterULBI/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="./CarterULBI/css/style.css" rel="stylesheet">

    <!-- Custom CSS for active navigation -->
    <style>
        .nav-item .nav-link.active {
            color: #ff0000 !important;
        }
        /* Add styles for profile dropdown */
        .profile-dropdown {
            position: relative;
            display: inline-block;
        }
        .profile-dropdown .dropdown {
            display: none;
            position: absolute;
            right: 0;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 4px;
            padding: 8px 0;
        }
        .profile-dropdown:hover .dropdown {
            display: block;
        }
        .profile-dropdown .dropdown li {
            list-style: none;
        }
        .profile-dropdown .dropdown li a {
            color: black;
            padding: 8px 16px;
            text-decoration: none;
            display: block;
        }
        .profile-dropdown .dropdown li a:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>

<body>
    <!-- Navbar & Hero Start -->
    <div class="container-fluid nav-bar sticky-top px-0 px-lg-4 py-2 py-lg-0">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light">
                <a href="" class="navbar-brand p-0">
                    <img src="./CarterULBI/logo/carter.png" alt="Logo">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav mx-auto py-0">
                        <a href="index.php" class="nav-item nav-link <?php echo ($current_page == 'index' || $current_page == '') ? 'active' : ''; ?>">Home</a>
                        <a href="utama.php?page=service" class="nav-item nav-link <?php echo ($current_page == 'service') ? 'active' : ''; ?>">Service</a>
                        <a href="utama.php?page=productfood" class="nav-item nav-link <?php echo ($current_page == 'productfood') ? 'active' : ''; ?>">Food</a>
                        <a href="utama.php?page=about" class="nav-item nav-link <?php echo ($current_page == 'about') ? 'active' : ''; ?>">About</a>
                        <a href="utama.php?page=contact" class="nav-item nav-link <?php echo ($current_page == 'contact') ? 'active' : ''; ?>">Contact</a>
                        <!-- Dropdown for Order -->
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?php echo (in_array($current_page, ['detailpengantaranorang', 'detailpesananmakanan'])) ? 'active' : ''; ?>" 
                               href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Order
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="utama.php?page=detailpengantaranorang">Pengantaran Orang</a></li>
                                <li><a class="dropdown-item" href="utama.php?page=detailpesananmakanan">Pesanan Makanan</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <div class="header__nav__option">
                            <a href="utama.php?page=keranjang">
                                <img src="./CarterULBI/logo/cart.jpg" alt="Cart" style="width: 40px; height: 40px;">
                            </a>
                            <!-- Updated Profile dropdown menu -->
                            <div class="profile-dropdown">
                                <a href="#">
                                    <img src="./CarterULBI/logo/profile.png" alt="Profile" style="width: 40px; height: 40px;">
                                </a>
                                <ul class="dropdown">
                                    <?php if (isset($_SESSION['username'])): ?>
                                        <li><a href="utama.php?page=profile">Profile</a></li>
                                        <li><a href="logout.php">Log Out</a></li>
                                    <?php else: ?>
                                        <li><a href="login.php">Log In</a></li>
                                        <li><a href="signup.php">Register</a></li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Rest of your existing code remains the same -->
    <!-- Navbar & Hero End -->

    <!-- Dynamic Content Section -->
    <section>
        <?php
        if (isset($_GET['page'])) {
            if ($_GET['page'] == 'index') {
                include "index.php";
            } elseif ($_GET['page'] == 'about') {
                include "about.php";
            } elseif ($_GET['page'] == 'service') {
                include "service.php";
            } elseif ($_GET['page'] == 'contact') {
                include "contact.php";
            } elseif ($_GET['page'] == 'faq') {
                include "faq.php";
            } elseif ($_GET['page'] == 'profile') {
                include "profile.php";
            } elseif ($_GET['page'] == 'pengantaran_orang') {
                include "pengantaran_orang.php";
            } elseif ($_GET['page'] == 'proses_pemesanan') {
                include "proses_pemesanan.php";           
            } elseif ($_GET['page'] == 'detailpengantaranorang') {
                include "detailpengantaranorang.php";
            } elseif ($_GET['page'] == 'productfood') {
                include "productfood.php";  
            } elseif ($_GET['page'] == 'detailproduct') {
                include "detailproduct.php";  
            } elseif ($_GET['page'] == 'keranjang') {
                include "keranjang.php";       
            } elseif ($_GET['page'] == 'detailpesananmakanan') {
                include "detailpesananmakanan.php";       
            } elseif ($_GET['page'] == 'formpemesananfood') {
                include "formpemesananfood.php";  
            } elseif ($_GET['page'] == 'ratingdriver') {
                include "ratingdriver.php";  
            } elseif ($_GET['page'] == 'ratingdriverfood') {
                include "ratingdriverfood.php"; 
            } elseif ($_GET['page'] == 'dashboard') {
                include "dashboard.php"; 
            } else {
                echo "<p>Halaman tidak ditemukan.</p>";
            }
        } else {
            include "login.php";  // Default ke login jika tidak ada parameter page
        }
        ?>
    </section>

    <br>

    <!-- Footer Start -->
    <div class="container-fluid footer py-5 wow fadeIn" data-wow-delay="0.2s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="footer-item d-flex flex-column">
                        <div class="footer-item">
                            <h4 class="text-white mb-4">Carter ULBI</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="footer-item d-flex flex-column">
                        <h4 class="text-white mb-4">Quick Links</h4>
                        <a href="#"><i class="fas fa-angle-right me-2"></i> About</a>
                        <a href="#"><i class="fas fa-angle-right me-2"></i> Cars</a>
                        <a href="#"><i class="fas fa-angle-right me-2"></i> FAQ</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="footer-item d-flex flex-column">
                        <h4 class="text-white mb-4">Jam Kerja</h4>
                        <div class="mb-3">
                            <h6 class="text-muted mb-0">Senin - Jumat:</h6>
                            <p class="text-white mb-0">09.00 am to 07.00 pm</p>
                        </div>
                        <div class="mb-3">
                            <h6 class="text-muted mb-0">Sabtu:</h6>
                            <p class="text-white mb-0">10.00 am to 05.00 pm</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="footer-item d-flex flex-column">
                        <h4 class="text-white mb-4">Informasi Kontak</h4>
                        <a href="#"><i class="fa fa-map-marker-alt me-2"></i> No. 54 Jl Sariasih 40151 Sukasari Jawa Barat</a>
                        <a href="mailto:info@example.com"><i class="fas fa-envelope me-2"></i> CarterULBI@gmail.com</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- Copyright Start -->
    <div class="container-fluid copyright py-4">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-md-6 text-center text-md-start mb-md-0">
                    <span class="text-body"><a href="#" class="border-bottom text-white"><i class="fas fa-copyright text-light me-2"></i>Carter ULBI</a>, All right reserved.</span>
                </div>
            </div>
        </div>
    </div>
    <!-- Copyright End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-secondary btn-lg-square rounded-circle back-to-top"><i class="fa fa-arrow-up"></i></a>

    <!--Start of Tawk.to Script-->
    <script>
    (function(){if(!window.chatbase||window.chatbase("getState")!=="initialized"){window.chatbase=(...arguments)=>{if(!window.chatbase.q){window.chatbase.q=[]}window.chatbase.q.push(arguments)};window.chatbase=new Proxy(window.chatbase,{get(target,prop){if(prop==="q"){return target.q}return(...args)=>target(prop,...args)}})}const onLoad=function(){const script=document.createElement("script");script.src="https://www.chatbase.co/embed.min.js";script.id="irnllxmO_Mi0nLrG9euqC";script.domain="www.chatbase.co";document.body.appendChild(script)};if(document.readyState==="complete"){onLoad()}else{window.addEventListener("load",onLoad)}})();
    </script>
    <!--End of Tawk.to Script-->

    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./CarterULBI/lib/wow/wow.min.js"></script>
    <script src="./CarterULBI/lib/easing/easing.min.js"></script>
    <script src="./CarterULBI/lib/waypoints/waypoints.min.js"></script>
    <script src="./CarterULBI/lib/counterup/counterup.min.js"></script>
    <script src="./CarterULBI/lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="./CarterULBI/js/main.js"></script>
</body>

</html>