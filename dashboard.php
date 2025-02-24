<?php
session_start();
include 'config.php';
session_start();
// Ambil data dari tabel driver
$query = "SELECT * FROM driver";
$result = mysqli_query($koneksi, $query);
?>



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
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="categories-carousel owl-carousel wow fadeInUp" data-wow-delay="0.1s">
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <div class="categories-item p-4 shadow rounded mx-2">
                            <div class="categories-item-inner">
                                <div class="categories-img rounded-top overflow-hidden">
                                    <img src="gambardriver/<?php echo $row['gambardriver']; ?>" class="img-fluid w-100 rounded-top" alt="<?php echo $row['nama']; ?>" style="height: 200px; object-fit: cover;">
                                </div>
                                <div class="categories-content rounded-bottom p-4 bg-light">
                                    <h4 class="text-primary mb-3 text-center"><?php echo $row['nama']; ?></h4>
                                    <div class="categories-review mb-3 d-flex justify-content-center">
                                        <div class="me-3">Rating</div>
                                        <div class="d-flex text-secondary">
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
                                    <div class="mb-3 text-center">
                                        <h6 class="bg-white text-primary rounded-pill py-2 px-3 mb-0">Plat: <?php echo $row['plat_nomor']; ?></h6>
                                    </div>
                                    <div class="row g-3 text-center mb-3">
                                        <div class="col-6 border-end">
                                            <i class="fa fa-id-card text-primary"></i>
                                            <span class="text-body ms-1 d-block">SIM: <?php echo $row['no_sim']; ?></span>
                                        </div>
                                        <div class="col-6">
                                            <i class="fa fa-check-circle text-primary"></i>
                                            <span class="text-body ms-1 d-block">Status: <?php echo $row['status']; ?></span>
                                        </div>
                                        <div class="col-6 border-end">
                                            <i class="fa fa-briefcase text-primary"></i>
                                            <span class="text-body ms-1 d-block">Pengalaman: <?php echo $row['pengalaman_kerja']; ?> Tahun</span>
                                        </div>
                                        <div class="col-6">
                                            <i class="fa fa-car text-primary"></i>
                                            <span class="text-body ms-1 d-block">Driver</span>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <?php if ($row['status'] == 'tersedia') { ?>
                                            <a href="utama.php?page=pengantaran_orang&id_driver=<?php echo $row['id_driver']; ?>" class="btn btn-primary rounded-pill py-2 px-4 w-100">Book Now</a>
                                        <?php } else { ?>
                                            <button class="btn btn-secondary rounded-pill py-2 px-4 w-100" disabled>Tidak Tersedia</button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
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