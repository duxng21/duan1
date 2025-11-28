<?php
$title = 'Báo Cáo Yêu Cầu Đặc Biệt';
require_once './views/core/header.php';
require_once './views/core/menu.php';
?>

<style>
.priority-high { color: #ff4757; font-weight: bold; }
.priority-medium { color: #ffa502; font-weight: bold; }
.priority-low { color: #3742fa; font-weight: bold; }
.note-type-badge {
    font-size: 1.2rem;
    margin-right: 0.5rem;
}
.requirements-summary {
    max-width: 400px;
    word-wrap: break-word;
}
.guest-card {
    border-left: 4px solid #ddd;
    margin-bottom: 1rem;
}
.guest-card.high-priority {
    border-left-color: #ff4757;
    background-color: #fff5f5;
}
.guest-card.medium-priority {
    border-left-color: #ffa502;
    background-color: #fff8f0;
}
.guest-card.low-priority {
    border-left-color: #3742fa;
    background-color: #f0f0ff;
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
                            Báo Cáo Yêu Cầu Đặc Biệt
                        </h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="?act=/">Trang chủ</a></li>
                                <li class="breadcrumb-item"><a href="?act=danh-sach-lich-khoi-hanh">Lịch khởi hành</a></li>
                                <li class="breadcrumb-item active">Báo cáo yêu cầu đặc biệt</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                <div class="form-group breadcrum-right">
                    <div class="dropdown">
                        <button class="btn-icon btn btn-primary btn-round btn-sm dropdown-toggle" type="button" 
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i data-feather="grid"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="?act=in-yeu-cau-dac-biet&schedule_id=<?= $schedule_id ?>">
                                <i data-feather="printer"></i> In báo cáo
                            </a>
                            <a class="dropdown-item" href="?act=xuat-pdf-yeu-cau-dac-biet&schedule_id=<?= $schedule_id ?>">
                                <i data-feather="download"></i> Xuất PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <?php require_once './views/core/alert.php'; ?>

            <!-- Schedule Information -->
            <?php if (isset($schedule) && $schedule): ?>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i data-feather="calendar"></i>
                                Thông tin lịch khởi hành
                            </h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <p><strong>Tour:</strong> <?= htmlspecialchars($schedule['tour_name']) ?></p>
                                        <p><strong>Mã tour:</strong> <?= htmlspecialchars($schedule['code']) ?></p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Ngày khởi hành:</strong> <?= date('d/m/Y', strtotime($schedule['departure_date'])) ?></p>
                                        <p><strong>Số ngày:</strong> <?= $schedule['duration_days'] ?> ngày</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>HDV:</strong> <?= htmlspecialchars($schedule['guide_names'] ?? 'Chưa phân công') ?></p>
                                        <p><strong>Tổng khách:</strong> <?= $schedule['total_guests'] ?? 0 ?> người</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Statistics Overview -->
            <div class="row">
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body text-left">
                                        <h3 class="danger"><?= $statistics['total_notes'] ?? 0 ?></h3>
                                        <span>Tổng yêu cầu</span>
                                    </div>
                                    <div class="align-self-center">
                                        <i data-feather="clipboard" class="danger font-large-2 float-right"></i>
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
                                    <div class="media-body text-left">
                                        <h3 class="warning"><?= $statistics['high_priority'] ?? 0 ?></h3>
                                        <span>Ưu tiên cao</span>
                                    </div>
                                    <div class="align-self-center">
                                        <i data-feather="alert-triangle" class="warning font-large-2 float-right"></i>
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
                                    <div class="media-body text-left">
                                        <h3 class="success"><?= $statistics['resolved'] ?? 0 ?></h3>
                                        <span>Đã hoàn thành</span>
                                    </div>
                                    <div class="align-self-center">
                                        <i data-feather="check-circle" class="success font-large-2 float-right"></i>
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
                                    <div class="media-body text-left">
                                        <h3 class="info"><?= $statistics['pending'] ?? 0 ?></h3>
                                        <span>Chờ xử lý</span>
                                    </div>
                                    <div class="align-self-center">
                                        <i data-feather="clock" class="info font-large-2 float-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Service Categories Chart -->
            <div class="row">
                <div class="col-lg-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Phân loại dịch vụ đặc biệt</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>🍽️ Ăn uống</span>
                                    <span><?= $statistics['dietary'] ?? 0 ?> yêu cầu</span>
                                </div>
                                <div class="progress progress-sm mb-2">
                                    <div class="progress-bar bg-primary" style="width: <?= $statistics['total_notes'] > 0 ? round(($statistics['dietary'] ?? 0) * 100 / $statistics['total_notes'], 1) : 0 ?>%"></div>
                                </div>

                                <div class="d-flex justify-content-between mb-1">
                                    <span>💊 Y tế</span>
                                    <span><?= $statistics['medical'] ?? 0 ?> yêu cầu</span>
                                </div>
                                <div class="progress progress-sm mb-2">
                                    <div class="progress-bar bg-warning" style="width: <?= $statistics['total_notes'] > 0 ? round(($statistics['medical'] ?? 0) * 100 / $statistics['total_notes'], 1) : 0 ?>%"></div>
                                </div>

                                <div class="d-flex justify-content-between mb-1">
                                    <span>⚠️ Dị ứng</span>
                                    <span><?= $statistics['allergy'] ?? 0 ?> yêu cầu</span>
                                </div>
                                <div class="progress progress-sm mb-2">
                                    <div class="progress-bar bg-danger" style="width: <?= $statistics['total_notes'] > 0 ? round(($statistics['allergy'] ?? 0) * 100 / $statistics['total_notes'], 1) : 0 ?>%"></div>
                                </div>

                                <div class="d-flex justify-content-between mb-1">
                                    <span>♿ Di chuyển</span>
                                    <span><?= $statistics['mobility'] ?? 0 ?> yêu cầu</span>
                                </div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-info" style="width: <?= $statistics['total_notes'] > 0 ? round(($statistics['mobility'] ?? 0) * 100 / $statistics['total_notes'], 1) : 0 ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Tình trạng xử lý</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="text-center">
                                            <div class="avatar bg-light-danger p-50 mb-1">
                                                <div class="avatar-content">
                                                    <i data-feather="clock" class="font-medium-5"></i>
                                                </div>
                                            </div>
                                            <h5 class="mb-0"><?= $statistics['pending'] ?? 0 ?></h5>
                                            <small>Chờ xử lý</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center">
                                            <div class="avatar bg-light-warning p-50 mb-1">
                                                <div class="avatar-content">
                                                    <i data-feather="eye" class="font-medium-5"></i>
                                                </div>
                                            </div>
                                            <h5 class="mb-0"><?= $statistics['acknowledged'] ?? 0 ?></h5>
                                            <small>Đã nhận</small>
                                        </div>
                                    </div>
                                    <div class="col-6 mt-2">
                                        <div class="text-center">
                                            <div class="avatar bg-light-primary p-50 mb-1">
                                                <div class="avatar-content">
                                                    <i data-feather="settings" class="font-medium-5"></i>
                                                </div>
                                            </div>
                                            <h5 class="mb-0"><?= $statistics['in_progress'] ?? 0 ?></h5>
                                            <small>Đang xử lý</small>
                                        </div>
                                    </div>
                                    <div class="col-6 mt-2">
                                        <div class="text-center">
                                            <div class="avatar bg-light-success p-50 mb-1">
                                                <div class="avatar-content">
                                                    <i data-feather="check" class="font-medium-5"></i>
                                                </div>
                                            </div>
                                            <h5 class="mb-0"><?= $statistics['resolved'] ?? 0 ?></h5>
                                            <small>Hoàn thành</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Guest List with Special Requirements -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">
                                <i data-feather="users"></i>
                                Danh sách khách có yêu cầu đặc biệt
                            </h4>
                            <div>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="sendPreTourReminder()">
                                    <i data-feather="send"></i> Gửi nhắc nhở
                                </button>
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <?php if (empty($guests)): ?>
                                    <div class="text-center py-3">
                                        <i data-feather="smile" class="font-large-2 text-muted mb-2"></i>
                                        <p class="text-muted">Không có yêu cầu đặc biệt nào cho lịch khởi hành này</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($guests as $guest): ?>
                                        <?php 
                                        $priority_class = '';
                                        switch ($guest['max_priority']) {
                                            case 3: $priority_class = 'high-priority'; break;
                                            case 2: $priority_class = 'medium-priority'; break;
                                            default: $priority_class = 'low-priority'; break;
                                        }
                                        ?>
                                        <div class="card guest-card <?= $priority_class ?>">
                                            <div class="card-body">
                                                <div class="row align-items-center">
                                                    <div class="col-md-3">
                                                        <h6 class="mb-0">
                                                            <?= htmlspecialchars($guest['full_name']) ?>
                                                            <?php if ($guest['max_priority'] == 3): ?>
                                                                <span class="badge badge-danger badge-sm ml-1">Cao</span>
                                                            <?php elseif ($guest['max_priority'] == 2): ?>
                                                                <span class="badge badge-warning badge-sm ml-1">Trung bình</span>
                                                            <?php endif; ?>
                                                        </h6>
                                                        <small class="text-muted">
                                                            📞 <?= htmlspecialchars($guest['phone']) ?>
                                                            <?php if ($guest['room_number']): ?>
                                                                <br>🏠 Phòng <?= htmlspecialchars($guest['room_number']) ?>
                                                            <?php endif; ?>
                                                        </small>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <div class="requirements-summary">
                                                            <?= $guest['all_requirements'] ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 text-right">
                                                        <span class="badge badge-light-primary">
                                                            <?= $guest['note_count'] ?> yêu cầu
                                                        </span>
                                                        <br>
                                                        <a href="?act=danh-sach-ghi-chu&guest_id=<?= $guest['guest_id'] ?>" 
                                                           class="btn btn-sm btn-outline-primary mt-1">
                                                            <i data-feather="eye"></i> Chi tiết
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <a href="?act=danh-sach-lich-khoi-hanh" class="btn btn-outline-secondary">
                                <i data-feather="arrow-left"></i> Quay lại danh sách
                            </a>
                            <a href="?act=in-yeu-cau-dac-biet&schedule_id=<?= $schedule_id ?>" 
                               class="btn btn-outline-primary" target="_blank">
                                <i data-feather="printer"></i> In báo cáo
                            </a>
                            <a href="?act=xuat-pdf-yeu-cau-dac-biet&schedule_id=<?= $schedule_id ?>" 
                               class="btn btn-primary">
                                <i data-feather="download"></i> Xuất PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: Content-->

<script>
function sendPreTourReminder() {
    if (!confirm('Gửi thông báo nhắc nhở đến tất cả HDV và nhân viên liên quan?')) {
        return;
    }
    
    fetch('?act=gui-nhac-nho-truoc-tour', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'schedule_id=<?= $schedule_id ?>'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        alert('❌ Có lỗi xảy ra: ' + error);
    });
}

// Auto refresh every 30 seconds if there are pending notes
<?php if (($statistics['pending'] ?? 0) > 0): ?>
setTimeout(() => {
    location.reload();
}, 30000);
<?php endif; ?>
</script>

<?php require_once './views/core/footer.php'; ?>