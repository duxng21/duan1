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
                        <h2 class="content-header-title float-left mb-0">Chi tiết nhân sự</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="?act=danh-sach-nhan-su">Danh sách nhân sự</a></li>
                                <li class="breadcrumb-item active"><?= htmlspecialchars($staff['full_name']) ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="staff-detail">
                <div class="row">
                    <!-- Thông tin cơ bản -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Thông tin cá nhân</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Họ tên:</strong></td>
                                            <td><?= htmlspecialchars($staff['full_name']) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Loại:</strong></td>
                                            <td>
                                                <?php
                                                switch ($staff['staff_type']) {
                                                    case 'Guide':
                                                        $typeClass = 'badge-primary';
                                                        $typeName = 'Hướng dẫn viên';
                                                        break;
                                                    case 'Driver':
                                                        $typeClass = 'badge-info';
                                                        $typeName = 'Tài xế';
                                                        break;
                                                    case 'Support':
                                                        $typeClass = 'badge-warning';
                                                        $typeName = 'Hỗ trợ';
                                                        break;
                                                    case 'Manager':
                                                        $typeClass = 'badge-success';
                                                        $typeName = 'Quản lý';
                                                        break;
                                                    default:
                                                        $typeClass = 'badge-light';
                                                        $typeName = $staff['staff_type'];
                                                        break;
                                                }
                                                ?>
                                                <span class="badge <?= $typeClass ?>"><?= $typeName ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Điện thoại:</strong></td>
                                            <td><?= htmlspecialchars($staff['phone'] ?? 'N/A') ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td><?= htmlspecialchars($staff['email'] ?? 'N/A') ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>CMND/CCCD:</strong></td>
                                            <td><?= htmlspecialchars($staff['id_card'] ?? 'N/A') ?></td>
                                        </tr>
                                        <?php if ($staff['license_number']): ?>
                                            <tr>
                                                <td><strong>Bằng lái:</strong></td>
                                                <td><?= htmlspecialchars($staff['license_number']) ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <td><strong>Kinh nghiệm:</strong></td>
                                            <td><?= $staff['experience_years'] ?> năm</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Ngôn ngữ:</strong></td>
                                            <td><?= htmlspecialchars($staff['languages'] ?? 'N/A') ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Trạng thái:</strong></td>
                                            <td>
                                                <span
                                                    class="badge <?= $staff['status'] ? 'badge-success' : 'badge-secondary' ?>">
                                                    <?= $staff['status'] ? 'Đang làm việc' : 'Nghỉ việc' ?>
                                                </span>
                                            </td>
                                        </tr>
                                    </table>

                                    <?php if ($staff['notes']): ?>
                                        <div class="mt-2">
                                            <strong>Ghi chú:</strong>
                                            <p><?= nl2br(htmlspecialchars($staff['notes'])) ?></p>
                                        </div>
                                    <?php endif; ?>

                                    <div class="mt-3">
                                        <a href="?act=sua-nhan-su&id=<?= $staff['staff_id'] ?>"
                                            class="btn btn-primary btn-block">
                                            <i class="feather icon-edit"></i> Chỉnh sửa
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Thông tin tài khoản -->
                        <?php if ($staff['staff_type'] === 'Guide'): ?>
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        <i class="feather icon-user"></i> Tài khoản đăng nhập
                                    </h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <?php if ($userAccount): ?>
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>Username:</strong></td>
                                                    <td>
                                                        <code><?= htmlspecialchars($userAccount['username']) ?></code>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Email:</strong></td>
                                                    <td><?= htmlspecialchars($userAccount['email'] ?? 'N/A') ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Vai trò:</strong></td>
                                                    <td>
                                                        <span class="badge badge-primary">
                                                            <?= htmlspecialchars($userAccount['role_name'] ?? $userAccount['role_code']) ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Trạng thái:</strong></td>
                                                    <td>
                                                        <?php
                                                        switch ($userAccount['status']) {
                                                            case 'Active':
                                                                $statusClass = 'badge-success';
                                                                $statusText = 'Hoạt động';
                                                                break;
                                                            case 'Locked':
                                                                $statusClass = 'badge-danger';
                                                                $statusText = 'Đã khóa';
                                                                break;
                                                            case 'Inactive':
                                                                $statusClass = 'badge-secondary';
                                                                $statusText = 'Không hoạt động';
                                                                break;
                                                            default:
                                                                $statusClass = 'badge-light';
                                                                $statusText = $userAccount['status'];
                                                                break;
                                                        }
                                                        ?>
                                                        <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Lần đăng nhập cuối:</strong></td>
                                                    <td>
                                                        <?php if ($userAccount['last_login']): ?>
                                                            <?= date('d/m/Y H:i', strtotime($userAccount['last_login'])) ?>
                                                        <?php else: ?>
                                                            <span class="text-muted">Chưa đăng nhập</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Số lần đăng nhập sai:</strong></td>
                                                    <td>
                                                        <?php
                                                        $attempts = $userAccount['login_attempts'] ?? 0;
                                                        $attemptsClass = $attempts >= 3 ? 'text-danger' : 'text-muted';
                                                        ?>
                                                        <span class="<?= $attemptsClass ?>">
                                                            <?= $attempts ?> lần
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Ngày tạo:</strong></td>
                                                    <td>
                                                        <?= date('d/m/Y', strtotime($userAccount['created_at'])) ?>
                                                    </td>
                                                </tr>
                                            </table>

                                            <div class="mt-3">
                                                <?php if ($userAccount['status'] === 'Active'): ?>
                                                    <a href="?act=doi-trang-thai-user&user_id=<?= $userAccount['user_id'] ?>&action=lock"
                                                        onclick="return confirm('Khóa tài khoản này?')"
                                                        class="btn btn-danger btn-block">
                                                        <i class="feather icon-lock"></i> Khóa tài khoản
                                                    </a>
                                                <?php else: ?>
                                                    <a href="?act=doi-trang-thai-user&user_id=<?= $userAccount['user_id'] ?>&action=unlock"
                                                        onclick="return confirm('Mở khóa tài khoản này?')"
                                                        class="btn btn-success btn-block">
                                                        <i class="feather icon-unlock"></i> Mở khóa tài khoản
                                                    </a>
                                                <?php endif; ?>
                                                <a href="?act=danh-sach-user" class="btn btn-outline-secondary btn-block mt-1">
                                                    <i class="feather icon-users"></i> Xem danh sách tài khoản
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center py-3">
                                                <i class="feather icon-user-x font-large-2 text-muted"></i>
                                                <p class="text-muted mt-2">Chưa có tài khoản đăng nhập</p>
                                                <a href="?act=tao-tai-khoan-hdv&id=<?= $staff['staff_id'] ?>"
                                                    onclick="return confirm('Tạo tài khoản đăng nhập cho HDV: <?= htmlspecialchars($staff['full_name']) ?>?')"
                                                    class="btn btn-primary">
                                                    <i class="feather icon-user-plus"></i> Tạo tài khoản
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Lịch làm việc -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <i class="feather icon-calendar"></i> Lịch làm việc
                                </h4>
                                <div class="heading-elements">
                                    <span class="badge badge-primary">
                                        <?= count($schedules) ?> lịch
                                    </span>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <!-- Bộ lọc ngày -->
                                    <form method="GET" action="" class="mb-3">
                                        <input type="hidden" name="act" value="chi-tiet-nhan-su">
                                        <input type="hidden" name="id" value="<?= $staff['staff_id'] ?>">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label>Từ ngày:</label>
                                                <input type="date" class="form-control" name="from_date"
                                                    value="<?= $from_date ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <label>Đến ngày:</label>
                                                <input type="date" class="form-control" name="to_date"
                                                    value="<?= $to_date ?>">
                                            </div>
                                            <div class="col-md-2">
                                                <label>&nbsp;</label>
                                                <button type="submit" class="btn btn-primary btn-block">
                                                    <i class="feather icon-search"></i> Lọc
                                                </button>
                                            </div>
                                            <div class="col-md-2">
                                                <label>&nbsp;</label>
                                                <a href="?act=chi-tiet-nhan-su&id=<?= $staff['staff_id'] ?>"
                                                    class="btn btn-secondary btn-block">
                                                    <i class="feather icon-refresh-cw"></i> Reset
                                                </a>
                                            </div>
                                        </div>

                                        <!-- Quick filters -->
                                        <div class="row mt-2">
                                            <div class="col-12">
                                                <small class="text-muted">Lọc nhanh:</small>
                                                <div class="btn-group btn-group-sm ml-2">
                                                    <a href="?act=chi-tiet-nhan-su&id=<?= $staff['staff_id'] ?>&from_date=<?= date('Y-m-01') ?>&to_date=<?= date('Y-m-t') ?>"
                                                        class="btn btn-outline-primary">Tháng này</a>
                                                    <a href="?act=chi-tiet-nhan-su&id=<?= $staff['staff_id'] ?>&from_date=<?= date('Y-01-01') ?>&to_date=<?= date('Y-12-31') ?>"
                                                        class="btn btn-outline-primary">Năm nay</a>
                                                    <a href="?act=chi-tiet-nhan-su&id=<?= $staff['staff_id'] ?>&from_date=<?= date('Y-m-d') ?>&to_date=<?= date('Y-m-d', strtotime('+30 days')) ?>"
                                                        class="btn btn-outline-primary">30 ngày tới</a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Thời gian</th>
                                                    <th>Tour</th>
                                                    <th>Vai trò</th>
                                                    <th>Trạng thái</th>
                                                    <th>Hành động</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($schedules)): ?>
                                                    <?php foreach ($schedules as $schedule): ?>
                                                        <tr>
                                                            <td>
                                                                <strong><?= date('d/m/Y', strtotime($schedule['departure_date'])) ?></strong>
                                                                <?php if ($schedule['return_date']): ?>
                                                                    <br>
                                                                    <small class="text-muted">
                                                                        <i class="feather icon-arrow-right"></i>
                                                                        <?= date('d/m/Y', strtotime($schedule['return_date'])) ?>
                                                                    </small>
                                                                <?php endif; ?>
                                                                <br>
                                                                <?php
                                                                $days_until = (strtotime($schedule['departure_date']) - time()) / 86400;
                                                                if ($days_until > 0 && $days_until <= 7): ?>
                                                                    <span class="badge badge-warning badge-sm">
                                                                        Còn <?= ceil($days_until) ?> ngày
                                                                    </span>
                                                                <?php elseif ($days_until < 0): ?>
                                                                    <span class="badge badge-secondary badge-sm">Đã qua</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <strong><?= htmlspecialchars($schedule['tour_name']) ?></strong>
                                                                <?php if (!empty($schedule['meeting_point'])): ?>
                                                                    <br>
                                                                    <small class="text-muted">
                                                                        <i class="feather icon-map-pin"></i>
                                                                        <?= htmlspecialchars($schedule['meeting_point']) ?>
                                                                    </small>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-info">
                                                                    <?= htmlspecialchars($schedule['role'] ?? 'Chưa xác định') ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                switch ($schedule['status']) {
                                                                    case 'Open':
                                                                        $statusClass = 'badge-success';
                                                                        $statusText = 'Mở đặt';
                                                                        break;
                                                                    case 'Full':
                                                                        $statusClass = 'badge-warning';
                                                                        $statusText = 'Đã đầy';
                                                                        break;
                                                                    case 'Confirmed':
                                                                        $statusClass = 'badge-primary';
                                                                        $statusText = 'Đã xác nhận';
                                                                        break;
                                                                    case 'Completed':
                                                                        $statusClass = 'badge-secondary';
                                                                        $statusText = 'Hoàn thành';
                                                                        break;
                                                                    case 'Cancelled':
                                                                        $statusClass = 'badge-danger';
                                                                        $statusText = 'Đã hủy';
                                                                        break;
                                                                    default:
                                                                        $statusClass = 'badge-light';
                                                                        $statusText = $schedule['status'];
                                                                        break;
                                                                }
                                                                ?>
                                                                <span
                                                                    class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                                            </td>
                                                            <td>
                                                                <a href="?act=chi-tiet-lich-khoi-hanh&id=<?= $schedule['schedule_id'] ?>"
                                                                    class="btn btn-info btn-sm" title="Chi tiết">
                                                                    <i class="feather icon-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center">
                                                            <div class="py-3">
                                                                <i
                                                                    class="feather icon-calendar font-large-2 text-muted"></i>
                                                                <p class="text-muted mt-2">
                                                                    Không có lịch làm việc trong khoảng thời gian này
                                                                </p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <?php if (!empty($schedules)): ?>
                                        <?php
                                        // Thống kê
                                        $upcoming = array_filter($schedules, fn($s) => strtotime($s['departure_date']) >= time());
                                        $completed = array_filter($schedules, fn($s) => $s['status'] == 'Completed');
                                        $cancelled = array_filter($schedules, fn($s) => $s['status'] == 'Cancelled');
                                        ?>
                                        <div class="row mt-3">
                                            <div class="col-md-3">
                                                <div class="alert alert-primary mb-0">
                                                    <strong><?= count($schedules) ?></strong>
                                                    <p class="mb-0 small">Tổng lịch</p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="alert alert-success mb-0">
                                                    <strong><?= count($upcoming) ?></strong>
                                                    <p class="mb-0 small">Sắp tới</p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="alert alert-secondary mb-0">
                                                    <strong><?= count($completed) ?></strong>
                                                    <p class="mb-0 small">Đã hoàn thành</p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="alert alert-danger mb-0">
                                                    <strong><?= count($cancelled) ?></strong>
                                                    <p class="mb-0 small">Đã hủy</p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
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
<?php require_once __DIR__ . '/../core/footer.php'; ?>