<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ</title>
    <link rel="stylesheet" type="text/css" href="admin/views/assetz/app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="admin/views/assetz/app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="admin/views/assetz/app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="admin/views/assetz/app-assets/css/components.css">
</head>

<body class="p-2">
    <?php if (session_status() === PHP_SESSION_NONE)
        session_start(); ?>
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <h2 class="mb-0">Xin chào, <?= htmlspecialchars($_SESSION['username'] ?? 'Khách') ?></h2>
                <small class="text-muted">Vai trò: <?= htmlspecialchars($_SESSION['role_code'] ?? 'N/A') ?></small>
            </div>
        </div>
        <?php require_once __DIR__ . '/../admin/views/core/alert.php'; ?>
        <?php if (!empty($title)): ?>
            <div class="alert alert-info"><strong><?= htmlspecialchars($title) ?></strong> -
                <?= htmlspecialchars($thoiTiet ?? '') ?></div>
        <?php endif; ?>

        <?php if (function_exists('isAdmin') && isAdmin()): ?>
            <div class="row">
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h5 class="card-title">Quản lý Tour</h5>
                            <p class="card-text small">Thêm / sửa tour và lịch khởi hành.</p>
                            <a href="admin/index.php?act=danh-sach-tour" class="btn btn-light btn-sm">Đi tới</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <h5 class="card-title">Nhân sự</h5>
                            <p class="card-text small">Theo dõi và phân công HDV / tài xế.</p>
                            <a href="admin/index.php?act=danh-sach-nhan-su" class="btn btn-light btn-sm">Đi tới</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <h5 class="card-title">Lịch khởi hành</h5>
                            <p class="card-text small">Tạo, phân công nhân sự và dịch vụ.</p>
                            <a href="admin/index.php?act=danh-sach-lich-khoi-hanh" class="btn btn-light btn-sm">Đi tới</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h5 class="card-title">Báo cáo</h5>
                            <p class="card-text small">Xuất báo cáo lịch & dịch vụ.</p>
                            <a href="admin/index.php?act=bao-cao" class="btn btn-light btn-sm">Xem</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif (function_exists('isGuide') && isGuide()): ?>
            <div class="row">
                <div class="col-12 mb-2">
                    <h4>Lịch khởi hành sắp tới của bạn</h4>
                </div>
                <?php
                // Hiển thị nhanh tối đa 6 lịch sắp tới (nếu controller đã chuẩn bị biến $upcomingSchedules)
                if (!empty($upcomingSchedules)):
                    foreach ($upcomingSchedules as $sch): ?>
                        <div class="col-md-4 col-sm-6 mb-2">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <h5 class="card-title mb-0"><?= htmlspecialchars($sch['tour_name'] ?? 'Tour') ?></h5>
                                    <small class="text-muted">Mã: <?= htmlspecialchars($sch['tour_code'] ?? '') ?></small>
                                    <div class="mt-1">
                                        <span class="badge badge-info">KH:
                                            <?= date('d/m', strtotime($sch['departure_date'])) ?></span>
                                        <?php if (!empty($sch['return_date'])): ?>
                                            <span class="badge badge-secondary">KT:
                                                <?= date('d/m', strtotime($sch['return_date'])) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mt-1 small">Điểm tập trung: <?= htmlspecialchars($sch['meeting_point'] ?? '-') ?>
                                    </div>
                                    <a href="admin/index.php?act=chi-tiet-lich-khoi-hanh&id=<?= $sch['schedule_id'] ?>"
                                        class="btn btn-sm btn-primary mt-2">Chi tiết</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; else: ?>
                    <div class="col-12">
                        <p class="text-muted">Chưa có lịch được phân công.</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="mt-4 text-center">
            <a href="?act=logout" class="btn btn-outline-danger btn-sm"><i class="feather icon-log-out"></i> Đăng
                xuất</a>
        </div>
    </div>
</body>

</html>