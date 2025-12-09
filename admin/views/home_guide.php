<?php
/**
 * Dashboard cho Hướng Dẫn Viên
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
                            <i class="feather icon-map text-primary"></i> Chào mừng,
                            <?= htmlspecialchars($_SESSION['full_name'] ?? 'Hướng dẫn viên') ?>
                        </h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item active">Dashboard HDV
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <!-- Thống kê nhanh -->
            <div class="row">
                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-start pb-0">
                            <div>
                                <h2 class="text-bold-700 mb-0"><?= $stats['upcoming_tours'] ?? 0 ?></h2>
                                <p class="mb-0">Tour sắp tới</p>
                            </div>
                            <div class="avatar bg-rgba-primary p-50 m-0">
                                <div class="avatar-content">
                                    <i class="feather icon-calendar text-primary font-medium-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-start pb-0">
                            <div>
                                <h2 class="text-bold-700 mb-0"><?= $stats['in_progress_tours'] ?? 0 ?></h2>
                                <p class="mb-0">Tour đang diễn ra</p>
                            </div>
                            <div class="avatar bg-rgba-success p-50 m-0">
                                <div class="avatar-content">
                                    <i class="feather icon-check-circle text-success font-medium-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-start pb-0">
                            <div>
                                <h2 class="text-bold-700 mb-0"><?= $stats['completed_this_month'] ?? 0 ?></h2>
                                <p class="mb-0">Hoàn thành tháng này</p>
                            </div>
                            <div class="avatar bg-rgba-warning p-50 m-0">
                                <div class="avatar-content">
                                    <i class="feather icon-star text-warning font-medium-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-start pb-0">
                            <div>
                                <h2 class="text-bold-700 mb-0"><?= number_format($stats['avg_rating'] ?? 0, 1) ?> <small
                                        class="text-muted">/ 5.0</small></h2>
                                <p class="mb-0">Đánh giá trung bình</p>
                            </div>
                            <div class="avatar bg-rgba-info p-50 m-0">
                                <div class="avatar-content">
                                    <i class="feather icon-award text-info font-medium-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tour sắp tới -->
            <?php if (!empty($upcoming_tours)): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><i class="feather icon-calendar text-primary"></i> Tour sắp tới của
                                    bạn
                                </h4>
                            </div>
                            <div class="card-content">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Tour</th>
                                                <th>Ngày khởi hành</th>
                                                <th>Điểm tập trung</th>
                                                <th>Số khách</th>
                                                <th>Vai trò</th>
                                                <th>Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($upcoming_tours as $tour): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= htmlspecialchars($tour['tour_name']) ?></strong>
                                                    </td>
                                                    <td>
                                                        <i class="feather icon-calendar"></i>
                                                        <?= date('d/m/Y', strtotime($tour['departure_date'])) ?>
                                                    </td>
                                                    <td>
                                                        <i class="feather icon-map-pin"></i>
                                                        <?= htmlspecialchars($tour['meeting_point'] ?? 'Chưa có') ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-light-info">
                                                            <?= $tour['current_participants'] ?? 0 ?>/<?= $tour['max_participants'] ?? 0 ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        switch ($tour['role'] ?? '') {
                                                            case 'Trưởng đoàn':
                                                                $roleClass = 'badge-primary';
                                                                break;
                                                            case 'Hướng dẫn viên':
                                                                $roleClass = 'badge-success';
                                                                break;
                                                            case 'Tài xế':
                                                                $roleClass = 'badge-info';
                                                                break;
                                                            default:
                                                                $roleClass = 'badge-secondary';
                                                                break;
                                                        }
                                                        ?>
                                                        <span class="badge <?= $roleClass ?>">
                                                            <?= htmlspecialchars($tour['role'] ?? 'N/A') ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="?act=hdv-chi-tiet-tour&id=<?= $tour['schedule_id'] ?>"
                                                            class="btn btn-sm btn-outline-primary">
                                                            <i class="feather icon-eye"></i> Chi tiết
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
            <?php endif; ?>

            <!-- Tour đang diễn ra -->
            <?php if (!empty($in_progress_tours)): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card border-success">
                            <div class="card-header bg-success">
                                <h4 class="card-title text-white"><i class="feather icon-activity"></i> Tour đang diễn ra
                                </h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <?php foreach ($in_progress_tours as $tour): ?>
                                        <div class="alert alert-success alert-dismissible mb-2" role="alert">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-success mr-1">
                                                    <i class="feather icon-map font-medium-3"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="mb-0"><?= htmlspecialchars($tour['tour_name']) ?></h5>
                                                    <small>
                                                        <i class="feather icon-calendar"></i>
                                                        <?= date('d/m/Y', strtotime($tour['departure_date'])) ?> -
                                                        <?= date('d/m/Y', strtotime($tour['return_date'])) ?>
                                                    </small>
                                                </div>
                                                <div>
                                                    <a href="?act=hdv-nhiem-vu-cua-toi&schedule_id=<?= $tour['schedule_id'] ?>"
                                                        class="btn btn-success btn-sm">
                                                        <i class="feather icon-check-square"></i> Nhiệm vụ
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Trạng thái trống -->
            <?php if (empty($upcoming_tours) && empty($in_progress_tours)): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="feather icon-calendar font-large-2 text-muted mb-2"></i>
                                <h4 class="text-muted">Hiện tại bạn chưa được phân công tour nào</h4>
                                <p class="text-muted">Vui lòng liên hệ quản lý để được phân công tour</p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"><i class="feather icon-link"></i> Thao tác nhanh</h4>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <a href="?act=hdv-lich-cua-toi" class="list-group-item list-group-item-action">
                                    <i class="feather icon-list text-primary"></i>
                                    <span class="ml-1">Xem tất cả tour của tôi</span>
                                </a>
                                <a href="?act=hdv-xem-lich-thang" class="list-group-item list-group-item-action">
                                    <i class="feather icon-calendar text-success"></i>
                                    <span class="ml-1">Xem lịch theo tháng</span>
                                </a>
                                <a href="?act=doi-mat-khau" class="list-group-item list-group-item-action">
                                    <i class="feather icon-lock text-warning"></i>
                                    <span class="ml-1">Đổi mật khẩu</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"><i class="feather icon-info"></i> Hỗ trợ & Liên hệ</h4>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info mb-2">
                                <h6 class="alert-heading mb-1">Điều hành 24/7</h6>
                                <p class="mb-0">
                                    <i class="feather icon-phone"></i> Hotline: <strong>1900 xxxx</strong>
                                </p>
                            </div>
                            <div class="alert alert-warning mb-0">
                                <h6 class="alert-heading mb-1">Khẩn cấp</h6>
                                <p class="mb-0">
                                    <i class="feather icon-phone-call"></i> SOS: <strong>0909 xxx xxx</strong>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: Content-->

<?php require_once './views/core/footer.php'; ?>