<?php
// Koneksi ke database
include("config.php");
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
// Query untuk menghitung total user dari tabel user
$query_user = "SELECT COUNT(*) AS total_user FROM user";
$result_user = mysqli_query($koneksi, $query_user);

if ($result_user) {
    $row_user = mysqli_fetch_assoc($result_user);
    $total_user = $row_user['total_user'];
} else {
    $total_user = 0; // Default value jika query gagal
}

// Query untuk menghitung total driver dari tabel driver
$query_driver = "SELECT COUNT(*) AS total_driver FROM driver";
$result_driver = mysqli_query($koneksi, $query_driver);

if ($result_driver) {
    $row_driver = mysqli_fetch_assoc($result_driver);
    $total_driver = $row_driver['total_driver'];
} else {
    $total_driver = 0; // Default value jika query gagal
}

// Query untuk menghitung total produk makanan dari tabel produk_makanan
$query_produk = "SELECT COUNT(*) AS total_produk FROM produk_makanan";
$result_produk = mysqli_query($koneksi, $query_produk);

if ($result_produk) {
    $row_produk = mysqli_fetch_assoc($result_produk);
    $total_produk = $row_produk['total_produk'];
} else {
    $total_produk = 0; // Default value jika query gagal
}

// Query untuk menghitung total pesanan dari tabel pesanan_makanan
$query_pesanan = "SELECT COUNT(*) AS total_pesanan FROM pesanan_makanan";
$result_pesanan = mysqli_query($koneksi, $query_pesanan);

if ($result_pesanan) {
    $row_pesanan = mysqli_fetch_assoc($result_pesanan);
    $total_pesanan = $row_pesanan['total_pesanan'];
} else {
    $total_pesanan = 0; // Default value jika query gagal
}

// Query untuk menghitung total pengantaran dari tabel pengantaran_orang
$query_pengantaran = "SELECT COUNT(*) AS total_pengantaran FROM pengantaran_orang";
$result_pengantaran = mysqli_query($koneksi, $query_pengantaran);

if ($result_pengantaran) {
    $row_pengantaran = mysqli_fetch_assoc($result_pengantaran);
    $total_pengantaran = $row_pengantaran['total_pengantaran'];
} else {
    $total_pengantaran = 0; // Default value jika query gagal
}

// Query untuk menghitung total pesanan makanan dengan status "Selesai"
$query_pesanan_selesai = "SELECT COUNT(*) AS total_pesanan_selesai FROM pesanan_makanan WHERE status_pesanan = 'Selesai'";
$result_pesanan_selesai = mysqli_query($koneksi, $query_pesanan_selesai);

if ($result_pesanan_selesai) {
    $row_pesanan_selesai = mysqli_fetch_assoc($result_pesanan_selesai);
    $total_pesanan_selesai = $row_pesanan_selesai['total_pesanan_selesai'];
} else {
    $total_pesanan_selesai = 0; // Default value jika query gagal
}

// Query untuk menghitung total pengantaran orang dengan status "Selesai"
$query_pengantaran_selesai = "SELECT COUNT(*) AS total_pengantaran_selesai FROM pengantaran_orang WHERE status = 'Selesai'";
$result_pengantaran_selesai = mysqli_query($koneksi, $query_pengantaran_selesai);

if ($result_pengantaran_selesai) {
    $row_pengantaran_selesai = mysqli_fetch_assoc($result_pengantaran_selesai);
    $total_pengantaran_selesai = $row_pengantaran_selesai['total_pengantaran_selesai'];
} else {
    $total_pengantaran_selesai = 0; // Default value jika query gagal
}

// Query untuk menghitung total harga pesanan makanan dengan status "Selesai"
$query_total_harga_selesai = "SELECT COALESCE(SUM(total_harga), 0) AS total_harga_selesai FROM pesanan_makanan WHERE status_pesanan = 'Selesai'";
$result_total_harga_selesai = mysqli_query($koneksi, $query_total_harga_selesai);

