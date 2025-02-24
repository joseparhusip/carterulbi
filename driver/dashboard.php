<?php

include 'config.php'; // Pastikan file koneksi.php sudah benar

// Ambil username dari tabel driver
$id_driver = $_SESSION['id_driver'];
$query_driver = mysqli_query($koneksi, "SELECT username FROM driver WHERE id_driver = '$id_driver'");
$data_driver = mysqli_fetch_assoc($query_driver);
$username = $data_driver['username'];

// Hitung total biaya dari tabel pengantaran_orang berdasarkan id_driver
$query_biaya = mysqli_query($koneksi, "SELECT SUM(biaya) AS total_biaya FROM pengantaran_orang WHERE id_driver = '$id_driver'");
$data_biaya = mysqli_fetch_assoc($query_biaya);
$total_biaya = $data_biaya['total_biaya'];

// Hitung total harga dari tabel pesanan_makanan berdasarkan id_driver
$query_pesanan = mysqli_query($koneksi, "SELECT SUM(total_harga) AS total_pesanan FROM pesanan_makanan WHERE id_driver = '$id_driver'");
$data_pesanan = mysqli_fetch_assoc($query_pesanan);
$total_pesanan = $data_pesanan['total_pesanan'];

// Hitung jumlah pengantaran dari tabel pengantaran_orang berdasarkan id_driver
$query_jumlah_pengantaran = mysqli_query($koneksi, "SELECT COUNT(id_pengantaran) AS jumlah_pengantaran FROM pengantaran_orang WHERE id_driver = '$id_driver'");
$data_jumlah_pengantaran = mysqli_fetch_assoc($query_jumlah_pengantaran);
$jumlah_pengantaran = $data_jumlah_pengantaran['jumlah_pengantaran'];

// Hitung jumlah pesanan dari tabel pesanan_makanan berdasarkan id_driver
$query_jumlah_pesanan = mysqli_query($koneksi, "SELECT COUNT(id_pesanan) AS jumlah_pesanan FROM pesanan_makanan WHERE id_driver = '$id_driver'");
$data_jumlah_pesanan = mysqli_fetch_assoc($query_jumlah_pesanan);
$jumlah_pesanan = $data_jumlah_pesanan['jumlah_pesanan'];

