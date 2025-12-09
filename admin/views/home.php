<?php
/**
 * Dashboard Admin - Trang chủ quản trị
 */
require_once './views/core/header.php';
require_once './views/core/menu.php';
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
                        <h2 class="content-header-title float-left mb-0">
                            <i class="feather icon-home"></i> Dashboard
                        </h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item active">Trang chủ quản trị
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="content-body">
                <div class="alert alert-success alert-dismissible mb-2" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div class="d-flex align-items-center">
                        <i class="feather icon-check-circle mr-1"></i>
                        <span><?= htmlspecialchars($_SESSION['success']) ?></span>
                    </div>
                </div>
            </div>
            <?php unset($_SESSION['success']); endif; ?>

        <div class="content-body">
            <!-- Stats Cards -->
            <section id="dashboard-analytics">
                <div class="row">
                    <div class="col-lg-3 col-sm-6 col-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-start pb-0">
                                <div>
                                    <h2 class="text-bold-700 mb-0">
                                        <?php
                                        try {
                                            $modelTour = new Tour();
                                            echo $modelTour->countAll();
                                        } catch (Exception $e) {
                                            echo '0';
                                        }
                                        ?>
                                    </h2>
                                    <p class="mb-0">TOUR</p>
                                </div>
                                <div class="avatar bg-rgba-primary p-50 ml-auto">
                                    <div class="avatar-content">
                                        <i class="feather icon-map text-primary font-medium-5"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body pt-0">
                                    <a href="?act=list-tour" class="btn btn-sm btn-primary">Xem chi tiết →</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6 col-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-start pb-0">
                                <div>
                                    <h2 class="text-bold-700 mb-0">
                                        <?php
                                        try {
                                            $modelBooking = new Booking();
                                            echo $modelBooking->countAll();
                                        } catch (Exception $e) {
                                            echo '0';
                                        }
                                        ?>
                                    </h2>
                                    <p class="mb-0">BOOKING</p>
                                </div>
                                <div class="avatar bg-rgba-success p-50 ml-auto">
                                    <div class="avatar-content">
                                        <i class="feather icon-calendar text-success font-medium-5"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body pt-0">
                                    <a href="?act=list-booking" class="btn btn-sm btn-success">Xem chi tiết →</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6 col-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-start pb-0">
                                <div>
                                    <h2 class="text-bold-700 mb-0">
                                        <?php
                                        try {
                                            $modelStaff = new Staff();
                                            echo $modelStaff->countAll();
                                        } catch (Exception $e) {
                                            echo '0';
                                        }
                                        ?>
                                    </h2>
                                    <p class="mb-0">NHÂN SỰ</p>
                                </div>
                                <div class="avatar bg-rgba-info p-50 ml-auto">
                                    <div class="avatar-content">
                                        <i class="feather icon-users text-info font-medium-5"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body pt-0">
                                    <a href="?act=danh-sach-nhan-su" class="btn btn-sm btn-info">Xem chi tiết →</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6 col-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-start pb-0">
                                <div>
                                    <h2 class="text-bold-700 mb-0">
                                        <?php
                                        try {
                                            $modelSchedule = new TourSchedule();
                                            $allSchedules = $modelSchedule->getAllSchedules();
                                            echo count($allSchedules);
                                        } catch (Exception $e) {
                                            echo '0';
                                        }
                                        ?>
                                    </h2>
                                    <p class="mb-0">LỊCH KHỞI HÀNH</p>
                                </div>
                                <div class="avatar bg-rgba-warning p-50 ml-auto">
                                    <div class="avatar-content">
                                        <i class="feather icon-clock text-warning font-medium-5"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body pt-0">
                                    <a href="?act=list-schedule" class="btn btn-sm btn-warning">Xem chi tiết →</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Quick Actions -->
            <section id="quick-actions">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <i class="feather icon-zap"></i> Thao tác nhanh
                                </h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-xl-3 col-md-6 col-12 mb-1">
                                            <a href="?act=them-lich-khoi-hanh" class="btn btn-primary btn-block">
                                                <i class="feather icon-plus-circle"></i> Thêm lịch khởi hành
                                            </a>
                                        </div>
                                        <div class="col-xl-3 col-md-6 col-12 mb-1">
                                            <a href="?act=add-booking" class="btn btn-success btn-block">
                                                <i class="feather icon-book"></i> Tạo booking
                                            </a>
                                        </div>
                                        <div class="col-xl-3 col-md-6 col-12 mb-1">
                                            <a href="?act=them-nhan-su" class="btn btn-warning btn-block">
                                                <i class="feather icon-user-plus"></i> Thêm nhân sự
                                            </a>
                                        </div>
                                        <div class="col-xl-3 col-md-6 col-12 mb-1">
                                            <a href="?act=create-quote" class="btn btn-info btn-block">
                                                <i class="feather icon-file-text"></i> Tạo báo giá
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- System Info & Quick Links -->
            <section id="system-info">
                <div class="row match-height">
                    <div class="col-lg-6 col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <i class="feather icon-activity"></i> Thông tin hệ thống
                                </h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <ul class="list-group">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="feather icon-check-circle text-success mr-1"></i> Trạng thái
                                                hệ thống</span>
                                            <span class="badge badge-pill badge-success">Hoạt động</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="feather icon-database text-info mr-1"></i> Database</span>
                                            <span class="badge badge-pill badge-info">Connected</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="feather icon-user text-primary mr-1"></i> Người dùng</span>
                                            <span
                                                class="badge badge-pill badge-primary"><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="feather icon-shield text-warning mr-1"></i> Vai trò</span>
                                            <span
                                                class="badge badge-pill badge-warning"><?= htmlspecialchars($_SESSION['role_name'] ?? 'ADMIN') ?></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <i class="feather icon-link"></i> Liên kết nhanh
                                </h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="list-group">
                                        <a href="?act=list-tour" class="list-group-item list-group-item-action">
                                            <i class="feather icon-map text-primary mr-1"></i> Quản lý Tour
                                        </a>
                                        <a href="?act=list-schedule" class="list-group-item list-group-item-action">
                                            <i class="feather icon-calendar text-success mr-1"></i> Lịch khởi hành
                                        </a>
                                        <a href="?act=list-booking" class="list-group-item list-group-item-action">
                                            <i class="feather icon-book text-info mr-1"></i> Quản lý Booking
                                        </a>
                                        <a href="?act=danh-sach-nhan-su" class="list-group-item list-group-item-action">
                                            <i class="feather icon-users text-warning mr-1"></i> Quản lý Nhân sự
                                        </a>
                                        <a href="?act=list-quotes" class="list-group-item list-group-item-action">
                                            <i class="feather icon-file-text text-secondary mr-1"></i> Báo giá
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<!-- END: Content-->

<?php require_once './views/core/footer.php'; ?>