if ($result_total_harga_selesai) {
    $row_total_harga_selesai = mysqli_fetch_assoc($result_total_harga_selesai);
    $total_harga_selesai = floatval($row_total_harga_selesai['total_harga_selesai']); // Konversi ke float
} else {
    $total_harga_selesai = 0.0; // Default value jika query gagal
}

// Query untuk statistik pesanan makanan per driver berdasarkan bulan
$query_statistik_pesanan = "
    SELECT 
        id_driver, 
        DATE_FORMAT(updated_at, '%Y-%m') AS bulan, 
        COUNT(*) AS total_pesanan 
    FROM pesanan_makanan 
    GROUP BY id_driver, bulan 
    ORDER BY bulan DESC, total_pesanan DESC
";
$result_statistik_pesanan = mysqli_query($koneksi, $query_statistik_pesanan);

$statistik_pesanan = [];
if ($result_statistik_pesanan) {
    while ($row = mysqli_fetch_assoc($result_statistik_pesanan)) {
        $statistik_pesanan[] = $row;
    }
}

// Query untuk statistik pengantaran orang per driver berdasarkan bulan
$query_statistik_pengantaran = "
    SELECT 
        id_driver, 
        DATE_FORMAT(created_at, '%Y-%m') AS bulan, 
        COUNT(*) AS total_pengantaran 
    FROM pengantaran_orang 
    GROUP BY id_driver, bulan 
    ORDER BY bulan DESC, total_pengantaran DESC
";
$result_statistik_pengantaran = mysqli_query($koneksi, $query_statistik_pengantaran);

