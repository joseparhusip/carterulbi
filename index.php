<?php
// Include file konfigurasi
include 'config.php';

// Ambil data dari tabel driver
$query = "SELECT * FROM driver";
$result = mysqli_query($koneksi, $query);
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
                        <a href="index.php" class="nav-item nav-link active">Home</a>
                        <a href="utama.php?page=service" class="nav-item nav-link">Service</a>
                        <a href="utama.php?page=productfood" class="nav-item nav-link">Food</a>
                        <a href="utama.php?page=about" class="nav-item nav-link">About</a>
                        <a href="utama.php?page=contact" class="nav-item nav-link">Contact</a>
                        <!-- Dropdown for Order -->
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
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
                            <div class="profile-dropdown">
                                <a href="utama.php?page=profile&id_user=">
                                    <img src="./CarterULBI/logo/profile.png" alt="Profile" style="width: 40px; height: 40px;">
                                </a>

                                <ul class="dropdown">
                                    <?php if (isset($_SESSION['username']) && $_SESSION['username']): ?>
                                        <!-- Menu untuk pengguna yang sudah login -->
                                        <li><a href="utama.php?page=profile">Profile (<?= $_SESSION['username'] ?>)</a></li> <!-- Menampilkan username -->
                                        <li><a href="utama.php?page=orderlist">Order History</a></li>
                                        <li><a href="utama.php?page=faq">FAQ</a></li>
                                        <li><a href="logout.php">Log Out</a></li> <!-- Tambahkan opsi untuk logout -->
                                    <?php else: ?>
                                        <!-- Menu untuk pengguna yang belum login -->
                                        <li><a href="login.php">Log In</a></li>
                                        <li><a href="signup.php">Register</a></li>
                                        <li><a href="logout.php">Log Out</a></li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
            </nav>
        </div>
    </div>
    <!-- Navbar & Hero End -->

    <!-- Carousel Start -->
    <div class="header-carousel">
        <div id="carouselId" class="carousel slide" data-bs-ride="carousel" data-bs-interval="false">
            <div class="carousel-inner" role="listbox">
                <div class="carousel-item active">
                    <img src="./CarterULBI/img/motorawal1.jpg" class="img-fluid w-100" alt="First slide" />
                    <div class="carousel-caption">
                        <div class="container py-4">
                            <div class="row g-5">
                                <div class="col-lg-6 fadeInLeft animated" data-animation="fadeInLeft" data-delay="1s" style="animation-delay: 1s;">
                                    <div class="bg-secondary rounded p-5">
                                        <h4 class="text-white mb-4">Search for a destination</h4>
                                        <form>
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <select class="form-select" aria-label="Default select example">
                                                        <option selected>Select Your Car type</option>
                                                        <option value="1">VW Golf VII</option>
                                                        <option value="2">Audi A1 S-Line</option>
                                                        <option value="3">Toyota Camry</option>
                                                        <option value="4">BMW 320 ModernLine</option>
                                                    </select>
                                                </div>
                                                <div class="col-12">
                                                    <div class="input-group">
                                                        <div class="d-flex align-items-center bg-light text-body rounded-start p-2">
                                                            <span class="fas fa-map-marker-alt"></span><span class="ms-1">Pick Up</span>
                                                        </div>
                                                        <input class="form-control" type="text" placeholder="Enter a City or Airport" aria-label="Enter a City or Airport">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <a href="#" class="text-start text-white d-block mb-2">Need a different drop-off location?</a>
                                                    <div class="input-group">
                                                        <div class="d-flex align-items-center bg-light text-body rounded-start p-2">
                                                            <span class="fas fa-map-marker-alt"></span><span class="ms-1">Drop off</span>
                                                        </div>
                                                        <input class="form-control" type="text" placeholder="Enter a City or Airport" aria-label="Enter a City or Airport">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <button class="btn btn-light w-100 py-2">Book Now</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-lg-6 d-none d-lg-flex fadeInRight animated" data-animation="fadeInRight" data-delay="1s" style="animation-delay: 1s;">
                                    <div class="text-start">
                                        <h1 class="display-5 text-white">Cari kendaraan? Carter Ulbi solusinya! </h1>
                                        <p>CaBi, Teman Perjalananmu!</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Carousel End -->
    </br>
    </br>
    </br>
    <!-- Car categories Start -->
    <div class="container-fluid categories pb-5">
        <div class="container pb-5">
            <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px;">
                <h1 class="display-5 text-capitalize mb-3">Kategori Kendaraan <span class="text-primary">CaBi</span></h1>
                <p class="mb-0">Nikmati berbagai pilihan kendaraan CaBi yang nyaman dan praktis. CaBi siap menemani perjalananmu dengan layanan cepat dan terpercaya. Pilih kendaraan sesuai kebutuhanmu, kapan saja dan di mana saja!</p>
            </div>
            <div class="categories-carousel owl-carousel wow fadeInUp" data-wow-delay="0.1s">
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <div class="categories-item p-4 shadow rounded">
                        <div class="categories-item-inner">
                            <div class="categories-img rounded-top overflow-hidden">
                                <img src="gambardriver/<?php echo $row['gambardriver']; ?>" class="img-fluid w-100 rounded-top" alt="<?php echo $row['nama']; ?>">
                            </div>
                            <div class="categories-content rounded-bottom p-4 bg-light">
                                <h4 class="text-primary mb-3"><?php echo $row['nama']; ?></h4>
                                <div class="categories-review mb-3">
                                    <div class="me-3">Rating</div>
                                    <div class="d-flex justify-content-center text-secondary">
                                        <?php
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $row['rating']) {
                                                echo '<i class="fas fa-star text-warning"></i>';
                                            } else {
                                                echo '<i class="fas fa-star text-body"></i>';
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <h6 class="bg-white text-primary rounded-pill py-2 px-3 mb-0">Plat: <?php echo $row['plat_nomor']; ?></h6>
                                </div>
                                <div class="row gy-2 gx-0 text-center mb-3">
                                    <div class="col-6 border-end">
                                        <i class="fa fa-id-card text-dark"></i> <span class="text-body ms-1">SIM: <?php echo $row['no_sim']; ?></span>
                                    </div>
                                    <div class="col-6">
                                        <i class="fa fa-check-circle text-dark"></i> <span class="text-body ms-1">Status: <?php echo $row['status']; ?></span>
                                    </div>
                                    <div class="col-6 border-end">
                                        <i class="fa fa-briefcase text-dark"></i> <span class="text-body ms-1">Pengalaman: <?php echo $row['pengalaman_kerja']; ?> Tahun</span>
                                    </div>
                                    <div class="col-6">
                                        <i class="fa fa-car text-dark"></i> <span class="text-body ms-1">Driver</span>
                                    </div>
                                </div>
                                <?php if ($row['status'] == 'tersedia') { ?>
                                    <a href="utama.php?page=pengantaran_orang&id_driver=<?php echo $row['id_driver']; ?>" class="btn btn-primary rounded-pill py-2 px-4">Book Now</a>
                                <?php } else { ?>
                                    <button class="btn btn-primary rounded-pill py-2 px-4" disabled>Book Now</button>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <!-- Car categories End -->

    <?php
    // Tutup koneksi database
    mysqli_close($koneksi);
    ?>

    <!-- Car Steps Start -->
    <div class="container-fluid steps py-5">
        <div class="container py-5">
            <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px;">
                <h1 class="display-5 text-capitalize text-white mb-3">Proses Pemesanan<span class="text-primary"> CaBi</span></h1>
                <p class="mb-0 text-white">Nikmati kemudahan menggunakan layanan CaBi. Mulai perjalananmu dengan motor yang nyaman dan praktis, hanya dengan beberapa langkah mudah. Kami siap memberikan pengalaman perjalanan terbaik!
                </p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="steps-item p-4 mb-4">
                        <h4>Pilih Lokasi dan Tujuan</h4>
                        <p class="mb-0">Tentukan lokasi penjemputan dan tujuan perjalananmu melalui aplikasi CaBi.</p>
                        <div class="setps-number">01.</div>
                    </div>
                </div>
                <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="steps-item p-4 mb-4">
                        <h4>Pilih Motor dan Konfirmasi</h4>
                        <p class="mb-0">Pilih motor yang tersedia dan konfirmasikan pemesanan untuk perjalananmu.</p>
                        <div class="setps-number">02.</div>
                    </div>
                </div>
                <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="steps-item p-4 mb-4">
                        <h4>Pembayaran Secara Cash</h4>
                        <p class="mb-0">Setelah perjalanan selesai, lakukan pembayaran secara cash kepada pengemudi CaBi.</p>
                        <div class="setps-number">03.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Car Steps End -->

    <!-- Services Start -->
    <div class="container-fluid service py-5">
        <div class="container py-5">
            <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px;">
                <h1 class="display-5 text-capitalize mb-3">Layanan <span class="text-primary">CaBi</span></h1>
                <p class="mb-0">Nikmati berbagai layanan pengantaran cepat dan praktis hanya dengan CaBi, solusi transportasi terbaik untuk semua kebutuhanmu!
                </p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="service-item p-4">
                        <div class="service-icon mb-4">
                            <i class="fa fa-road fa-2x"></i>
                        </div>
                        <h5 class="mb-3">Pengantaran Orang</h5>
                        <p class="mb-0">Layanan antar-jemput orang, baik untuk kebutuhan pribadi maupun bisnis, CaBi selalu siap mengantarkan dengan aman.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="service-item p-4">
                        <div class="service-icon mb-4">
                            <i class="fa fa-car-alt fa-2x"></i>
                        </div>
                        <h5 class="mb-3">Pengantaran Makanan</h5>
                        <p class="mb-0">CaBi siap mengantarkan makanan dari restoran favoritmu dengan cepat dan aman ke tujuan.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="service-item p-4">
                        <div class="service-icon mb-4">
                            <i class="fa fa-phone-alt fa-2x"></i>
                        </div>
                        <h5 class="mb-3">Pesan Melalui WhatsApp</h5>
                        <p class="mb-0">Pesan motor CaBi dengan mudah melalui WhatsApp. Layanan kami siap membantu kebutuhan perjalananmu kapan saja!</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="service-item p-4">
                        <div class="service-icon mb-4">
                            <i class="fa fa-money-bill-alt fa-2x"></i>
                        </div>
                        <h5 class="mb-3">Tarif Spesial</h5>
                        <p class="mb-0">Dapatkan tarif spesial untuk perjalananmu dengan CaBi. Solusi transportasi hemat yang selalu diandalkan!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Services End -->

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