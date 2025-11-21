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
                        <h2 class="content-header-title float-left mb-0">Tổng quan phân công nhân sự</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="staff-assignments">
                <!-- Bộ lọc -->
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="">
                            <input type="hidden" name="act" value="tong-quan-phan-cong">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Nhân viên:</label>
                                    <select name="staff_id" class="form-control">
                                        <option value="">Tất cả nhân viên</option>
                                        <?php foreach ($allStaff as $s): ?>
                                            <option value="<?= $s['staff_id'] ?>" <?= ($_GET['staff_id'] ?? '') == $s['staff_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($s['full_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Loại nhân sự:</label>
                                    <select name="staff_type" class="form-control">
                                        <option value="">Tất cả loại</option>
                                        <option value="Guide" <?= ($_GET['staff_type'] ?? '') == 'Guide' ? 'selected' : '' ?>>Hướng dẫn viên</option>
                                        <option value="Driver" <?= ($_GET['staff_type'] ?? '') == 'Driver' ? 'selected' : '' ?>>Tài xế</option>
                                        <option value="Support" <?= ($_GET['staff_type'] ?? '') == 'Support' ? 'selected' : '' ?>>Hỗ trợ</option>
                                        <option value="Manager" <?= ($_GET['staff_type'] ?? '') == 'Manager' ? 'selected' : '' ?>>Quản lý</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>Từ ngày:</label>
                                    <input type="date" name="from_date" class="form-control"
                                        value="<?= $_GET['from_date'] ?? date('Y-m-01') ?>">
                                </div>
                                <div class="col-md-2">
                                    <label>Đến ngày:</label>
                                    <input type="date" name="to_date" class="form-control"
                                        value="<?= $_GET['to_date'] ?? date('Y-m-t') ?>">
                                </div>
                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="feather icon-search"></i> Lọc
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Thống kê tổng quan -->
                <div class="row">
                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media d-flex">
                                        <div class="align-self-center">
                                            <i class="feather icon-users text-primary font-large-2 float-left"></i>
                                        </div>
                                        <div class="media-body text-right">
                                            <h3><?= $stats['total_staff'] ?? 0 ?></h3>
                                            <span>Nhân viên được phân công</span>
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
                                            <i class="feather icon-calendar text-success font-large-2 float-left"></i>
                                        </div>
                                        <div class="media-body text-right">
                                            <h3><?= $stats['total_schedules'] ?? 0 ?></h3>
                                            <span>Lịch khởi hành</span>
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
                                            <i class="feather icon-check-circle text-info font-large-2 float-left"></i>
                                        </div>
                                        <div class="media-body text-right">
                                            <h3><?= $stats['upcoming_schedules'] ?? 0 ?></h3>
                                            <span>Lịch sắp tới</span>
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
                                            <i class="feather icon-user-check text-warning font-large-2 float-left"></i>
                                        </div>
                                        <div class="media-body text-right">
                                            <h3><?= $stats['total_assignments'] ?? 0 ?></h3>
                                            <span>Tổng phân công</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Danh sách phân công -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Danh sách phân công nhân sự</h4>
                        <div>
                            <a href="?act=export-phan-cong&<?= http_build_query($_GET) ?>"
                                class="btn btn-success btn-sm">
                                <i class="feather icon-download"></i> Export Excel
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nhân viên</th>
                                        <th>Loại</th>
                                        <th>Tour</th>
                                        <th>Ngày khởi hành</th>
                                        <th>Vai trò</th>
                                        <th>Trạng thái</th>
                                        <th>Liên hệ</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($assignments)): ?>
                                        <?php foreach ($assignments as $assignment): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= htmlspecialchars($assignment['staff_name']) ?></strong>
                                                </td>
                                                <td>
                                                    <?php
                                                    $typeClass = match ($assignment['staff_type']) {
                                                        'Guide' => 'badge-primary',
                                                        'Driver' => 'badge-info',
                                                        'Support' => 'badge-success',
                                                        'Manager' => 'badge-warning',
                                                        default => 'badge-secondary'
                                                    };
                                                    $typeName = match ($assignment['staff_type']) {
                                                        'Guide' => 'HDV',
                                                        'Driver' => 'Tài xế',
                                                        'Support' => 'Hỗ trợ',
                                                        'Manager' => 'Quản lý',
                                                        default => $assignment['staff_type']
                                                    };
                                                    ?>
                                                    <span class="badge <?= $typeClass ?>"><?= $typeName ?></span>
                                                </td>
                                                <td>
                                                    <strong><?= htmlspecialchars($assignment['tour_name']) ?></strong><br>
                                                    <small
                                                        class="text-muted"><?= htmlspecialchars($assignment['tour_code'] ?? '') ?></small>
                                                </td>
                                                <td>
                                                    <?= date('d/m/Y', strtotime($assignment['departure_date'])) ?>
                                                    <?php if ($assignment['return_date']): ?>
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="feather icon-arrow-right"></i>
                                                            <?= date('d/m/Y', strtotime($assignment['return_date'])) ?>
                                                        </small>
                                                    <?php endif; ?>
                                                    <?php
                                                    $days_until = (strtotime($assignment['departure_date']) - time()) / 86400;
                                                    if ($days_until > 0 && $days_until <= 7): ?>
                                                        <br>
                                                        <span class="badge badge-warning badge-sm">
                                                            Còn <?= ceil($days_until) ?> ngày
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info">
                                                        <?= htmlspecialchars($assignment['role'] ?? 'Chưa xác định') ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php
                                                    $statusClass = match ($assignment['schedule_status']) {
                                                        'Open' => 'badge-success',
                                                        'Full' => 'badge-warning',
                                                        'Confirmed' => 'badge-primary',
                                                        'Completed' => 'badge-secondary',
                                                        'Cancelled' => 'badge-danger',
                                                        default => 'badge-light'
                                                    };
                                                    $statusText = match ($assignment['schedule_status']) {
                                                        'Open' => 'Mở đặt',
                                                        'Full' => 'Đã đầy',
                                                        'Confirmed' => 'Đã xác nhận',
                                                        'Completed' => 'Hoàn thành',
                                                        'Cancelled' => 'Đã hủy',
                                                        default => $assignment['schedule_status']
                                                    };
                                                    ?>
                                                    <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                                </td>
                                                <td>
                                                    <?php if (!empty($assignment['staff_phone'])): ?>
                                                        <a href="tel:<?= $assignment['staff_phone'] ?>">
                                                            <i class="feather icon-phone"></i>
                                                            <?= htmlspecialchars($assignment['staff_phone']) ?>
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="?act=chi-tiet-lich-khoi-hanh&id=<?= $assignment['schedule_id'] ?>"
                                                        class="btn btn-info btn-sm" title="Chi tiết lịch">
                                                        <i class="feather icon-eye"></i>
                                                    </a>
                                                    <a href="?act=chi-tiet-nhan-su&id=<?= $assignment['staff_id'] ?>"
                                                        class="btn btn-primary btn-sm" title="Hồ sơ nhân viên">
                                                        <i class="feather icon-user"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center">
                                                <div class="py-3">
                                                    <i class="feather icon-users font-large-2 text-muted"></i>
                                                    <p class="text-muted mt-2">Không có dữ liệu phân công trong khoảng thời
                                                        gian này</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if (!empty($assignments)): ?>
                            <div class="alert alert-info mt-3">
                                <strong>Tổng số:</strong> <?= count($assignments) ?> phân công trong khoảng thời gian đã
                                chọn
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<!-- END: Content-->
<?php require_once __DIR__ . '/../core/footer.php'; ?>