<?php require_once __DIR__ . '/../core/header.php'; ?>
<?php require_once __DIR__ . '/../core/menu.php'; ?>
<?php require_once __DIR__ . '/../core/alert.php'; ?>

<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">
                            <i class="feather icon-pie-chart"></i> Báo cáo tóm tắt đoàn
                        </h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="?act=danh-sach-booking">Booking</a></li>
                                <li class="breadcrumb-item"><a
                                        href="?act=danh-sach-khach&booking_id=<?= $booking_id ?? '' ?>&schedule_id=<?= $schedule_id ?? '' ?>">Danh
                                        sách khách</a></li>
                                <li class="breadcrumb-item active">Báo cáo</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-right col-md-3 col-12">
                <a href="?act=danh-sach-khach&booking_id=<?= $booking_id ?? '' ?>&schedule_id=<?= $schedule_id ?? '' ?>"
                    class="btn btn-secondary">
                    <i class="feather icon-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>

        <div class="content-body">
            <!-- Booking/Schedule Info -->
            <?php if (isset($booking)): ?>
                <div class="card bg-gradient-primary">
                    <div class="card-body text-white">
                        <h3><i class="feather icon-briefcase"></i> Thông tin Booking</h3>
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <h5 class="text-white">Mã Booking</h5>
                                <p class="font-large-1">#<?= $booking['booking_id'] ?></p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="text-white">Tour</h5>
                                <p class="font-large-1"><?= htmlspecialchars($booking['tour_name']) ?></p>
                            </div>
                            <div class="col-md-3">
                                <h5 class="text-white">Tổng tiền</h5>
                                <p class="font-large-1"><?= number_format($booking['total_amount'], 0, ',', '.') ?> đ</p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Summary Statistics Cards -->
            <div class="row">
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="align-self-center">
                                        <i class="feather icon-users primary font-large-2 float-left"></i>
                                    </div>
                                    <div class="media-body text-right">
                                        <h3 class="primary"><?= $summary['total_guests'] ?? 0 ?></h3>
                                        <span>Tổng số khách</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="align-self-center">
                                        <i class="feather icon-check-circle success font-large-2 float-left"></i>
                                    </div>
                                    <div class="media-body text-right">
                                        <h3 class="success"><?= $summary['checked_in'] ?? 0 ?></h3>
                                        <span>Đã check-in</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="align-self-center">
                                        <i class="feather icon-x-circle danger font-large-2 float-left"></i>
                                    </div>
                                    <div class="media-body text-right">
                                        <h3 class="danger"><?= $summary['no_show'] ?? 0 ?></h3>
                                        <span>Vắng mặt</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="align-self-center">
                                        <i class="feather icon-home warning font-large-2 float-left"></i>
                                    </div>
                                    <div class="media-body text-right">
                                        <h3 class="warning"><?= $summary['room_assigned'] ?? 0 ?></h3>
                                        <span>Đã phân phòng</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Statistics -->
            <div class="row">
                <!-- Age Group -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"><i class="feather icon-pie-chart"></i> Phân loại theo độ tuổi</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="ageChart" height="200"></canvas>
                            <div class="mt-2 text-center">
                                <p class="mb-0">
                                    <span class="badge badge-primary mr-1">Người lớn:
                                        <?= $summary['adult_count'] ?? 0 ?></span>
                                    <span class="badge badge-info">Trẻ em: <?= $summary['child_count'] ?? 0 ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gender -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"><i class="feather icon-users"></i> Phân loại theo giới tính</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="genderChart" height="200"></canvas>
                            <div class="mt-2 text-center">
                                <p class="mb-0">
                                    <span class="badge badge-primary mr-1">Nam:
                                        <?= $summary['male_count'] ?? 0 ?></span>
                                    <span class="badge badge-danger">Nữ: <?= $summary['female_count'] ?? 0 ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Check-in Status -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"><i class="feather icon-check-square"></i> Tình trạng check-in</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="checkinChart" height="200"></canvas>
                            <div class="mt-2 text-center">
                                <p class="mb-0">
                                    <span class="badge badge-success mr-1">Đã đến:
                                        <?= $summary['checked_in'] ?? 0 ?></span>
                                    <span class="badge badge-warning mr-1">Chưa đến:
                                        <?= ($summary['total_guests'] - $summary['checked_in'] - $summary['no_show']) ?? 0 ?></span>
                                    <span class="badge badge-danger">Vắng: <?= $summary['no_show'] ?? 0 ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Bars -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><i class="feather icon-bar-chart-2"></i> Tiến độ tổng hợp</h4>
                </div>
                <div class="card-body">
                    <h6>Tỷ lệ check-in:
                        <?= $summary['total_guests'] > 0 ? round(($summary['checked_in'] / $summary['total_guests']) * 100, 1) : 0 ?>%
                    </h6>
                    <div class="progress mb-3">
                        <div class="progress-bar bg-success" role="progressbar"
                            style="width: <?= $summary['total_guests'] > 0 ? ($summary['checked_in'] / $summary['total_guests']) * 100 : 0 ?>%"
                            aria-valuenow="<?= $summary['checked_in'] ?>" aria-valuemin="0"
                            aria-valuemax="<?= $summary['total_guests'] ?>">
                            <?= $summary['checked_in'] ?>/<?= $summary['total_guests'] ?>
                        </div>
                    </div>

                    <h6>Tỷ lệ phân phòng:
                        <?= $summary['total_guests'] > 0 ? round(($summary['room_assigned'] / $summary['total_guests']) * 100, 1) : 0 ?>%
                    </h6>
                    <div class="progress mb-3">
                        <div class="progress-bar bg-warning" role="progressbar"
                            style="width: <?= $summary['total_guests'] > 0 ? ($summary['room_assigned'] / $summary['total_guests']) * 100 : 0 ?>%"
                            aria-valuenow="<?= $summary['room_assigned'] ?>" aria-valuemin="0"
                            aria-valuemax="<?= $summary['total_guests'] ?>">
                            <?= $summary['room_assigned'] ?>/<?= $summary['total_guests'] ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- END: Content-->

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Age Chart
    var ageCtx = document.getElementById('ageChart').getContext('2d');
    var ageChart = new Chart(ageCtx, {
        type: 'doughnut',
        data: {
            labels: ['Người lớn', 'Trẻ em'],
            datasets: [{
                data: [<?= $summary['adult_count'] ?? 0 ?>, <?= $summary['child_count'] ?? 0 ?>],
                backgroundColor: ['#7367F0', '#00CFE8']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Gender Chart
    var genderCtx = document.getElementById('genderChart').getContext('2d');
    var genderChart = new Chart(genderCtx, {
        type: 'doughnut',
        data: {
            labels: ['Nam', 'Nữ'],
            datasets: [{
                data: [<?= $summary['male_count'] ?? 0 ?>, <?= $summary['female_count'] ?? 0 ?>],
                backgroundColor: ['#7367F0', '#EA5455']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Check-in Chart
    var checkinCtx = document.getElementById('checkinChart').getContext('2d');
    var checkinChart = new Chart(checkinCtx, {
        type: 'doughnut',
        data: {
            labels: ['Đã check-in', 'Chưa đến', 'Vắng mặt'],
            datasets: [{
                data: [
                    <?= $summary['checked_in'] ?? 0 ?>,
                    <?= ($summary['total_guests'] - $summary['checked_in'] - $summary['no_show']) ?? 0 ?>,
                    <?= $summary['no_show'] ?? 0 ?>
                ],
                backgroundColor: ['#28C76F', '#FF9F43', '#EA5455']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>

<?php require_once __DIR__ . '/../core/footer.php'; ?>