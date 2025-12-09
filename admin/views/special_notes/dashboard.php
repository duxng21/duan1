<?php
$title = 'Dashboard Ghi Chú Đặc Biệt';
require_once './views/core/header.php';
require_once './views/core/menu.php';
?>

<style>
.dashboard-card {
    transition: transform 0.2s;
}
.dashboard-card:hover {
    transform: translateY(-2px);
}
.urgent-item {
    border-left: 4px solid #ff4757;
    background-color: #fff5f5;
}
.notification-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #ff4757;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.chart-container {
    position: relative;
    height: 300px;
}
</style>

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
                            <i data-feather="clipboard"></i>
                            Dashboard Ghi Chú Đặc Biệt
                        </h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="?act=/">Trang chủ</a></li>
                                <li class="breadcrumb-item active">Dashboard ghi chú đặc biệt</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                <div class="form-group breadcrum-right">
                    <a href="?act=quan-ly-ghi-chu-dac-biet" class="btn btn-primary btn-sm">
                        <i data-feather="settings"></i> Quản lý
                    </a>
                </div>
            </div>
        </div>

        <div class="content-body">
            <?php require_once './views/core/alert.php'; ?>

            <!-- Overview Statistics -->
            <div class="row">
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card dashboard-card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body text-left">
                                        <h3 class="primary"><?= $overallStats['total_notes'] ?? 0 ?></h3>
                                        <span>Tổng ghi chú (30 ngày)</span>
                                    </div>
                                    <div class="align-self-center">
                                        <i data-feather="clipboard" class="primary font-large-2 float-right"></i>
                                    </div>
                                </div>
                                <div class="progress progress-sm mt-1 mb-0">
                                    <div class="progress-bar bg-primary" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card dashboard-card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body text-left">
                                        <h3 class="warning"><?= $overallStats['pending_notes'] ?? 0 ?></h3>
                                        <span>Chờ xử lý</span>
                                        <?php if (($overallStats['pending_notes'] ?? 0) > 0): ?>
                                            <div class="notification-badge"><?= $overallStats['pending_notes'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="align-self-center">
                                        <i data-feather="clock" class="warning font-large-2 float-right"></i>
                                    </div>
                                </div>
                                <div class="progress progress-sm mt-1 mb-0">
                                    <div class="progress-bar bg-warning" 
                                         style="width: <?= $overallStats['total_notes'] > 0 ? round(($overallStats['pending_notes'] ?? 0) * 100 / $overallStats['total_notes']) : 0 ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card dashboard-card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body text-left">
                                        <h3 class="danger"><?= $overallStats['high_priority'] ?? 0 ?></h3>
                                        <span>Ưu tiên cao</span>
                                    </div>
                                    <div class="align-self-center">
                                        <i data-feather="alert-triangle" class="danger font-large-2 float-right"></i>
                                    </div>
                                </div>
                                <div class="progress progress-sm mt-1 mb-0">
                                    <div class="progress-bar bg-danger" 
                                         style="width: <?= $overallStats['total_notes'] > 0 ? round(($overallStats['high_priority'] ?? 0) * 100 / $overallStats['total_notes']) : 0 ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card dashboard-card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body text-left">
                                        <h3 class="success"><?= number_format($overallStats['avg_resolution_hours'] ?? 0, 1) ?>h</h3>
                                        <span>Thời gian xử lý TB</span>
                                    </div>
                                    <div class="align-self-center">
                                        <i data-feather="trending-up" class="success font-large-2 float-right"></i>
                                    </div>
                                </div>
                                <div class="progress progress-sm mt-1 mb-0">
                                    <div class="progress-bar bg-success" style="width: 85%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i data-feather="zap"></i>
                                Thao tác nhanh
                            </h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-3 col-md-6 col-12 mb-2">
                                        <a href="?act=quan-ly-ghi-chu-dac-biet" class="btn btn-outline-primary btn-block">
                                            <i data-feather="plus"></i> Thêm ghi chú mới
                                        </a>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-12 mb-2">
                                        <a href="?act=danh-sach-lich-khoi-hanh" class="btn btn-outline-info btn-block">
                                            <i data-feather="calendar"></i> Xem lịch khởi hành
                                        </a>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-12 mb-2">
                                        <a href="?act=danh-sach-booking" class="btn btn-outline-success btn-block">
                                            <i data-feather="users"></i> Quản lý booking
                                        </a>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-12 mb-2">
                                        <button type="button" class="btn btn-outline-warning btn-block" onclick="refreshNotifications()">
                                            <i data-feather="refresh-cw"></i> Làm mới thông báo
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Urgent Notes & Notifications -->
            <div class="row">
                <div class="col-lg-8 col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">
                                <i data-feather="alert-circle"></i>
                                Ghi chú ưu tiên cao cần xử lý
                            </h4>
                            <span class="badge badge-danger"><?= count($urgentNotes) ?> mục</span>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <?php if (empty($urgentNotes)): ?>
                                    <div class="text-center py-4">
                                        <i data-feather="check-circle" class="font-large-2 text-success"></i>
                                        <p class="text-muted mt-2">Tuyệt vời! Không có ghi chú ưu tiên cao nào cần xử lý</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($urgentNotes as $note): ?>
                                        <div class="urgent-item p-3 mb-3 rounded">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">
                                                        <?php
                                                        $type_icons = [
                                                            'Dietary' => '🍽️',
                                                            'Medical' => '💊',
                                                            'Allergy' => '⚠️',
                                                            'Mobility' => '♿',
                                                            'Other' => '📝'
                                                        ];
                                                        echo $type_icons[$note['note_type']] ?? '📝';
                                                        ?>
                                                        <?= htmlspecialchars($note['full_name']) ?>
                                                        
                                                        <span class="badge badge-danger badge-sm ml-1">KHẨN CẤP</span>
                                                    </h6>
                                                    <p class="mb-2 text-muted"><?= htmlspecialchars(substr($note['note_content'], 0, 100)) ?>...</p>
                                                    
                                                    <div class="row small text-muted">
                                                        <div class="col-md-6">
                                                            <i data-feather="calendar"></i> <?= htmlspecialchars($note['tour_name']) ?>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <i data-feather="clock"></i> Chờ <?= $note['hours_pending'] ?> giờ
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <a href="?act=ghi-chu-dac-biet&schedule_id=<?= $note['schedule_id'] ?? '' ?>" 
                                                       class="btn btn-sm btn-danger">
                                                        <i data-feather="eye"></i> Xử lý
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    
                                    <div class="text-center">
                                        <a href="?act=quan-ly-ghi-chu-dac-biet" class="btn btn-outline-danger">
                                            <i data-feather="list"></i> Xem tất cả
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i data-feather="bell"></i>
                                Thông báo chưa đọc
                                <?php if (count($unreadNotifications) > 0): ?>
                                    <span class="badge badge-warning ml-1"><?= count($unreadNotifications) ?></span>
                                <?php endif; ?>
                            </h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <?php if (empty($unreadNotifications)): ?>
                                    <div class="text-center py-3">
                                        <i data-feather="check" class="font-large-1 text-success"></i>
                                        <p class="text-muted mt-1">Không có thông báo mới</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($unreadNotifications as $notification): ?>
                                        <div class="media mb-3 p-2 border-bottom">
                                            <div class="avatar bg-light-<?= $notification['priority_level'] == 'High' ? 'danger' : 'warning' ?> mr-1">
                                                <div class="avatar-content">
                                                    <?php
                                                    $icons = [
                                                        'Dietary' => '🍽️',
                                                        'Medical' => '💊',
                                                        'Allergy' => '⚠️',
                                                        'Mobility' => '♿',
                                                        'Other' => '📝'
                                                    ];
                                                    echo $icons[$notification['note_type']] ?? '📝';
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="media-body">
                                                <h6 class="media-heading mb-0">
                                                    <small><?= htmlspecialchars($notification['guest_name']) ?></small>
                                                </h6>
                                                <small class="text-muted">
                                                    <?= htmlspecialchars(substr($notification['note_content'], 0, 50)) ?>...
                                                </small>
                                                <br>
                                                <small class="text-primary">
                                                    <?= date('d/m H:i', strtotime($notification['sent_at'])) ?>
                                                </small>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    
                                    <div class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="markAllAsRead()">
                                            <i data-feather="check-circle"></i> Đánh dấu đã đọc tất cả
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Efficiency Chart -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i data-feather="trending-up"></i>
                                Hiệu quả xử lý theo tháng
                            </h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <?php if (!empty($monthlyEfficiency)): ?>
                                    <div class="chart-container">
                                        <canvas id="efficiencyChart"></canvas>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i data-feather="bar-chart" class="font-large-2 text-muted"></i>
                                        <p class="text-muted mt-2">Chưa có dữ liệu để hiển thị biểu đồ</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Key Performance Indicators -->
            <div class="row">
                <div class="col-md-4 col-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body text-center">
                                <div class="avatar bg-light-info mx-auto mb-1">
                                    <div class="avatar-content">
                                        <i data-feather="users" class="avatar-icon font-medium-3"></i>
                                    </div>
                                </div>
                                <h5><?= $overallStats['affected_bookings'] ?? 0 ?></h5>
                                <p class="card-text">Booking có yêu cầu</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body text-center">
                                <div class="avatar bg-light-success mx-auto mb-1">
                                    <div class="avatar-content">
                                        <i data-feather="user-check" class="avatar-icon font-medium-3"></i>
                                    </div>
                                </div>
                                <h5><?= $overallStats['affected_guests'] ?? 0 ?></h5>
                                <p class="card-text">Khách có yêu cầu đặc biệt</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body text-center">
                                <div class="avatar bg-light-primary mx-auto mb-1">
                                    <div class="avatar-content">
                                        <i data-feather="percent" class="avatar-icon font-medium-3"></i>
                                    </div>
                                </div>
                                <h5>
                                    <?= $overallStats['total_notes'] > 0 ? 
                                        number_format(($overallStats['resolved_notes'] ?? 0) * 100 / $overallStats['total_notes'], 1) : 0 ?>%
                                </h5>
                                <p class="card-text">Tỷ lệ hoàn thành</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: Content-->

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Auto refresh notifications every 30 seconds
setInterval(() => {
    fetch('?act=dem-thong-bao-chua-doc')
        .then(response => response.json())
        .then(data => {
            if (data.count > 0) {
                // Update notification badges
                document.querySelectorAll('.notification-badge').forEach(badge => {
                    badge.textContent = data.count;
                    badge.style.display = 'flex';
                });
            }
        })
        .catch(error => console.log('Error checking notifications:', error));
}, 30000);

// Functions for notifications
function refreshNotifications() {
    location.reload();
}

function markAllAsRead() {
    // Implementation for marking all notifications as read
    if (confirm('Đánh dấu tất cả thông báo là đã đọc?')) {
        // Send AJAX request to mark all as read
        fetch('?act=danh-dau-da-doc', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'mark_all=1'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

// Initialize efficiency chart
<?php if (!empty($monthlyEfficiency)): ?>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('efficiencyChart').getContext('2d');
    
    const chartData = {
        labels: [<?php echo implode(',', array_map(function($item) { return '"' . $item['month'] . '"'; }, $monthlyEfficiency)); ?>],
        datasets: [{
            label: 'Tỷ lệ hoàn thành (%)',
            data: [<?php echo implode(',', array_map(function($item) { return $item['resolution_rate']; }, $monthlyEfficiency)); ?>],
            borderColor: '#7367f0',
            backgroundColor: 'rgba(115, 103, 240, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }, {
            label: 'Thời gian xử lý TB (giờ)',
            data: [<?php echo implode(',', array_map(function($item) { return $item['avg_resolution_hours'] ?? 0; }, $monthlyEfficiency)); ?>],
            borderColor: '#ff6b6b',
            backgroundColor: 'rgba(255, 107, 107, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            yAxisID: 'y1'
        }]
    };

    new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    title: {
                        display: true,
                        text: 'Tỷ lệ hoàn thành (%)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Thời gian (giờ)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    }
                }
            }
        }
    });
});
<?php endif; ?>
</script>

<?php require_once './views/core/footer.php'; ?>