$statistik_pengantaran = [];
if ($result_statistik_pengantaran) {
    while ($row = mysqli_fetch_assoc($result_statistik_pengantaran)) {
        $statistik_pengantaran[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin Carter Ulbi</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            font-size: 24px;
        }
        .bg-primary-light {
            background-color: #e3f2fd;
        }
        .bg-success-light {
            background-color: #d4edda;
        }
        .bg-warning-light {
            background-color: #fff3cd;
        }
        .bg-danger-light {
            background-color: #f8d7da;
        }
        .bg-info-light {
            background-color: #d1ecf1;
        }
        .bg-secondary-light {
            background-color: #f8f9fa;
        }
        .text-primary {
            color: #0d6efd;
        }
        .text-success {
            color: #28a745;
        }
        .text-warning {
            color: #ffc107;
        }
        .text-danger {
            color: #dc3545;
        }
        .text-info {
            color: #17a2b8;
        }
        .text-secondary {
            color: #6c757d;
        }
        .mb-1 {
            margin-bottom: 0.5rem;
        }
        .text-sm {
            font-size: 0.875rem;
        }
        canvas {
            max-height: 400px;
        }
    </style>
</head>
<body>
    <main>
        <form action="utama.php?page=dasboard" method="GET">
            <section class="content-main">
                <div class="content-header">
                    <div>
                        <h2 class="content-title card-title">Dashboard Admin Carter Ulbi</h2>
                        <p>Whole data about your business here</p>
                    </div>
                    
                </div>
                <div class="row">
                    <!-- Card Jumlah User -->
                    <div class="col-lg-3">
                        <div class="card card-body mb-4">
                            <article class="icontext">
                                <span class="icon icon-sm rounded-circle bg-primary-light">
                                    <i class="text-primary material-icons md-person"></i>
                                </span>
                                <div class="text">
                                    <h6 class="mb-1 card-title">Jumlah User</h6>
                                    <span><?php echo $total_user; ?></span>
                                    <span class="text-sm">Total pengguna terdaftar</span>
                                </div>
                            </article>
                        </div>
                    </div>

                    <!-- Card Jumlah Driver -->
                    <div class="col-lg-3">
                        <div class="card card-body mb-4">
                            <article class="icontext">
                                <span class="icon icon-sm rounded-circle bg-success-light">
                                    <i class="text-success material-icons md-directions_car"></i>
                                </span>
                                <div class="text">
                                    <h6 class="mb-1 card-title">Jumlah Driver</h6>
                                    <span><?php echo $total_driver; ?></span>
                                    <span class="text-sm">Total driver terdaftar</span>
                                </div>
                            </article>
                        </div>
                    </div>

                    <!-- Card Produk Makanan -->
                    <div class="col-lg-3">
                        <div class="card card-body mb-4">
                            <article class="icontext">
                                <span class="icon icon-sm rounded-circle bg-warning-light">
                                    <i class="text-warning material-icons md-local_dining"></i>
                                </span>
                                <div class="text">
                                    <h6 class="mb-1 card-title">Produk Makanan</h6>
                                    <span><?php echo $total_produk; ?></span>
                                    <span class="text-sm">Total produk makanan</span>
                                </div>
                            </article>
                        </div>
                    </div>

                    <!-- Card Pesanan Makanan -->
                    <div class="col-lg-3">
                        <div class="card card-body mb-4">
                            <article class="icontext">
                                <span class="icon icon-sm rounded-circle bg-danger-light">
                                    <i class="text-danger material-icons md-fastfood"></i>
                                </span>
                                <div class="text">
                                    <h6 class="mb-1 card-title">Pesanan Makanan</h6>
                                    <span><?php echo $total_pesanan; ?></span>
                                    <span class="text-sm">Total pesanan masuk</span>
                                </div>
                            </article>
                        </div>
                    </div>

                    <!-- Card Pengantaran Orang -->
                    <div class="col-lg-3">
                        <div class="card card-body mb-4">
                            <article class="icontext">
                                <span class="icon icon-sm rounded-circle bg-info-light">
                                    <i class="text-info material-icons md-people"></i>
                                </span>
                                <div class="text">
                                    <h6 class="mb-1 card-title">Pengantaran Orang</h6>
                                    <span><?php echo $total_pengantaran; ?></span>
                                    <span class="text-sm">Total pengantaran orang</span>
                                </div>
                            </article>
                        </div>
                    </div>

                    <!-- Card Status Pesanan Makanan Selesai -->
                    <div class="col-lg-3">
                        <div class="card card-body mb-4">
                            <article class="icontext">
                                <span class="icon icon-sm rounded-circle bg-secondary-light">
                                    <i class="text-secondary material-icons md-check_circle"></i>
                                </span>
                                <div class="text">
                                    <h6 class="mb-1 card-title">Status Pesanan Makanan Selesai</h6>
                                    <span><?php echo $total_pesanan_selesai; ?></span>
                                    <span class="text-sm">Total pesanan selesai</span>
                                </div>
                            </article>
                        </div>
                    </div>

                    <!-- Card Status Pengantaran Orang Selesai -->
                    <div class="col-lg-3">
                        <div class="card card-body mb-4">
                            <article class="icontext">
                                <span class="icon icon-sm rounded-circle bg-secondary-light">
                                    <i class="text-secondary material-icons md-check_circle_outline"></i>
                                </span>
                                <div class="text">
                                    <h6 class="mb-1 card-title">Status Pengantaran Orang Selesai</h6>
                                    <span><?php echo $total_pengantaran_selesai; ?></span>
                                    <span class="text-sm">Total pengantaran selesai</span>
                                </div>
                            </article>
                        </div>
                    </div>

                    <!-- Card Total Harga Pesanan Selesai -->
                    <div class="col-lg-3">
                        <div class="card card-body mb-4">
                            <article class="icontext">
                                <span class="icon icon-sm rounded-circle bg-success-light">
                                    <i class="text-success material-icons md-attach_money"></i>
                                </span>
                                <div class="text">
                                    <h6 class="mb-1 card-title">Total Harga Pesanan Selesai</h6>
                                    <span><?php echo number_format($total_harga_selesai, 2, ',', '.'); ?></span>
                                    <span class="text-sm">Total harga pesanan selesai</span>
                                </div>
                            </article>
                        </div>
                    </div>

                    <!-- Card Statistik Pesanan Makanan per Driver -->
                    <div class="col-lg-6">
                        <div class="card card-body mb-4">
                            <h6 class="mb-3 card-title">Statistik Pesanan Makanan per Driver</h6>
                            <canvas id="statistikPesananChart"></canvas>
                        </div>
                    </div>

                    <!-- Card Statistik Pengantaran Orang per Driver -->
                    <div class="col-lg-6">
                        <div class="card card-body mb-4">
                            <h6 class="mb-3 card-title">Statistik Pengantaran Orang per Driver</h6>
                            <canvas id="statistikPengantaranChart"></canvas>
                        </div>
                    </div>
                </div>
            </section>
        </form>
    </main>

    <!-- Bootstrap JS and dependencies -->
    <script src="assets/js/vendors/jquery-3.6.0.min.js"></script>
    <script src="assets/js/vendors/bootstrap.bundle.min.js"></script>
    <script src="assets/js/vendors/select2.min.js"></script>
    <script src="assets/js/vendors/perfect-scrollbar.js"></script>
    <script src="assets/js/vendors/jquery.fullscreen.min.js"></script>
    <script src="assets/js/vendors/chart.js"></script>
    <!-- Main Script -->
    <script src="assets/js/main.js?v=1.1" type="text/javascript"></script>
    <script src="assets/js/custom-chart.js" type="text/javascript"></script>
    <script>
        // Data untuk grafik statistik pesanan makanan per driver
        const statistikPesananData = <?php echo json_encode($statistik_pesanan); ?>;

        // Proses data untuk grafik statistik pesanan makanan per driver
        const labelsPesanan = [...new Set(statistikPesananData.map(item => item.bulan))].sort();
        const datasetsPesanan = [];

        const driversPesanan = [...new Set(statistikPesananData.map(item => item.id_driver))];
        driversPesanan.forEach(driver => {
            const dataPesanan = labelsPesanan.map(bulan => {
                const entry = statistikPesananData.find(item => item.id_driver === driver && item.bulan === bulan);
                return entry ? entry.total_pesanan : 0;
            });
            datasetsPesanan.push({
                label: `Driver ${driver}`,
                data: dataPesanan,
                borderColor: getRandomColor(),
                fill: false
            });
        });

        // Data untuk grafik statistik pengantaran orang per driver
        const statistikPengantaranData = <?php echo json_encode($statistik_pengantaran); ?>;

        // Proses data untuk Chart.js
        const labelsPengantaran = [...new Set(statistikPengantaranData.map(item => item.bulan))].sort();
        const datasetsPengantaran = [];

        const driversPengantaran = [...new Set(statistikPengantaranData.map(item => item.id_driver))];
        driversPengantaran.forEach(driver => {
            const dataPengantaran = labelsPengantaran.map(bulan => {
                const entry = statistikPengantaranData.find(item => item.id_driver === driver && item.bulan === bulan);
                return entry ? entry.total_pengantaran : 0;
            });
            datasetsPengantaran.push({
                label: `Driver ${driver}`,
                data: dataPengantaran,
                borderColor: getRandomColor(),
                fill: false
            });
        });

        // Fungsi untuk mendapatkan warna acak
        function getRandomColor() {
            const letters = '0123456789ABCDEF';
            let color = '#';
            for (let i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }

        // Inisialisasi Chart.js untuk Statistik Pesanan Makanan per Driver
        const ctxPesanan = document.getElementById('statistikPesananChart').getContext('2d');
        new Chart(ctxPesanan, {
            type: 'line',
            data: {
                labels: labelsPesanan,
                datasets: datasetsPesanan
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Pesanan'
                        }
                    }
                }
            }
        });

        // Inisialisasi Chart.js untuk Statistik Pengantaran Orang per Driver
        const ctxPengantaran = document.getElementById('statistikPengantaranChart').getContext('2d');
        new Chart(ctxPengantaran, {
            type: 'line',
            data: {
                labels: labelsPengantaran,
                datasets: datasetsPengantaran
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Pengantaran'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