// Fetch monthly delivery statistics
$query_monthly_deliveries = mysqli_query($koneksi, "
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') AS month,
        COUNT(id_pengantaran) AS total_deliveries
    FROM 
        pengantaran_orang
    WHERE 
        id_driver = '$id_driver'
    GROUP BY 
        DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY 
        month
");

$monthly_deliveries = [];
while ($row = mysqli_fetch_assoc($query_monthly_deliveries)) {
    $monthly_deliveries[$row['month']] = $row['total_deliveries'];
}

// Fetch monthly order statistics
$query_monthly_orders = mysqli_query($koneksi, "
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') AS month,
        COUNT(id_pesanan) AS total_orders
    FROM 
        pesanan_makanan
    WHERE 
        id_driver = '$id_driver'
    GROUP BY 
        DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY 
        month
");

$monthly_orders = [];
while ($row = mysqli_fetch_assoc($query_monthly_orders)) {
    $monthly_orders[$row['month']] = $row['total_orders'];
}

// Fetch monthly rating statistics for pesanan_makanan
$query_monthly_ratings_pesanan = mysqli_query($koneksi, "
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') AS month,
        COUNT(id_pesanan) AS total_ratings
    FROM 
        pesanan_makanan
    WHERE 
        id_driver = '$id_driver'
    GROUP BY 
        DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY 
        month
");

$monthly_ratings_pesanan = [];
while ($row = mysqli_fetch_assoc($query_monthly_ratings_pesanan)) {
    $monthly_ratings_pesanan[$row['month']] = $row['total_ratings'];
}

// Fetch monthly rating statistics for pengantaran_orang
$query_monthly_ratings_pengantaran = mysqli_query($koneksi, "
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') AS month,
        COUNT(id_pengantaran) AS total_ratings
    FROM 
        pengantaran_orang
    WHERE 
        id_driver = '$id_driver'
    GROUP BY 
        DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY 
        month
");

$monthly_ratings_pengantaran = [];
while ($row = mysqli_fetch_assoc($query_monthly_ratings_pengantaran)) {
    $monthly_ratings_pengantaran[$row['month']] = $row['total_ratings'];
}

// Prepare data for Chart.js for deliveries, orders, and ratings
$all_months = array_unique(array_merge(array_keys($monthly_deliveries), array_keys($monthly_orders), array_keys($monthly_ratings_pesanan), array_keys($monthly_ratings_pengantaran)));
sort($all_months);

$deliveries = array_fill_keys($all_months, 0);
$orders = array_fill_keys($all_months, 0);
$ratings_pesanan = array_fill_keys($all_months, 0);
$ratings_pengantaran = array_fill_keys($all_months, 0);

foreach ($monthly_deliveries as $month => $count) {
    $deliveries[$month] = $count;
}

foreach ($monthly_orders as $month => $count) {
    $orders[$month] = $count;
}

foreach ($monthly_ratings_pesanan as $month => $count) {
    $ratings_pesanan[$month] = $count;
}

foreach ($monthly_ratings_pengantaran as $month => $count) {
    $ratings_pengantaran[$month] = $count;
}

$months = array_keys($deliveries);
$deliveries = array_values($deliveries);
$orders = array_values($orders);
$ratings_pesanan = array_values($ratings_pesanan);
$ratings_pengantaran = array_values($ratings_pengantaran);

// Fetch payment method statistics
$query_payment_methods = mysqli_query($koneksi, "
    SELECT 
        mp.nama_metode,
        COUNT(pm.id_metode_pembayaran) AS count
    FROM 
        pesanan_makanan pm
    JOIN 
        metode_pembayaran mp ON pm.id_metode_pembayaran = mp.id_metode_pembayaran
    WHERE 
        pm.id_driver = '$id_driver'
    GROUP BY 
        pm.id_metode_pembayaran
");

$payment_methods = [];
$payment_counts = [];
while ($row = mysqli_fetch_assoc($query_payment_methods)) {
    $payment_methods[] = $row['nama_metode'];
    $payment_counts[] = $row['count'];
}

?>

<form action="utama.php?page=dashboard" method="GET">
    <section class="content-main">
        <div class="content-header">
            <div>
                <h2 class="content-title card-title">Dashboard</h2>
            </div>
            <div>
                <a href="#" class="btn btn-primary"><i class="text-muted material-icons md-post_add"></i>Buat Laporan</a>
            </div>
        </div>
        <div class="row d-flex justify-content-between">
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card card-body d-flex flex-column align-items-center justify-content-center">
                    <article class="icontext">
                        <span class="icon icon-sm rounded-circle bg-primary-light">
                            <i class="text-primary material-icons md-monetization_on"></i>
                        </span>
                        <div class="text">
                            <h6 class="mb-1 card-title">Pengantaran Orang</h6>
                            <div class="d-flex">
                                <span class="rp-label">Rp</span>
                                <span class="harga"><?php echo number_format($total_biaya, 0, ',', '.'); ?></span>
                            </div>
                            <span class="text-sm"> Total biaya pengantaran orang </span>
                        </div>
                    </article>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card card-body d-flex flex-column align-items-center justify-content-center">
                    <article class="icontext">
                        <span class="icon icon-sm rounded-circle bg-success-light">
                            <i class="text-success material-icons md-local_dining"></i>
                        </span>
                        <div class="text">
                            <h6 class="mb-1 card-title">Pesanan Makanan</h6>
                            <div class="d-flex">
                                <span class="rp-label">Rp</span>
                                <span class="harga"><?php echo number_format($total_pesanan, 0, ',', '.'); ?></span>
                            </div>
                            <span class="text-sm"> Total harga pesanan makanan </span>
                        </div>
                    </article>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card card-body d-flex flex-column align-items-center justify-content-center">
                    <article class="icontext">
                        <span class="icon icon-sm rounded-circle bg-warning-light">
                            <i class="text-warning material-icons md-delivery_dining"></i>
                        </span>
                        <div class="text">
                            <h6 class="mb-1 card-title">Jumlah Pengantaran</h6>
                            <span><?php echo $jumlah_pengantaran; ?></span>
                            <span class="text-sm"> Total pengantaran orang </span>
                        </div>
                    </article>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card card-body d-flex flex-column align-items-center justify-content-center">
                    <article class="icontext">
                        <span class="icon icon-sm rounded-circle bg-info-light">
                            <i class="text-info material-icons md-fastfood"></i>
                        </span>
                        <div class="text">
                            <h6 class="mb-1 card-title">Jumlah Pesanan</h6>
                            <span><?php echo $jumlah_pesanan; ?></span>
                            <span class="text-sm"> Total pesanan makanan </span>
                        </div>
                    </article>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Monthly Deliveries</h5>
                        <canvas id="monthlyDeliveriesChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Monthly Orders</h5>
                        <canvas id="monthlyOrdersChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Monthly Ratings for Food Orders</h5>
                        <canvas id="monthlyRatingsPesananChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Monthly Ratings for Deliveries</h5>
                        <canvas id="monthlyRatingsPengantaranChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Payment Methods</h5>
                        <canvas id="paymentMethodsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>
</form>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- JavaScript to render the charts -->
<script>
    // Monthly Deliveries Chart
    var ctxDeliveries = document.getElementById('monthlyDeliveriesChart').getContext('2d');
    var monthlyDeliveriesChart = new Chart(ctxDeliveries, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($months); ?>,
            datasets: [{
                label: 'Total Deliveries',
                data: <?php echo json_encode($deliveries); ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Monthly Orders Chart
    var ctxOrders = document.getElementById('monthlyOrdersChart').getContext('2d');
    var monthlyOrdersChart = new Chart(ctxOrders, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($months); ?>,
            datasets: [{
                label: 'Total Orders',
                data: <?php echo json_encode($orders); ?>,
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Monthly Ratings for Food Orders Chart
    var ctxRatingsPesanan = document.getElementById('monthlyRatingsPesananChart').getContext('2d');
    var monthlyRatingsPesananChart = new Chart(ctxRatingsPesanan, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($months); ?>,
            datasets: [{
                label: 'Total Ratings for Food Orders',
                data: <?php echo json_encode($ratings_pesanan); ?>,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Monthly Ratings for Deliveries Chart
    var ctxRatingsPengantaran = document.getElementById('monthlyRatingsPengantaranChart').getContext('2d');
    var monthlyRatingsPengantaranChart = new Chart(ctxRatingsPengantaran, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($months); ?>,
            datasets: [{
                label: 'Total Ratings for Deliveries',
                data: <?php echo json_encode($ratings_pengantaran); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Payment Methods Chart
    var ctxPaymentMethods = document.getElementById('paymentMethodsChart').getContext('2d');
    var paymentMethodsChart = new Chart(ctxPaymentMethods, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($payment_methods); ?>,
            datasets: [{
                label: 'Payment Methods',
                data: <?php echo json_encode($payment_counts); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Payment Methods'
                }
            }
        }
    });
</script>

<!-- CSS -->
<style>
    .d-flex {
        display: flex;
        align-items: center;
    }
    .rp-label {
        margin-right: 5px;
        font-weight: bold;
    }
    .harga {
        font-size: 0.9rem; /* Mengurangi ukuran font */
    }
    .icontext .text {
        display: inline-block;
        vertical-align: middle;
    }
    .card.card-body {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 100%;
    }
    .row {
        display: flex;
        flex-wrap: wrap;
    }
    .row > [class*='col-'] {
        display: flex;
        flex-direction: column;
    }
</style>
