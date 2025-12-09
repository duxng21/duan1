<?php
// revenue_report.php
// Hiển thị báo cáo doanh thu từ bookings
/** @var array $monthlyReport */
/** @var array $tourReport */

// Tính toán tổng hợp cho cards
$total_bookings = 0;
$total_guests = 0;
$total_revenue = 0;
$confirmed_revenue = 0;
$pending_revenue = 0;

foreach ($monthlyReport as $row) {
    $total_bookings += $row['total_bookings'];
    $total_guests += $row['total_guests'];
    $total_revenue += $row['total_revenue'];
    $confirmed_revenue += $row['confirmed_revenue'];
    $pending_revenue += $row['pending_revenue'];
}

require_once __DIR__ . '/../core/header.php';
require_once __DIR__ . '/../core/menu.php';
?>

<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Báo cáo doanh thu từ Booking</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="?act=dashboard">Trang chủ</a></li>
                                <li class="breadcrumb-item"><a href="#">Báo cáo</a></li>
                                <li class="breadcrumb-item active">Doanh thu</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="media d-flex">
                                <div class="media-body text-left">
                                    <h3 class="primary"><?= number_format($total_bookings) ?></h3>
                                    <span>Tổng Booking</span>
                                </div>
                                <div class="align-self-center">
                                    <i class="feather icon-book-open primary font-large-2 float-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="media d-flex">
                                <div class="media-body text-left">
                                    <h3 class="success"><?= number_format($total_guests) ?></h3>
                                    <span>Tổng Khách</span>
                                </div>
                                <div class="align-self-center">
                                    <i class="feather icon-users success font-large-2 float-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="media d-flex">
                                <div class="media-body text-left">
                                    <h3 class="warning"><?= number_format($total_revenue, 0, ',', '.') ?>đ</h3>
                                    <span>Tổng Doanh Thu</span>
                                </div>
                                <div class="align-self-center">
                                    <i class="feather icon-dollar-sign warning font-large-2 float-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="media d-flex">
                                <div class="media-body text-left">
                                    <h3 class="info"><?= number_format($confirmed_revenue, 0, ',', '.') ?>đ</h3>
                                    <span>Đã Xác Nhận</span>
                                </div>
                                <div class="align-self-center">
                                    <i class="feather icon-check-circle info font-large-2 float-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Form -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Bộ lọc</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <form method="get" class="row">
                            <input type="hidden" name="act" value="bao-cao-doanh-thu">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Từ ngày</label>
                                    <input type="date" name="from_date" class="form-control"
                                        value="<?= htmlspecialchars($_GET['from_date'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Đến ngày</label>
                                    <input type="date" name="to_date" class="form-control"
                                        value="<?= htmlspecialchars($_GET['to_date'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tour</label>
                                    <input type="text" name="tour_id" class="form-control"
                                        placeholder="ID hoặc tên tour"
                                        value="<?= htmlspecialchars($_GET['tour_id'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="feather icon-search"></i> Xem báo cáo
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Chart: Revenue by Month -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Biểu đồ doanh thu theo tháng</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <canvas id="revenueChart" height="80"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Report Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Báo cáo theo tháng</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Tháng</th>
                                                <th class="text-center">Số booking</th>
                                                <th class="text-center">Khách</th>
                                                <th class="text-right">Doanh thu</th>
                                                <th class="text-right">Đã xác nhận</th>
                                                <th class="text-right">Chờ xác nhận</th>
                                                <th class="text-right">Đã hủy</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($monthlyReport)):
                                                foreach ($monthlyReport as $row): ?>
                                                    <tr>
                                                        <td><strong><?= htmlspecialchars($row['month']) ?></strong></td>
                                                        <td class="text-center"><?= $row['total_bookings'] ?></td>
                                                        <td class="text-center"><?= $row['total_guests'] ?></td>
                                                        <td class="text-right"><span
                                                                class="badge badge-light-primary"><?= number_format($row['total_revenue'], 0, ',', '.') ?>₫</span>
                                                        </td>
                                                        <td class="text-right"><span
                                                                class="badge badge-light-success"><?= number_format($row['confirmed_revenue'], 0, ',', '.') ?>₫</span>
                                                        </td>
                                                        <td class="text-right"><span
                                                                class="badge badge-light-warning"><?= number_format($row['pending_revenue'], 0, ',', '.') ?>₫</span>
                                                        </td>
                                                        <td class="text-right"><span
                                                                class="badge badge-light-danger"><?= number_format($row['cancelled_revenue'], 0, ',', '.') ?>₫</span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; else: ?>
                                                <tr>
                                                    <td colspan="7" class="text-center">Không có dữ liệu</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tour Report & Chart -->
                <div class="row">
                    <div class="col-lg-7 col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Báo cáo theo tour</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Tour</th>
                                                    <th>Mã tour</th>
                                                    <th class="text-center">Booking</th>
                                                    <th class="text-center">Khách</th>
                                                    <th class="text-right">Doanh thu</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($tourReport)):
                                                    foreach ($tourReport as $row): ?>
                                                        <tr>
                                                            <td><strong><?= htmlspecialchars($row['tour_name']) ?></strong></td>
                                                            <td><span
                                                                    class="badge badge-light-info"><?= htmlspecialchars($row['tour_code']) ?></span>
                                                            </td>
                                                            <td class="text-center"><?= $row['total_bookings'] ?></td>
                                                            <td class="text-center"><?= $row['total_guests'] ?></td>
                                                            <td class="text-right"><span
                                                                    class="badge badge-light-success"><?= number_format($row['total_revenue'], 0, ',', '.') ?>₫</span>
                                                            </td>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; else: ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center">Không có dữ liệu</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Top 5 Tour theo doanh thu</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <canvas id="topToursChart" height="140"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- END: Content-->

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        // Revenue by Month Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: [<?php foreach ($monthlyReport as $row)
                    echo "'" . $row['month'] . "',"; ?>],
                datasets: [{
                    label: 'Tổng doanh thu',
                    data: [<?php foreach ($monthlyReport as $row)
                        echo $row['total_revenue'] . ","; ?>],
                    borderColor: 'rgb(115, 103, 240)',
                    backgroundColor: 'rgba(115, 103, 240, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Đã xác nhận',
                    data: [<?php foreach ($monthlyReport as $row)
                        echo $row['confirmed_revenue'] . ","; ?>],
                    borderColor: 'rgb(40, 199, 111)',
                    backgroundColor: 'rgba(40, 199, 111, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return context.dataset.label + ': ' + context.parsed.y.toLocaleString('vi-VN') + '₫';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return value.toLocaleString('vi-VN') + '₫';
                            }
                        }
                    }
                }
            }
        });

        // Top Tours Chart
        const topToursCtx = document.getElementById('topToursChart').getContext('2d');
        const topToursData = <?php echo json_encode(array_slice($tourReport, 0, 5)); ?>;
        const topToursChart = new Chart(topToursCtx, {
            type: 'bar',
            data: {
                labels: topToursData.map(t => t.tour_name),
                datasets: [{
                    label: 'Doanh thu',
                    data: topToursData.map(t => t.total_revenue),
                    backgroundColor: [
                        'rgba(115, 103, 240, 0.8)',
                        'rgba(40, 199, 111, 0.8)',
                        'rgba(255, 159, 67, 0.8)',
                        'rgba(0, 207, 232, 0.8)',
                        'rgba(234, 84, 85, 0.8)'
                    ],
                    borderColor: [
                        'rgb(115, 103, 240)',
                        'rgb(40, 199, 111)',
                        'rgb(255, 159, 67)',
                        'rgb(0, 207, 232)',
                        'rgb(234, 84, 85)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return 'Doanh thu: ' + context.parsed.y.toLocaleString('vi-VN') + '₫';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return value.toLocaleString('vi-VN') + '₫';
                            }
                        }
                    }
                }
            }
        });
    </script>

    <?php require_once __DIR__ . '/../core/footer.php'; ?>