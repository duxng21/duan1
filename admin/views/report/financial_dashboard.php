<?php require_once __DIR__ . '/../core/header.php'; ?>
<?php require_once __DIR__ . '/../core/menu.php'; ?>

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">
                            <i class="feather icon-bar-chart-2"></i> Dashboard Tài Chính
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <!-- Filter -->
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="?act=financial-dashboard" class="form-inline">
                        <input type="hidden" name="act" value="financial-dashboard">
                        <div class="form-group mr-2">
                            <label class="mr-2">Từ ngày:</label>
                            <input type="date" name="from_date" class="form-control"
                                value="<?= $filters['from_date'] ?>">
                        </div>
                        <div class="form-group mr-2">
                            <label class="mr-2">Đến ngày:</label>
                            <input type="date" name="to_date" class="form-control" value="<?= $filters['to_date'] ?>">
                        </div>
                        <button type="submit" class="btn btn-primary mr-2">
                            <i class="feather icon-filter"></i> Lọc
                        </button>
                        <a href="?act=export-dashboard&from_date=<?= $filters['from_date'] ?>&to_date=<?= $filters['to_date'] ?>&format=csv"
                            class="btn btn-success">
                            <i class="feather icon-download"></i> Export CSV
                        </a>
                    </form>
                </div>
            </div>

            <!-- KPIs -->
            <div class="row">
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="align-self-center">
                                        <i class="feather icon-dollar-sign text-success font-large-2 float-left"></i>
                                    </div>
                                    <div class="media-body text-right">
                                        <h3 class="text-success">
                                            <?= number_format($kpis['total_revenue'], 0, ',', '.') ?></h3>
                                        <span>Tổng doanh thu (VNĐ)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="align-self-center">
                                        <i class="feather icon-trending-up text-info font-large-2 float-left"></i>
                                    </div>
                                    <div class="media-body text-right">
                                        <h3 class="text-info"><?= number_format($kpis['total_profit'], 0, ',', '.') ?>
                                        </h3>
                                        <span>Lợi nhuận (VNĐ)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="align-self-center">
                                        <i class="feather icon-percent text-warning font-large-2 float-left"></i>
                                    </div>
                                    <div class="media-body text-right">
                                        <h3 class="text-warning"><?= number_format($kpis['profit_margin'], 1) ?>%</h3>
                                        <span>Tỷ suất lợi nhuận</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="align-self-center">
                                        <i class="feather icon-users text-primary font-large-2 float-left"></i>
                                    </div>
                                    <div class="media-body text-right">
                                        <h3 class="text-primary">
                                            <?= number_format($kpis['total_guests'], 0, ',', '.') ?></h3>
                                        <span>Tổng số khách</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secondary KPIs -->
            <div class="row">
                <div class="col-md-3 col-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <h4><?= $kpis['total_schedules'] ?></h4>
                            <p class="text-muted mb-0">Lịch khởi hành</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <h4><?= $kpis['total_bookings'] ?></h4>
                            <p class="text-muted mb-0">Booking</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <h4><?= $kpis['total_customers'] ?></h4>
                            <p class="text-muted mb-0">Khách hàng</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <h4><?= number_format($kpis['avg_booking_value'], 0, ',', '.') ?></h4>
                            <p class="text-muted mb-0">Giá trị TB/Booking</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Monthly Revenue Chart -->
                <div class="col-md-8 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Doanh thu theo tháng (<?= date('Y') ?>)</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="monthlyRevenueChart" height="100"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Cost Breakdown -->
                <div class="col-md-4 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Cơ cấu chi phí</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="costBreakdownChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Tours -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Top 10 Tour theo Doanh thu</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Tour</th>
                                            <th>Danh mục</th>
                                            <th class="text-right">Số đoàn</th>
                                            <th class="text-right">Booking</th>
                                            <th class="text-right">Doanh thu</th>
                                            <th class="text-right">Chi phí</th>
                                            <th class="text-right">Lợi nhuận</th>
                                            <th class="text-center">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($topTours as $index => $tour): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><strong><?= htmlspecialchars($tour['tour_name']) ?></strong></td>
                                                <td><?= htmlspecialchars($tour['category_name']) ?></td>
                                                <td class="text-right"><?= $tour['total_schedules'] ?></td>
                                                <td class="text-right"><?= $tour['total_bookings'] ?></td>
                                                <td class="text-right text-success">
                                                    <?= number_format($tour['total_revenue'], 0, ',', '.') ?></td>
                                                <td class="text-right text-danger">
                                                    <?= number_format($tour['total_cost'], 0, ',', '.') ?></td>
                                                <td class="text-right">
                                                    <strong><?= number_format($tour['profit'], 0, ',', '.') ?></strong></td>
                                                <td class="text-center">
                                                    <a href="?act=tour-detail-report&tour_id=<?= $tour['tour_id'] ?>"
                                                        class="btn btn-sm btn-info">
                                                        <i class="feather icon-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue by Category -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Doanh thu theo Danh mục</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="categoryRevenueChart" height="60"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Monthly Revenue Chart
    const monthlyCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
    const monthlyData = <?= json_encode($monthlyRevenue) ?>;
    const monthLabels = monthlyData.map(m => 'Tháng ' + m.month);
    const revenueData = monthlyData.map(m => parseFloat(m.total_revenue));
    const profitData = monthlyData.map(m => parseFloat(m.profit));

    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Doanh thu',
                data: revenueData,
                borderColor: '#28C76F',
                backgroundColor: 'rgba(40, 199, 111, 0.1)',
                tension: 0.4
            }, {
                label: 'Lợi nhuận',
                data: profitData,
                borderColor: '#00CFE8',
                backgroundColor: 'rgba(0, 207, 232, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Cost Breakdown Chart
    const costCtx = document.getElementById('costBreakdownChart').getContext('2d');
    const costData = <?= json_encode($costBreakdown) ?>;
    new Chart(costCtx, {
        type: 'doughnut',
        data: {
            labels: costData.map(c => c.cost_type),
            datasets: [{
                data: costData.map(c => parseFloat(c.total_cost)),
                backgroundColor: ['#FF9F43', '#7367F0']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true
        }
    });

    // Category Revenue Chart
    const catCtx = document.getElementById('categoryRevenueChart').getContext('2d');
    const catData = <?= json_encode($revenueByCategory) ?>;
    new Chart(catCtx, {
        type: 'bar',
        data: {
            labels: catData.map(c => c.category_name),
            datasets: [{
                label: 'Doanh thu',
                data: catData.map(c => parseFloat(c.total_revenue)),
                backgroundColor: '#28C76F'
            }, {
                label: 'Lợi nhuận',
                data: catData.map(c => parseFloat(c.profit)),
                backgroundColor: '#00CFE8'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>

<?php require_once __DIR__ . '/../core/footer.php'; ?>