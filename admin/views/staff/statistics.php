<?php require_once __DIR__ . '/../core/header.php'; ?>
<?php require_once __DIR__ . '/../core/menu.php'; ?>
<?php require_once __DIR__ . '/../core/alert.php'; ?>

<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">
                            <i class="feather icon-bar-chart-2"></i> Thống kê và phân tích HDV
                        </h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="?act=danh-sach-nhan-su">Danh sách HDV</a></li>
                                <li class="breadcrumb-item active">Thống kê</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <!-- === Use Case 1 Bước 5: Thống kê tổng quan === -->
            <section id="statistics-overview">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Tổng quan nhân sự</h4>
                                <div>
                                    <a href="?act=xuat-excel-nhan-su" class="btn btn-success btn-sm">
                                        <i class="feather icon-download"></i> Xuất Excel
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php foreach ($overview as $stat): ?>
                                        <div class="col-xl-3 col-md-6 col-12">
                                            <div class="card text-center">
                                                <div class="card-body">
                                                    <div class="avatar bg-rgba-primary p-50 m-0 mb-1 mx-auto">
                                                        <div class="avatar-content">
                                                            <i class="feather icon-users text-primary font-medium-5"></i>
                                                        </div>
                                                    </div>
                                                    <h2 class="font-weight-bolder"><?= $stat['total'] ?></h2>
                                                    <p class="card-text"><?= $stat['staff_type'] ?></p>
                                                    <p class="text-success">
                                                        <small>Hoạt động: <?= $stat['active_count'] ?></small>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- === Use Case 1 Bước 5a: Phân nhóm HDV === -->
            <section id="category-statistics">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Phân loại hướng dẫn viên</h4>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($byCategory)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Phân loại</th>
                                                    <th>Tổng số</th>
                                                    <th>Đang hoạt động</th>
                                                    <th>Tổng số tour</th>
                                                    <th>Đánh giá TB</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($byCategory as $cat): ?>
                                                    <tr>
                                                        <td>
                                                            <strong>
                                                                <?= $cat['staff_category'] == 'Domestic' ? 'Nội địa' :
                                                                    ($cat['staff_category'] == 'International' ? 'Quốc tế' : 'Cả hai') ?>
                                                            </strong>
                                                        </td>
                                                        <td><?= $cat['total'] ?></td>
                                                        <td><span class="badge badge-success"><?= $cat['active'] ?></span></td>
                                                        <td><strong class="text-primary"><?= $cat['total_tours'] ?></strong>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-warning">
                                                                <i class="feather icon-star"></i>
                                                                <?= number_format($cat['avg_rating'], 1) ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted">Chưa có dữ liệu phân loại.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- === Use Case 1 Bước 5b: Top HDV theo hiệu suất === -->
            <section id="top-guides">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <i class="feather icon-award"></i> Top 10 HDV xuất sắc
                                </h4>
                                <p class="card-text">Xếp hạng theo số tour và đánh giá</p>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($topGuides)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Họ tên</th>
                                                    <th>Phân loại</th>
                                                    <th>Ngôn ngữ</th>
                                                    <th>Số tour</th>
                                                    <th>Kinh nghiệm</th>
                                                    <th>Đánh giá</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $rank = 1;
                                                foreach ($topGuides as $guide): ?>
                                                    <tr>
                                                        <td>
                                                            <span
                                                                class="badge badge-pill <?= $rank <= 3 ? 'badge-warning' : 'badge-light' ?>">
                                                                <?= $rank ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="?act=chi-tiet-nhan-su&id=<?= $guide['staff_id'] ?>">
                                                                <strong><?= htmlspecialchars($guide['full_name']) ?></strong>
                                                            </a>
                                                        </td>
                                                        <td><?= $guide['staff_category'] == 'Domestic' ? 'Nội địa' :
                                                            ($guide['staff_category'] == 'International' ? 'Quốc tế' : 'Cả hai') ?>
                                                        </td>
                                                        <td><?= htmlspecialchars($guide['languages'] ?? 'N/A') ?></td>
                                                        <td><strong class="text-primary"><?= $guide['total_tours'] ?></strong>
                                                        </td>
                                                        <td><?= $guide['experience_years'] ?> năm</td>
                                                        <td>
                                                            <span class="badge badge-success">
                                                                <i class="feather icon-star"></i>
                                                                <?= number_format($guide['performance_rating'], 1) ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <?php $rank++; endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted">Chưa có dữ liệu đánh giá HDV.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- === Use Case 1 Bước 5c: Biểu đồ thống kê theo tháng === -->
            <section id="monthly-chart">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Thống kê theo tháng (<?= $_GET['year'] ?? date('Y') ?>)</h4>
                                <form method="GET" action="" class="d-inline">
                                    <input type="hidden" name="act" value="thong-ke-nhan-su">
                                    <select name="year" class="form-control d-inline-block" style="width: auto;"
                                        onchange="this.form.submit()">
                                        <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                                            <option value="<?= $y ?>" <?= ($_GET['year'] ?? date('Y')) == $y ? 'selected' : '' ?>><?= $y ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </form>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($monthlyStats)): ?>
                                    <canvas id="monthlyChart" height="100"></canvas>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function () {
                                            var ctx = document.getElementById('monthlyChart').getContext('2d');
                                            var monthlyData = <?= json_encode($monthlyStats) ?>;

                                            var labels = monthlyData.map(function (item) {
                                                return 'Tháng ' + item.month;
                                            });
                                            var tourData = monthlyData.map(function (item) {
                                                return item.total_tours || 0;
                                            });
                                            var guideData = monthlyData.map(function (item) {
                                                return item.active_guides || 0;
                                            });
                                            var ratingData = monthlyData.map(function (item) {
                                                return item.avg_rating || 0;
                                            });

                                            var chart = new Chart(ctx, {
                                                type: 'line',
                                                data: {
                                                    labels: labels,
                                                    datasets: [{
                                                        label: 'Số tour',
                                                        data: tourData,
                                                        borderColor: 'rgb(75, 192, 192)',
                                                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                                        yAxisID: 'y'
                                                    }, {
                                                        label: 'Số HDV hoạt động',
                                                        data: guideData,
                                                        borderColor: 'rgb(255, 99, 132)',
                                                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                                        yAxisID: 'y'
                                                    }, {
                                                        label: 'Đánh giá TB',
                                                        data: ratingData,
                                                        borderColor: 'rgb(255, 205, 86)',
                                                        backgroundColor: 'rgba(255, 205, 86, 0.2)',
                                                        yAxisID: 'y1'
                                                    }]
                                                },
                                                options: {
                                                    responsive: true,
                                                    interaction: {
                                                        mode: 'index',
                                                        intersect: false,
                                                    },
                                                    scales: {
                                                        y: {
                                                            type: 'linear',
                                                            display: true,
                                                            position: 'left',
                                                            title: {
                                                                display: true,
                                                                text: 'Số lượng'
                                                            }
                                                        },
                                                        y1: {
                                                            type: 'linear',
                                                            display: true,
                                                            position: 'right',
                                                            title: {
                                                                display: true,
                                                                text: 'Đánh giá (0-5)'
                                                            },
                                                            max: 5,
                                                            grid: {
                                                                drawOnChartArea: false
                                                            }
                                                        }
                                                    }
                                                }
                                            });
                                        });
                                    </script>
                                <?php else: ?>
                                    <p class="text-muted">Chưa có dữ liệu thống kê theo tháng.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Thống kê theo ngôn ngữ -->
            <section id="language-stats">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Thống kê theo ngôn ngữ</h4>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($languageStats)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Ngôn ngữ</th>
                                                    <th>Số HDV</th>
                                                    <th>Đánh giá TB</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($languageStats as $lang): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($lang['languages']) ?></td>
                                                        <td><span class="badge badge-primary"><?= $lang['total'] ?></span></td>
                                                        <td>
                                                            <span class="badge badge-warning">
                                                                <?= number_format($lang['avg_rating'], 1) ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted">Chưa có dữ liệu ngôn ngữ.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php require_once __DIR__ . '/../core/footer.php'; ?>