<?php
// Include file konfigurasi
include 'config.php';

// Ambil data dari tabel driver
$query = "SELECT * FROM driver";
$result = mysqli_query($koneksi, $query);
?>


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

<!-- Banner Start -->
<div class="container-fluid banner py-5 wow zoomInDown" data-wow-delay="0.1s">
    <div class="container py-5">
        <div class="banner-item rounded">
            <img src="./CarterULBI/img/banner-1.jpg" class="img-fluid rounded w-100" alt="">
            <div class="banner-content ">
                <h2 class="text-primary">Tertarik Menggunakan Layanan CaBi?</h2>
                <h5 class="text-white">Nikmati perjalanan nyaman dengan CaBi! Kirim pesan kepada kami sekarang juga.</h1>
                    </br>
                    </br>
                    <h2 class="text-primary">Tertarik Menjadi Driver CaBi?</h2>
                    <h5 class="text-white">Gabung dengan CaBi dan dapatkan kesempatan menjadi driver! </h1>
                        </br>
                        </br>
                        <div class="banner-btn">
                            <a href="#" class="btn btn-secondary rounded-pill py-3 px-4 px-md-5 me-2">WhatsApp</a>
                            <a href="#" class="btn btn-primary rounded-pill py-3 px-4 px-md-5 ms-2">Contact Us</a>
                        </div>
            </div>
        </div>
    </div>
</div>
<!-- Banner End -->
